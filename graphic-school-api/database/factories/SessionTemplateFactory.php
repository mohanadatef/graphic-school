<?php

namespace Database\Factories;

use App\Models\SessionTemplate;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionTemplateFactory extends Factory
{
    protected $model = SessionTemplate::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->sentence(3),
            'session_order' => fake()->numberBetween(1, 20),
            'description' => fake()->paragraph(),
            'duration_minutes' => fake()->numberBetween(60, 180),
            'default_start_time' => fake()->time('H:i'),
            'default_end_time' => fake()->time('H:i'),
            'is_required' => true,
            'materials' => [
                'slides' => fake()->url(),
                'resources' => [fake()->url(), fake()->url()],
            ],
        ];
    }
}

