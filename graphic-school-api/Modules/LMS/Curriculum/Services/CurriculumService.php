<?php

namespace Modules\LMS\Curriculum\Services;

use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Curriculum\Models\CourseModule;
use Modules\LMS\Curriculum\Models\Lesson;
use Modules\LMS\Curriculum\Models\LessonResource;
use App\Services\EntityTranslationService;
use Illuminate\Support\Facades\DB;

class CurriculumService
{
    public function getCourseCurriculum(int $courseId): Course
    {
        $course = Course::with([
            'modules' => fn($q) => $q->where('is_published', true)->orderBy('order'),
            'modules.lessons' => fn($q) => $q->where('is_published', true)->orderBy('order'),
            'modules.lessons.resources' => fn($q) => $q->orderBy('order'),
        ])
        ->where('id', $courseId)
        ->firstOrFail();

        // Load quizzes and projects for each lesson
        $lessonIds = $course->modules->flatMap(fn($m) => $m->lessons->pluck('id'))->all();
        
        if (!empty($lessonIds)) {
            $quizzes = Quiz::whereIn('lesson_id', $lessonIds)
                ->where('is_published', true)
                ->get()
                ->keyBy('lesson_id');

            $projects = StudentProject::whereIn('lesson_id', $lessonIds)
                ->get()
                ->groupBy('lesson_id');

            // Attach quizzes and projects to lessons
            foreach ($course->modules as $module) {
                foreach ($module->lessons as $lesson) {
                    if (isset($quizzes[$lesson->id])) {
                        $lesson->setRelation('quiz', $quizzes[$lesson->id]);
                    }
                    if (isset($projects[$lesson->id])) {
                        $lesson->setRelation('projects', $projects[$lesson->id]);
                    }
                }
            }
        }

        return $course;
    }

    public function createModule(array $data): CourseModule
    {
        return DB::transaction(function () use ($data) {
            $translations = $data['translations'] ?? [];
            unset($data['translations']);

            $module = CourseModule::create($data);

            // Save translations if provided
            if (!empty($translations)) {
                $translationService = app(EntityTranslationService::class);
                $translationService->saveTranslations($module, $translations);
            }

            $this->updateCourseModuleCount($data['course_id']);
            return $module;
        });
    }

    public function updateModule(int $moduleId, array $data): CourseModule
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $module = CourseModule::findOrFail($moduleId);
        $module->update($data);

        // Update translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($module, $translations);
        }

        $this->updateCourseModuleCount($module->course_id);
        return $module->fresh();
    }

    public function deleteModule(int $moduleId): bool
    {
        return DB::transaction(function () use ($moduleId) {
            $module = CourseModule::findOrFail($moduleId);
            $courseId = $module->course_id;

            // Delete translations
            $translationService = app(EntityTranslationService::class);
            $translationService->deleteTranslations($module);

            $module->delete();
            $this->updateCourseModuleCount($courseId);
            return true;
        });
    }

    public function createLesson(array $data): Lesson
    {
        return DB::transaction(function () use ($data) {
            $translations = $data['translations'] ?? [];
            unset($data['translations']);

            $lesson = Lesson::create($data);

            // Save translations if provided
            if (!empty($translations)) {
                $translationService = app(EntityTranslationService::class);
                $translationService->saveTranslations($lesson, $translations);
            }

            $module = CourseModule::findOrFail($data['module_id']);
            $this->updateCourseLessonCount($module->course_id);
            return $lesson;
        });
    }

    public function updateLesson(int $lessonId, array $data): Lesson
    {
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $lesson = Lesson::findOrFail($lessonId);
        $lesson->update($data);

        // Update translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($lesson, $translations);
        }

        $module = CourseModule::findOrFail($lesson->module_id);
        $this->updateCourseLessonCount($module->course_id);
        return $lesson->fresh();
    }

    public function deleteLesson(int $lessonId): bool
    {
        return DB::transaction(function () use ($lessonId) {
            $lesson = Lesson::findOrFail($lessonId);
            $module = CourseModule::findOrFail($lesson->module_id);
            $courseId = $module->course_id;

            // Delete translations
            $translationService = app(EntityTranslationService::class);
            $translationService->deleteTranslations($lesson);

            $lesson->delete();
            $this->updateCourseLessonCount($courseId);
            return true;
        });
    }

    public function addResource(array $data): LessonResource
    {
        return LessonResource::create($data);
    }

    public function updateResource(int $resourceId, array $data): LessonResource
    {
        $resource = LessonResource::findOrFail($resourceId);
        $resource->update($data);
        return $resource->fresh();
    }

    public function deleteResource(int $resourceId): bool
    {
        $resource = LessonResource::findOrFail($resourceId);
        return $resource->delete();
    }

    protected function updateCourseModuleCount(int $courseId): void
    {
        $count = CourseModule::where('course_id', $courseId)
            ->where('is_published', true)
            ->count();
        
        Course::where('id', $courseId)->update(['total_modules' => $count]);
    }

    protected function updateCourseLessonCount(int $courseId): void
    {
        $count = Lesson::whereHas('module', fn($q) => $q->where('course_id', $courseId))
            ->where('is_published', true)
            ->count();
        
        Course::where('id', $courseId)->update(['total_lessons' => $count]);
    }
}

