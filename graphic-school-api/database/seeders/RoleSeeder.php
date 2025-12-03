<?php

namespace Database\Seeders;

use Modules\ACL\Permissions\Models\Permission;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create core roles: admin, instructor, student
     */
    public function run(): void
    {
        // Create core roles (admin, instructor, student) - these are REQUIRED
        $roles = [
            ['name' => 'admin', 'description' => 'Full access to dashboard', 'is_system' => true, 'is_active' => true],
            ['name' => 'instructor', 'description' => 'Manage own courses & attendance', 'is_system' => true, 'is_active' => true],
            ['name' => 'student', 'description' => 'Access to enrolled courses', 'is_system' => true, 'is_active' => true],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $this->command->info('✓ Core roles created: admin, instructor, student');

        // Optional: Assign permissions if they exist
        try {
            $permissions = Permission::pluck('id', 'slug');
            
            if ($permissions->isNotEmpty()) {
                // Super Admin - All permissions
                $superAdmin = Role::updateOrCreate(
                    ['name' => 'super_admin'],
                    ['description' => 'Super administrator with all permissions', 'is_system' => true, 'is_active' => true]
                );
                $superAdmin->permissions()->sync($permissions->values());

                // Admin - Most permissions
                $admin = Role::where('name', 'admin')->first();
                $adminPermissions = $permissions->values();
                $admin?->permissions()->sync($adminPermissions);

                $this->command->info('✓ Permissions assigned to roles');
            }
        } catch (\Exception $e) {
            // Permissions not available yet - this is okay
            $this->command->warn('⚠ Permissions not available - skipping permission assignment');
        }
    }
}
