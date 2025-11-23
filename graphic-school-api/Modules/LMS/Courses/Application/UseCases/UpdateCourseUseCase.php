<?php

namespace Modules\LMS\Courses\Application\UseCases;

use App\Support\BaseUseCase;
use App\Contracts\Services\TransactionManagerInterface;
use App\Contracts\Services\FileStorageInterface;
use Modules\LMS\Courses\Application\DTOs\UpdateCourseDTO;
use Modules\LMS\Courses\Domain\Events\CourseUpdated;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use App\Contracts\Repositories\SessionRepositoryInterface;
use Modules\LMS\Courses\Domain\Services\CourseSessionGeneratorService;
use Modules\LMS\Courses\Domain\Services\CourseEndDateCalculatorService;
use Modules\LMS\Courses\Domain\Services\CourseSlugGeneratorService;
use Modules\LMS\Courses\Models\Course;
use App\Services\EntityTranslationService;
use Illuminate\Support\Facades\Event;

/**
 * UseCase for updating a course
 * Follows SOLID Principles:
 * - SRP: Only responsible for orchestrating course update
 * - DIP: Depends on interfaces, not concrete implementations
 */
class UpdateCourseUseCase extends BaseUseCase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private SessionRepositoryInterface $sessionRepository,
        private CourseSessionGeneratorService $sessionGenerator,
        private CourseEndDateCalculatorService $endDateCalculator,
        private CourseSlugGeneratorService $slugGenerator,
        private FileStorageInterface $fileStorage,
        private TransactionManagerInterface $transactionManager
    ) {
    }

    protected function handle(mixed $input): Course
    {
        /** @var Course $course */
        /** @var UpdateCourseDTO $dto */
        [$course, $dto] = $input;

        return $this->transactionManager->transaction(function () use ($course, $dto) {
            $oldData = $course->toArray();
            $data = $dto->toArray();

            // Handle image upload using service (DIP)
            if ($dto->image) {
                if ($course->image_path) {
                    $this->fileStorage->delete($course->image_path, 'public');
                }
                $data['image_path'] = $this->fileStorage->upload($dto->image, 'courses', 'public');
            }
            unset($data['image']);

            // Update slug if title changed using domain service (SRP)
            if (isset($data['title'])) {
                $data['slug'] = $this->slugGenerator->generateSlug($data['title']);
            }

            // Recalculate end_date using domain service (SRP)
            if (($data['auto_generate_sessions'] ?? $course->auto_generate_sessions) &&
                ($data['start_date'] ?? $course->start_date) &&
                ($data['session_count'] ?? $course->session_count) > 0 &&
                (isset($data['start_date']) || isset($data['session_count']) || isset($data['days_of_week']))) {
                $endDate = $this->endDateCalculator->calculateEndDate(
                    $data['start_date'] ?? $course->start_date,
                    $data['session_count'] ?? $course->session_count,
                    $data['days_of_week'] ?? $course->days_of_week ?? []
                );
                $data['end_date'] = $endDate?->toDateString();
            }

            $course = $this->courseRepository->update($course, $data);

            // Sync instructors if provided
            if (isset($data['instructors']) || isset($data['supervisors'])) {
                $this->courseRepository->syncInstructors(
                    $course,
                    $data['instructors'] ?? [],
                    $data['supervisors'] ?? []
                );
            }

            // Regenerate sessions using domain service (SRP)
            if ($dto->regenerateSessions) {
                $this->sessionRepository->deleteByCourseId($course->id);
                $this->sessionGenerator->generateSessionsForCourse(
                    $course,
                    $course->session_count,
                    $course->days_of_week ?? [],
                    $course->start_date
                );
            }

            $course = $this->courseRepository->loadRelations($course, ['instructors', 'sessions']);

            // Update translations if provided
            if (!empty($dto->translations)) {
                $translationService = app(EntityTranslationService::class);
                $translationService->saveTranslations($course, $dto->translations);
            }

            // Dispatch domain event
            Event::dispatch(new CourseUpdated(
                $course->id,
                $data
            ));

            return $course;
        });
    }
}

