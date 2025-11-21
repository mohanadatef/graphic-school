<?php

namespace Database\Factories;

use Modules\LMS\Categories\Models\Category;
use Modules\LMS\Categories\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'is_active' => true,
        ];
    }

    /**
     * Configure the factory to create translations
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Category $category) {
            // Create English translation
            \Modules\LMS\Categories\Models\CategoryTranslation::factory()->create([
                'category_id' => $category->id,
                'locale' => 'en',
                'name' => fake()->words(2, true),
            ]);

            // Create Arabic translation
            \Modules\LMS\Categories\Models\CategoryTranslation::factory()->create([
                'category_id' => $category->id,
                'locale' => 'ar',
                'name' => fake('ar_SA')->words(2, true),
            ]);
        });
    }
}

