<?php

namespace Tests\Feature\Api\Phase4;

use Tests\TestCase;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Modules\ACL\Users\Models\User;
use App\Models\Program;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DynamicLearningSeeder::class);
    }

    public function test_instructor_can_create_assignment(): void
    {
        $instructor = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))->first();
        $program = Program::first();
        $group = Group::whereHas('batch', fn($q) => $q->where('program_id', $program->id))->first();

        $response = $this->actingAs($instructor, 'api')
            ->postJson('/api/instructor/assignments', [
                'program_id' => $program->id,
                'batch_id' => $group->batch_id,
                'group_id' => $group->id,
                'title' => 'Test Assignment',
                'description' => 'This is a test assignment',
                'due_date' => Carbon::now()->addDays(7)->toDateTimeString(),
                'max_grade' => 100,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'description', 'due_date'],
            ]);

        $this->assertDatabaseHas('assignments', [
            'title' => 'Test Assignment',
            'created_by' => $instructor->id,
        ]);
    }

    public function test_student_can_view_assignments(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $program = Program::first();
        $group = Group::whereHas('batch', fn($q) => $q->where('program_id', $program->id))->first();
        $instructor = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))->first();

        // Enroll student
        \Modules\LMS\Enrollments\Models\Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'batch_id' => $group->batch_id,
            'group_id' => $group->id,
            'status' => 'approved',
        ]);

        Assignment::create([
            'program_id' => $program->id,
            'batch_id' => $group->batch_id,
            'group_id' => $group->id,
            'title' => 'Student Assignment',
            'due_date' => Carbon::now()->addDays(7),
            'max_grade' => 100,
            'created_by' => $instructor->id,
        ]);

        $response = $this->actingAs($student, 'api')
            ->getJson('/api/student/assignments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'title', 'due_date']],
            ]);
    }

    public function test_student_can_submit_assignment(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $program = Program::first();
        $group = Group::whereHas('batch', fn($q) => $q->where('program_id', $program->id))->first();
        $instructor = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))->first();

        // Enroll student
        \Modules\LMS\Enrollments\Models\Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'batch_id' => $group->batch_id,
            'group_id' => $group->id,
            'status' => 'approved',
        ]);

        $assignment = Assignment::create([
            'program_id' => $program->id,
            'batch_id' => $group->batch_id,
            'group_id' => $group->id,
            'title' => 'Submit Assignment',
            'due_date' => Carbon::now()->addDays(7),
            'max_grade' => 100,
            'created_by' => $instructor->id,
        ]);

        $response = $this->actingAs($student, 'api')
            ->postJson("/api/student/assignments/{$assignment->id}/submit", [
                'text_submission' => 'This is my submission text.',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'assignment_id', 'student_id', 'text_submission'],
            ]);

        $this->assertDatabaseHas('assignment_submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'status' => 'submitted',
        ]);
    }

    public function test_instructor_can_grade_submission(): void
    {
        $instructor = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))->first();
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $program = Program::first();
        $group = Group::whereHas('batch', fn($q) => $q->where('program_id', $program->id))->first();

        $assignment = Assignment::create([
            'program_id' => $program->id,
            'batch_id' => $group->batch_id,
            'group_id' => $group->id,
            'title' => 'Grade Assignment',
            'due_date' => Carbon::now()->addDays(7),
            'max_grade' => 100,
            'created_by' => $instructor->id,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'submitted_at' => now(),
            'text_submission' => 'Submission text',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($instructor, 'api')
            ->postJson("/api/instructor/assignments/submissions/{$submission->id}/grade", [
                'grade' => 85,
                'feedback' => 'Good work!',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'grade', 'feedback', 'status'],
            ]);

        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'grade' => 85,
            'status' => 'graded',
        ]);
    }
}

