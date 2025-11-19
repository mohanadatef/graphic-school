<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->count();
        $instructors = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))->count();
        $activeCourses = Course::whereIn('status', ['upcoming', 'running'])
            ->where('is_hidden', false)
            ->count();

        $sessionsCount = Session::count();
        $totalPaid = Enrollment::sum('paid_amount');

        $attendanceRate = $this->calculateAttendanceRate();

        $coursesQuery = Course::query()
            ->with(['category:id,name'])
            ->withCount([
                'enrollments as students_count' => fn ($q) => $q->where('status', 'approved'),
                'sessions as sessions_total',
                'sessions as sessions_completed' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->withSum(['enrollments as paid_total' => fn ($q) => $q->where('payment_status', 'paid')], 'paid_amount');

        if ($request->filled('category_id')) {
            $coursesQuery->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('status')) {
            $coursesQuery->where('status', $request->get('status'));
        }

        if ($request->filled('instructor_id')) {
            $coursesQuery->whereHas('instructors', fn ($q) => $q->where('users.id', $request->integer('instructor_id')));
        }

        $coursesPaginator = $coursesQuery
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return response()->json([
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
                'statuses' => ['draft', 'upcoming', 'running', 'completed', 'archived'],
                'instructors' => User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get(),
            ],
        ]);
    }

    protected function calculateAttendanceRate(): float
    {
        $totalAttendance = \DB::table('attendance')->count();
        $present = \DB::table('attendance')->where('status', 'present')->count();

        if ($totalAttendance === 0) {
            return 0;
        }

        return round(($present / $totalAttendance) * 100, 2);
    }
}
