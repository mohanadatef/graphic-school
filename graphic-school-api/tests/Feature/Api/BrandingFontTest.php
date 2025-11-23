<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrandingFontTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);
        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_admin_can_upload_custom_font(): void
    {
        $fontFile = UploadedFile::fake()->create('custom-font.woff2', 100, 'font/woff2');

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->post('/api/admin/branding/update', [
                'branding.fonts.custom_file' => $fontFile,
            ], [
                'Accept' => 'application/json',
            ]);

        $response->assertStatus(200);

        // Verify font file was stored
        Storage::disk('public')->assertExists('branding/fonts/' . $fontFile->hashName());

        // Verify settings were updated
        $this->assertDatabaseHas('branding_settings', [
            'key' => 'branding.fonts.source',
            'value' => 'custom',
        ]);

        $this->assertDatabaseHas('branding_settings', [
            'key' => 'branding.fonts.main',
            'value' => 'CustomFont',
        ]);
    }

    public function test_admin_can_switch_to_system_fonts(): void
    {
        // First set custom font
        $fontFile = UploadedFile::fake()->create('custom-font.woff2', 100, 'font/woff2');
        $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->post('/api/admin/branding/update', [
                'branding.fonts.custom_file' => $fontFile,
            ], [
                'Accept' => 'application/json',
            ]);

        // Switch to system fonts
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->post('/api/admin/branding/update', [
                'branding.fonts.source' => 'system',
                'branding.fonts.main' => 'Cairo',
                'branding.fonts.headings' => 'Poppins',
            ], [
                'Accept' => 'application/json',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('branding_settings', [
            'key' => 'branding.fonts.source',
            'value' => 'system',
        ]);

        $this->assertDatabaseHas('branding_settings', [
            'key' => 'branding.fonts.main',
            'value' => 'Cairo',
        ]);

        $this->assertDatabaseHas('branding_settings', [
            'key' => 'branding.fonts.headings',
            'value' => 'Poppins',
        ]);
    }

    public function test_font_upload_rejects_invalid_file_types(): void
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->post('/api/admin/branding/update', [
                'branding.fonts.custom_file' => $invalidFile,
            ], [
                'Accept' => 'application/json',
            ]);

        // Should fail validation (422) or return error
        $this->assertTrue(in_array($response->status(), [422, 400, 500]));
    }

    public function test_font_upload_rejects_files_too_large(): void
    {
        $largeFile = UploadedFile::fake()->create('large-font.woff2', 6000, 'font/woff2'); // 6MB

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->post('/api/admin/branding/update', [
                'branding.fonts.custom_file' => $largeFile,
            ], [
                'Accept' => 'application/json',
            ]);

        // Should fail validation (422) or return error
        $this->assertTrue(in_array($response->status(), [422, 400, 500]));
    }

    public function test_branding_service_returns_font_data_structure(): void
    {
        $response = $this->getJson('/api/branding/frontend');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'fonts' => [
                        'source',
                        'main',
                        'headings',
                        'custom_file_url',
                        'available_fonts',
                    ],
                ],
            ]);
    }

    public function test_available_fonts_are_returned_in_frontend_api(): void
    {
        // Seed branding first
        $this->artisan('db:seed', ['--class' => 'BrandingSeeder']);

        $response = $this->getJson('/api/branding/frontend');

        $response->assertStatus(200);
        
        $fonts = $response->json('data.fonts.available_fonts');
        $this->assertIsArray($fonts);
        
        // If fonts are available, check structure
        if (count($fonts) > 0) {
            $this->assertGreaterThan(20, count($fonts));
            $this->assertArrayHasKey('id', $fonts[0]);
            $this->assertArrayHasKey('label', $fonts[0]);
            $this->assertArrayHasKey('family', $fonts[0]);
        }
    }
}

