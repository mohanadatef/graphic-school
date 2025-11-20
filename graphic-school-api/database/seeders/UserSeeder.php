<?php

namespace Database\Seeders;

use Modules\ACL\Roles\Models\Role;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Seeder;
use App\Contracts\Services\PasswordHasherInterface;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $passwordHasher = app(PasswordHasherInterface::class);
        
        $adminRole = Role::where('name', 'admin')->first();
        $instructorRole = Role::where('name', 'instructor')->first();
        $studentRole = Role::where('name', 'student')->first();

        User::updateOrCreate(
            ['email' => 'admin@graphicschool.com'],
            [
                'name' => 'Graphic School Admin',
                'password' => $passwordHasher->hash('password'),
                'role_id' => $adminRole?->id,
                'is_active' => true,
            ]
        );

        for ($i = 1; $i <= 3; $i++) {
            User::updateOrCreate(
                ['email' => "instructor{$i}@graphicschool.com"],
                [
                    'name' => "Instructor {$i}",
                    'password' => $passwordHasher->hash('password'),
                    'role_id' => $instructorRole?->id,
                    'bio' => 'Senior graphic design instructor',
                    'is_active' => true,
                ]
            );
        }

        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "student{$i}@graphicschool.com"],
                [
                    'name' => "Student {$i}",
                    'password' => $passwordHasher->hash('password'),
                    'role_id' => $studentRole?->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
