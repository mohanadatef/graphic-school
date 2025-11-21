<?php

namespace Modules\Support\Tickets\Database\Factories;

use Modules\Support\Tickets\Models\SupportTicket;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CHANGE-006: Support Ticket Factory
 */
class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['bug', 'change_request', 'new_feature']),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'assigned_to' => null,
            'attachments' => null,
            'updates' => null,
        ];
    }
}
