<?php

namespace Database\Seeders;

use Modules\CMS\Settings\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // Language Settings
        SystemSetting::updateOrCreateSetting(
            'default_language',
            'ar',
            'string',
            'languages',
            true // Public
        );

        SystemSetting::updateOrCreateSetting(
            'available_languages',
            ['ar', 'en'],
            'array',
            'languages',
            true // Public
        );

        // Currency Settings
        SystemSetting::updateOrCreateSetting(
            'default_currency',
            'EGP',
            'string',
            'currency',
            true // Public
        );

        SystemSetting::updateOrCreateSetting(
            'currency_symbol',
            'ج.م',
            'string',
            'currency',
            true // Public
        );

        SystemSetting::updateOrCreateSetting(
            'currency_position',
            'after',
            'string',
            'currency',
            true // Public
        );
    }
}

