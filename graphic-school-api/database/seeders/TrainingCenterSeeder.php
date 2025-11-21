<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TrainingCenterSeeder extends Seeder
{
    public function call($class, $silent = false, array $parameters = [])
    {
        return parent::call($class, $silent, $parameters);
    }

    public function run(): void
    {
        $this->command->info('Seeding Training Center data...');

        // Seed Course Modules, Lessons, and Resources
        $this->call(\Modules\LMS\Curriculum\Database\Seeders\CourseModuleSeeder::class);

        // Seed Quizzes and Questions
        $this->call(\Modules\LMS\Assessments\Database\Seeders\QuizSeeder::class);

        // Seed Student Progress
        $this->call(\Modules\LMS\Progress\Database\Seeders\ProgressSeeder::class);

        // Seed Certificates
        $this->call(\Modules\LMS\Certificates\Database\Seeders\CertificateSeeder::class);

        $this->command->info('Training Center data seeded successfully!');
    }
}

