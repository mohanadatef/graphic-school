<?php

namespace Modules\LMS\Courses\Application\UseCases;

use App\Support\BaseUseCase;
use App\Support\Database\TransactionManager;
use Modules\LMS\Courses\Domain\Events\CourseDeleted;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Modules\LMS\Courses\Models\Course;
use App\Services\EntityTranslationService;
use Illuminate\Support\Facades\Event;

class DeleteCourseUseCase extends BaseUseCase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {
    }

    protected function handle(mixed $input): void
    {
        /** @var Course $course */
        $course = $input;

        TransactionManager::transaction(function () use ($course) {
            $courseId = $course->id;
            $courseTitle = $course->title;

            // Delete translations (cascade should handle this, but explicit for cache clearing)
            $translationService = app(EntityTranslationService::class);
            $translationService->deleteTranslations($course);

            $this->courseRepository->delete($course);

            // Dispatch domain event
            Event::dispatch(new CourseDeleted($courseId, $courseTitle));
        });
    }
}

