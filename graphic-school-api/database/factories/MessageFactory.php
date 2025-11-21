<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Conversation;
use Modules\ACL\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CHANGE-005: Message Factory
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $conversation = Conversation::factory()->create();

        return [
            'conversation_id' => $conversation->id,
            'sender_id' => fake()->randomElement([$conversation->student_id, $conversation->instructor_id]),
            'message' => fake()->paragraph(2),
            'attachments' => null,
            'read_at' => fake()->boolean(70) ? now() : null, // 70% read
        ];
    }
}
