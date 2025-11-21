<?php

namespace Modules\Operations\Dashboard\Services;

use Modules\LMS\Attendance\Enums\AttendanceStatus;
use Modules\LMS\Courses\Enums\CourseStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentPaymentStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Modules\LMS\Sessions\Enums\SessionStatus;
use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function getDashboardData(array $filters): array
    {
        try {
            // Test database connection first
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            Log::error('Database connection failed in DashboardService: ' . $e->getMessage());
            throw new \Illuminate\Database\QueryException(
                'Connection',
                'Database connection error',
                [],
                $e
            );
        }
        
        try {
            $students = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->count();
            $instructors = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))->count();
            $activeCourses = Course::whereIn('status', [
                    CourseStatus::UPCOMING->value,
                    CourseStatus::RUNNING->value,
                ])
                ->where('is_hidden', false)
                ->count();

            $sessionsCount = Session::count();
            $sessionsCompleted = Session::where('status', SessionStatus::COMPLETED->value)->count();
            $sessionsUpcoming = Session::where('status', SessionStatus::SCHEDULED->value)
                ->whereDate('session_date', '>=', now())
                ->count();
            
            $totalPaid = Enrollment::where('payment_status', EnrollmentPaymentStatus::PAID->value)->sum('paid_amount') ?? 0;
            $totalAmount = Enrollment::where('status', EnrollmentStatus::APPROVED->value)->sum('total_amount') ?? 0;
            $pendingAmount = $totalAmount - $totalPaid;
            
            $enrollmentsPending = Enrollment::where('status', EnrollmentStatus::PENDING->value)->count();
            $enrollmentsApproved = Enrollment::where('status', EnrollmentStatus::APPROVED->value)->count();
            $enrollmentsRejected = Enrollment::where('status', EnrollmentStatus::REJECTED->value)->count();
            
            $attendanceRate = $this->calculateAttendanceRate();
            
            // Recent activity (last 7 days)
            $recentEnrollments = Enrollment::where('created_at', '>=', now()->subDays(7))->count();
            $recentCourses = Course::where('created_at', '>=', now()->subDays(7))->count();
            
            // Monthly revenue trend (last 6 months)
            $monthlyRevenue = $this->getMonthlyRevenueTrend();
            
            // Top performing courses
            $topCourses = Course::withCount([
                'enrollments as students_count' => fn ($q) => $q->where('status', EnrollmentStatus::APPROVED->value),
            ])
            ->withSum(['enrollments as revenue' => fn ($q) => $q->where('payment_status', EnrollmentPaymentStatus::PAID->value)], 'paid_amount')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get(['id', 'title', 'code']);

        $coursesQuery = Course::query()
            ->with(['category:id,name'])
            ->withCount([
                'enrollments as students_count' => fn ($q) => $q->where('status', EnrollmentStatus::APPROVED->value),
                'sessions as sessions_total',
                'sessions as sessions_completed' => fn ($q) => $q->where('status', SessionStatus::COMPLETED->value),
            ])
            ->withSum(['enrollments as paid_total' => fn ($q) => $q->where('payment_status', EnrollmentPaymentStatus::PAID->value)], 'paid_amount');

        if (! empty($filters['category_id'])) {
            $coursesQuery->where('category_id', (int) $filters['category_id']);
        }

        if (! empty($filters['status'])) {
            $coursesQuery->where('status', $filters['status']);
        }

        if (! empty($filters['instructor_id'])) {
            $coursesQuery->whereHas('instructors', fn ($q) => $q->where('users.id', (int) $filters['instructor_id']));
        }

        $perPage = $filters['per_page'] ?? 10;
        $coursesPaginator = $coursesQuery
            ->orderByDesc('created_at')
            ->paginate((int) $perPage);

        return [
            'stats' => [
                'active_courses' => $activeCourses,
                'students_count' => $students,
                'instructors_count' => $instructors,
                'sessions_count' => $sessionsCount,
                'sessions_completed' => $sessionsCompleted,
                'sessions_upcoming' => $sessionsUpcoming,
                'total_paid' => round($totalPaid, 2),
                'total_amount' => round($totalAmount, 2),
                'pending_amount' => round($pendingAmount, 2),
                'attendance_rate' => $attendanceRate,
                'enrollments_pending' => $enrollmentsPending,
                'enrollments_approved' => $enrollmentsApproved,
                'enrollments_rejected' => $enrollmentsRejected,
                'recent_enrollments' => $recentEnrollments,
                'recent_courses' => $recentCourses,
                'collection_rate' => $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100, 2) : 0,
            ],
            'courses' => $coursesPaginator->items(),
            'pagination' => [
                'current_page' => $coursesPaginator->currentPage(),
                'per_page' => $coursesPaginator->perPage(),
                'total' => $coursesPaginator->total(),
                'last_page' => $coursesPaginator->lastPage(),
            ],
            'filters' => [
                'categories' => Category::select('id', 'name')->orderBy('name')->get(),
                'statuses' => CourseStatus::values(),
                'instructors' => User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get(),
            ],
            'trends' => [
                'monthly_revenue' => $monthlyRevenue,
            ],
            'top_courses' => $topCourses->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'code' => $c->code,
                'students_count' => $c->students_count ?? 0,
                'revenue' => round($c->revenue ?? 0, 2),
            ]),
        ];
        } catch (\Exception $e) {
            Log::error('Error in getDashboardData: ' . $e->getMessage());
            // Return minimal data structure to prevent complete failure
            return [
                'stats' => [
                    'active_courses' => 0,
                    'students_count' => 0,
                    'instructors_count' => 0,
                    'sessions_count' => 0,
                    'sessions_completed' => 0,
                    'sessions_upcoming' => 0,
                    'total_paid' => 0,
                    'total_amount' => 0,
                    'pending_amount' => 0,
                    'attendance_rate' => 0,
                    'enrollments_pending' => 0,
                    'enrollments_approved' => 0,
                    'enrollments_rejected' => 0,
                    'recent_enrollments' => 0,
                    'recent_courses' => 0,
                    'collection_rate' => 0,
                ],
                'courses' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'last_page' => 1,
                ],
                'filters' => [
                    'categories' => [],
                    'statuses' => [],
                    'instructors' => [],
                ],
                'trends' => [
                    'monthly_revenue' => [],
                ],
                'top_courses' => [],
            ];
        }
    }

    protected function calculateAttendanceRate(): float
    {
        try {
            $totalAttendance = DB::table('attendance')->count();
            $present = DB::table('attendance')->where('status', AttendanceStatus::PRESENT->value)->count();

            if ($totalAttendance === 0) {
                return 0;
            }

            return round(($present / $totalAttendance) * 100, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating attendance rate: ' . $e->getMessage());
            return 0;
        }
    }
    
    protected function getMonthlyRevenueTrend(): array
    {
        try {
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthStart = (clone $date)->startOfMonth();
                $monthEnd = (clone $date)->endOfMonth();
                
                $revenue = Enrollment::where('payment_status', EnrollmentPaymentStatus::PAID->value)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('paid_amount') ?? 0;
                
                $months[] = [
                    'month' => $date->format('Y-m'),
                    'month_name' => $date->format('M Y'),
                    'revenue' => round($revenue, 2),
                    'count' => Enrollment::where('payment_status', EnrollmentPaymentStatus::PAID->value)
                        ->whereBetween('created_at', [$monthStart, $monthEnd])
                        ->count(),
                ];
            }
            
            return $months;
        } catch (\Exception $e) {
            Log::error('Error getting monthly revenue trend: ' . $e->getMessage());
            return [];
        }
    }
}

