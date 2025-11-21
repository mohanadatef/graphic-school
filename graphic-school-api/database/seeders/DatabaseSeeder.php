<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            EnrollmentSeeder::class,
            SessionSeeder::class,
            SettingsSeeder::class,
            TranslationSeeder::class,
            \Modules\Core\Localization\Database\Seeders\LanguageSeeder::class,
            // Curriculum data (Modules, Lessons, Resources)
            \Modules\LMS\Curriculum\Database\Seeders\CourseModuleSeeder::class,
            // Comprehensive data (Attendance, Reviews, Quizzes, Projects, Progress, Certificates, etc.)
            ComprehensiveDataSeeder::class,
        ]);
    }
}
