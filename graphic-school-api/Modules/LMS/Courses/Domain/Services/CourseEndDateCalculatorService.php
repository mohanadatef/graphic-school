<?php

namespace Modules\LMS\Courses\Domain\Services;

use Carbon\Carbon;

/**
 * Domain Service for calculating course end date
 * Follows Single Responsibility Principle - only responsible for end date calculation
 */
class CourseEndDateCalculatorService
{
    /**
     * Calculate end date based on start date, session count, and days of week
     */
    public function calculateEndDate(
        string $startDate,
        int $sessionCount,
        array $daysOfWeek
    ): ?Carbon {
        if (!$startDate || !$sessionCount || empty($daysOfWeek)) {
            return null;
        }

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
        $lastDate = null;

        while ($created < $sessionCount) {
            foreach ($daysOfWeek as $day) {
                if (!isset($dayMap[$day])) {
                    continue;
                }

                $sessionDate = $current->copy()->next($dayMap[$day]);

                if ($sessionDate->lessThan(Carbon::parse($startDate))) {
                    $sessionDate = Carbon::parse($startDate)->next($dayMap[$day]);
                }

                if ($created >= $sessionCount) {
                    break;
                }

                $lastDate = $sessionDate;
                $created++;
            }

            $current->addWeek();
        }

        return $lastDate;
    }
}

