<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Attendance\Models\Attendance;
use Modules\LMS\Attendance\Enums\AttendanceStatus;
use Modules\LMS\CourseReviews\Models\CourseReview;
use Modules\LMS\Assessments\Models\Quiz;
use Modules\LMS\Assessments\Models\QuizAttempt;
use Modules\LMS\Assessments\Models\StudentProject;
use Modules\LMS\Progress\Models\StudentProgress;
use Modules\LMS\Certificates\Models\Certificate;
use Modules\LMS\Curriculum\Models\CourseModule;
use Modules\LMS\Curriculum\Models\Lesson;
use Modules\CMS\Testimonials\Models\Testimonial;
use Modules\CMS\Sliders\Models\Slider;
use Modules\CMS\Contacts\Models\ContactMessage;
use Modules\ACL\Users\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ComprehensiveDataSeeder extends Seeder
{
    private $faker;

    public function run(): void
    {
        $this->faker = Faker::create('ar_EG');
        
        $this->command->info('Seeding comprehensive data for 5 years...');

        $this->seedAttendance();
        $this->seedCourseReviews();
        $this->seedQuizzesAndAttempts();
        $this->seedStudentProjects();
        $this->seedProgress();
        $this->seedCertificates();
        $this->seedTestimonials();
        $this->seedSliders();
        $this->seedContacts();

        $this->command->info('Comprehensive data seeded successfully!');
    }

    protected function seedAttendance(): void
    {
        $this->command->info('Seeding attendance records...');
        
        $sessions = Session::where('status', 'completed')
            ->orWhere('session_date', '<', Carbon::now())
            ->get();
        
        $attendanceCount = 0;

        foreach ($sessions as $session) {
            $course = $session->course;
            $enrolledStudents = Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->where('can_attend', true)
                ->with('student')
                ->get()
                ->pluck('student');

            foreach ($enrolledStudents as $student) {
                // 75% حضور, 25% غياب
                $rand = rand(1, 100);
                
                if ($rand <= 75) {
                    $status = AttendanceStatus::PRESENT;
                } else {
                    $status = AttendanceStatus::ABSENT;
                }

                Attendance::updateOrCreate(
                    [
                        'session_id' => $session->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => $status->value,
                        'note' => $status === AttendanceStatus::PRESENT && rand(1, 10) <= 2 
                            ? 'تأخر ' . rand(5, 30) . ' دقيقة' 
                            : null,
                        'created_at' => Carbon::parse($session->session_date),
                        'updated_at' => Carbon::parse($session->session_date),
                    ]
                );
                
                $attendanceCount++;
            }
        }

        $this->command->info("Attendance seeded: {$attendanceCount} records");
    }

    protected function seedCourseReviews(): void
    {
        $this->command->info('Seeding course reviews...');
        
        $completedEnrollments = Enrollment::where('status', 'approved')
            ->whereHas('course', function ($q) {
                $q->where('status', 'completed');
            })
            ->with(['student', 'course'])
            ->get();

        $reviewCount = 0;

        foreach ($completedEnrollments as $enrollment) {
            // 60% من الطلاب يقيمون الكورس
            if (rand(1, 100) > 60) {
                continue;
            }

            $course = $enrollment->course;
            $instructor = $course->instructors()->first();

            CourseReview::updateOrCreate(
                [
                    'student_id' => $enrollment->student_id,
                    'course_id' => $course->id,
                ],
                [
                    'instructor_id' => $instructor?->id,
                    'rating_course' => rand(3, 5), // معظم التقييمات إيجابية
                    'rating_instructor' => $instructor ? rand(3, 5) : null,
                    'comment' => $this->faker->paragraph(2),
                    'is_published' => rand(1, 10) <= 8, // 80% منشور
                    'created_at' => Carbon::parse($course->end_date)->addDays(rand(1, 30)),
                    'updated_at' => Carbon::parse($course->end_date)->addDays(rand(1, 30)),
                ]
            );
            
            $reviewCount++;
        }

        $this->command->info("Course reviews seeded: {$reviewCount} reviews");
    }

    protected function seedQuizzesAndAttempts(): void
    {
        $this->command->info('Seeding quizzes and attempts...');
        
        $courses = Course::where('is_published', true)->get();
        $quizCount = 0;
        $attemptCount = 0;

        foreach ($courses as $course) {
            $modules = $course->modules;
            
            if ($modules->isEmpty()) {
                continue;
            }

            // 2-3 quizzes لكل كورس
            $quizzesPerCourse = rand(2, 3);
            
            for ($i = 0; $i < $quizzesPerCourse; $i++) {
                $module = $modules->random();
                
                $quiz = Quiz::create([
                    'course_id' => $course->id,
                    'module_id' => $module->id,
                    'title' => "Quiz " . ($i + 1) . " - " . $module->title,
                    'description' => $this->faker->sentence(10),
                    'time_limit' => rand(15, 60),
                    'passing_score' => rand(60, 80),
                    'max_attempts' => rand(1, 3),
                    'show_results' => true,
                    'is_published' => true,
                    'created_at' => Carbon::parse($course->start_date)->addDays(rand(7, 30)),
                ]);

                // 5-10 أسئلة لكل Quiz
                $questionsCount = rand(5, 10);
                for ($q = 1; $q <= $questionsCount; $q++) {
                    $options = [
                        $this->faker->sentence(5),
                        $this->faker->sentence(5),
                        $this->faker->sentence(5),
                        $this->faker->sentence(5),
                    ];
                    
                    \Modules\LMS\Assessments\Models\QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question' => $this->faker->sentence(8) . '?',
                        'type' => 'multiple_choice',
                        'options' => $options,
                        'correct_answers' => [$options[0]],
                        'points' => 10,
                        'order' => $q,
                    ]);
                }

                // محاولات الطلاب
                $enrolledStudents = Enrollment::where('course_id', $course->id)
                    ->where('status', 'approved')
                    ->with('student')
                    ->get();

                foreach ($enrolledStudents as $enrollment) {
                    // 70% من الطلاب يجتازون Quiz
                    if (rand(1, 100) > 70) {
                        continue;
                    }

                    $attempts = rand(1, $quiz->max_attempts);
                    
                    for ($a = 1; $a <= $attempts; $a++) {
                        $score = rand(50, 100);
                        $isPassed = $score >= $quiz->passing_score;
                        
                        $startedAt = Carbon::parse($course->start_date)->addDays(rand(14, 60));
                        
                        $timeTaken = rand(10, $quiz->time_limit);
                        $totalPoints = \Modules\LMS\Assessments\Models\QuizQuestion::where('quiz_id', $quiz->id)
                            ->sum('points');
                        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;
                        
                        QuizAttempt::create([
                            'quiz_id' => $quiz->id,
                            'student_id' => $enrollment->student_id,
                            'enrollment_id' => $enrollment->id,
                            'answers' => [], // Simplified
                            'score' => $score,
                            'total_points' => $totalPoints,
                            'percentage' => $percentage,
                            'is_passed' => $isPassed,
                            'started_at' => $startedAt,
                            'completed_at' => $startedAt->copy()->addMinutes($timeTaken),
                            'time_taken' => $timeTaken * 60, // in seconds
                            'created_at' => $startedAt,
                        ]);
                        
                        $attemptCount++;
                        
                        // إذا نجح في المحاولة الأولى، لا نحتاج محاولات أخرى
                        if ($isPassed && $a === 1) {
                            break;
                        }
                    }
                }
                
                $quizCount++;
            }
        }

        $this->command->info("Quizzes seeded: {$quizCount} quizzes, {$attemptCount} attempts");
    }

    protected function seedStudentProjects(): void
    {
        $this->command->info('Seeding student projects...');
        
        $courses = Course::where('is_published', true)->get();
        $projectCount = 0;

        foreach ($courses as $course) {
            $enrolledStudents = Enrollment::where('course_id', $course->id)
                ->where('status', 'approved')
                ->with('student')
                ->get();

            foreach ($enrolledStudents as $enrollment) {
                // 50% من الطلاب يقدمون مشروع
                if (rand(1, 100) > 50) {
                    continue;
                }

                $statuses = ['pending', 'submitted', 'in_review', 'approved', 'needs_revision'];
                $weights = [10, 20, 20, 40, 10]; // 10% pending, 20% submitted, 20% in_review, 40% approved, 10% needs_revision
                $status = $this->weightedRandom($statuses, $weights);
                
                $module = $course->modules()->first();
                $lesson = $module ? $module->lessons()->first() : null;
                $instructor = $course->instructors()->first();

                StudentProject::create([
                    'course_id' => $course->id,
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $enrollment->id,
                    'module_id' => $module?->id,
                    'lesson_id' => $lesson?->id,
                    'title' => "Project - " . $this->faker->words(3, true),
                    'description' => $this->faker->paragraph(3),
                    'files' => [
                        ['name' => 'project.pdf', 'path' => 'projects/' . $this->faker->uuid() . '.pdf', 'size' => rand(100000, 5000000)],
                    ],
                    'status' => $status,
                    'score' => in_array($status, ['approved', 'needs_revision']) ? rand(60, 100) : null,
                    'instructor_feedback' => in_array($status, ['approved', 'needs_revision']) ? $this->faker->paragraph(2) : null,
                    'reviewed_by' => in_array($status, ['approved', 'needs_revision']) ? $instructor?->id : null,
                    'submitted_at' => in_array($status, ['submitted', 'in_review', 'approved', 'needs_revision']) 
                        ? Carbon::parse($course->start_date)->addDays(rand(30, 90))
                        : null,
                    'reviewed_at' => in_array($status, ['approved', 'needs_revision'])
                        ? Carbon::parse($course->start_date)->addDays(rand(90, 120))
                        : null,
                    'created_at' => Carbon::parse($course->start_date)->addDays(rand(20, 60)),
                ]);
                
                $projectCount++;
            }
        }

        $this->command->info("Student projects seeded: {$projectCount} projects");
    }

    protected function seedProgress(): void
    {
        $this->command->info('Seeding student progress...');
        
        $enrollments = Enrollment::where('status', 'approved')
            ->where('can_attend', true)
            ->with(['student', 'course'])
            ->get();

        $progressCount = 0;

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $lessons = Lesson::whereHas('module', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })->get();

            if ($lessons->isEmpty()) {
                continue;
            }

            $totalLessons = $lessons->count();
            $completedLessons = (int)($totalLessons * (rand(20, 100) / 100)); // 20-100% completion
            
            foreach ($lessons as $index => $lesson) {
                $isCompleted = $index < $completedLessons;
                $progressPercentage = $isCompleted ? 100 : rand(0, 90);
                $timeSpent = rand(300, 3600); // 5-60 minutes

                $startedAt = Carbon::parse($course->start_date)->addDays(rand(1, 30));
                $completedAt = $isCompleted 
                    ? $startedAt->copy()->addDays(rand(1, 7))
                    : null;

                StudentProgress::create([
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $enrollment->id,
                    'course_id' => $course->id,
                    'module_id' => $lesson->module_id,
                    'lesson_id' => $lesson->id,
                    'type' => 'lesson',
                    'is_completed' => $isCompleted,
                    'progress_percentage' => $progressPercentage,
                    'time_spent' => $timeSpent,
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                    'last_accessed_at' => $isCompleted 
                        ? $completedAt 
                        : Carbon::now()->subDays(rand(0, 30)),
                    'created_at' => $startedAt,
                ]);
                
                $progressCount++;
            }
        }

        $this->command->info("Student progress seeded: {$progressCount} progress records");
    }

    protected function seedCertificates(): void
    {
        $this->command->info('Seeding certificates...');
        
        $completedEnrollments = Enrollment::where('status', 'approved')
            ->whereHas('course', function ($q) {
                $q->where('status', 'completed');
            })
            ->with(['student', 'course'])
            ->get();

        $certificateCount = 0;

        foreach ($completedEnrollments as $enrollment) {
            $course = $enrollment->course;
            
            // التحقق من إتمام الكورس (100% progress)
            $totalLessons = Lesson::whereHas('module', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })->count();
            
            $completedLessons = StudentProgress::where('enrollment_id', $enrollment->id)
                ->where('is_completed', true)
                ->count();
            
            // 70% من الطلاب المكملين يحصلون على شهادة
            if ($completedLessons >= $totalLessons * 0.8 && rand(1, 100) <= 70) {
                Certificate::create([
                    'course_id' => $course->id,
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $enrollment->id,
                    'issued_date' => Carbon::parse($course->end_date)->addDays(rand(1, 7)),
                    'is_verified' => true,
                ]);
                
                $certificateCount++;
            }
        }

        $this->command->info("Certificates seeded: {$certificateCount} certificates");
    }

    protected function seedTestimonials(): void
    {
        $this->command->info('Seeding testimonials...');
        
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'student'))
            ->inRandomOrder()
            ->take(30)
            ->get();

        $testimonialCount = 0;

        foreach ($students as $student) {
            Testimonial::create([
                'name' => $student->name,
                'relation' => 'Student',
                'rating' => rand(4, 5), // معظم التقييمات إيجابية
                'comment' => $this->faker->paragraph(3),
                'is_approved' => rand(1, 10) <= 9, // 90% approved
                'created_at' => Carbon::now()->subMonths(rand(1, 24)),
            ]);
            
            $testimonialCount++;
        }

        $this->command->info("Testimonials seeded: {$testimonialCount} testimonials");
    }

    protected function seedSliders(): void
    {
        $this->command->info('Seeding sliders...');
        
        $sliders = [
            [
                'title' => 'مرحباً بك في جرافيك سكول',
                'subtitle' => 'منصة تعليمية متكاملة',
                'description' => 'تعلم التصميم الجرافيكي من الصفر للإحتراف',
                'button_text' => 'استكشف الكورسات',
                'button_url' => '/courses',
            ],
            [
                'title' => 'كورسات احترافية',
                'subtitle' => 'مع مدربين محترفين',
                'description' => 'دروس مباشرة ومتابعة شخصية',
                'button_text' => 'سجل الآن',
                'button_url' => '/register',
            ],
            [
                'title' => 'شهادات معتمدة',
                'subtitle' => 'عند إتمام الكورس',
                'description' => 'احصل على شهادة معتمدة تثبت مهاراتك',
                'button_text' => 'اعرف المزيد',
                'button_url' => '/about',
            ],
        ];

        foreach ($sliders as $index => $slider) {
            Slider::create([
                'title' => $slider['title'],
                'subtitle' => $slider['subtitle'],
                'description' => $slider['description'],
                'button_text' => $slider['button_text'],
                'button_url' => $slider['button_url'],
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => Carbon::now()->subYears(5),
            ]);
        }

        $this->command->info('Sliders seeded: 3 sliders');
    }

    protected function seedContacts(): void
    {
        $this->command->info('Seeding contact messages...');
        
        $contactCount = 0;

        // 200 رسالة على مدى 5 سنوات
        for ($i = 0; $i < 200; $i++) {
            $createdAt = Carbon::now()->subYears(5)->addDays(rand(0, 1825));
            
            $isResolved = $createdAt->isPast() && rand(1, 100) <= 70; // 70% resolved
            
            ContactMessage::create([
                'name' => $this->faker->name(),
                'email' => $this->faker->email(),
                'phone' => '01' . rand(10000000, 99999999),
                'message' => $this->faker->paragraph(3),
                'is_resolved' => $isResolved,
                'created_at' => $createdAt,
                'updated_at' => $isResolved 
                    ? $createdAt->copy()->addDays(rand(1, 7))
                    : $createdAt,
            ]);
            
            $contactCount++;
        }

        $this->command->info("Contact messages seeded: {$contactCount} messages");
    }

    private function weightedRandom(array $items, array $weights): string
    {
        $total = array_sum($weights);
        $rand = rand(1, $total);
        $current = 0;
        
        foreach ($items as $index => $item) {
            $current += $weights[$index];
            if ($rand <= $current) {
                return $item;
            }
        }
        
        return $items[0];
    }
}

