<?php

namespace Database\Factories;

use App\Models\Conversation;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * CHANGE-005: Conversation Factory
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        $student = User::factory()->create(['role_id' => 3]);
        $instructor = User::factory()->create(['role_id' => 2]);
        $course = Course::factory()->create();
        $course->instructors()->attach($instructor->id);

        return [
            'student_id' => $student->id,
            'instructor_id' => $instructor->id,
            'course_id' => $course->id,
            'session_id' => null,
            'subject' => fake()->sentence(3),
            'last_message_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'last_message_by' => fake()->randomElement([$student->id, $instructor->id]),
            'is_archived' => false,
        ];
    }
}
