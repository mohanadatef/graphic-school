<?php

namespace Modules\Operations\Analytics\Services;

use Modules\Operations\Analytics\Models\Visit;
use Modules\LMS\Courses\Models\Course;
use Modules\ACL\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Track a visit
     */
    public function trackVisit(
        string $visitableType,
        int $visitableId,
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $referer = null
    ): Visit {
        return Visit::create([
            'visitable_type' => $visitableType,
            'visitable_id' => $visitableId,
            'user_id' => $userId,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'referer' => $referer ?? request()->header('referer'),
            'visited_at' => now(),
        ]);
    }

    /**
     * Get course visits statistics
     */
    public function getCourseVisits(?int $courseId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = Visit::forType(Course::class);

        if ($courseId) {
            $query->where('visitable_id', $courseId);
        }

        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }

        return $query->select('visitable_id', DB::raw('COUNT(*) as total_visits'))
            ->selectRaw('COUNT(DISTINCT COALESCE(user_id, ip_address)) as unique_visits')
            ->groupBy('visitable_id')
            ->get()
            ->map(function ($visit) {
                $course = Course::find($visit->visitable_id);
                return [
                    'course_id' => $visit->visitable_id,
                    'course_title' => $course?->title,
                    'total_visits' => $visit->total_visits,
                    'unique_visits' => $visit->unique_visits,
                ];
            });
    }

    /**
     * Get instructor visits statistics
     */
    public function getInstructorVisits(?int $instructorId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $query = Visit::forType(User::class)
            ->whereHas('visitable', function ($q) {
                $q->whereHas('role', fn ($r) => $r->where('name', 'instructor'));
            });

        if ($instructorId) {
            $query->where('visitable_id', $instructorId);
        }

        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }

        return $query->select('visitable_id', DB::raw('COUNT(*) as total_visits'))
            ->selectRaw('COUNT(DISTINCT COALESCE(user_id, ip_address)) as unique_visits')
            ->groupBy('visitable_id')
            ->get()
            ->map(function ($visit) {
                $instructor = User::find($visit->visitable_id);
                return [
                    'instructor_id' => $visit->visitable_id,
                    'instructor_name' => $instructor?->name,
                    'total_visits' => $visit->total_visits,
                    'unique_visits' => $visit->unique_visits,
                ];
            });
    }

    /**
     * Get overview statistics
     */
    public function getOverview(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Visit::query();

        if ($startDate && $endDate) {
            $query->inDateRange($startDate, $endDate);
        }

        $totalVisits = $query->count();
        $uniqueVisits = $query->selectRaw('COUNT(DISTINCT COALESCE(user_id, ip_address)) as unique')->value('unique');
        $courseVisits = Visit::forType(Course::class)->count();
        $instructorVisits = Visit::forType(User::class)->count();

        return [
            'total_visits' => $totalVisits,
            'unique_visits' => $uniqueVisits ?? 0,
            'course_visits' => $courseVisits,
            'instructor_visits' => $instructorVisits,
        ];
    }

    /**
     * Get daily visits trend
     */
    public function getDailyTrend(?Carbon $startDate = null, ?Carbon $endDate = null, int $days = 30): Collection
    {
        $endDate = $endDate ?? Carbon::today();
        $startDate = $startDate ?? $endDate->copy()->subDays($days);

        return Visit::whereBetween('visited_at', [$startDate, $endDate])
            ->selectRaw('DATE(visited_at) as date')
            ->selectRaw('COUNT(*) as visits')
            ->selectRaw('COUNT(DISTINCT COALESCE(user_id, ip_address)) as unique_visits')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}

