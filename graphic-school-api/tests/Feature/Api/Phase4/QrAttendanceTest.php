<?php

namespace Tests\Feature\Api\Phase4;

use Tests\TestCase;
use App\Models\QrToken;
use App\Models\Attendance;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Sessions\Models\Session;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class QrAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DynamicLearningSeeder::class);
    }

    public function test_instructor_can_generate_qr_token(): void
    {
        $instructor = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))->first();
        $session = Session::whereHas('group')->first();

        $response = $this->actingAs($instructor, 'api')
            ->postJson("/api/instructor/sessions/{$session->id}/qr-generate");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'session_id', 'token', 'expires_at'],
            ]);

        $this->assertDatabaseHas('qr_tokens', [
            'session_id' => $session->id,
        ]);
    }

    public function test_student_can_check_in_with_valid_qr_token(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $session = Session::whereHas('group')->first();
        
        // Enroll student in group
        $group = $session->group;
        Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $group->batch->program_id,
            'batch_id' => $group->batch_id,
            'group_id' => $group->id,
            'status' => 'approved',
        ]);

        $qrToken = QrToken::create([
            'session_id' => $session->id,
            'token' => Str::random(64),
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->actingAs($student, 'api')
            ->postJson('/api/student/qr-checkin', [
                'token' => $qrToken->token,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'session_id', 'student_id', 'status'],
            ]);

        $this->assertDatabaseHas('attendance', [
            'session_id' => $session->id,
            'student_id' => $student->id,
            'status' => 'present',
        ]);
    }

    public function test_student_cannot_check_in_with_expired_token(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $session = Session::whereHas('group')->first();

        $qrToken = QrToken::create([
            'session_id' => $session->id,
            'token' => Str::random(64),
            'expires_at' => now()->subMinutes(10),
        ]);

        $response = $this->actingAs($student, 'api')
            ->postJson('/api/student/qr-checkin', [
                'token' => $qrToken->token,
            ]);

        $response->assertStatus(422);
    }

    public function test_student_cannot_check_in_if_not_enrolled(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $session = Session::whereHas('group')->first();

        $qrToken = QrToken::create([
            'session_id' => $session->id,
            'token' => Str::random(64),
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->actingAs($student, 'api')
            ->postJson('/api/student/qr-checkin', [
                'token' => $qrToken->token,
            ]);

        $response->assertStatus(422);
    }
}

