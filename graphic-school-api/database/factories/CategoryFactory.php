<?php

namespace Database\Factories;

use Modules\LMS\Categories\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'is_active' => true,
        ];
    }
}

