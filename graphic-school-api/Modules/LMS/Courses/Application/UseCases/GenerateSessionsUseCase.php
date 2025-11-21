<?php

namespace Modules\LMS\Courses\Application\UseCases;

use App\Support\BaseUseCase;
use App\Contracts\Services\TransactionManagerInterface;
use Modules\LMS\Courses\Application\DTOs\GenerateSessionsDTO;
use Modules\LMS\Courses\Repositories\Interfaces\CourseRepositoryInterface;
use App\Contracts\Repositories\SessionRepositoryInterface;
use Modules\LMS\Courses\Domain\Services\CourseSessionGeneratorService;
use Modules\LMS\Courses\Domain\Services\CourseEndDateCalculatorService;
use Modules\LMS\Courses\Models\Course;

/**
 * UseCase for generating course sessions
 * Follows SOLID Principles:
 * - SRP: Only responsible for orchestrating session generation
 * - DIP: Depends on interfaces and domain services, not concrete implementations
 */
class GenerateSessionsUseCase extends BaseUseCase
{
    public function __construct(
        private CourseRepositoryInterface $courseRepository,
        private SessionRepositoryInterface $sessionRepository,
        private CourseSessionGeneratorService $sessionGenerator,
        private CourseEndDateCalculatorService $endDateCalculator,
        private TransactionManagerInterface $transactionManager
    ) {
    }

    protected function handle(mixed $input): Course
    {
        /** @var Course $course */
        /** @var GenerateSessionsDTO $dto */
        [$course, $dto] = $input;
        $dto->validate();

        return $this->transactionManager->transaction(function () use ($course, $dto) {
            // Delete existing sessions
            $this->sessionRepository->deleteByCourseId($course->id);

            // Generate new sessions using domain service (SRP)
            $this->sessionGenerator->generateSessionsForCourse(
                $course,
                $dto->sessionCount,
                $dto->daysOfWeek,
                $dto->startDate
            );

            // Calculate end date using domain service (SRP)
            $endDate = $this->endDateCalculator->calculateEndDate(
                $dto->startDate,
                $dto->sessionCount,
                $dto->daysOfWeek
            );

            // Update course
            $this->courseRepository->update($course, [
                'session_count' => $dto->sessionCount,
                'days_of_week' => $dto->daysOfWeek,
                'start_date' => $dto->startDate,
                'end_date' => $endDate?->toDateString(),
            ]);

            return $this->courseRepository->loadRelations($course, ['sessions']);
        });
    }
}

