<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Add default website settings
     */
    public function run(): void
    {
        $defaultSettings = [
            'academy_id' => null,
            'is_activated' => false,
            'default_language' => 'en',
            'default_currency' => 'EGP',
            'default_country' => 'EG',
            'timezone' => 'Africa/Cairo',
            'branding' => [
                'logo' => null,
                'primary_color' => '#3b82f6',
                'secondary_color' => '#6366f1',
                'font_main' => 'Cairo',
                'font_headings' => 'Poppins',
            ],
            'general_info' => [
                'site_name' => 'Graphic School',
                'description' => 'Advanced Learning Management & Coaching system for training centers & academies',
            ],
            'contact_settings' => [
                'email' => 'info@graphic-school.com',
                'phone' => '+20 123 456 7890',
                'address' => 'Cairo, Egypt',
            ],
            'enabled_pages' => [
                'home' => true,
                'about' => true,
                'courses' => true,
                'instructors' => true,
                'faq' => true,
                'contact' => true,
            ],
            'email_settings' => [],
        ];

        WebsiteSetting::updateOrCreate(
            ['academy_id' => null],
            $defaultSettings
        );

        $this->command->info('✓ Website settings seeded successfully!');
    }
}

