<?php

namespace Tests\Feature\Api\Phase4;

use Tests\TestCase;
use App\Models\GradebookEntry;
use App\Models\AssignmentSubmission;
use Modules\ACL\Users\Models\User;
use App\Models\Program;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradebookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DynamicLearningSeeder::class);
    }

    public function test_student_can_view_gradebook(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $program = Program::first();
        $group = Group::whereHas('batch', fn($q) => $q->where('program_id', $program->id))->first();

        GradebookEntry::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'batch_id' => $group->batch_id,
            'assignment_grade' => 85,
            'attendance_percentage' => 90,
            'participation_grade' => 80,
            'overall_grade' => 85,
        ]);

        $response = $this->actingAs($student, 'api')
            ->getJson('/api/student/gradebook');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'student_id', 'program_id', 'overall_grade']],
            ]);
    }

    public function test_instructor_can_view_gradebook(): void
    {
        $instructor = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))->first();
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $program = Program::first();
        $group = Group::whereHas('batch', fn($q) => $q->where('program_id', $program->id))->first();

        // Assign instructor to group
        $group->instructor_id = $instructor->id;
        $group->save();

        GradebookEntry::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'batch_id' => $group->batch_id,
            'assignment_grade' => 85,
            'attendance_percentage' => 90,
            'participation_grade' => 80,
            'overall_grade' => 85,
        ]);

        $response = $this->actingAs($instructor, 'api')
            ->getJson('/api/instructor/gradebook');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'student_id', 'overall_grade']],
            ]);
    }

    public function test_gradebook_updates_on_assignment_grading(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        $program = Program::first();
        $group = Group::whereHas('batch', fn($q) => $q->where('program_id', $program->id))->first();
        $instructor = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))->first();

        $assignment = \App\Models\Assignment::create([
            'program_id' => $program->id,
            'batch_id' => $group->batch_id,
            'group_id' => $group->id,
            'title' => 'Test Assignment',
            'due_date' => now()->addDays(7),
            'max_grade' => 100,
            'created_by' => $instructor->id,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Grade the submission
        $submission->grade = 90;
        $submission->graded_by = $instructor->id;
        $submission->graded_at = now();
        $submission->status = 'graded';
        $submission->save();

        // Update gradebook
        $gradebookService = app(\App\Services\GradebookService::class);
        $entry = $gradebookService->updateForStudent($student->id, $program->id, $group->batch_id);

        $this->assertNotNull($entry->assignment_grade);
        $this->assertGreaterThan(0, $entry->assignment_grade);
    }
}

