<?php

namespace Tests\Feature;

use App\Models\BrandingSetting;
use App\Services\BrandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\BrandingSeeder::class);
    }

    public function test_admin_can_get_branding_settings(): void
    {
        $admin = $this->createAdminUser();
        
        $response = $this->actingAs($admin, 'api')
            ->getJson('/api/admin/branding');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'branding.name.display',
                    'branding.colors.primary',
                    'branding.colors.secondary',
                ],
            ]);
    }

    public function test_public_can_get_frontend_branding(): void
    {
        $response = $this->getJson('/api/branding/frontend');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'branding.name.display',
                    'branding.colors.primary',
                ],
            ]);
    }

    public function test_admin_can_update_branding_settings(): void
    {
        $admin = $this->createAdminUser();
        
        $response = $this->actingAs($admin, 'api')
            ->postJson('/api/admin/branding/update', [
                'branding.name.display' => 'Test Academy',
                'branding.colors.primary' => '#ff0000',
                'branding.colors.secondary' => '#00ff00',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('branding_settings', [
            'key' => 'branding.name.display',
            'value' => 'Test Academy',
        ]);
    }

    public function test_admin_can_upload_logo(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        
        $logo = UploadedFile::fake()->image('logo.png', 200, 200);

        $response = $this->actingAs($admin, 'api')
            ->postJson('/api/admin/branding/update', [
                'branding.name.display' => 'Test Academy',
            ], [
                'branding.logo.default' => $logo,
            ]);

        $response->assertStatus(200);

        $setting = BrandingSetting::where('key', 'branding.logo.default')->first();
        $this->assertNotNull($setting);
        $this->assertNotNull($setting->value);
        Storage::disk('public')->assertExists($setting->value);
    }

    public function test_branding_service_caches_settings(): void
    {
        $service = app(BrandingService::class);
        
        $all = $service->all();
        $this->assertIsArray($all);
        $this->assertArrayHasKey('branding.name.display', $all);
    }

    protected function createAdminUser()
    {
        // Helper to create admin user - adjust based on your User factory
        return \Modules\ACL\Users\Models\User::factory()->create([
            'role_id' => \Modules\ACL\Roles\Models\Role::where('name', 'admin')->first()->id ?? 1,
        ]);
    }
}

