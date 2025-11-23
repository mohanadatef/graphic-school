<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use App\Models\Program;
use App\Models\Batch;
use App\Models\BatchTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class BatchTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected string $adminToken;
    protected Program $program;

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
        $this->program = Program::factory()->create();
    }

    public function test_admin_can_create_batch_with_translations(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/batches', [
                'program_id' => $this->program->id,
                'code' => 'BATCH-001',
                'start_date' => '2025-02-01',
                'end_date' => '2025-05-31',
                'max_students' => 30,
                'is_active' => true,
                'translations' => [
                    [
                        'locale' => 'en',
                        'name' => 'February - May 2025',
                        'description' => 'First batch',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'فبراير - مايو 2025',
                        'description' => 'الدفعة الأولى',
                    ],
                ],
            ]);

        $response->assertStatus(201);

        $batchId = $response->json('data.id');
        
        $this->assertDatabaseHas('batches', [
            'id' => $batchId,
            'program_id' => $this->program->id,
        ]);
        
        $this->assertDatabaseHas('batch_translations', [
            'batch_id' => $batchId,
            'locale' => 'en',
            'name' => 'February - May 2025',
        ]);
    }

    public function test_admin_can_list_batches_for_program(): void
    {
        Batch::factory()->count(3)->create(['program_id' => $this->program->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson("/api/admin/batches?program_id={$this->program->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'code', 'start_date'],
                ],
            ]);
    }
}

