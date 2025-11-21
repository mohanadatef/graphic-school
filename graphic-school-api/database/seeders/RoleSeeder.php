<?php

namespace Database\Seeders;

use Modules\ACL\Permissions\Models\Permission;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Full access to dashboard', 'is_system' => true],
            ['name' => 'instructor', 'description' => 'Manage own courses & attendance', 'is_system' => true],
            ['name' => 'student', 'description' => 'Access to enrolled courses', 'is_system' => true],
            ['name' => 'super_admin', 'description' => 'Super administrator with all permissions', 'is_system' => true],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $permissions = Permission::pluck('id', 'slug');

        // Super Admin - All permissions
        $superAdmin = Role::where('name', 'super_admin')->first();
        $superAdmin?->permissions()->sync($permissions->values());

        // Admin - Most permissions except super admin only
        $admin = Role::where('name', 'admin')->first();
        $adminPermissions = $permissions->except([
            // Super admin only permissions (if any)
        ])->values();
        $admin?->permissions()->sync($adminPermissions);

        // Instructor - Limited permissions
        $instructorPermissions = $permissions->only([
            'dashboard.access',
            'courses.view',
            'sessions.view',
            'sessions.manage',
            'enrollments.view',
            'attendance.view',
            'attendance.take',
            'attendance.manage',
            'quizzes.view',
            'quizzes.manage',
            'projects.view',
            'projects.manage',
            'progress.view',
            'reviews.view',
            'messaging.view',
            'messaging.manage',
            'notifications.view',
            'instructor.access',
            'notes.manage',
        ])->values();

        Role::where('name', 'instructor')->first()
            ?->permissions()
            ->sync($instructorPermissions);

        // Student - Basic permissions
        $studentPermissions = $permissions->only([
            'courses.view',
            'sessions.view',
            'enrollments.view',
            'attendance.view',
            'quizzes.view',
            'projects.view',
            'progress.view',
            'certificates.view',
            'reviews.view',
            'messaging.view',
            'messaging.manage',
            'notifications.view',
            'payments.view',
            'student.access',
        ])->values();

        Role::where('name', 'student')->first()
            ?->permissions()
            ->sync($studentPermissions);
            
        $this->command->info('Roles and permissions assigned successfully!');
    }
}
