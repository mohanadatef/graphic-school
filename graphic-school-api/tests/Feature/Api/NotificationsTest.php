<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\Core\Notification\Models\InAppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CHANGE-003: In-App Notifications Tests
 */
class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_get_notifications(): void
    {
        InAppNotification::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'type', 'title', 'message', 'read_at', 'created_at'],
                ],
            ]);
    }

    public function test_user_can_get_unread_count(): void
    {
        InAppNotification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'read_at' => null,
        ]);
        InAppNotification::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'read_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/notifications/unread-count');

        $response->assertStatus(200)
            ->assertJson(['data' => ['count' => 3]]);
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $notification = InAppNotification::factory()->create([
            'user_id' => $this->user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_as_read(): void
    {
        InAppNotification::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->putJson('/api/notifications/read-all');

        $response->assertStatus(200);
        $this->assertEquals(0, InAppNotification::where('user_id', $this->user->id)->unread()->count());
    }

    public function test_user_can_delete_notification(): void
    {
        $notification = InAppNotification::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('in_app_notifications', ['id' => $notification->id]);
    }

    public function test_user_cannot_access_other_user_notifications(): void
    {
        $otherUser = User::factory()->create();
        $notification = InAppNotification::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(404);
    }
}

