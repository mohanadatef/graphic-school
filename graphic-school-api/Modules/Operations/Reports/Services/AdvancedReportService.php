<?php

namespace Modules\Operations\Reports\Services;

use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Assessments\Models\QuizAttempt;
use Modules\LMS\Assessments\Models\StudentProject;
use Modules\LMS\Attendance\Models\Attendance;
use Modules\LMS\Progress\Models\StudentProgress;
use Modules\LMS\CourseReviews\Models\CourseReview;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * CHANGE-007: Advanced Reports & Analytics
 */
class AdvancedReportService
{
    /**
     * Get top students by grades
     */
    public function topStudentsByGrades(array $filters = [], int $limit = 10): Collection
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', 'student'))
            ->with(['enrollments.course']);

        if (!empty($filters['course_id'])) {
            $query->whereHas('enrollments', fn ($q) => $q->where('course_id', $filters['course_id']));
        }

        return $query->get()->map(function ($student) use ($filters) {
            $quizAttempts = QuizAttempt::where('student_id', $student->id);
            $projects = StudentProject::where('student_id', $student->id);

            if (!empty($filters['course_id'])) {
                $quizAttempts->whereHas('quiz', fn ($q) => $q->where('course_id', $filters['course_id']));
                $projects->where('course_id', $filters['course_id']);
            }

            $avgQuizScore = $quizAttempts->avg('percentage') ?? 0;
            $avgProjectScore = $projects->whereNotNull('score')->avg('score') ?? 0;
            $totalQuizzes = $quizAttempts->count();
            $totalProjects = $projects->count();

            // Calculate overall average
            $scores = [];
            if ($avgQuizScore > 0) $scores[] = $avgQuizScore;
            if ($avgProjectScore > 0) $scores[] = $avgProjectScore;
            $overallAverage = !empty($scores) ? array_sum($scores) / count($scores) : 0;

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_email' => $student->email,
                'average_quiz_score' => round($avgQuizScore, 2),
                'average_project_score' => round($avgProjectScore, 2),
                'overall_average' => round($overallAverage, 2),
                'total_quizzes' => $totalQuizzes,
                'total_projects' => $totalProjects,
            ];
        })->sortByDesc('overall_average')->take($limit)->values();
    }

    /**
     * Get top students by attendance
     */
    public function topStudentsByAttendance(array $filters = [], int $limit = 10): Collection
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', 'student'));

        if (!empty($filters['course_id'])) {
            $query->whereHas('enrollments', fn ($q) => $q->where('course_id', $filters['course_id']));
        }

        return $query->get()->map(function ($student) use ($filters) {
            $attendanceQuery = Attendance::where('student_id', $student->id);

            if (!empty($filters['course_id'])) {
                $sessionIds = DB::table('sessions')->where('course_id', $filters['course_id'])->pluck('id');
                $attendanceQuery->whereIn('session_id', $sessionIds);
            }

            $total = $attendanceQuery->count();
            $present = $attendanceQuery->where('status', 'present')->count();
            $attendanceRate = $total > 0 ? round(($present / $total) * 100, 2) : 0;

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_email' => $student->email,
                'total_sessions' => $total,
                'present_sessions' => $present,
                'absent_sessions' => $total - $present,
                'attendance_rate' => $attendanceRate,
            ];
        })->sortByDesc('attendance_rate')->take($limit)->values();
    }

    /**
     * Get top students by engagement
     */
    public function topStudentsByEngagement(array $filters = [], int $limit = 10): Collection
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', 'student'));

        if (!empty($filters['course_id'])) {
            $query->whereHas('enrollments', fn ($q) => $q->where('course_id', $filters['course_id']));
        }

        return $query->get()->map(function ($student) use ($filters) {
            $progressQuery = StudentProgress::where('student_id', $student->id);
            $quizAttemptsQuery = QuizAttempt::where('student_id', $student->id);
            $projectsQuery = StudentProject::where('student_id', $student->id);

            if (!empty($filters['course_id'])) {
                $progressQuery->where('course_id', $filters['course_id']);
                $quizAttemptsQuery->whereHas('quiz', fn ($q) => $q->where('course_id', $filters['course_id']));
                $projectsQuery->where('course_id', $filters['course_id']);
            }

            $lessonsCompleted = $progressQuery->where('is_completed', true)->count();
            $totalLessons = $progressQuery->count();
            $quizAttempts = $quizAttemptsQuery->count();
            $projectsSubmitted = $projectsQuery->count();
            $timeSpent = $progressQuery->sum('time_spent') ?? 0; // in seconds

            $engagementScore = ($lessonsCompleted * 10) + ($quizAttempts * 5) + ($projectsSubmitted * 15) + ($timeSpent / 60); // minutes

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_email' => $student->email,
                'lessons_completed' => $lessonsCompleted,
                'total_lessons' => $totalLessons,
                'completion_rate' => $totalLessons > 0 ? round(($lessonsCompleted / $totalLessons) * 100, 2) : 0,
                'quiz_attempts' => $quizAttempts,
                'projects_submitted' => $projectsSubmitted,
                'time_spent_minutes' => round($timeSpent / 60, 2),
                'engagement_score' => round($engagementScore, 2),
            ];
        })->sortByDesc('engagement_score')->take($limit)->values();
    }

    /**
     * Get average grades by course
     */
    public function averageGradesByCourse(array $filters = []): Collection
    {
        $query = Course::with('category');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->get()->map(function ($course) {
            $quizAttempts = QuizAttempt::whereHas('quiz', fn ($q) => $q->where('course_id', $course->id));
            $projects = StudentProject::where('course_id', $course->id)->whereNotNull('score');

            $avgQuizScore = $quizAttempts->avg('percentage') ?? 0;
            $avgProjectScore = $projects->avg('score') ?? 0;
            $totalStudents = Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->count();

            $scores = [];
            if ($avgQuizScore > 0) $scores[] = $avgQuizScore;
            if ($avgProjectScore > 0) $scores[] = $avgProjectScore;
            $overallAverage = !empty($scores) ? array_sum($scores) / count($scores) : 0;

            return [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'course_code' => $course->code,
                'category' => $course->category?->name,
                'total_students' => $totalStudents,
                'average_quiz_score' => round($avgQuizScore, 2),
                'average_project_score' => round($avgProjectScore, 2),
                'overall_average' => round($overallAverage, 2),
            ];
        })->sortByDesc('overall_average')->values();
    }

    /**
     * Get average grades by batch (if batches exist)
     */
    public function averageGradesByBatch(array $filters = []): Collection
    {
        // If batches don't exist, group by course start_date month
        $enrollments = Enrollment::where('status', 'approved')
            ->with(['course', 'student'])
            ->get();

        return $enrollments->groupBy(function ($enrollment) {
            return Carbon::parse($enrollment->course->start_date ?? $enrollment->created_at)->format('Y-m');
        })->map(function ($group, $month) {
            $courseIds = $group->pluck('course_id')->unique();
            $studentIds = $group->pluck('student_id')->unique();

            $quizAttempts = QuizAttempt::whereIn('student_id', $studentIds)
                ->whereHas('quiz', fn ($q) => $q->whereIn('course_id', $courseIds));
            
            $projects = StudentProject::whereIn('student_id', $studentIds)
                ->whereIn('course_id', $courseIds)
                ->whereNotNull('score');

            $avgQuizScore = $quizAttempts->avg('percentage') ?? 0;
            $avgProjectScore = $projects->avg('score') ?? 0;

            return [
                'batch_month' => $month,
                'total_students' => $studentIds->count(),
                'total_courses' => $courseIds->count(),
                'average_quiz_score' => round($avgQuizScore, 2),
                'average_project_score' => round($avgProjectScore, 2),
            ];
        })->sortByDesc('batch_month')->values();
    }

    /**
     * Get average grades by instructor
     */
    public function averageGradesByInstructor(array $filters = []): Collection
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))
            ->with('instructorCourses');

        if (!empty($filters['instructor_id'])) {
            $query->where('id', $filters['instructor_id']);
        }

        return $query->get()->map(function ($instructor) {
            $courseIds = $instructor->instructorCourses->pluck('id');
            
            if ($courseIds->isEmpty()) {
                return [
                    'instructor_id' => $instructor->id,
                    'instructor_name' => $instructor->name,
                    'average_quiz_score' => 0,
                    'average_project_score' => 0,
                    'overall_average' => 0,
                ];
            }

            $studentIds = Enrollment::whereIn('course_id', $courseIds)
                ->where('status', 'approved')
                ->pluck('student_id')
                ->unique();

            $quizAttempts = QuizAttempt::whereIn('student_id', $studentIds)
                ->whereHas('quiz', fn ($q) => $q->whereIn('course_id', $courseIds));
            
            $projects = StudentProject::whereIn('student_id', $studentIds)
                ->whereIn('course_id', $courseIds)
                ->whereNotNull('score');

            $avgQuizScore = $quizAttempts->avg('percentage') ?? 0;
            $avgProjectScore = $projects->avg('score') ?? 0;

            $scores = [];
            if ($avgQuizScore > 0) $scores[] = $avgQuizScore;
            if ($avgProjectScore > 0) $scores[] = $avgProjectScore;
            $overallAverage = !empty($scores) ? array_sum($scores) / count($scores) : 0;

            return [
                'instructor_id' => $instructor->id,
                'instructor_name' => $instructor->name,
                'instructor_email' => $instructor->email,
                'total_courses' => $courseIds->count(),
                'total_students' => $studentIds->count(),
                'average_quiz_score' => round($avgQuizScore, 2),
                'average_project_score' => round($avgProjectScore, 2),
                'overall_average' => round($overallAverage, 2),
            ];
        })->sortByDesc('overall_average')->values();
    }

    /**
     * Get attendance rate by course
     */
    public function attendanceRateByCourse(array $filters = []): Collection
    {
        $query = Course::with('category');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->get()->map(function ($course) {
            $sessionIds = DB::table('sessions')->where('course_id', $course->id)->pluck('id');
            
            if ($sessionIds->isEmpty()) {
                return [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'attendance_rate' => 0,
                    'total_sessions' => 0,
                ];
            }

            $totalAttendance = Attendance::whereIn('session_id', $sessionIds)->count();
            $present = Attendance::whereIn('session_id', $sessionIds)
                ->where('status', 'present')
                ->count();
            
            $attendanceRate = $totalAttendance > 0 ? round(($present / $totalAttendance) * 100, 2) : 0;

            return [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'course_code' => $course->code,
                'category' => $course->category?->name,
                'total_sessions' => $sessionIds->count(),
                'total_attendance_records' => $totalAttendance,
                'present_count' => $present,
                'absent_count' => $totalAttendance - $present,
                'attendance_rate' => $attendanceRate,
            ];
        })->sortByDesc('attendance_rate')->values();
    }

    /**
     * Get attendance rate by student
     */
    public function attendanceRateByStudent(array $filters = []): Collection
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', 'student'));

        if (!empty($filters['course_id'])) {
            $query->whereHas('enrollments', fn ($q) => $q->where('course_id', $filters['course_id']));
        }

        if (!empty($filters['student_id'])) {
            $query->where('id', $filters['student_id']);
        }

        return $query->get()->map(function ($student) use ($filters) {
            $attendanceQuery = Attendance::where('student_id', $student->id);

            if (!empty($filters['course_id'])) {
                $sessionIds = DB::table('sessions')->where('course_id', $filters['course_id'])->pluck('id');
                $attendanceQuery->whereIn('session_id', $sessionIds);
            }

            $total = $attendanceQuery->count();
            $present = $attendanceQuery->where('status', 'present')->count();
            $attendanceRate = $total > 0 ? round(($present / $total) * 100, 2) : 0;

            return [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_email' => $student->email,
                'total_sessions' => $total,
                'present_sessions' => $present,
                'absent_sessions' => $total - $present,
                'attendance_rate' => $attendanceRate,
            ];
        })->sortByDesc('attendance_rate')->values();
    }

    /**
     * Get engagement quality metrics
     */
    public function engagementQuality(array $filters = []): array
    {
        $query = User::whereHas('role', fn ($q) => $q->where('name', 'student'));

        if (!empty($filters['course_id'])) {
            $query->whereHas('enrollments', fn ($q) => $q->where('course_id', $filters['course_id']));
        }

        $students = $query->get();

        $totalQuestions = 0; // Would need a questions/comments table
        $totalComments = 0; // Would need a comments table
        $totalFilesUploaded = StudentProject::whereIn('student_id', $students->pluck('id'))
            ->whereNotNull('files')
            ->count();

        $progressRecords = StudentProgress::whereIn('student_id', $students->pluck('id'))->get();
        $totalTimeSpent = $progressRecords->sum('time_spent') / 60; // Convert to minutes
        $lessonsCompleted = $progressRecords->where('is_completed', true)->count();

        return [
            'total_students' => $students->count(),
            'total_questions' => $totalQuestions,
            'total_comments' => $totalComments,
            'total_files_uploaded' => $totalFilesUploaded,
            'total_time_spent_minutes' => round($totalTimeSpent, 2),
            'lessons_completed' => $lessonsCompleted,
            'average_time_per_student' => $students->count() > 0 ? round($totalTimeSpent / $students->count(), 2) : 0,
        ];
    }

    /**
     * Get instructor performance report
     */
    public function instructorPerformance(int $instructorId, array $filters = []): array
    {
        $instructor = User::findOrFail($instructorId);
        $courseIds = $instructor->instructorCourses->pluck('id');

        if ($courseIds->isEmpty()) {
            return [
                'instructor_id' => $instructorId,
                'instructor_name' => $instructor->name,
                'total_students' => 0,
                'average_grades' => 0,
                'attendance_rate' => 0,
                'student_ratings' => 0,
                'success_rate' => 0,
            ];
        }

        $studentIds = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'approved')
            ->pluck('student_id')
            ->unique();

        // Average grades
        $quizAttempts = QuizAttempt::whereIn('student_id', $studentIds)
            ->whereHas('quiz', fn ($q) => $q->whereIn('course_id', $courseIds));
        $avgGrades = $quizAttempts->avg('percentage') ?? 0;

        // Attendance rate
        $sessionIds = DB::table('sessions')->whereIn('course_id', $courseIds)->pluck('id');
        $totalAttendance = Attendance::whereIn('session_id', $sessionIds)->count();
        $present = Attendance::whereIn('session_id', $sessionIds)->where('status', 'present')->count();
        $attendanceRate = $totalAttendance > 0 ? round(($present / $totalAttendance) * 100, 2) : 0;

        // Student ratings
        $reviews = CourseReview::where('instructor_id', $instructorId);
        $avgRating = $reviews->avg('rating_instructor') ?? 0;
        $reviewsCount = $reviews->count();

        // Success rate (students who passed quizzes)
        $passedQuizzes = $quizAttempts->where('is_passed', true)->count();
        $totalQuizzes = $quizAttempts->count();
        $successRate = $totalQuizzes > 0 ? round(($passedQuizzes / $totalQuizzes) * 100, 2) : 0;

        return [
            'instructor_id' => $instructorId,
            'instructor_name' => $instructor->name,
            'instructor_email' => $instructor->email,
            'total_courses' => $courseIds->count(),
            'total_students' => $studentIds->count(),
            'average_grades' => round($avgGrades, 2),
            'attendance_rate' => $attendanceRate,
            'student_ratings' => round($avgRating, 2),
            'reviews_count' => $reviewsCount,
            'success_rate' => $successRate,
        ];
    }
}

