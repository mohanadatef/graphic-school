<?php

namespace Database\Seeders;

use Modules\Core\Localization\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Add English (default) and Arabic (RTL=true)
     */
    public function run(): void
    {
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'image_path' => null,
                'is_active' => true,
                'is_default' => true,
                'is_rtl' => false,
                'sort_order' => 1,
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'image_path' => null,
                'is_active' => true,
                'is_default' => false,
                'is_rtl' => true,
                'sort_order' => 2,
            ],
        ];

        // If there's already a default language, unset it before setting new default
        foreach ($languages as $languageData) {
            if ($languageData['is_default']) {
                Language::where('is_default', true)->update(['is_default' => false]);
            }
        }

        foreach ($languages as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }

        $this->command->info('✓ Languages seeded successfully!');
    }
}

