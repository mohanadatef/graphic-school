<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Modules\LMS\Courses\Models\Course;
use Modules\LMS\Categories\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CoursesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $this->user = User::factory()->create(['role_id' => $adminRole->id]);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_admin_can_list_courses(): void
    {
        Course::factory()->count(5)->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/admin/courses');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'code'],
                ],
                'meta' => ['pagination'],
            ]);
    }

    public function test_admin_can_create_course(): void
    {
        $category = Category::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/admin/courses', [
                'title' => 'Test Course',
                'code' => 'TC001',
                'category_id' => $category->id,
                'description' => 'Test Description',
                'price' => 1000,
                'session_count' => 10,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'code'],
            ]);
    }

    public function test_admin_can_update_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson("/api/admin/courses/{$course->id}", [
                'title' => 'Updated Course',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Course',
        ]);
    }

    public function test_admin_can_delete_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson("/api/admin/courses/{$course->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}

