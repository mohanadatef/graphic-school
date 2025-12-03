<?php

namespace Database\Seeders;

use App\Models\Group;
use Modules\LMS\Courses\Models\Course;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create group for the demo course
     */
    public function run(): void
    {
        // Get or create demo course
        $course = Course::where('slug', 'graphic-design-fundamentals')->first();

        if (!$course) {
            $this->command->warn('⚠ Demo course not found. Creating demo course...');
            
            // Try to get any course
            $course = Course::first();
            
            if (!$course) {
                // Create minimal demo course
                $category = \Modules\LMS\Categories\Models\Category::first();
                if (!$category) {
                    $category = \Modules\LMS\Categories\Models\Category::create([
                        'slug' => 'demo',
                        'is_active' => true,
                    ]);
                }
                
                $course = Course::create([
                    'title' => 'Demo Course',
                    'slug' => 'demo-course',
                    'code' => 'DEMO-001',
                    'category_id' => $category->id,
                    'price' => 1000.00,
                    'is_published' => true,
                    'status' => 'upcoming',
                    'delivery_type' => 'on-site',
                ]);
                Log::warning('Demo course was created automatically by GroupSeeder');
            }
        }

        // Get or create instructor and student
        $instructor = User::whereHas('role', function ($q) {
            $q->where('name', 'instructor');
        })->first();

        if (!$instructor) {
            $this->command->warn('⚠ Instructor not found. Creating demo instructor...');
            $instructorRole = Role::where('name', 'instructor')->first() 
                ?? Role::create(['name' => 'instructor', 'is_system' => true, 'is_active' => true]);
            
            $instructor = User::create([
                'name' => 'Demo Instructor',
                'email' => 'demo-instructor@graphic-school.com',
                'password' => \Hash::make('password'),
                'role_id' => $instructorRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $student = User::whereHas('role', function ($q) {
            $q->where('name', 'student');
        })->first();

        if (!$student) {
            $this->command->warn('⚠ Student not found. Creating demo student...');
            $studentRole = Role::where('name', 'student')->first() 
                ?? Role::create(['name' => 'student', 'is_system' => true, 'is_active' => true]);
            
            $student = User::create([
                'name' => 'Demo Student',
                'email' => 'demo-student@graphic-school.com',
                'password' => \Hash::make('password'),
                'role_id' => $studentRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Ensure course has instructor
        if (!$course->instructors()->where('users.id', $instructor->id)->exists()) {
            $course->instructors()->attach($instructor->id, ['is_supervisor' => true]);
        }

        // Get course instructors for group
        $courseInstructors = $course->instructors()->get();
        $groupInstructorId = $courseInstructors->first()->id ?? $instructor->id;

        // Create group
        $group = Group::updateOrCreate(
            [
                'course_id' => $course->id,
                'name' => 'Group A',
            ],
            [
                'code' => 'GDF-001-A',
                'start_date' => Carbon::today(),
                'end_date' => Carbon::today()->addMonth(),
                'notes' => 'First group for Graphic Design Fundamentals course',
                'capacity' => 20,
                'room' => 'Room 101',
                'instructor_id' => $groupInstructorId,
                'is_active' => true,
            ]
        );

        // Assign instructor to group (many-to-many)
        if (!$group->instructors()->where('users.id', $groupInstructorId)->exists()) {
            $group->instructors()->attach($groupInstructorId, [
                'assigned_at' => now(),
            ]);
        }

        // Assign student to group
        if (!$group->students()->where('users.id', $student->id)->exists()) {
            $group->students()->attach($student->id, [
                'enrolled_at' => now(),
            ]);
        }

        $this->command->info('✓ Demo group seeded successfully!');
        $this->command->info("  Group: {$group->name}");
        $this->command->info("  Course: {$course->title}");
        $this->command->info("  Students: 1");
    }
}
