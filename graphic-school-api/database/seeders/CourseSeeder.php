<?php

namespace Database\Seeders;

use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Courses\Enums\CourseStatus;
use Modules\ACL\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_EG');
        $categories = Category::pluck('id')->all();
        $instructors = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))->pluck('id')->all();

        if (count($categories) === 0 || count($instructors) < 2) {
            return;
        }

        $courseTemplates = [
            ['title' => 'Professional Branding Bootcamp', 'days' => ['mon', 'wed'], 'sessions' => 12],
            ['title' => 'Advanced Illustration Lab', 'days' => ['tue', 'thu'], 'sessions' => 10],
            ['title' => 'UX Research & Prototyping', 'days' => ['sat'], 'sessions' => 8],
            ['title' => 'Creative Typography Workshop', 'days' => ['sun'], 'sessions' => 6],
            ['title' => 'Motion Design Essentials', 'days' => ['mon', 'thu'], 'sessions' => 14],
            ['title' => 'Digital Marketing Design', 'days' => ['tue', 'fri'], 'sessions' => 10],
            ['title' => 'Packaging Design Mastery', 'days' => ['wed', 'sat'], 'sessions' => 12],
            ['title' => 'Logo Design Fundamentals', 'days' => ['mon', 'wed', 'fri'], 'sessions' => 8],
            ['title' => 'Web Design & Development', 'days' => ['tue', 'thu'], 'sessions' => 16],
            ['title' => 'Print Design Workshop', 'days' => ['sat', 'sun'], 'sessions' => 6],
        ];

        // إنشاء كورسات للاختبار - 10 كورسات فقط
        
        $courseCounter = 1;
        $courseCodeCounter = 1;
        $totalCourses = 10;
        
        for ($i = 0; $i < $totalCourses; $i++) {
            $template = $courseTemplates[array_rand($courseTemplates)];
            
            // توزيع التواريخ على مدار السنة الماضية
            $startDate = Carbon::now()->subMonths(rand(1, 12))->addDays(rand(1, 30));
            
            // تحديد الحالة بناءً على التاريخ
            $now = Carbon::now();
            if ($startDate->isPast() && $startDate->copy()->addWeeks($template['sessions'])->isPast()) {
                $status = CourseStatus::COMPLETED;
            } elseif ($startDate->isPast()) {
                $status = CourseStatus::RUNNING;
            } elseif ($startDate->isFuture() && $startDate->diffInDays($now) < 30) {
                $status = CourseStatus::UPCOMING;
            } else {
                $status = CourseStatus::DRAFT;
            }
            
            $course = Course::create([
                'title' => $template['title'] . ' - Batch ' . str_pad($courseCounter, 2, '0', STR_PAD_LEFT),
                'slug' => Str::slug($template['title'] . '-batch-' . $courseCounter),
                'code' => 'GS-' . str_pad($courseCodeCounter, 4, '0', STR_PAD_LEFT),
                'category_id' => $categories[array_rand($categories)],
                'description' => $faker->paragraph(5),
                'price' => rand(1500, 5000),
                'start_date' => $startDate->toDateString(),
                'end_date' => $startDate->copy()->addWeeks($template['sessions'])->toDateString(),
                'session_count' => $template['sessions'],
                'days_of_week' => $template['days'],
                'duration_weeks' => $template['sessions'],
                'max_students' => rand(20, 40),
                'default_start_time' => rand(9, 11) . ':00',
                'default_end_time' => (rand(9, 11) + 2) . ':00',
                'auto_generate_sessions' => true,
                'status' => $status->value,
                'delivery_type' => $faker->randomElement(['on-site', 'online', 'hybrid']),
                'is_published' => $status !== CourseStatus::DRAFT,
                'is_hidden' => false,
                'created_at' => $startDate->copy()->subDays(rand(30, 90)),
                'updated_at' => $startDate->copy()->subDays(rand(1, 30)),
            ]);

            // تعيين 2-3 مدربين
            $assigned = collect($instructors)->random(rand(2, min(3, count($instructors))))->values();
            $instructorsData = [];
            foreach ($assigned as $index => $instructorId) {
                $instructorsData[$instructorId] = ['is_supervisor' => $index === 0];
            }
            $course->instructors()->sync($instructorsData);
            
            $courseCounter++;
            $courseCodeCounter++;
        }

        $this->command->info("Courses seeded: {$courseCounter} courses");
    }
}
