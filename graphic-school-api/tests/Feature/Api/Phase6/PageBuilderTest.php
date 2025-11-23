<?php

namespace Tests\Feature\Api\Phase6;

use Tests\TestCase;
use App\Models\PageBuilderPage;
use App\Models\PageBuilderStructure;
use App\Models\PageBuilderTemplate;
use App\Services\PageBuilderService;
use Modules\ACL\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'SubscriptionSeeder']);
        $this->artisan('db:seed', ['--class' => 'PageBuilderSeeder']);
    }

    public function test_page_builder_templates_are_seeded(): void
    {
        $templates = PageBuilderTemplate::all();
        $this->assertGreaterThanOrEqual(2, $templates->count());
        $this->assertDatabaseHas('page_builder_templates', ['name' => 'Landing Page']);
    }

    public function test_admin_can_create_page(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        $response = $this->actingAs($admin, 'api')
            ->postJson('/api/page-builder/pages', [
                'title' => 'Test Page',
                'slug' => 'test-page',
                'description' => 'Test description',
                'language' => 'en',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('page_builder_pages', [
            'academy_id' => $admin->id,
            'slug' => 'test-page',
            'title' => 'Test Page',
        ]);
    }

    public function test_admin_can_save_structure(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        $page = PageBuilderPage::factory()->create(['academy_id' => $admin->id]);

        $structure = [
            [
                'type' => 'hero',
                'id' => 'hero_1',
                'config' => [
                    'title' => 'Test Hero',
                    'subtitle' => 'Test Subtitle',
                ],
            ],
        ];

        $response = $this->actingAs($admin, 'api')
            ->postJson("/api/page-builder/pages/{$page->id}/structure", [
                'structure' => $structure,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('page_builder_structures', [
            'page_id' => $page->id,
        ]);
    }

    public function test_admin_can_publish_page(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        $page = PageBuilderPage::factory()->create([
            'academy_id' => $admin->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin, 'api')
            ->postJson("/api/page-builder/pages/{$page->id}/publish");

        $response->assertStatus(200);
        $this->assertDatabaseHas('page_builder_pages', [
            'id' => $page->id,
            'status' => 'published',
        ]);
    }

    public function test_page_limit_enforcement(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        // Subscribe to basic plan (3 pages limit)
        $plan = \App\Models\SubscriptionPlan::where('code', 'basic')->first();
        if (!$plan) {
            $this->markTestSkipped('No basic plan found');
            return;
        }

        $subscriptionService = app(\App\Services\SubscriptionService::class);
        $subscriptionService->subscribeAcademyToPlan($admin, $plan, 0);

        // Set usage to limit
        $tracker = \App\Models\SubscriptionUsageTracker::where('academy_id', $admin->id)
            ->where('key', 'pages')
            ->first();
        if ($tracker) {
            $tracker->update(['used' => $tracker->limit]);
        }

        // Try to create page - should fail
        $response = $this->actingAs($admin, 'api')
            ->postJson('/api/page-builder/pages', [
                'title' => 'Test Page',
                'slug' => 'test-page',
            ]);

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'Page limit exceeded for your current plan. Upgrade to create more pages.',
        ]);
    }

    public function test_admin_can_apply_template(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        $page = PageBuilderPage::factory()->create(['academy_id' => $admin->id]);
        $template = PageBuilderTemplate::first();

        if (!$template) {
            $this->markTestSkipped('No template found');
            return;
        }

        $response = $this->actingAs($admin, 'api')
            ->postJson("/api/page-builder/pages/{$page->id}/apply-template", [
                'template_id' => $template->id,
            ]);

        $response->assertStatus(200);
        $structure = PageBuilderStructure::where('page_id', $page->id)->first();
        $this->assertNotNull($structure);
        $this->assertEquals($template->structure, $structure->structure);
    }
}

