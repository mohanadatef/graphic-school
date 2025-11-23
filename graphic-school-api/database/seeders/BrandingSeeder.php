<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultBranding = [
            [
                'key' => 'branding.name.display',
                'value' => 'Graphic School',
                'type' => 'string',
            ],
            [
                'key' => 'branding.logo.default',
                'value' => null,
                'type' => 'image',
            ],
            [
                'key' => 'branding.logo.dark',
                'value' => null,
                'type' => 'image',
            ],
            [
                'key' => 'branding.logo.favicon',
                'value' => null,
                'type' => 'image',
            ],
            [
                'key' => 'branding.colors.primary',
                'value' => '#3b82f6',
                'type' => 'color',
            ],
            [
                'key' => 'branding.colors.secondary',
                'value' => '#0ea5e9',
                'type' => 'color',
            ],
            [
                'key' => 'branding.colors.background',
                'value' => '#ffffff',
                'type' => 'color',
            ],
            [
                'key' => 'branding.colors.text',
                'value' => '#111111',
                'type' => 'color',
            ],
            [
                'key' => 'branding.fonts.source',
                'value' => 'system',
                'type' => 'string',
            ],
            [
                'key' => 'branding.fonts.main',
                'value' => 'Cairo',
                'type' => 'font',
            ],
            [
                'key' => 'branding.fonts.headings',
                'value' => 'Poppins',
                'type' => 'font',
            ],
            [
                'key' => 'branding.fonts.custom_file',
                'value' => null,
                'type' => 'string',
            ],
            [
                'key' => 'branding.fonts.available_fonts',
                'value' => json_encode([
                    ['id' => 'inter', 'label' => 'Inter', 'family' => 'Inter', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'roboto', 'label' => 'Roboto', 'family' => 'Roboto', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'poppins', 'label' => 'Poppins', 'family' => 'Poppins', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'cairo', 'label' => 'Cairo', 'family' => 'Cairo', 'category' => 'sans-serif', 'supports_arabic' => true],
                    ['id' => 'tajawal', 'label' => 'Tajawal', 'family' => 'Tajawal', 'category' => 'sans-serif', 'supports_arabic' => true],
                    ['id' => 'ibm-plex-sans-arabic', 'label' => 'IBM Plex Sans Arabic', 'family' => 'IBM Plex Sans Arabic', 'category' => 'sans-serif', 'supports_arabic' => true],
                    ['id' => 'noto-sans-arabic', 'label' => 'Noto Sans Arabic', 'family' => 'Noto Sans Arabic', 'category' => 'sans-serif', 'supports_arabic' => true],
                    ['id' => 'almarai', 'label' => 'Almarai', 'family' => 'Almarai', 'category' => 'sans-serif', 'supports_arabic' => true],
                    ['id' => 'open-sans', 'label' => 'Open Sans', 'family' => 'Open Sans', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'lato', 'label' => 'Lato', 'family' => 'Lato', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'montserrat', 'label' => 'Montserrat', 'family' => 'Montserrat', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'source-sans-pro', 'label' => 'Source Sans Pro', 'family' => 'Source Sans Pro', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'fira-sans', 'label' => 'Fira Sans', 'family' => 'Fira Sans', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'space-grotesk', 'label' => 'Space Grotesk', 'family' => 'Space Grotesk', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'tahoma', 'label' => 'Tahoma', 'family' => 'Tahoma', 'category' => 'sans-serif', 'supports_arabic' => true],
                    ['id' => 'verdana', 'label' => 'Verdana', 'family' => 'Verdana', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'nunito', 'label' => 'Nunito', 'family' => 'Nunito', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'work-sans', 'label' => 'Work Sans', 'family' => 'Work Sans', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'plus-jakarta-sans', 'label' => 'Plus Jakarta Sans', 'family' => 'Plus Jakarta Sans', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'rubik', 'label' => 'Rubik', 'family' => 'Rubik', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'raleway', 'label' => 'Raleway', 'family' => 'Raleway', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'ubuntu', 'label' => 'Ubuntu', 'family' => 'Ubuntu', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'playfair-display', 'label' => 'Playfair Display', 'family' => 'Playfair Display', 'category' => 'serif', 'supports_arabic' => false],
                    ['id' => 'merriweather', 'label' => 'Merriweather', 'family' => 'Merriweather', 'category' => 'serif', 'supports_arabic' => false],
                    ['id' => 'dm-sans', 'label' => 'DM Sans', 'family' => 'DM Sans', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'manrope', 'label' => 'Manrope', 'family' => 'Manrope', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'outfit', 'label' => 'Outfit', 'family' => 'Outfit', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'chivo', 'label' => 'Chivo', 'family' => 'Chivo', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'sora', 'label' => 'Sora', 'family' => 'Sora', 'category' => 'sans-serif', 'supports_arabic' => false],
                    ['id' => 'vazirmatn', 'label' => 'Vazirmatn', 'family' => 'Vazirmatn', 'category' => 'sans-serif', 'supports_arabic' => true],
                ]),
                'type' => 'json',
            ],
            [
                'key' => 'branding.layout.radius',
                'value' => '0.5rem',
                'type' => 'string',
            ],
            [
                'key' => 'branding.layout.shadow',
                'value' => '0',
                'type' => 'string',
            ],
        ];

        foreach ($defaultBranding as $setting) {
            DB::table('branding_settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

