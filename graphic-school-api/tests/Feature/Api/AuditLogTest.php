<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\Operations\Logging\Models\ActivityLog;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CHANGE-008: Audit Log Tests
 */
class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role_id' => 1]);
    }

    public function test_admin_can_get_audit_logs(): void
    {
        ActivityLog::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/audit-logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'action', 'model_type', 'user_id', 'created_at'],
                ],
            ]);
    }

    public function test_admin_can_filter_audit_logs_by_action(): void
    {
        ActivityLog::factory()->create(['action' => 'created']);
        ActivityLog::factory()->create(['action' => 'updated']);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/audit-logs?action=created');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
    }

    public function test_admin_can_get_audit_logs_for_entity(): void
    {
        $course = Course::factory()->create();
        ActivityLog::factory()->create([
            'model_type' => Course::class,
            'model_id' => $course->id,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/admin/audit-logs/entity/" . str_replace('\\', '/', Course::class) . "/{$course->id}");

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_audit_logs(): void
    {
        $student = User::factory()->create(['role_id' => 3]);

        $response = $this->actingAs($student, 'api')
            ->getJson('/api/admin/audit-logs');

        $response->assertStatus(403);
    }
}

