<?php

namespace Database\Seeders;

use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Categories\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Branding & Identity',
            'Illustration',
            'UI / UX',
            'Typography',
            'Web Design',
            'Motion Graphics',
            'Packaging Design',
        ];

        foreach ($categories as $name) {
            // Check if category exists by checking translations
            $existingTranslation = CategoryTranslation::where('name', $name)
                ->where('locale', 'en')
                ->first();
            
            if ($existingTranslation) {
                $category = $existingTranslation->category;
            } else {
                // Create new category
                $category = Category::create(['is_active' => true]);
                
                // Create translations for both languages
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => 'en',
                    'name' => $name,
                ]);
                
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => 'ar',
                    'name' => $name, // You can add Arabic translations here
                ]);
            }
        }
    }
}
