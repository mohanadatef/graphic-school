<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Seeders run in this EXACT order to respect dependencies
     */
    public function run(): void
    {
        $this->call([
            // Step 1: Core roles (MUST run first)
            RoleSeeder::class,
            
            // Step 2: Localization & Settings
            LanguageSeeder::class,
            CurrencySeeder::class,
            CountrySeeder::class,
            WebsiteSettingSeeder::class,
            
            // Step 3: CMS Pages
            PagesSeeder::class,
            
            // Step 4: Categories
            CategorySeeder::class,
            
            // Step 5: Users (admin, instructor & student)
            AdminSeeder::class,
            InstructorSeeder::class,
            StudentSeeder::class,
            
            // Step 6: Courses (depends on categories & instructors)
            CourseSeeder::class,
            
            // Step 7: Groups (depends on courses)
            GroupSeeder::class,
            
            // Step 8: Sessions (depends on groups)
            SessionSeeder::class,
        ]);

        $this->command->info('✔ All seeders completed successfully!');
    }
}
