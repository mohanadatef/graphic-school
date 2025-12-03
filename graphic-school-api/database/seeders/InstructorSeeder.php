<?php

namespace Database\Seeders;

use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create 1 instructor user (role=instructor)
     */
    public function run(): void
    {
        // Get or create instructor role
        $instructorRole = Role::where('name', 'instructor')->first();
        
        if (!$instructorRole) {
            $this->command->warn('⚠ Instructor role not found. Creating it now...');
            $instructorRole = Role::create([
                'name' => 'instructor',
                'description' => 'Manage own courses & attendance',
                'is_system' => true,
                'is_active' => true,
            ]);
            Log::warning('Instructor role was missing and created automatically by InstructorSeeder');
        }

        $instructor = User::updateOrCreate(
            ['email' => 'instructor@graphic-school.com'],
            [
                'name' => 'Ahmed Mohamed',
                'password' => Hash::make('password'),
                'role_id' => $instructorRole->id,
                'phone' => '+20 123 456 7891',
                'bio' => 'Experienced graphic design instructor with over 10 years of experience in the industry.',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✓ Instructor seeded successfully!');
        $this->command->info("  Email: instructor@graphic-school.com");
        $this->command->info("  Password: password");
    }
}
