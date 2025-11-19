<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Full access to dashboard', 'is_system' => true],
            ['name' => 'instructor', 'description' => 'Manage own courses & attendance', 'is_system' => true],
            ['name' => 'student', 'description' => 'Access to enrolled courses', 'is_system' => true],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $permissions = Permission::pluck('id', 'slug');

        $admin = Role::where('name', 'admin')->first();
        $admin?->permissions()->sync($permissions->values());

        $instructorPermissions = $permissions->only([
            'courses.view',
            'sessions.view',
            'sessions.manage',
            'attendance.take',
            'attendance.view',
            'notes.manage',
        ])->values();

        Role::where('name', 'instructor')->first()
            ?->permissions()
            ->sync($instructorPermissions);

        $studentPermissions = $permissions->only([
            'student.access',
        ])->values();

        Role::where('name', 'student')->first()
            ?->permissions()
            ->sync($studentPermissions);
    }
}
