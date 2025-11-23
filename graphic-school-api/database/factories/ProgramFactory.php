<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'slug' => Str::slug($this->faker->words(3, true)),
            'type' => $this->faker->randomElement(['bootcamp', 'track', 'workshop', 'course']),
            'duration_weeks' => $this->faker->numberBetween(4, 16),
            'price' => $this->faker->numberBetween(1000, 10000),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}

