<?php

namespace Modules\Core\Notification\Database\Factories;

use Modules\Core\Notification\Models\InAppNotification;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CHANGE-003: In-App Notification Factory
 */
class InAppNotificationFactory extends Factory
{
    protected $model = InAppNotification::class;

    public function definition(): array
    {
        $types = ['enrollment_created', 'enrollment_approved', 'payment_updated', 'quiz_created', 'message_received'];
        $categories = ['info', 'success', 'warning', 'error'];

        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement($types),
            'category' => fake()->randomElement($categories),
            'title' => fake()->sentence(3),
            'message' => fake()->paragraph(1),
            'data' => null,
            'read_at' => fake()->boolean(30) ? now() : null, // 30% read
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }
}
