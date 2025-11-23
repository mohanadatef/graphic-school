<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentLog;
use App\Models\GradebookEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignmentService
{
    /**
     * Create assignment
     */
    public function create(array $data, ?array $attachments = null): Assignment
    {
        return DB::transaction(function () use ($data, $attachments) {
            if ($attachments) {
                $data['attachments'] = $attachments;
            }

            $assignment = Assignment::create($data);

            // Log creation
            AssignmentLog::create([
                'assignment_id' => $assignment->id,
                'user_id' => $data['created_by'],
                'action' => 'created',
                'metadata' => [
                    'title' => $assignment->title,
                    'due_date' => $assignment->due_date->toIso8601String(),
                ],
            ]);

            // Create calendar event
            $this->createCalendarEvent($assignment);

            return $assignment;
        });
    }

    /**
     * Submit assignment
     */
    public function submit(int $assignmentId, int $studentId, ?string $fileUrl = null, ?string $textSubmission = null): AssignmentSubmission
    {
        return DB::transaction(function () use ($assignmentId, $studentId, $fileUrl, $textSubmission) {
            $assignment = Assignment::findOrFail($assignmentId);

            // Check if already submitted
            $existing = AssignmentSubmission::where('assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->first();

            if ($existing) {
                throw new \Exception('Assignment already submitted');
            }

            $isLate = now() > $assignment->due_date;
            $status = $isLate ? 'late' : 'submitted';

            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignmentId,
                'student_id' => $studentId,
                'submitted_at' => now(),
                'file_url' => $fileUrl,
                'text_submission' => $textSubmission,
                'status' => $status,
            ]);

            // Log submission
            AssignmentLog::create([
                'assignment_id' => $assignmentId,
                'user_id' => $studentId,
                'action' => 'submitted',
                'metadata' => [
                    'is_late' => $isLate,
                    'submitted_at' => $submission->submitted_at->toIso8601String(),
                ],
            ]);

            // Award gamification points for assignment submission
            try {
                $gamificationService = app(\App\Services\GamificationService::class);
                $student = \Modules\ACL\Users\Models\User::findOrFail($studentId);
                $gamificationService->awardPointsForEvent(
                    $student,
                    'assignment_submitted',
                    'assignment_submissions',
                    $submission->id,
                    [
                        'assignment_id' => $assignmentId,
                        'is_late' => $isLate,
                    ]
                );
            } catch (\Exception $e) {
                // Log but don't fail submission if gamification fails
                \Illuminate\Support\Facades\Log::warning('Gamification failed for assignment submission', [
                    'submission_id' => $submission->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $submission;
        });
    }

    /**
     * Grade assignment submission
     */
    public function grade(int $submissionId, float $grade, ?string $feedback = null, int $gradedBy): AssignmentSubmission
    {
        return DB::transaction(function () use ($submissionId, $grade, $feedback, $gradedBy) {
            $submission = AssignmentSubmission::findOrFail($submissionId);
            $submission->grade = $grade;
            $submission->feedback = $feedback;
            $submission->graded_by = $gradedBy;
            $submission->graded_at = now();
            $submission->status = 'graded';
            $submission->save();

            // Log grading
            AssignmentLog::create([
                'assignment_id' => $submission->assignment_id,
                'user_id' => $gradedBy,
                'action' => 'graded',
                'metadata' => [
                    'submission_id' => $submissionId,
                    'grade' => $grade,
                ],
            ]);

            // Update gradebook
            $this->updateGradebook($submission);

            // Award gamification points for perfect score
            if ($submission->assignment && $grade >= $submission->assignment->max_grade * 0.95) {
                try {
                    $gamificationService = app(\App\Services\GamificationService::class);
                    $student = $submission->student;
                    $gamificationService->awardPointsForEvent(
                        $student,
                        'assignment_perfect_score',
                        'assignment_submissions',
                        $submission->id,
                        [
                            'assignment_id' => $submission->assignment_id,
                            'grade' => $grade,
                            'max_grade' => $submission->assignment->max_grade,
                        ]
                    );
                } catch (\Exception $e) {
                    // Log but don't fail grading if gamification fails
                    \Illuminate\Support\Facades\Log::warning('Gamification failed for assignment perfect score', [
                        'submission_id' => $submission->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $submission;
        });
    }

    /**
     * Update gradebook entry
     */
    protected function updateGradebook(AssignmentSubmission $submission): void
    {
        $assignment = $submission->assignment;
        $student = $submission->student;

        if (!$assignment->program_id) {
            return;
        }

        $entry = GradebookEntry::firstOrNew([
            'student_id' => $student->id,
            'program_id' => $assignment->program_id,
            'batch_id' => $assignment->batch_id,
        ]);

        // Calculate average assignment grade
        $allSubmissions = AssignmentSubmission::whereHas('assignment', function ($q) use ($assignment) {
            $q->where('program_id', $assignment->program_id);
        })
        ->where('student_id', $student->id)
        ->where('status', 'graded')
        ->get();

        if ($allSubmissions->count() > 0) {
            $entry->assignment_grade = $allSubmissions->avg('grade');
        }

        // Update attendance percentage using GradebookService
        $gradebookService = app(\App\Services\GradebookService::class);
        $updatedEntry = $gradebookService->updateForStudent(
            $student->id,
            $assignment->program_id,
            $assignment->batch_id
        );
        
        $entry->attendance_percentage = $updatedEntry->attendance_percentage;
        $entry->calculateOverallGrade();
    }

    /**
     * Create calendar event for assignment
     */
    protected function createCalendarEvent(Assignment $assignment): void
    {
        \App\Models\CalendarEvent::create([
            'user_id' => null, // System event
            'event_type' => 'assignment',
            'reference_id' => $assignment->id,
            'title' => $assignment->title,
            'description' => $assignment->description,
            'start_datetime' => $assignment->due_date,
            'end_datetime' => $assignment->due_date,
            'color' => '#ef4444', // Red for deadlines
        ]);
    }
}

