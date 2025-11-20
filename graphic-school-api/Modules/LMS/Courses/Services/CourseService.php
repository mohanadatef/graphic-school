<?php

namespace Modules\LMS\Courses\Services;

use Modules\LMS\Courses\Enums\CourseStatus;
use Modules\LMS\Sessions\Enums\SessionStatus;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Modules\LMS\Sessions\Repositories\Interfaces\SessionRepositoryInterface;
use Modules\LMS\Courses\Domain\Services\CourseSessionGeneratorService;
use Modules\LMS\Courses\Domain\Services\CourseEndDateCalculatorService;
use Modules\LMS\Courses\Domain\Services\CourseSlugGeneratorService;
use App\Contracts\Services\FileStorageInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @deprecated This service is deprecated. Use UseCases instead.
 * This class is kept for backward compatibility only.
 * All new code should use UseCases in Application/UseCases directory.
 */
class CourseService
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private SessionRepositoryInterface $sessionRepository,
        private CourseSessionGeneratorService $sessionGenerator,
        private CourseEndDateCalculatorService $endDateCalculator,
        private CourseSlugGeneratorService $slugGenerator,
        private FileStorageInterface $fileStorage
    ) {
    }

    public function paginate(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->courseRepository->paginateWithRelations($filters, $perPage);
    }

    public function create(array $data, ?UploadedFile $image = null): Course
    {
        Log::info('Creating new course', ['title' => $data['title'] ?? 'N/A']);

        if ($image) {
            $data['image_path'] = $this->fileStorage->upload($image, 'courses', 'public');
            Log::debug('Course image uploaded', ['path' => $data['image_path']]);
        }

        unset($data['image']);

        $data['slug'] = $this->slugGenerator->generateSlug($data['title']);
        $data['code'] = $this->slugGenerator->generateCode($data['code'] ?? null);

        // Calculate end_date using domain service
        if (!isset($data['end_date']) && isset($data['start_date']) && isset($data['session_count']) && !empty($data['days_of_week'])) {
            $endDate = $this->endDateCalculator->calculateEndDate(
                $data['start_date'],
                $data['session_count'],
                $data['days_of_week']
            );
            $data['end_date'] = $endDate?->toDateString();
        }

        $course = $this->courseRepository->create($data);

        Log::info('Course created', [
            'course_id' => $course->id,
            'code' => $course->code,
            'title' => $course->title,
        ]);

        $this->courseRepository->syncInstructors(
            $course,
            $data['instructors'] ?? [],
            $data['supervisors'] ?? []
        );

        if ($course->auto_generate_sessions && $course->start_date && $course->session_count) {
            Log::debug('Auto-generating sessions for course', [
                'course_id' => $course->id,
                'session_count' => $course->session_count,
            ]);
            $this->sessionGenerator->generateSessionsForCourse(
                $course,
                $course->session_count,
                $course->days_of_week ?? [],
                $course->start_date
            );
        }

        return $course->load('instructors', 'sessions');
    }

    public function update(Course $course, array $data, ?UploadedFile $image = null, bool $regenerateSessions = false): Course
    {
        Log::info('Updating course', [
            'course_id' => $course->id,
            'title' => $course->title,
        ]);

        if ($image) {
            if ($course->image_path) {
                $this->fileStorage->delete($course->image_path, 'public');
                Log::debug('Deleted old course image', ['path' => $course->image_path]);
            }

            $data['image_path'] = $this->fileStorage->upload($image, 'courses', 'public');
            Log::debug('New course image uploaded', ['path' => $data['image_path']]);
        }

        if (isset($data['title'])) {
            $data['slug'] = $this->slugGenerator->generateSlug($data['title']);
        }

        // Recalculate end_date using domain service
        if (!isset($data['end_date']) && (
            isset($data['start_date']) || 
            isset($data['session_count']) || 
            isset($data['days_of_week'])
        )) {
            $endDate = $this->endDateCalculator->calculateEndDate(
                $data['start_date'] ?? $course->start_date,
                $data['session_count'] ?? $course->session_count,
                $data['days_of_week'] ?? $course->days_of_week ?? []
            );
            $data['end_date'] = $endDate?->toDateString();
        }

        unset($data['image'], $data['regenerate_sessions']);

        $course = $this->courseRepository->update($course, $data);

        $this->courseRepository->syncInstructors(
            $course,
            $data['instructors'] ?? [],
            $data['supervisors'] ?? []
        );

        if ($regenerateSessions) {
            Log::info('Regenerating sessions for course', ['course_id' => $course->id]);
            $this->sessionRepository->deleteByCourseId($course->id);
            $this->sessionGenerator->generateSessionsForCourse(
                $course,
                $course->session_count,
                $course->days_of_week ?? [],
                $course->start_date
            );
        }

        Log::info('Course updated successfully', ['course_id' => $course->id]);

        return $this->courseRepository->loadRelations($course, ['instructors', 'sessions']);
    }

    public function delete(Course $course): void
    {
        Log::warning('Deleting course', [
            'course_id' => $course->id,
            'title' => $course->title,
            'code' => $course->code,
        ]);

        $this->courseRepository->delete($course);

        Log::info('Course deleted successfully', ['course_id' => $course->id]);
    }

    public function assignInstructors(Course $course, array $data): Course
    {
        $this->courseRepository->syncInstructors(
            $course,
            $data['instructors'] ?? [],
            $data['supervisors'] ?? []
        );

        return $this->courseRepository->loadRelations($course, ['instructors']);
    }

    public function generateSessions(Course $course, array $data): Course
    {
        $this->sessionRepository->deleteByCourse($course->id);

        $this->sessionGenerator->generateSessionsForCourse(
            $course,
            (int) $data['session_count'],
            $data['days_of_week'],
            $data['start_date']
        );

        // Calculate end_date using domain service
        $endDate = $this->endDateCalculator->calculateEndDate(
            $data['start_date'],
            $data['session_count'],
            $data['days_of_week']
        );

        $this->courseRepository->update($course, [
            'session_count' => $data['session_count'],
            'days_of_week' => $data['days_of_week'],
            'start_date' => $data['start_date'],
            'end_date' => $endDate?->toDateString(),
        ]);

        return $this->courseRepository->loadRelations($course, ['sessions']);
    }
}

