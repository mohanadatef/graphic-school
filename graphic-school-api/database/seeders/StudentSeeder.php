<?php

namespace Database\Seeders;

use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create 1 student user (role=student)
     */
    public function run(): void
    {
        // Get or create student role
        $studentRole = Role::where('name', 'student')->first();
        
        if (!$studentRole) {
            $this->command->warn('⚠ Student role not found. Creating it now...');
            $studentRole = Role::create([
                'name' => 'student',
                'description' => 'Access to enrolled courses',
                'is_system' => true,
                'is_active' => true,
            ]);
            Log::warning('Student role was missing and created automatically by StudentSeeder');
        }

        $student = User::updateOrCreate(
            ['email' => 'student@graphic-school.com'],
            [
                'name' => 'Mohamed Ali',
                'password' => Hash::make('password'),
                'role_id' => $studentRole->id,
                'phone' => '+20 123 456 7892',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✓ Student seeded successfully!');
        $this->command->info("  Email: student@graphic-school.com");
        $this->command->info("  Password: password");
    }
}
