<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Courses\Models\Course;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CHANGE-005: Messaging System Tests
 */
class MessagingTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $instructor;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->create(['role_id' => 3]); // Student
        $this->instructor = User::factory()->create(['role_id' => 2]); // Instructor
        $this->course = Course::factory()->create();
        $this->course->instructors()->attach($this->instructor->id);
    }

    public function test_student_can_get_conversations(): void
    {
        Conversation::factory()->create([
            'student_id' => $this->student->id,
            'instructor_id' => $this->instructor->id,
            'course_id' => $this->course->id,
        ]);

        $response = $this->actingAs($this->student, 'api')
            ->getJson('/api/messaging/conversations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'student_id', 'instructor_id', 'course_id'],
                ],
            ]);
    }

    public function test_student_can_create_conversation(): void
    {
        $response = $this->actingAs($this->student, 'api')
            ->postJson('/api/messaging/conversations', [
                'course_id' => $this->course->id,
                'instructor_id' => $this->instructor->id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('conversations', [
            'student_id' => $this->student->id,
            'instructor_id' => $this->instructor->id,
            'course_id' => $this->course->id,
        ]);
    }

    public function test_student_can_send_message(): void
    {
        $conversation = Conversation::factory()->create([
            'student_id' => $this->student->id,
            'instructor_id' => $this->instructor->id,
            'course_id' => $this->course->id,
        ]);

        $response = $this->actingAs($this->student, 'api')
            ->postJson('/api/messaging/messages', [
                'conversation_id' => $conversation->id,
                'message' => 'Hello instructor!',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->student->id,
            'message' => 'Hello instructor!',
        ]);
    }

    public function test_instructor_can_get_conversations(): void
    {
        Conversation::factory()->create([
            'student_id' => $this->student->id,
            'instructor_id' => $this->instructor->id,
            'course_id' => $this->course->id,
        ]);

        $response = $this->actingAs($this->instructor, 'api')
            ->getJson('/api/messaging/conversations');

        $response->assertStatus(200);
    }

    public function test_student_cannot_message_non_assigned_instructor(): void
    {
        $otherInstructor = User::factory()->create(['role_id' => 2]);

        $response = $this->actingAs($this->student, 'api')
            ->postJson('/api/messaging/conversations', [
                'course_id' => $this->course->id,
                'instructor_id' => $otherInstructor->id,
            ]);

        $response->assertStatus(403);
    }
}

