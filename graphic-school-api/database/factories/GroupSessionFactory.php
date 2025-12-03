<?php

namespace Database\Factories;

use Modules\LMS\Sessions\Models\GroupSession;
use App\Models\Group;
use App\Models\SessionTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupSessionFactory extends Factory
{
    protected $model = GroupSession::class;

    public function definition(): array
    {
        $sessionDate = fake()->dateTimeBetween('now', '+3 months');
        
        return [
            'group_id' => Group::factory(),
            'session_template_id' => SessionTemplate::factory(),
            'title' => fake()->sentence(3),
            'session_order' => fake()->numberBetween(1, 20),
            'session_date' => $sessionDate,
            'start_time' => fake()->time('H:i'),
            'end_time' => fake()->time('H:i'),
            'meeting_link' => fake()->optional()->url(),
            'note' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['scheduled', 'completed', 'cancelled']),
            'student_comment' => fake()->optional()->sentence(),
            'student_file_path' => fake()->optional()->filePath(),
            'instructor_comment' => fake()->optional()->paragraph(),
            'supervisor_comment' => fake()->optional()->paragraph(),
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'session_date' => fake()->dateTimeBetween('now', '+1 month'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'session_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}

