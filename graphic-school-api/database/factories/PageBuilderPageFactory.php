<?php

namespace Database\Factories;

use App\Models\PageBuilderPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageBuilderPageFactory extends Factory
{
    protected $model = PageBuilderPage::class;

    public function definition(): array
    {
        return [
            'academy_id' => \Modules\ACL\Users\Models\User::factory(),
            'slug' => $this->faker->slug(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'language' => $this->faker->randomElement(['en', 'ar']),
            'status' => $this->faker->randomElement(['published', 'draft']),
        ];
    }
}

