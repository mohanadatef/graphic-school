<?php

namespace Modules\LMS\Courses\Domain\Services;

use App\Contracts\Repositories\SessionRepositoryInterface;
use Modules\LMS\Courses\Models\Course;
use Carbon\Carbon;

/**
 * Domain Service for generating course sessions
 * Follows Single Responsibility Principle - only responsible for session generation logic
 */
class CourseSessionGeneratorService
{
    public function __construct(
        private SessionRepositoryInterface $sessionRepository
    ) {
    }

    /**
     * Generate sessions for a course
     */
    public function generateSessionsForCourse(
        Course $course,
        int $sessionCount,
        array $daysOfWeek,
        string $startDate
    ): void {
        $dayMap = [
            'mon' => Carbon::MONDAY,
            'tue' => Carbon::TUESDAY,
            'wed' => Carbon::WEDNESDAY,
            'thu' => Carbon::THURSDAY,
            'fri' => Carbon::FRIDAY,
            'sat' => Carbon::SATURDAY,
            'sun' => Carbon::SUNDAY,
        ];

        $current = Carbon::parse($startDate);
        $created = 0;
        $order = 1;

        $startTime = $course->default_start_time ?? '10:00';
        $endTime = $course->default_end_time ?? '12:00';

        while ($created < $sessionCount) {
            foreach ($daysOfWeek as $day) {
                if (!isset($dayMap[$day])) {
                    continue;
                }

                $currentDate = $current->copy()->next($dayMap[$day]);

                if ($currentDate->lessThan(Carbon::parse($startDate))) {
                    $currentDate = Carbon::parse($startDate)->next($dayMap[$day]);
                }

                if ($created >= $sessionCount) {
                    break;
                }

                $this->sessionRepository->create([
                    'course_id' => $course->id,
                    'title' => "{$course->title} - Session {$order}",
                    'session_order' => $order,
                    'session_date' => $currentDate->toDateString(),
                    'start_time' => $startTime ? "{$startTime}:00" : null,
                    'end_time' => $endTime ? "{$endTime}:00" : null,
                    'status' => \Modules\LMS\Sessions\Enums\SessionStatus::SCHEDULED->value,
                ]);

                $order++;
                $created++;
            }

            $current->addWeek();
        }
    }
}

