<?php

namespace Database\Seeders;

use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Courses\Enums\CourseStatus;
use Modules\ACL\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id')->all();
        $instructors = User::whereHas('role', fn ($q) => $q->where('name', 'instructor'))->pluck('id')->all();

        if (count($categories) === 0 || count($instructors) < 2) {
            return;
        }

        $courses = [
            ['title' => 'Professional Branding Bootcamp', 'days' => ['mon', 'wed']],
            ['title' => 'Advanced Illustration Lab', 'days' => ['tue', 'thu']],
            ['title' => 'UX Research & Prototyping', 'days' => ['sat']],
            ['title' => 'Creative Typography Workshop', 'days' => ['sun']],
            ['title' => 'Motion Design Essentials', 'days' => ['mon', 'thu']],
        ];

        foreach ($courses as $index => $courseData) {
            $startDate = Carbon::now()->addDays($index * 3)->toDateString();

            $course = Course::updateOrCreate(
                ['slug' => Str::slug($courseData['title'])],
                [
                    'title' => $courseData['title'],
                    'code' => 'GS-' . strtoupper(Str::random(5)),
                    'category_id' => $categories[array_rand($categories)],
                    'description' => 'Intensive hands-on course at Graphic School.',
                    'price' => rand(2000, 4000),
                    'start_date' => $startDate,
                    'session_count' => 8,
                    'days_of_week' => $courseData['days'],
                    'max_students' => 25,
                    'auto_generate_sessions' => true,
                    'status' => CourseStatus::UPCOMING->value,
                    'is_published' => true,
                    'is_hidden' => false,
                ]
            );

            $assigned = collect($instructors)->random(2)->values();
            $course->instructors()->sync([
                $assigned[0] => ['is_supervisor' => true],
                $assigned[1] => ['is_supervisor' => false],
            ]);
        }
    }
}
