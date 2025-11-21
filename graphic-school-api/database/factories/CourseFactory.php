<?php

namespace Database\Factories;

use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Courses\Enums\CourseStatus;
use Modules\LMS\Categories\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'code' => 'GS-' . strtoupper(fake()->unique()->bothify('####')),
            'slug' => fake()->unique()->slug(),
            'category_id' => Category::factory(),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(500, 5000),
            'session_count' => fake()->numberBetween(5, 20),
            'days_of_week' => ['mon', 'wed', 'fri'],
            'start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'default_start_time' => '10:00',
            'default_end_time' => '12:00',
            'delivery_type' => fake()->randomElement(['on-site', 'online', 'hybrid']),
            'status' => CourseStatus::DRAFT->value, // Use enum instead of string
            'is_published' => false,
            'is_hidden' => false,
            'auto_generate_sessions' => false,
        ];
    }
}

