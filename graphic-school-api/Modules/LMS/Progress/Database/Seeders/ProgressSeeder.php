<?php

namespace Modules\LMS\Progress\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Progress\Models\StudentProgress;
use Modules\LMS\Curriculum\Models\Lesson;

class ProgressSeeder extends Seeder
{
    public function run(): void
    {
        $enrollments = Enrollment::where('status', 'approved')
            ->with(['student', 'course'])
            ->take(10)
            ->get();

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            $lessons = Lesson::whereHas('module', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })->get();

            $completedLessons = 0;
            $totalLessons = $lessons->count();
            
            foreach ($lessons as $index => $lesson) {
                $isCompleted = $index < ($totalLessons * 0.7); // 70% completion
                $progressPercentage = $isCompleted ? 100 : rand(20, 90);
                $timeSpent = rand(300, 1800); // 5-30 minutes

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
                    'started_at' => now()->subDays(rand(1, 30)),
                    'completed_at' => $isCompleted ? now()->subDays(rand(1, 20)) : null,
                    'last_accessed_at' => now()->subHours(rand(1, 48)),
                ]);

                if ($isCompleted) {
                    $completedLessons++;
                }
            }

            // Update enrollment progress
            $courseProgress = $totalLessons > 0 
                ? round(($completedLessons / $totalLessons) * 100, 2) 
                : 0;

            $totalTimeSpent = StudentProgress::where('enrollment_id', $enrollment->id)
                ->sum('time_spent');

            $enrollment->update([
                'progress_percentage' => $courseProgress,
                'lessons_completed' => $completedLessons,
                'total_lessons' => $totalLessons,
                'time_spent' => $totalTimeSpent,
                'started_at' => now()->subDays(rand(1, 30)),
                'last_accessed_at' => now()->subHours(rand(1, 24)),
                'completed_at' => $courseProgress >= 100 ? now()->subDays(rand(1, 10)) : null,
            ]);
        }

        $this->command->info('Student progress seeded successfully!');
    }
}

