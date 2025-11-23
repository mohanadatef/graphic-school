<?php

namespace Database\Seeders;

use Modules\ACL\Roles\Models\Role;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Seeder;
use App\Contracts\Services\PasswordHasherInterface;
use Carbon\Carbon;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $passwordHasher = app(PasswordHasherInterface::class);
        $faker = Faker::create('ar_EG');
        
        $adminRole = Role::where('name', 'admin')->first();
        $instructorRole = Role::where('name', 'instructor')->first();
        $studentRole = Role::where('name', 'student')->first();

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@graphicschool.com'],
            [
                'name' => env('APP_NAME', 'Graphic School') . ' Admin',
                'password' => $passwordHasher->hash('password'),
                'role_id' => $adminRole?->id,
                'is_active' => true,
                'created_at' => Carbon::now()->subYears(5),
            ]
        );

        // Instructors - 5 مدربين للاختبار
        $instructorNames = [
            'أحمد محمد علي', 'سارة أحمد حسن', 'محمد خالد إبراهيم', 'فاطمة محمود سعيد',
            'علي حسن محمد',
        ];

        foreach ($instructorNames as $index => $name) {
            $createdAt = Carbon::now()->subYears(5)->addMonths($index * 2)->addDays(rand(1, 30));
            
            User::updateOrCreate(
                ['email' => "instructor" . ($index + 1) . "@graphicschool.com"],
                [
                    'name' => $name,
                    'password' => $passwordHasher->hash('password'),
                    'role_id' => $instructorRole?->id,
                    'phone' => '01' . rand(10000000, 99999999),
                    'address' => $faker->address(),
                    'bio' => $faker->sentence(10),
                    'is_active' => rand(1, 10) <= 9, // 90% active
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(rand(1, 100)),
                ]
            );
        }

        // Students - 50 طالب للاختبار
        $studentCounter = 1;
        for ($i = 0; $i < 50; $i++) {
            $createdAt = Carbon::now()->subMonths(rand(1, 12))->addDays(rand(1, 30));
            
            User::create([
                'name' => $faker->name(),
                'email' => "student{$studentCounter}@graphicschool.com",
                'password' => $passwordHasher->hash('password'),
                'role_id' => $studentRole?->id,
                'phone' => '01' . rand(10000000, 99999999),
                'address' => $faker->address(),
                'is_active' => rand(1, 10) <= 8, // 80% active
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(1, 30)),
            ]);
            
            $studentCounter++;
        }

        $this->command->info('Users seeded: 1 Admin, 5 Instructors, 50 Students');
    }
}
