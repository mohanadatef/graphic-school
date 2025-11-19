<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\CourseStatus;
use App\Enums\EnrollmentPaymentStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\SessionStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function getDashboardData(array $filters): array
    {
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->count();
        $instructors = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))->count();
        $activeCourses = Course::whereIn('status', [
                CourseStatus::UPCOMING->value,
                CourseStatus::RUNNING->value,
            ])
            ->where('is_hidden', false)
            ->count();

        $sessionsCount = Session::count();
        $totalPaid = Enrollment::sum('paid_amount');
        $attendanceRate = $this->calculateAttendanceRate();

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
                'total_paid' => $totalPaid,
                'attendance_rate' => $attendanceRate,
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
        ];
    }

    protected function calculateAttendanceRate(): float
    {
        $totalAttendance = DB::table('attendance')->count();
        $present = DB::table('attendance')->where('status', AttendanceStatus::PRESENT->value)->count();

        if ($totalAttendance === 0) {
            return 0;
        }

        return round(($present / $totalAttendance) * 100, 2);
    }
}


