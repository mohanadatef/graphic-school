<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use App\Models\Attendance;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class Phase3AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $instructor;
    protected User $student;
    protected Session $session;
    protected string $instructorToken;

    protected function setUp(): void
    {
        parent::setUp();

        $instructorRole = Role::factory()->create(['name' => 'instructor']);
        $studentRole = Role::factory()->create(['name' => 'student']);

        $this->instructor = User::factory()->create([
            'email' => 'instructor@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $instructorRole->id,
        ]);

        $this->student = User::factory()->create([
            'email' => 'student@test.com',
            'password' => Hash::make('password123'),
            'role_id' => $studentRole->id,
        ]);

        $this->instructorToken = $this->instructor->createToken('test-token')->plainTextToken;

        $this->session = Session::factory()->create();
    }

    public function test_instructor_can_update_attendance(): void
    {
        $attendance = Attendance::factory()->create([
            'session_id' => $this->session->id,
            'student_id' => $this->student->id,
            'status' => 'absent',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->instructorToken)
            ->postJson("/api/instructor/sessions/{$this->session->id}/attendance/update", [
                'attendance' => [
                    [
                        'student_id' => $this->student->id,
                        'status' => 'present',
                    ],
                ],
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('attendance', [
            'session_id' => $this->session->id,
            'student_id' => $this->student->id,
            'status' => 'present',
        ]);
    }
}

