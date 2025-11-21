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
                'name' => 'Graphic School Admin',
                'password' => $passwordHasher->hash('password'),
                'role_id' => $adminRole?->id,
                'is_active' => true,
                'created_at' => Carbon::now()->subYears(5),
            ]
        );

        // Instructors - 25 مدرب على مدى 5 سنوات
        $instructorNames = [
            'أحمد محمد علي', 'سارة أحمد حسن', 'محمد خالد إبراهيم', 'فاطمة محمود سعيد',
            'علي حسن محمد', 'نورا أحمد فتحي', 'خالد محمود علي', 'مريم سعد الدين',
            'يوسف أحمد كمال', 'ليلى محمد رضا', 'طارق حسن علي', 'سلمى أحمد محمود',
            'حسام الدين محمد', 'رانيا خالد فتحي', 'أيمن سعد الدين', 'هند محمود علي',
            'كريم أحمد حسن', 'دينا محمد سعيد', 'عمرو خالد إبراهيم', 'شيماء أحمد فتحي',
            'مصطفى محمود علي', 'نور الدين حسن', 'ريم أحمد كمال', 'وائل محمد رضا',
            'إيمان سعد الدين',
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

        // Students - 500 طالب على مدى 5 سنوات
        // توزيع الطلاب على 5 سنوات: 50, 80, 100, 120, 150
        $studentsPerYear = [50, 80, 100, 120, 150];
        
        $studentCounter = 1;
        for ($year = 0; $year < 5; $year++) {
            $yearStart = Carbon::now()->subYears(5 - $year)->startOfYear();
            $yearEnd = Carbon::now()->subYears(5 - $year)->endOfYear();
            
            for ($i = 0; $i < $studentsPerYear[$year]; $i++) {
                $createdAt = Carbon::createFromTimestamp(
                    rand($yearStart->timestamp, $yearEnd->timestamp)
                );
                
                User::create([
                    'name' => $faker->name(),
                    'email' => "student{$studentCounter}@graphicschool.com",
                    'password' => $passwordHasher->hash('password'),
                    'role_id' => $studentRole?->id,
                    'phone' => '01' . rand(10000000, 99999999),
                    'address' => $faker->address(),
                    'is_active' => rand(1, 10) <= 8, // 80% active
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->copy()->addDays(rand(1, 365)),
                ]);
                
                $studentCounter++;
            }
        }

        $this->command->info('Users seeded: 1 Admin, 25 Instructors, 500 Students');
    }
}
