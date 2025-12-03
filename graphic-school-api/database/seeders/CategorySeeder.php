<?php

namespace Database\Seeders;

use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Categories\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create example categories: Graphics, Marketing, Programming
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Graphics',
                    'ar' => 'جرافيك',
                ],
            ],
            [
                'name' => [
                    'en' => 'Marketing',
                    'ar' => 'تسويق',
                ],
            ],
            [
                'name' => [
                    'en' => 'Programming',
                    'ar' => 'برمجة',
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            // Check if category exists by checking translations
            $existingTranslation = CategoryTranslation::where('name', $categoryData['name']['en'])
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
                    'name' => $categoryData['name']['en'],
                ]);

                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => 'ar',
                    'name' => $categoryData['name']['ar'],
                ]);
            }
        }

        $this->command->info('✓ Categories seeded successfully!');
    }
}
