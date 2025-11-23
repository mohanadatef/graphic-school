<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);
        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_admin_can_create_program_with_translations(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/programs', [
                'type' => 'bootcamp',
                'duration_weeks' => 12,
                'price' => 5000,
                'level' => 'beginner',
                'is_active' => true,
                'translations' => [
                    [
                        'locale' => 'en',
                        'title' => 'Graphic Design Bootcamp',
                        'description' => 'A comprehensive bootcamp',
                        'meta_title' => 'Graphic Design Bootcamp',
                        'meta_description' => 'Learn graphic design',
                    ],
                    [
                        'locale' => 'ar',
                        'title' => 'معسكر التصميم الجرافيكي',
                        'description' => 'معسكر شامل',
                        'meta_title' => 'معسكر التصميم الجرافيكي',
                        'meta_description' => 'تعلم التصميم الجرافيكي',
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'slug', 'type'],
            ]);

        $programId = $response->json('data.id');
        
        $this->assertDatabaseHas('programs', [
            'id' => $programId,
            'type' => 'bootcamp',
        ]);
        
        $this->assertDatabaseHas('program_translations', [
            'program_id' => $programId,
            'locale' => 'en',
            'title' => 'Graphic Design Bootcamp',
        ]);
        
        $this->assertDatabaseHas('program_translations', [
            'program_id' => $programId,
            'locale' => 'ar',
            'title' => 'معسكر التصميم الجرافيكي',
        ]);
        
        // Verify translations are returned in response
        $response->assertJsonPath('data.translations.0.locale', 'en');
        $response->assertJsonPath('data.translations.0.title', 'Graphic Design Bootcamp');
    }

    public function test_admin_can_update_program_with_translations(): void
    {
        $program = Program::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->putJson("/api/admin/programs/{$program->id}", [
                'translations' => [
                    [
                        'locale' => 'en',
                        'title' => 'Updated Program Title',
                        'description' => 'Updated Description',
                    ],
                    [
                        'locale' => 'ar',
                        'title' => 'عنوان محدث',
                        'description' => 'وصف محدث',
                    ],
                ],
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('program_translations', [
            'program_id' => $program->id,
            'locale' => 'en',
            'title' => 'Updated Program Title',
        ]);
    }

    public function test_public_api_returns_translated_program(): void
    {
        $program = Program::factory()->create(['is_active' => true, 'slug' => 'test-program']);

        \App\Models\ProgramTranslation::create([
            'program_id' => $program->id,
            'locale' => 'en',
            'title' => 'English Title',
            'description' => 'English Description',
        ]);

        \App\Models\ProgramTranslation::create([
            'program_id' => $program->id,
            'locale' => 'ar',
            'title' => 'العنوان بالعربية',
            'description' => 'الوصف بالعربية',
        ]);
        
        // Reload program with translations
        $program->load('translations');

        // Test English locale
        $responseEn = $this->withHeader('Accept-Language', 'en')
            ->getJson("/api/programs/{$program->slug}");

        $responseEn->assertStatus(200);
        $this->assertStringContainsString('English Title', json_encode($responseEn->json('data.title')));

        // Test Arabic locale
        $responseAr = $this->withHeader('Accept-Language', 'ar')
            ->getJson("/api/programs/{$program->slug}");

        $responseAr->assertStatus(200);
        $this->assertStringContainsString('العنوان', json_encode($responseAr->json('data.title')));
    }

    public function test_public_api_returns_program_batches(): void
    {
        $program = Program::factory()->create(['is_active' => true]);
        $batch = \App\Models\Batch::factory()->create(['program_id' => $program->id]);

        $response = $this->getJson("/api/programs/{$program->slug}/batches");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'code', 'start_date'],
                ],
            ]);
    }

    public function test_admin_can_delete_program(): void
    {
        $program = Program::factory()->create();
        \App\Models\ProgramTranslation::create([
            'program_id' => $program->id,
            'locale' => 'en',
            'title' => 'Test Program',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->deleteJson("/api/admin/programs/{$program->id}");

        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('programs', ['id' => $program->id]);
        $this->assertDatabaseMissing('program_translations', ['program_id' => $program->id]);
    }
}

