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
        $this->call(BrandingSeeder::class);
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            EnrollmentSeeder::class,
            SessionSeeder::class,
            SettingsSeeder::class,
            SystemSettingsSeeder::class, // Language & Currency settings
            TranslationSeeder::class,
            \Modules\Core\Localization\Database\Seeders\LanguageSeeder::class,
            // CMS Pages
            PageSeeder::class,
            // Curriculum data (Modules, Lessons, Resources)
            \Modules\LMS\Curriculum\Database\Seeders\CourseModuleSeeder::class,
            // Comprehensive data (Attendance, Reviews, Quizzes, Projects, Progress, Certificates, etc.)
            ComprehensiveDataSeeder::class,
            // Translation data (adds EN/AR translations to existing entities)
            TranslationDataSeeder::class,
            // Phase 2: Dynamic Learning Structure
            DynamicLearningSeeder::class,
            // Phase 3: Enrollment + Payments + Attendance + Certificates
            Phase3DataSeeder::class,
            // Phase 4: QR Attendance + Assignments + Calendar + Gradebook
            Phase4DataSeeder::class,
            // Phase 5.1: Gamification Core System
            GamificationSeeder::class,
            // Phase 5.2: Community System
            CommunitySeeder::class,
            // Phase 5.3: Subscriptions & Plans
            SubscriptionSeeder::class,
            // Phase 6: Advanced Page Builder
            PageBuilderSeeder::class,
        ]);
    }
}
