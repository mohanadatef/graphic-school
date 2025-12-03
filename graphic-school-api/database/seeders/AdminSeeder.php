<?php

namespace Database\Seeders;

use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Database\Seeder;
use App\Contracts\Services\PasswordHasherInterface;
use Illuminate\Support\Facades\Log;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create admin user (role=admin)
     */
    public function run(): void
    {
        // Get or create admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->command->warn('⚠ Admin role not found. Please run RoleSeeder first!');
            Log::error('Admin role was missing when AdminSeeder ran');
            return;
        }

        $passwordHasher = app(PasswordHasherInterface::class);
        
        $admin = User::updateOrCreate(
            ['email' => 'admin@graphic-school.com'],
            [
                'name' => env('APP_NAME', 'Graphic School') . ' Admin',
                'password' => $passwordHasher->hash('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✓ Admin user seeded successfully!');
        $this->command->info("  Email: admin@graphic-school.com");
        $this->command->info("  Password: password");
    }
}

