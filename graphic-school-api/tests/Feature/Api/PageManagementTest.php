<?php

namespace Tests\Feature\Api;

use App\Models\Page;
use Modules\ACL\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role
        $adminRole = \Modules\ACL\Roles\Models\Role::factory()->create(['name' => 'admin']);
        
        // Create admin user
        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
    }

    public function test_admin_can_list_pages(): void
    {
        Page::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/pages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'slug', 'title']
                ],
                'meta' => [
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total'
                    ]
                ]
            ]);
    }

    public function test_admin_can_create_page(): void
    {
        $pageData = [
            'slug' => 'test-page',
            'title' => 'Test Page',
            'content' => '<p>Test content</p>',
            'template' => 'default',
            'meta_title' => 'Test Page Title',
            'meta_description' => 'Test page description',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/pages', $pageData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'slug' => 'test-page',
                    'title' => 'Test Page',
                ]
            ]);

        $this->assertDatabaseHas('pages', [
            'slug' => 'test-page',
            'title' => 'Test Page',
        ]);
    }

    public function test_admin_can_update_page(): void
    {
        $page = Page::factory()->create([
            'slug' => 'existing-page',
            'title' => 'Existing Page',
        ]);

        $updateData = [
            'title' => 'Updated Page Title',
            'content' => '<p>Updated content</p>',
        ];

        $response = $this->actingAs($this->admin, 'api')
            ->putJson("/api/admin/pages/{$page->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => 'Updated Page Title',
                ]
            ]);

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Updated Page Title',
        ]);
    }

    public function test_admin_can_delete_page(): void
    {
        $page = Page::factory()->create();

        $response = $this->actingAs($this->admin, 'api')
            ->deleteJson("/api/admin/pages/{$page->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
        ]);
    }

    public function test_public_can_view_active_page(): void
    {
        $page = Page::factory()->create([
            'slug' => 'public-page',
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/pages/{$page->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'slug' => 'public-page',
                ]
            ]);
    }

    public function test_public_cannot_view_inactive_page(): void
    {
        $page = Page::factory()->create([
            'slug' => 'inactive-page',
            'is_active' => false,
        ]);

        $response = $this->getJson("/api/pages/{$page->slug}");

        $response->assertStatus(404);
    }

    public function test_page_search_works(): void
    {
        Page::factory()->create(['title' => 'Test Page 1']);
        Page::factory()->create(['title' => 'Another Page']);
        Page::factory()->create(['title' => 'Test Page 2']);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/admin/pages?search=Test');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(2, $data);
    }
}

