<?php

namespace Modules\LMS\Progress\Services;

use Modules\LMS\Progress\Models\StudentProgress;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Curriculum\Models\Lesson;
use Modules\LMS\Curriculum\Models\CourseModule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProgressService
{
    public function markLessonComplete(int $studentId, int $enrollmentId, int $lessonId): StudentProgress
    {
        return DB::transaction(function () use ($studentId, $enrollmentId, $lessonId) {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            $lesson = Lesson::findOrFail($lessonId);
            $module = CourseModule::findOrFail($lesson->module_id);

            // Create or update progress
            $progress = StudentProgress::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'enrollment_id' => $enrollmentId,
                    'lesson_id' => $lessonId,
                ],
                [
                    'course_id' => $enrollment->course_id,
                    'module_id' => $module->id,
                    'type' => 'lesson',
                    'is_completed' => true,
                    'progress_percentage' => 100,
                    'completed_at' => now(),
                    'last_accessed_at' => now(),
                ]
            );

            // Update enrollment progress
            $this->updateEnrollmentProgress($enrollmentId);

            // Check if module is complete
            $this->checkModuleCompletion($studentId, $enrollmentId, $module->id);

            // Check if course is complete
            $this->checkCourseCompletion($studentId, $enrollmentId, $enrollment->course_id);

            return $progress;
        });
    }

    public function updateLessonProgress(int $studentId, int $enrollmentId, int $lessonId, int $percentage, int $timeSpent = 0): StudentProgress
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $lesson = Lesson::findOrFail($lessonId);
        $module = CourseModule::findOrFail($lesson->module_id);

        $progress = StudentProgress::updateOrCreate(
            [
                'student_id' => $studentId,
                'enrollment_id' => $enrollmentId,
                'lesson_id' => $lessonId,
            ],
            [
                'course_id' => $enrollment->course_id,
                'module_id' => $module->id,
                'type' => 'lesson',
                'progress_percentage' => min(100, max(0, $percentage)),
                'time_spent' => DB::raw("time_spent + {$timeSpent}"),
                'last_accessed_at' => now(),
            ]
        );

        if ($progress->started_at === null) {
            $progress->update(['started_at' => now()]);
        }

        $this->updateEnrollmentProgress($enrollmentId);

        return $progress->fresh();
    }

    public function getStudentProgress(int $studentId, int $enrollmentId): array
    {
        $enrollment = Enrollment::with('course')->findOrFail($enrollmentId);
        $course = $enrollment->course;

        $progress = StudentProgress::where('student_id', $studentId)
            ->where('enrollment_id', $enrollmentId)
            ->get();

        $lessonsCompleted = $progress->where('type', 'lesson')->where('is_completed', true)->count();
        $totalLessons = $course->total_lessons ?? 0;
        $modulesCompleted = $progress->where('type', 'module')->where('is_completed', true)->count();
        $totalModules = $course->total_modules ?? 0;

        $courseProgress = $totalLessons > 0 
            ? round(($lessonsCompleted / $totalLessons) * 100, 2) 
            : 0;

        $timeSpent = $progress->sum('time_spent');

        return [
            'enrollment' => $enrollment,
            'course' => $course,
            'lessons_completed' => $lessonsCompleted,
            'total_lessons' => $totalLessons,
            'modules_completed' => $modulesCompleted,
            'total_modules' => $totalModules,
            'course_progress' => $courseProgress,
            'time_spent' => $timeSpent,
            'time_spent_formatted' => $this->formatTime($timeSpent),
            'is_course_completed' => $courseProgress >= 100,
            'progress_by_module' => $this->getProgressByModule($studentId, $enrollmentId),
        ];
    }

    protected function updateEnrollmentProgress(int $enrollmentId): void
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $course = Course::findOrFail($enrollment->course_id);

        $lessonsCompleted = StudentProgress::where('enrollment_id', $enrollmentId)
            ->where('type', 'lesson')
            ->where('is_completed', true)
            ->count();

        $totalLessons = $course->total_lessons ?? 0;
        $progressPercentage = $totalLessons > 0 
            ? round(($lessonsCompleted / $totalLessons) * 100, 2) 
            : 0;

        $timeSpent = StudentProgress::where('enrollment_id', $enrollmentId)
            ->sum('time_spent');

        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'lessons_completed' => $lessonsCompleted,
            'total_lessons' => $totalLessons,
            'time_spent' => $timeSpent,
            'last_accessed_at' => now(),
        ]);

        if ($progressPercentage >= 100 && $enrollment->completed_at === null) {
            $enrollment->update(['completed_at' => now()]);
        }
    }

    protected function checkModuleCompletion(int $studentId, int $enrollmentId, int $moduleId): void
    {
        $module = CourseModule::findOrFail($moduleId);
        $totalLessons = $module->lessons()->where('is_published', true)->count();
        
        if ($totalLessons === 0) {
            return;
        }

        $completedLessons = StudentProgress::where('student_id', $studentId)
            ->where('enrollment_id', $enrollmentId)
            ->where('module_id', $moduleId)
            ->where('type', 'lesson')
            ->where('is_completed', true)
            ->count();

        if ($completedLessons >= $totalLessons) {
            StudentProgress::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'enrollment_id' => $enrollmentId,
                    'module_id' => $moduleId,
                ],
                [
                    'course_id' => $module->course_id,
                    'type' => 'module',
                    'is_completed' => true,
                    'progress_percentage' => 100,
                    'completed_at' => now(),
                ]
            );
        }
    }

    protected function checkCourseCompletion(int $studentId, int $enrollmentId, int $courseId): void
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        
        if ($enrollment->progress_percentage >= 100 && $enrollment->completed_at === null) {
            $enrollment->update(['completed_at' => now()]);
            
            // Mark course as completed in progress table
            StudentProgress::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'enrollment_id' => $enrollmentId,
                    'course_id' => $courseId,
                ],
                [
                    'type' => 'course',
                    'is_completed' => true,
                    'progress_percentage' => 100,
                    'completed_at' => now(),
                ]
            );
        }
    }

    protected function getProgressByModule(int $studentId, int $enrollmentId): array
    {
        $enrollment = Enrollment::findOrFail($enrollmentId);
        $course = Course::with('modules.lessons')->findOrFail($enrollment->course_id);

        $allProgress = StudentProgress::where('student_id', $studentId)
            ->where('enrollment_id', $enrollmentId)
            ->where('type', 'lesson')
            ->get()
            ->keyBy('lesson_id');

        return $course->modules->map(function ($module) use ($studentId, $enrollmentId, $allProgress) {
            $moduleLessons = $module->lessons()->where('is_published', true)->get();
            $totalLessons = $moduleLessons->count();
            
            $completedLessons = $moduleLessons->filter(function ($lesson) use ($allProgress) {
                $progress = $allProgress->get($lesson->id);
                return $progress && $progress->is_completed;
            })->count();

            $progress = $totalLessons > 0 
                ? round(($completedLessons / $totalLessons) * 100, 2) 
                : 0;

            // Get lesson-level progress
            $lessons = $moduleLessons->map(function ($lesson) use ($allProgress) {
                $progress = $allProgress->get($lesson->id);
                return [
                    'lesson_id' => $lesson->id,
                    'is_completed' => $progress ? $progress->is_completed : false,
                    'progress_percentage' => $progress ? $progress->progress_percentage : 0,
                ];
            })->toArray();

            return [
                'module_id' => $module->id,
                'module_title' => $module->title,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'progress' => $progress,
                'is_completed' => $progress >= 100,
                'lessons' => $lessons,
            ];
        })->toArray();
    }

    protected function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        if ($hours > 0) {
            return "{$hours} ساعة و {$minutes} دقيقة";
        }
        return "{$minutes} دقيقة";
    }
}

