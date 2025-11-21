<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\Support\Tickets\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CHANGE-006: Ticketing System Tests
 */
class TicketsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role_id' => 1]);
    }

    public function test_admin_can_create_ticket(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/tickets', [
                'type' => 'bug',
                'title' => 'Test Bug',
                'description' => 'This is a test bug',
                'priority' => 'high',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('support_tickets', [
            'user_id' => $this->admin->id,
            'type' => 'bug',
            'title' => 'Test Bug',
        ]);
    }

    public function test_admin_can_get_tickets(): void
    {
        SupportTicket::factory()->count(5)->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/tickets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'type', 'title', 'status', 'priority'],
                ],
            ]);
    }

    public function test_admin_can_update_ticket_status(): void
    {
        $ticket = SupportTicket::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->putJson("/api/admin/tickets/{$ticket->id}", [
                'status' => 'in_progress',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('in_progress', $ticket->fresh()->status);
    }

    public function test_student_cannot_create_ticket(): void
    {
        $student = User::factory()->create(['role_id' => 3]);

        $response = $this->actingAs($student, 'api')
            ->postJson('/api/admin/tickets', [
                'type' => 'bug',
                'title' => 'Test',
                'description' => 'Test',
            ]);

        $response->assertStatus(403);
    }
}

