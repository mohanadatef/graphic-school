<?php

namespace Modules\LMS\Curriculum\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Curriculum\Models\CourseModule;
use Modules\LMS\Curriculum\Models\Lesson;
use Modules\LMS\Curriculum\Models\LessonResource;

class CourseModuleSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::where('is_published', true)->get();

        if ($courses->isEmpty()) {
            $this->command->warn('No published courses found. Please create courses first.');
            return;
        }

        foreach ($courses as $course) {
            // Module 1: Introduction
            $module1 = CourseModule::create([
                'course_id' => $course->id,
                'title' => 'مقدمة في الكورس',
                'description' => 'هذه الوحدة تقدم نظرة عامة على الكورس والمفاهيم الأساسية',
                'order' => 1,
                'is_published' => true,
                'is_preview' => true, // Free preview
            ]);

            // Lesson 1.1
            $lesson1 = Lesson::create([
                'module_id' => $module1->id,
                'title' => 'مرحباً بك في الكورس',
                'description' => 'تعرف على الكورس والأهداف التعليمية',
                'content' => '<p>مرحباً بك في هذا الكورس المميز. سنتعلم معاً كل ما تحتاجه لإتقان هذا المجال.</p>',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'video_duration' => '10:30',
                'video_provider' => 'youtube',
                'order' => 1,
                'lesson_type' => 'video',
                'is_preview' => true,
                'is_published' => true,
            ]);

            LessonResource::create([
                'lesson_id' => $lesson1->id,
                'title' => 'ملف PDF للمقدمة',
                'description' => 'ملف PDF يحتوي على نظرة عامة',
                'type' => 'document',
                'file_path' => '/resources/intro.pdf',
                'file_name' => 'intro.pdf',
                'file_size' => 1024000,
                'file_type' => 'application/pdf',
                'is_downloadable' => true,
                'order' => 1,
            ]);

            // Lesson 1.2
            $lesson2 = Lesson::create([
                'module_id' => $module1->id,
                'title' => 'الأدوات المطلوبة',
                'description' => 'تعرف على الأدوات والبرامج التي ستحتاجها',
                'content' => '<p>في هذا الدرس سنتعرف على جميع الأدوات والبرامج المطلوبة للبدء.</p>',
                'order' => 2,
                'lesson_type' => 'text',
                'is_preview' => true,
                'is_published' => true,
            ]);

            LessonResource::create([
                'lesson_id' => $lesson2->id,
                'title' => 'قائمة الأدوات',
                'description' => 'رابط لقائمة الأدوات المطلوبة',
                'type' => 'link',
                'external_url' => 'https://example.com/tools',
                'is_downloadable' => false,
                'order' => 1,
            ]);

            // Module 2: Basics
            $module2 = CourseModule::create([
                'course_id' => $course->id,
                'title' => 'الأساسيات',
                'description' => 'تعلم الأساسيات والمفاهيم الأولية',
                'order' => 2,
                'is_published' => true,
                'is_preview' => false,
            ]);

            // Lesson 2.1
            $lesson3 = Lesson::create([
                'module_id' => $module2->id,
                'title' => 'المفاهيم الأساسية',
                'description' => 'تعلم المفاهيم الأساسية في المجال',
                'content' => '<p>في هذا الدرس سنتعلم المفاهيم الأساسية التي يجب أن تعرفها.</p>',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'video_duration' => '15:45',
                'video_provider' => 'youtube',
                'order' => 1,
                'lesson_type' => 'video',
                'is_preview' => false,
                'is_published' => true,
            ]);

            // Lesson 2.2
            $lesson4 = Lesson::create([
                'module_id' => $module2->id,
                'title' => 'التطبيق العملي',
                'description' => 'تطبيق عملي على ما تعلمناه',
                'content' => '<p>الآن سنطبق ما تعلمناه في مثال عملي.</p>',
                'order' => 2,
                'lesson_type' => 'text',
                'is_preview' => false,
                'is_published' => true,
            ]);

            // Module 3: Advanced
            $module3 = CourseModule::create([
                'course_id' => $course->id,
                'title' => 'المستوى المتقدم',
                'description' => 'تعلم التقنيات المتقدمة',
                'order' => 3,
                'is_published' => true,
                'is_preview' => false,
            ]);

            // Lesson 3.1
            $lesson5 = Lesson::create([
                'module_id' => $module3->id,
                'title' => 'التقنيات المتقدمة',
                'description' => 'تعلم التقنيات المتقدمة في المجال',
                'content' => '<p>في هذا الدرس سنتعلم التقنيات المتقدمة.</p>',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'video_duration' => '20:00',
                'video_provider' => 'youtube',
                'order' => 1,
                'lesson_type' => 'video',
                'is_preview' => false,
                'is_published' => true,
            ]);

            // Update course counts
            $course->update([
                'total_modules' => 3,
                'total_lessons' => 5,
                'estimated_duration' => 8, // hours
                'course_type' => 'self_paced',
                'has_certificate' => true,
            ]);
        }

        $this->command->info('Course modules, lessons, and resources seeded successfully!');
    }
}

