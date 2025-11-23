<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use App\Models\Program;
use App\Models\Batch;
use App\Models\Group;
use App\Models\GroupTranslation;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $instructor;
    protected string $adminToken;
    protected Program $program;
    protected Batch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $instructorRole = Role::factory()->create(['name' => 'instructor']);
        
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);
        
        $this->instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $instructorRole->id,
        ]);
        
        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;
        $this->program = Program::factory()->create();
        $this->batch = Batch::factory()->create(['program_id' => $this->program->id]);
    }

    public function test_admin_can_create_group_with_translations(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/groups', [
                'batch_id' => $this->batch->id,
                'code' => 'GROUP-A',
                'capacity' => 20,
                'room' => 'Room 101',
                'instructor_id' => $this->instructor->id,
                'is_active' => true,
                'translations' => [
                    [
                        'locale' => 'en',
                        'name' => 'Group A',
                        'description' => 'Morning session group',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'المجموعة أ',
                        'description' => 'مجموعة الجلسة الصباحية',
                    ],
                ],
            ]);

        $response->assertStatus(201);

        $groupId = $response->json('data.id');
        
        $this->assertDatabaseHas('groups', [
            'id' => $groupId,
            'batch_id' => $this->batch->id,
        ]);
        
        $this->assertDatabaseHas('group_translations', [
            'group_id' => $groupId,
            'locale' => 'en',
            'name' => 'Group A',
        ]);
    }

    public function test_public_api_returns_group_sessions(): void
    {
        $group = Group::factory()->create(['batch_id' => $this->batch->id]);
        Session::factory()->count(3)->create(['group_id' => $group->id]);

        $response = $this->getJson("/api/groups/{$group->id}/sessions");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'title', 'session_date'],
                ],
            ]);
    }

    public function test_admin_can_assign_students_to_group(): void
    {
        $group = Group::factory()->create(['batch_id' => $this->batch->id]);
        $student = User::factory()->create(['role_id' => Role::where('name', 'student')->first()->id ?? Role::factory()->create(['name' => 'student'])->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->putJson("/api/admin/groups/{$group->id}", [
                'student_ids' => [$student->id],
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('group_student', [
            'group_id' => $group->id,
            'student_id' => $student->id,
        ]);
    }
}

