<?php

namespace Database\Seeders;

use Modules\LMS\Sessions\Models\GroupSession;
use App\Models\Group;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create 3 demo sessions attached to the group
     */
    public function run(): void
    {
        // Get or create demo group
        $group = Group::where('name', 'Group A')->first();

        if (!$group) {
            $this->command->warn('⚠ Demo group not found. Creating demo group...');
            
            // Try to get any group
            $group = Group::first();
            
            if (!$group) {
                // Create minimal demo group
                $course = \Modules\LMS\Courses\Models\Course::first();
                if (!$course) {
                    $category = \Modules\LMS\Categories\Models\Category::first() 
                        ?? \Modules\LMS\Categories\Models\Category::create(['slug' => 'demo', 'is_active' => true]);
                    
                    $course = \Modules\LMS\Courses\Models\Course::create([
                        'title' => 'Demo Course',
                        'slug' => 'demo-course',
                        'code' => 'DEMO-001',
                        'category_id' => $category->id,
                        'price' => 1000.00,
                        'is_published' => true,
                        'status' => 'upcoming',
                    ]);
                }
                
                $instructor = User::whereHas('role', function ($q) {
                    $q->where('name', 'instructor');
                })->first();
                
                if (!$instructor) {
                    $instructorRole = Role::where('name', 'instructor')->first() 
                        ?? Role::create(['name' => 'instructor', 'is_system' => true, 'is_active' => true]);
                    
                    $instructor = User::create([
                        'name' => 'Demo Instructor',
                        'email' => 'demo-instructor@graphic-school.com',
                        'password' => \Hash::make('password'),
                        'role_id' => $instructorRole->id,
                        'is_active' => true,
                    ]);
                }
                
                $group = Group::create([
                    'course_id' => $course->id,
                    'name' => 'Group A',
                    'code' => 'DEMO-A',
                    'start_date' => Carbon::today(),
                    'end_date' => Carbon::today()->addMonth(),
                    'instructor_id' => $instructor->id,
                    'is_active' => true,
                ]);
                Log::warning('Demo group was created automatically by SessionSeeder');
            }
        }

        // Get instructor from group
        $instructor = $group->instructors()->first();
        
        if (!$instructor) {
            $instructor = $group->instructor;
        }

        if (!$instructor) {
            $this->command->warn('⚠ Group has no instructor. Creating demo instructor...');
            $instructorRole = Role::where('name', 'instructor')->first() 
                ?? Role::create(['name' => 'instructor', 'is_system' => true, 'is_active' => true]);
            
            $instructor = User::create([
                'name' => 'Demo Instructor',
                'email' => 'demo-instructor@graphic-school.com',
                'password' => \Hash::make('password'),
                'role_id' => $instructorRole->id,
                'is_active' => true,
            ]);
            
            $group->instructor_id = $instructor->id;
            $group->save();
        }

        // Create 3 sessions
        $sessions = [
            [
                'title' => 'Session 1: Introduction to Graphic Design',
                'session_order' => 1,
                'session_date' => Carbon::today()->addDays(7),
                'start_time' => '10:00',
                'end_time' => '13:00',
                'note' => 'Introduction to the course and basic concepts',
                'status' => 'scheduled',
            ],
            [
                'title' => 'Session 2: Typography Fundamentals',
                'session_order' => 2,
                'session_date' => Carbon::today()->addDays(9),
                'start_time' => '10:00',
                'end_time' => '13:00',
                'note' => 'Understanding fonts, typefaces, and typography principles',
                'status' => 'scheduled',
            ],
            [
                'title' => 'Session 3: Color Theory',
                'session_order' => 3,
                'session_date' => Carbon::today()->addDays(14),
                'start_time' => '10:00',
                'end_time' => '13:00',
                'note' => 'Color psychology and color schemes',
                'status' => 'scheduled',
            ],
        ];

        foreach ($sessions as $sessionData) {
            GroupSession::updateOrCreate(
                [
                    'group_id' => $group->id,
                    'session_order' => $sessionData['session_order'],
                ],
                [
                    'title' => $sessionData['title'],
                    'session_date' => $sessionData['session_date'],
                    'start_time' => $sessionData['start_time'],
                    'end_time' => $sessionData['end_time'],
                    'note' => $sessionData['note'],
                    'instructor_id' => $instructor ? $instructor->id : null,
                    'status' => $sessionData['status'],
                ]
            );
        }

        $this->command->info('✓ Demo sessions seeded successfully!');
        $this->command->info("  Group: {$group->name}");
        $this->command->info("  Sessions created: 3");
    }
}
