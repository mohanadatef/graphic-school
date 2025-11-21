<?php

namespace Database\Factories;

use App\Models\FAQ;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CHANGE-002: FAQ Factory
 */
class FAQFactory extends Factory
{
    protected $model = FAQ::class;

    public function definition(): array
    {
        return [
            'question' => fake()->sentence(5) . '?',
            'answer' => fake()->paragraphs(2, true),
            'category' => fake()->randomElement(['general', 'courses', 'payments', 'enrollment', 'technical']),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
