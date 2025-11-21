<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use App\Models\Page;
use App\Models\FAQ;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CHANGE-002: CMS Tests
 */
class CmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role_id' => 1]); // Admin
    }

    public function test_admin_can_create_page(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/pages', [
                'slug' => 'test-page',
                'title' => 'Test Page',
                'content' => 'Test content',
                'is_active' => true,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('pages', ['slug' => 'test-page']);
    }

    public function test_public_can_view_active_page(): void
    {
        $page = Page::factory()->create([
            'slug' => 'about',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/pages/about');

        $response->assertStatus(200)
            ->assertJson(['data' => ['slug' => 'about']]);
    }

    public function test_admin_can_create_faq(): void
    {
        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/admin/faqs', [
                'question' => 'What is this?',
                'answer' => 'This is a test FAQ',
                'is_active' => true,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('faqs', ['question' => 'What is this?']);
    }

    public function test_public_can_view_active_faqs(): void
    {
        FAQ::factory()->count(3)->create(['is_active' => true]);
        FAQ::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/faqs');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }
}

