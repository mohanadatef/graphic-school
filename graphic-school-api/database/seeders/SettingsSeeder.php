<?php

namespace Database\Seeders;

use Modules\CMS\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'Graphic School',
            'email' => 'hello@graphicschool.com',
            'phone' => '+20 111 222 3333',
            'address' => '12 Design Street, Cairo, Egypt',
            'about_us' => 'Graphic School is a creative academy specialized in branding, UI/UX and visual storytelling.',
            'logo' => '/assets/logo.png',
            'primary_color' => '#1d4ed8',
            'secondary_color' => '#f97316',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}

