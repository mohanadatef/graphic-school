<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Assessments\Models\QuizAttempt;
use Modules\LMS\Assessments\Models\Quiz;
use Modules\LMS\Assessments\Models\StudentProject;
use Modules\LMS\CourseReviews\Models\CourseReview;
use Modules\LMS\Attendance\Models\Attendance;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Sessions\Enums\SessionStatus;
use Modules\LMS\Attendance\Enums\AttendanceStatus;
use Modules\ACL\Users\Models\User;
use Modules\CMS\Testimonials\Models\Testimonial;
use Modules\CMS\Sliders\Models\Slider;

class ComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding comprehensive data...');

        // Seed Course Reviews
        $this->seedCourseReviews();

        // Seed Attendance
        $this->seedAttendance();

        // Seed Quiz Attempts
        $this->seedQuizAttempts();

        // Seed Student Projects
        $this->seedStudentProjects();

        // Seed Testimonials
        $this->seedTestimonials();

        // Seed Sliders
        $this->seedSliders();

        // Update course statistics
        $this->updateCourseStatistics();

        $this->command->info('Comprehensive data seeded successfully!');
    }

    protected function seedCourseReviews(): void
    {
        $enrollments = Enrollment::where('status', 'approved')
            ->with(['student', 'course'])
            ->take(10)
            ->get();

        foreach ($enrollments as $enrollment) {
            if (rand(1, 3) === 1) { // 33% chance
                $instructor = $enrollment->course->instructors()->first();
                CourseReview::updateOrCreate(
                    [
                        'course_id' => $enrollment->course_id,
                        'student_id' => $enrollment->student_id,
                    ],
                    [
                        'rating_course' => rand(4, 5),
                        'rating_instructor' => rand(4, 5),
                        'instructor_id' => $instructor?->id,
                        'comment' => 'كورس رائع ومفيد جداً. أنصح به بشدة!',
                        'is_published' => true,
                    ]
                );
            }
        }
    }

    protected function seedAttendance(): void
    {
        $sessions = Session::where('status', SessionStatus::COMPLETED->value)
            ->take(20)
            ->get();

        foreach ($sessions as $session) {
            $enrollments = Enrollment::where('course_id', $session->course_id)
                ->where('status', 'approved')
                ->with('student')
                ->take(rand(5, 15))
                ->get();

            foreach ($enrollments as $enrollment) {
                Attendance::updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'student_id' => $enrollment->student_id,
                    ],
                    [
                        'status' => rand(1, 10) <= 8 ? AttendanceStatus::PRESENT->value : AttendanceStatus::ABSENT->value,
                        'note' => rand(1, 5) === 1 ? 'حضور ممتاز' : null,
                    ]
                );
            }
        }
    }

    protected function seedQuizAttempts(): void
    {
        $quizzes = Quiz::where('is_published', true)->take(5)->get();
        $enrollments = Enrollment::where('status', 'approved')->take(10)->get();

        foreach ($quizzes as $quiz) {
            foreach ($enrollments as $enrollment) {
                if ($enrollment->course_id !== $quiz->course_id) {
                    continue;
                }

                if (rand(1, 3) === 1) { // 33% chance
                    $score = rand(60, 100);
                    $totalPoints = 100;
                    $percentage = $score;
                    $isPassed = $percentage >= $quiz->passing_score;

                    QuizAttempt::create([
                        'student_id' => $enrollment->student_id,
                        'quiz_id' => $quiz->id,
                        'enrollment_id' => $enrollment->id,
                        'answers' => [],
                        'score' => $score,
                        'total_points' => $totalPoints,
                        'percentage' => $percentage,
                        'is_passed' => $isPassed,
                        'started_at' => now()->subDays(rand(1, 20)),
                        'completed_at' => now()->subDays(rand(1, 20)),
                        'time_taken' => rand(300, 1800), // 5-30 minutes
                    ]);
                }
            }
        }
    }

    protected function seedStudentProjects(): void
    {
        $enrollments = Enrollment::where('status', 'approved')
            ->with(['course', 'student'])
            ->take(8)
            ->get();

        foreach ($enrollments as $enrollment) {
            if (rand(1, 2) === 1) { // 50% chance
                $statuses = ['pending', 'submitted', 'in_review', 'approved'];
                $status = $statuses[rand(0, count($statuses) - 1)];

                StudentProject::create([
                    'student_id' => $enrollment->student_id,
                    'course_id' => $enrollment->course_id,
                    'enrollment_id' => $enrollment->id,
                    'title' => 'مشروع نهائي - ' . $enrollment->course->title,
                    'description' => 'مشروع نهائي يطبق ما تم تعلمه في الكورس',
                    'files' => ['/projects/project1.pdf'],
                    'status' => $status,
                    'score' => $status === 'approved' ? rand(75, 100) : null,
                    'submitted_at' => $status !== 'pending' ? now()->subDays(rand(1, 15)) : null,
                ]);
            }
        }
    }

    protected function seedTestimonials(): void
    {
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'student'))->take(5)->get();

        foreach ($students as $student) {
            Testimonial::updateOrCreate(
                ['name' => $student->name],
                [
                    'comment' => 'تجربة رائعة! الكورسات ممتازة والمدربين محترفين. أنصح الجميع بالتسجيل.',
                    'rating_course' => rand(4, 5),
                    'rating_instructor' => rand(4, 5),
                    'role' => 'طالب',
                    'is_approved' => true,
                    'is_featured' => rand(1, 3) === 1,
                ]
            );
        }
    }

    protected function seedSliders(): void
    {
        $sliders = [
            [
                'title' => 'مرحباً بك في Graphic School',
                'subtitle' => 'تعلم من أفضل المدربين',
                'image_path' => '/images/slider1.jpg',
                'button_text' => 'استكشف الكورسات',
                'button_url' => '/courses',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'كورسات احترافية في التصميم',
                'subtitle' => 'طور مهاراتك معنا',
                'image_path' => '/images/slider2.jpg',
                'button_text' => 'ابدأ التعلم',
                'button_url' => '/courses',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $sliderData) {
            Slider::updateOrCreate(
                ['title' => $sliderData['title']],
                $sliderData
            );
        }
    }

    protected function updateCourseStatistics(): void
    {
        $courses = Course::all();

        foreach ($courses as $course) {
            $studentsCount = Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->count();

            $completionCount = Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->where('progress_percentage', '>=', 100)
                ->count();

            $avgRating = CourseReview::where('course_id', $course->id)
                ->where('is_published', true)
                ->avg('rating_course');

            $ratingCount = CourseReview::where('course_id', $course->id)
                ->where('is_published', true)
                ->count();

            $course->update([
                'students_count' => $studentsCount,
                'completion_count' => $completionCount,
                'rating' => round($avgRating ?? 0, 2),
                'rating_count' => $ratingCount,
            ]);
        }
    }
}

