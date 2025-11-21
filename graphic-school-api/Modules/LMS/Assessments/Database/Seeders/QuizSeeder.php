<?php

namespace Modules\LMS\Assessments\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Curriculum\Models\CourseModule;
use Modules\LMS\Assessments\Models\Quiz;
use Modules\LMS\Assessments\Models\QuizQuestion;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::where('is_published', true)->take(3)->get();

        foreach ($courses as $course) {
            $module = $course->modules()->first();
            
            if (!$module) {
                continue;
            }

            // Quiz 1: Module Quiz
            $quiz1 = Quiz::create([
                'course_id' => $course->id,
                'module_id' => $module->id,
                'title' => 'اختبار الوحدة الأولى',
                'description' => 'اختبار شامل على محتوى الوحدة الأولى',
                'time_limit' => 30, // minutes
                'passing_score' => 60,
                'max_attempts' => 3,
                'show_results' => true,
                'is_published' => true,
            ]);

            // Question 1
            QuizQuestion::create([
                'quiz_id' => $quiz1->id,
                'question' => 'ما هي المفهوم الأساسي الذي تعلمناه في هذا الكورس؟',
                'type' => 'multiple_choice',
                'options' => [
                    'الخيار الأول',
                    'الخيار الثاني',
                    'الخيار الثالث',
                    'الخيار الرابع',
                ],
                'correct_answers' => ['الخيار الأول'],
                'explanation' => 'الخيار الأول هو الصحيح لأنه يمثل المفهوم الأساسي',
                'points' => 10,
                'order' => 1,
            ]);

            // Question 2
            QuizQuestion::create([
                'quiz_id' => $quiz1->id,
                'question' => 'هل هذا المفهوم صحيح؟',
                'type' => 'true_false',
                'options' => ['صحيح', 'خطأ'],
                'correct_answers' => ['صحيح'],
                'explanation' => 'نعم، هذا المفهوم صحيح',
                'points' => 5,
                'order' => 2,
            ]);

            // Question 3
            QuizQuestion::create([
                'quiz_id' => $quiz1->id,
                'question' => 'اكتب تعريفاً مختصراً للمفهوم الأساسي',
                'type' => 'short_answer',
                'correct_answers' => ['المفهوم الأساسي', 'concept'],
                'explanation' => 'هذا السؤال يحتاج تقييم يدوي',
                'points' => 15,
                'order' => 3,
            ]);

            // Quiz 2: Course Final Quiz
            $quiz2 = Quiz::create([
                'course_id' => $course->id,
                'title' => 'الاختبار النهائي',
                'description' => 'اختبار شامل على محتوى الكورس بالكامل',
                'time_limit' => 60,
                'passing_score' => 70,
                'max_attempts' => 2,
                'show_results' => true,
                'is_published' => true,
            ]);

            QuizQuestion::create([
                'quiz_id' => $quiz2->id,
                'question' => 'ما هي أهم النقاط التي تعلمتها في هذا الكورس؟',
                'type' => 'essay',
                'correct_answers' => [],
                'explanation' => 'هذا السؤال يحتاج تقييم يدوي من المدرب',
                'points' => 25,
                'order' => 1,
            ]);
        }

        $this->command->info('Quizzes and questions seeded successfully!');
    }
}

