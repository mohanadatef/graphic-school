<?php

namespace Database\Seeders;

use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Courses\Models\Course;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create one example course: "Graphic Design Fundamentals"
     */
    public function run(): void
    {
        // Get or create Graphics category
        $graphicsCategory = Category::whereHas('translations', function ($q) {
            $q->where('name', 'Graphics')->where('locale', 'en');
        })->first();

        if (!$graphicsCategory) {
            $this->command->warn('⚠ Graphics category not found. Using first available category or creating demo category...');
            $graphicsCategory = Category::first();
            if (!$graphicsCategory) {
                // Create a demo category
                $graphicsCategory = Category::create([
                    'slug' => 'graphics',
                    'is_active' => true,
                ]);
                // Create translation
                if (method_exists($graphicsCategory, 'translations')) {
                    $graphicsCategory->translations()->create([
                        'name' => 'Graphics',
                        'description' => 'Graphic Design Courses',
                        'locale' => 'en',
                    ]);
                }
            }
        }

        // Get or create instructor
        $instructor = User::whereHas('role', function ($q) {
            $q->where('name', 'instructor');
        })->first();

        if (!$instructor) {
            $this->command->warn('⚠ Instructor not found. Creating demo instructor...');
            $instructorRole = Role::where('name', 'instructor')->first();
            if (!$instructorRole) {
                $instructorRole = Role::create([
                    'name' => 'instructor',
                    'description' => 'Instructor role',
                    'is_system' => true,
                    'is_active' => true,
                ]);
            }
            
            $instructor = User::create([
                'name' => 'Demo Instructor',
                'email' => 'demo-instructor@graphic-school.com',
                'password' => \Hash::make('password'),
                'role_id' => $instructorRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            Log::warning('Demo instructor was created automatically by CourseSeeder');
        }

        // Create course
        $course = Course::updateOrCreate(
            ['slug' => 'graphic-design-fundamentals'],
            [
                'title' => 'Graphic Design Fundamentals',
                'code' => 'GDF-001',
                'category_id' => $graphicsCategory->id,
                'description' => 'Learn the fundamentals of graphic design including typography, color theory, layout, and composition. This comprehensive course will equip you with the essential skills needed to create professional designs.',
                'image_path' => null,
                'price' => 1500.00,
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addMonths(3),
                'default_start_time' => '10:00',
                'default_end_time' => '13:00',
                'session_count' => 12,
                'days_of_week' => ['monday', 'wednesday'],
                'duration_weeks' => 6,
                'max_students' => 20,
                'auto_generate_sessions' => true,
                'is_published' => true,
                'is_hidden' => false,
                'status' => 'upcoming',
                'delivery_type' => 'on-site',
            ]
        );

        // Assign instructor to course
        if (!$course->instructors()->where('users.id', $instructor->id)->exists()) {
            $course->instructors()->attach($instructor->id, [
                'is_supervisor' => true,
            ]);
        }

        $this->command->info('✓ Demo course seeded successfully!');
        $this->command->info("  Course: {$course->title}");
        $this->command->info("  Price: {$course->price} EGP");
        $this->command->info("  Instructor: {$instructor->name}");
    }
}
