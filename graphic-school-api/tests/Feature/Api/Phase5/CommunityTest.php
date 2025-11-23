<?php

namespace Tests\Feature\Api\Phase5;

use Tests\TestCase;
use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityReply;
use App\Models\CommunityLike;
use App\Models\CommunityTag;
use App\Models\CommunityReport;
use Modules\ACL\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'GamificationSeeder']);
        $this->artisan('db:seed', ['--class' => 'CommunitySeeder']);
    }

    public function test_community_tags_are_seeded(): void
    {
        $tags = CommunityTag::all();
        $this->assertGreaterThan(0, $tags->count());
    }

    public function test_student_can_create_post(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found');
            return;
        }

        $response = $this->actingAs($student, 'api')
            ->postJson('/api/student/community/posts', [
                'title' => 'Test Post',
                'body' => 'This is a test post body',
                'tags' => ['test', 'question'],
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'title', 'body', 'user'],
        ]);

        $this->assertDatabaseHas('community_posts', [
            'title' => 'Test Post',
            'user_id' => $student->id,
        ]);
    }

    public function test_student_can_list_posts(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found');
            return;
        }

        $response = $this->actingAs($student, 'api')
            ->getJson('/api/student/community/posts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => ['id', 'title', 'body', 'user'],
                ],
            ],
        ]);
    }

    public function test_student_can_create_comment(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found');
            return;
        }

        $post = CommunityPost::first();
        if (!$post) {
            $this->markTestSkipped('No posts found');
            return;
        }

        $response = $this->actingAs($student, 'api')
            ->postJson('/api/student/community/comments', [
                'post_id' => $post->id,
                'body' => 'Test comment',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('community_comments', [
            'post_id' => $post->id,
            'user_id' => $student->id,
            'body' => 'Test comment',
        ]);
    }

    public function test_student_can_toggle_like(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found');
            return;
        }

        $post = CommunityPost::first();
        if (!$post) {
            $this->markTestSkipped('No posts found');
            return;
        }

        $response = $this->actingAs($student, 'api')
            ->postJson('/api/student/community/like', [
                'type' => 'post',
                'id' => $post->id,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['liked', 'likes_count'],
        ]);

        $this->assertDatabaseHas('community_likes', [
            'user_id' => $student->id,
            'likeable_type' => CommunityPost::class,
            'likeable_id' => $post->id,
        ]);
    }

    public function test_student_can_report_content(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found');
            return;
        }

        $post = CommunityPost::first();
        if (!$post) {
            $this->markTestSkipped('No posts found');
            return;
        }

        $response = $this->actingAs($student, 'api')
            ->postJson('/api/student/community/report', [
                'type' => 'post',
                'id' => $post->id,
                'reason' => 'Inappropriate content',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('community_reports', [
            'user_id' => $student->id,
            'reportable_type' => CommunityPost::class,
            'reportable_id' => $post->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_pin_post(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin users found');
            return;
        }

        $post = CommunityPost::first();
        if (!$post) {
            $this->markTestSkipped('No posts found');
            return;
        }

        $response = $this->actingAs($admin, 'api')
            ->putJson("/api/admin/community/posts/{$post->id}/pin", ['pin' => true]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('community_posts', [
            'id' => $post->id,
            'is_pinned' => true,
        ]);
    }

    public function test_admin_can_list_reports(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin users found');
            return;
        }

        $response = $this->actingAs($admin, 'api')
            ->getJson('/api/admin/community/reports');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => ['id', 'user', 'reason', 'status'],
                ],
            ],
        ]);
    }
}

