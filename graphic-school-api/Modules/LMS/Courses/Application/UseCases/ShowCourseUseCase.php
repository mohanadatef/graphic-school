<?php

namespace Modules\LMS\Courses\Application\UseCases;

use App\Support\BaseQuery;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Modules\LMS\Courses\Models\Course;

class ShowCourseUseCase extends BaseQuery
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {
    }

    protected function handle(mixed $input): Course
    {
        /** @var Course $course */
        $course = $input;

        return $this->courseRepository->loadRelations($course, ['category', 'instructors', 'sessions', 'enrollments']);
    }
}

