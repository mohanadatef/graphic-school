<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EnrollmentPaymentStatus;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\User;

class ReportController extends Controller
{
    public function courses()
    {
        $courses = Course::withCount('sessions')
            ->withSum('enrollments as paid_total', 'paid_amount')
            ->withCount([
                'enrollments as students_count' => function ($q) {
                    $q->where('status', EnrollmentStatus::APPROVED->value);
                },
            ])
            ->get()
            ->map(function ($course) {
                $rejected = Enrollment::where('course_id', $course->id)
                    ->where('status', EnrollmentStatus::REJECTED->value)
                    ->count();

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'students' => $course->students_count,
                    'sessions' => $course->sessions_count,
                    'paid_total' => $course->paid_total ?? 0,
                    'not_paid' => Enrollment::where('course_id', $course->id)
                        ->where('payment_status', EnrollmentPaymentStatus::NOT_PAID->value)
                        ->count(),
                    'rejected' => $rejected,
                ];
            });

        return response()->json($courses);
    }

    public function instructors()
    {
        $instructors = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))
            ->get()
            ->map(function ($instructor) {
                $coursesCount = $instructor->instructorCourses()->count();
                $rating = CourseReview::where('instructor_id', $instructor->id)->avg('rating_instructor') ?? 0;

                return [
                    'id' => $instructor->id,
                    'name' => $instructor->name,
                    'courses' => $coursesCount,
                    'rating' => round($rating, 2),
                ];
            });

        return response()->json($instructors);
    }
}
