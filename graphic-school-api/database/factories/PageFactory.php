<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * CHANGE-002: Page Factory
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = fake()->sentence(2);
        
        return [
            'slug' => Str::slug($title),
            'title' => $title,
            'content' => fake()->paragraphs(3, true),
            'template' => fake()->randomElement(['default', 'home', 'about', 'contact']),
            'sections' => null,
            'meta_title' => $title,
            'meta_description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
