<?php

namespace Modules\LMS\Courses\Application\UseCases;

use App\Support\BaseUseCase;
use App\Contracts\Services\TransactionManagerInterface;
use App\Contracts\Services\FileStorageInterface;
use Modules\LMS\Courses\Application\DTOs\CreateCourseDTO;
use Modules\LMS\Courses\Domain\Events\CourseCreated;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Modules\LMS\Courses\Domain\Services\CourseSessionGeneratorService;
use Modules\LMS\Courses\Domain\Services\CourseEndDateCalculatorService;
use Modules\LMS\Courses\Domain\Services\CourseSlugGeneratorService;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Support\Facades\Event;

/**
 * UseCase for creating a course
 * Follows SOLID Principles:
 * - SRP: Only responsible for orchestrating course creation
 * - DIP: Depends on interfaces, not concrete implementations
 */
class CreateCourseUseCase extends BaseUseCase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private CourseSessionGeneratorService $sessionGenerator,
        private CourseEndDateCalculatorService $endDateCalculator,
        private CourseSlugGeneratorService $slugGenerator,
        private FileStorageInterface $fileStorage,
        private TransactionManagerInterface $transactionManager
    ) {
    }

    protected function handle(mixed $input): Course
    {
        /** @var CreateCourseDTO $dto */
        $dto = $input;
        $dto->validate();

        return $this->transactionManager->transaction(function () use ($dto) {
            $data = $dto->toArray();
            
            // Handle image upload using service (DIP)
            if ($dto->image) {
                $data['image_path'] = $this->fileStorage->upload($dto->image, 'courses', 'public');
            }
            unset($data['image']);

            // Generate slug and code using domain service (SRP)
            $data['slug'] = $this->slugGenerator->generateSlug($dto->title);
            $data['code'] = $this->slugGenerator->generateCode($dto->code);

            // Calculate end_date using domain service (SRP)
            if ($dto->autoGenerateSessions && $dto->startDate && $dto->sessionCount > 0) {
                $endDate = $this->endDateCalculator->calculateEndDate(
                    $dto->startDate,
                    $dto->sessionCount,
                    $dto->daysOfWeek ?? []
                );
                $data['end_date'] = $endDate?->toDateString();
            }

            $course = $this->courseRepository->create($data);

            // Sync instructors
            $this->courseRepository->syncInstructors(
                $course,
                $dto->instructors ?? [],
                $dto->supervisors ?? []
            );

            // Auto-generate sessions using domain service (SRP)
            if ($course->auto_generate_sessions && $course->start_date && $course->session_count) {
                $this->sessionGenerator->generateSessionsForCourse(
                    $course,
                    $course->session_count,
                    $course->days_of_week ?? [],
                    $course->start_date
                );
            }

            $course = $this->courseRepository->loadRelations($course, ['instructors', 'sessions']);

            // Dispatch domain event
            Event::dispatch(new CourseCreated(
                $course->id,
                $course->title,
                $course->code,
                $course->category_id
            ));

            return $course;
        });
    }
}

