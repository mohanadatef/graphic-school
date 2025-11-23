<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use App\Models\Program;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class Phase3EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $student;
    protected Program $program;
    protected string $adminToken;
    protected string $studentToken;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $studentRole = Role::factory()->create(['name' => 'student']);

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        $this->student = User::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $studentRole->id,
        ]);

        $this->adminToken = $this->admin->createToken('test-token')->plainTextToken;
        $this->studentToken = $this->student->createToken('test-token')->plainTextToken;

        $this->program = Program::factory()->create();
    }

    public function test_student_can_enroll_in_program(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->postJson('/api/student/enroll', [
                'program_id' => $this->program->id,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_enrollment(): void
    {
        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson("/api/admin/enrollments/{$enrollment->id}/approve");

        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reject_enrollment(): void
    {
        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->id,
            'program_id' => $this->program->id,
            'status' => 'pending',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson("/api/admin/enrollments/{$enrollment->id}/reject");

        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'rejected',
        ]);
    }
}

