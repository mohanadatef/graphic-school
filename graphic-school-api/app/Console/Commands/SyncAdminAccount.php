<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;

class SyncAdminAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-admin-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Super Admin account with standardized credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing Super Admin account...');
        $this->newLine();

        try {
            // Find super_admin role
            $superAdminRole = Role::where('name', 'super_admin')->first();
            
            if (!$superAdminRole) {
                // Try to find admin role as fallback
                $superAdminRole = Role::where('name', 'admin')->first();
            }

            if (!$superAdminRole) {
                $this->error('No admin or super_admin role found. Please run migrations and seeders first.');
                $this->info('Run: php artisan migrate && php artisan db:seed --class=RoleSeeder');
                return 1;
            }

            $roleId = $superAdminRole->id;
            $this->info("Using role: {$superAdminRole->name} (ID: {$roleId})");

            // Check if a user with this role_id exists
            $existingUser = User::where('role_id', $roleId)->first();

            $credentials = [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role_id' => $roleId,
                'is_active' => true,
            ];

            if ($existingUser) {
                // Update existing user
                $this->info("Found existing user with role_id {$roleId}. Updating...");
                
                $existingUser->update([
                    'name' => $credentials['name'],
                    'email' => $credentials['email'],
                    'password' => $credentials['password'],
                    'is_active' => true,
                ]);

                $this->info("✓ User updated successfully");
                $this->newLine();
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID', $existingUser->id],
                        ['Name', $existingUser->name],
                        ['Email', $existingUser->email],
                        ['Password', 'admin123'],
                        ['Role', $superAdminRole->name],
                        ['Status', $existingUser->is_active ? 'Active' : 'Inactive'],
                    ]
                );
            } else {
                // Create new user
                $this->info("No user with role_id {$roleId} found. Creating new Super Admin...");
                
                $newUser = User::create($credentials);

                $this->info("✓ User created successfully");
                $this->newLine();
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['ID', $newUser->id],
                        ['Name', $newUser->name],
                        ['Email', $newUser->email],
                        ['Password', 'admin123'],
                        ['Role', $superAdminRole->name],
                        ['Status', $newUser->is_active ? 'Active' : 'Inactive'],
                    ]
                );
            }

            $this->newLine();
            $this->info('Super Admin account synced successfully!');
            $this->info('Credentials:');
            $this->line('  Email: admin@example.com');
            $this->line('  Password: admin123');
            $this->newLine();

            return 0;

        } catch (\Exception $e) {
            $this->error('Error syncing Super Admin account: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}

