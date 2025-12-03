<?php

namespace Modules\Core\Localization\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Localization\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
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

        foreach ($languages as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}

