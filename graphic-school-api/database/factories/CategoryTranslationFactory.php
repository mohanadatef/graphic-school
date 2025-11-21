<?php

namespace Database\Factories;

use Modules\LMS\Categories\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryTranslationFactory extends Factory
{
    protected $model = CategoryTranslation::class;

    public function definition(): array
    {
        return [
            'category_id' => \Modules\LMS\Categories\Models\Category::factory(),
            'locale' => 'en',
            'name' => fake()->words(2, true),
        ];
    }
}

