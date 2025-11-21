<?php

namespace Modules\Operations\Reports\Services;

use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Enums\EnrollmentPaymentStatus;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Modules\LMS\CourseReviews\Models\CourseReview;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Sessions\Enums\SessionStatus;
use Modules\LMS\Attendance\Models\Attendance;
use Modules\LMS\Attendance\Enums\AttendanceStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate comprehensive course report
     */
    public function coursesReport(array $filters = []): Collection
    {
        $query = Course::with(['category', 'instructors'])
            ->withCount([
                'sessions',
                'sessions as sessions_completed' => fn ($q) => $q->where('status', SessionStatus::COMPLETED->value),
                'enrollments as students_count' => fn ($q) => $q->where('status', EnrollmentStatus::APPROVED->value),
                'enrollments as enrollments_pending' => fn ($q) => $q->where('status', EnrollmentStatus::PENDING->value),
                'enrollments as enrollments_rejected' => fn ($q) => $q->where('status', EnrollmentStatus::REJECTED->value),
            ])
            ->withSum(['enrollments as paid_total' => fn ($q) => $q->where('payment_status', EnrollmentPaymentStatus::PAID->value)], 'paid_amount')
            ->withSum(['enrollments as total_amount' => fn ($q) => $q->where('status', EnrollmentStatus::APPROVED->value)], 'total_amount');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $query->where('start_date', '>=', Carbon::parse($filters['from_date']));
        }

        if (!empty($filters['to_date'])) {
            $query->where('start_date', '<=', Carbon::parse($filters['to_date']));
        }

        return $query->get()->map(function ($course) {
            $notPaid = Enrollment::where('course_id', $course->id)
                ->where('payment_status', EnrollmentPaymentStatus::NOT_PAID->value)
                ->where('status', EnrollmentStatus::APPROVED->value)
                ->count();

            $partiallyPaid = Enrollment::where('course_id', $course->id)
                ->where('payment_status', EnrollmentPaymentStatus::PARTIALLY_PAID->value)
                ->where('status', EnrollmentStatus::APPROVED->value)
                ->count();

            $averageRating = CourseReview::where('course_id', $course->id)
                ->avg('rating_course') ?? 0;

            $attendanceRate = $this->calculateCourseAttendanceRate($course->id);

            return [
                'id' => $course->id,
                'title' => $course->title,
                'code' => $course->code,
                'category' => $course->category?->name,
                'status' => $course->status,
                'delivery_type' => $course->delivery_type ?? 'on-site',
                'start_date' => $course->start_date?->format('Y-m-d'),
                'end_date' => $course->end_date?->format('Y-m-d'),
                'price' => (float) $course->price,
                'instructors' => $course->instructors->pluck('name')->toArray(),
                'statistics' => [
                    'sessions_total' => $course->sessions_count,
                    'sessions_completed' => $course->sessions_completed,
                    'sessions_remaining' => $course->sessions_count - $course->sessions_completed,
                    'students_enrolled' => $course->students_count,
                    'enrollments_pending' => $course->enrollments_pending,
                    'enrollments_rejected' => $course->enrollments_rejected,
                ],
                'financial' => [
                    'total_amount' => (float) ($course->total_amount ?? 0),
                    'paid_total' => (float) ($course->paid_total ?? 0),
                    'not_paid_count' => $notPaid,
                    'partially_paid_count' => $partiallyPaid,
                    'outstanding_amount' => (float) (($course->total_amount ?? 0) - ($course->paid_total ?? 0)),
                ],
                'performance' => [
                    'average_rating' => round($averageRating, 2),
                    'reviews_count' => CourseReview::where('course_id', $course->id)->count(),
                    'attendance_rate' => $attendanceRate,
                ],
            ];
        });
    }

    /**
     * Generate comprehensive instructor report
     */
    public function instructorsReport(array $filters = []): Collection
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))
            ->with(['instructorCourses']);

        if (!empty($filters['instructor_id'])) {
            $query->where('id', $filters['instructor_id']);
        }

        return $query->get()->map(function ($instructor) {
            $courses = $instructor->instructorCourses()->with('category')->get();
            $coursesCount = $courses->count();

            $totalStudents = 0;
            $totalSessions = 0;
            $completedSessions = 0;
            $totalRevenue = 0;

            foreach ($courses as $course) {
                $enrollments = Enrollment::where('course_id', $course->id)
                    ->where('status', EnrollmentStatus::APPROVED->value)
                    ->count();
                $totalStudents += $enrollments;

                $sessions = Session::where('course_id', $course->id)->count();
                $totalSessions += $sessions;

                $completed = Session::where('course_id', $course->id)
                    ->where('status', SessionStatus::COMPLETED->value)
                    ->count();
                $completedSessions += $completed;

                $revenue = Enrollment::where('course_id', $course->id)
                    ->where('status', EnrollmentStatus::APPROVED->value)
                    ->where('payment_status', EnrollmentPaymentStatus::PAID->value)
                    ->sum('paid_amount');
                $totalRevenue += $revenue;
            }

            $rating = CourseReview::where('instructor_id', $instructor->id)
                ->avg('rating_instructor') ?? 0;

            $reviewsCount = CourseReview::where('instructor_id', $instructor->id)->count();

            $attendanceRate = $this->calculateInstructorAttendanceRate($instructor->id);

            return [
                'id' => $instructor->id,
                'name' => $instructor->name,
                'email' => $instructor->email,
                'is_active' => $instructor->is_active,
                'statistics' => [
                    'courses_count' => $coursesCount,
                    'total_students' => $totalStudents,
                    'total_sessions' => $totalSessions,
                    'completed_sessions' => $completedSessions,
                    'sessions_completion_rate' => $totalSessions > 0 
                        ? round(($completedSessions / $totalSessions) * 100, 2) 
                        : 0,
                ],
                'financial' => [
                    'total_revenue' => round($totalRevenue, 2),
                ],
                'performance' => [
                    'average_rating' => round($rating, 2),
                    'reviews_count' => $reviewsCount,
                    'attendance_rate' => $attendanceRate,
                ],
                'courses' => $courses->map(fn ($c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'category' => $c->category?->name,
                    'status' => $c->status,
                ])->toArray(),
            ];
        });
    }

    /**
     * Generate financial report
     */
    public function financialReport(array $filters = []): array
    {
        $query = Enrollment::where('status', EnrollmentStatus::APPROVED->value)
            ->with(['course', 'student']);

        if (!empty($filters['from_date'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['from_date']));
        }

        if (!empty($filters['to_date'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['to_date']));
        }

        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        $enrollments = $query->get();

        $totalAmount = $enrollments->sum('total_amount');
        $paidTotal = $enrollments->where('payment_status', EnrollmentPaymentStatus::PAID->value)->sum('paid_amount');
        $notPaidTotal = $enrollments->where('payment_status', EnrollmentPaymentStatus::NOT_PAID->value)->sum('total_amount');
        $partiallyPaidTotal = $enrollments->where('payment_status', EnrollmentPaymentStatus::PARTIALLY_PAID->value)->sum('total_amount');
        $outstandingAmount = $totalAmount - $paidTotal;

        // Group by payment status
        $byStatus = [
            'paid' => [
                'count' => $enrollments->where('payment_status', EnrollmentPaymentStatus::PAID->value)->count(),
                'amount' => $paidTotal,
            ],
            'not_paid' => [
                'count' => $enrollments->where('payment_status', EnrollmentPaymentStatus::NOT_PAID->value)->count(),
                'amount' => $notPaidTotal,
            ],
            'partially_paid' => [
                'count' => $enrollments->where('payment_status', EnrollmentPaymentStatus::PARTIALLY_PAID->value)->count(),
                'amount' => $partiallyPaidTotal,
            ],
            'rejected' => [
                'count' => $enrollments->where('payment_status', EnrollmentPaymentStatus::REJECTED->value)->count(),
                'amount' => 0,
            ],
        ];

        // Group by month
        $byMonth = $enrollments->groupBy(function ($enrollment) {
            return Carbon::parse($enrollment->created_at)->format('Y-m');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('total_amount'),
                'paid_amount' => $group->where('payment_status', EnrollmentPaymentStatus::PAID->value)->sum('paid_amount'),
            ];
        });

        // Group by course
        $byCourse = $enrollments->groupBy('course_id')->map(function ($group, $courseId) {
            $course = Course::find($courseId);
            return [
                'course_id' => $courseId,
                'course_title' => $course?->title,
                'count' => $group->count(),
                'total_amount' => $group->sum('total_amount'),
                'paid_amount' => $group->where('payment_status', EnrollmentPaymentStatus::PAID->value)->sum('paid_amount'),
            ];
        })->values();

        return [
            'summary' => [
                'total_enrollments' => $enrollments->count(),
                'total_amount' => round($totalAmount, 2),
                'paid_total' => round($paidTotal, 2),
                'outstanding_amount' => round($outstandingAmount, 2),
                'collection_rate' => $totalAmount > 0 
                    ? round(($paidTotal / $totalAmount) * 100, 2) 
                    : 0,
            ],
            'by_status' => $byStatus,
            'by_month' => $byMonth,
            'by_course' => $byCourse,
        ];
    }

    /**
     * Calculate attendance rate for a course
     */
    protected function calculateCourseAttendanceRate(int $courseId): float
    {
        $sessions = Session::where('course_id', $courseId)->pluck('id');
        
        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalAttendance = Attendance::whereIn('session_id', $sessions)->count();
        $present = Attendance::whereIn('session_id', $sessions)
            ->where('status', AttendanceStatus::PRESENT->value)
            ->count();

        if ($totalAttendance === 0) {
            return 0;
        }

        return round(($present / $totalAttendance) * 100, 2);
    }

    /**
     * Calculate attendance rate for an instructor
     */
    protected function calculateInstructorAttendanceRate(int $instructorId): float
    {
        $courseIds = DB::table('course_instructor')
            ->where('instructor_id', $instructorId)
            ->pluck('course_id');

        if ($courseIds->isEmpty()) {
            return 0;
        }

        $sessions = Session::whereIn('course_id', $courseIds)->pluck('id');
        
        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalAttendance = Attendance::whereIn('session_id', $sessions)->count();
        $present = Attendance::whereIn('session_id', $sessions)
            ->where('status', AttendanceStatus::PRESENT->value)
            ->count();

        if ($totalAttendance === 0) {
            return 0;
        }

        return round(($present / $totalAttendance) * 100, 2);
    }
}

