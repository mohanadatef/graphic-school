<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Dashboard Access', 'slug' => 'dashboard.access', 'module' => 'dashboard'],
            ['name' => 'Dashboard Reports', 'slug' => 'dashboard.reports', 'module' => 'dashboard'],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'module' => 'users'],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'module' => 'roles'],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'module' => 'roles'],
            ['name' => 'Manage Categories', 'slug' => 'categories.manage', 'module' => 'catalog'],
            ['name' => 'Manage Courses', 'slug' => 'courses.manage', 'module' => 'catalog'],
            ['name' => 'View Courses', 'slug' => 'courses.view', 'module' => 'catalog'],
            ['name' => 'Manage Sessions', 'slug' => 'sessions.manage', 'module' => 'sessions'],
            ['name' => 'View Sessions', 'slug' => 'sessions.view', 'module' => 'sessions'],
            ['name' => 'Manage Enrollments', 'slug' => 'enrollments.manage', 'module' => 'enrollments'],
            ['name' => 'Take Attendance', 'slug' => 'attendance.take', 'module' => 'attendance'],
            ['name' => 'View Attendance', 'slug' => 'attendance.view', 'module' => 'attendance'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'settings'],
            ['name' => 'Manage Contacts', 'slug' => 'contacts.manage', 'module' => 'contacts'],
            ['name' => 'Manage Sliders', 'slug' => 'sliders.manage', 'module' => 'website'],
            ['name' => 'Manage Testimonials', 'slug' => 'testimonials.manage', 'module' => 'website'],
            ['name' => 'Instructor Access', 'slug' => 'instructor.access', 'module' => 'instructor'],
            ['name' => 'Student Access', 'slug' => 'student.access', 'module' => 'student'],
            ['name' => 'Manage Notes', 'slug' => 'notes.manage', 'module' => 'sessions'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
