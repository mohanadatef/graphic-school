<?php

namespace Modules\LMS\Courses\Application\UseCases;

use App\Support\BaseUseCase;
use App\Support\Database\TransactionManager;
use Modules\LMS\Courses\Application\DTOs\AssignInstructorsDTO;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use Modules\LMS\Courses\Models\Course;

class AssignInstructorsUseCase extends BaseUseCase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository
    ) {
    }

    protected function handle(mixed $input): Course
    {
        /** @var Course $course */
        /** @var AssignInstructorsDTO $dto */
        [$course, $dto] = $input;
        $dto->validate();

        return TransactionManager::transaction(function () use ($course, $dto) {
            $this->courseRepository->syncInstructors(
                $course,
                $dto->instructors ?? [],
                $dto->supervisors ?? []
            );

            return $this->courseRepository->loadRelations($course, ['instructors']);
        });
    }
}

