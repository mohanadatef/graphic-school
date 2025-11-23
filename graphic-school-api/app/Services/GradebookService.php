<?php

namespace App\Services;

use App\Models\GradebookEntry;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Support\Facades\DB;

class GradebookService
{
    /**
     * Update gradebook for student in program
     */
    public function updateForStudent(int $studentId, int $programId, ?int $batchId = null): GradebookEntry
    {
        return DB::transaction(function () use ($studentId, $programId, $batchId) {
            $entry = GradebookEntry::firstOrNew([
                'student_id' => $studentId,
                'program_id' => $programId,
                'batch_id' => $batchId,
            ]);

            // Calculate assignment grade (average of all graded assignments)
            $assignmentGrade = AssignmentSubmission::whereHas('assignment', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            })
            ->where('student_id', $studentId)
            ->where('status', 'graded')
            ->avg('grade') ?? 0;

            $entry->assignment_grade = $assignmentGrade;

            // Calculate attendance percentage
            $attendancePercentage = $this->calculateAttendancePercentage($studentId, $programId, $batchId);
            $entry->attendance_percentage = $attendancePercentage;

            // Calculate overall grade
            $entry->calculateOverallGrade();

            return $entry;
        });
    }

    /**
     * Calculate attendance percentage
     */
    protected function calculateAttendancePercentage(int $studentId, int $programId, ?int $batchId = null): float
    {
        $query = Session::whereHas('group', function ($q) use ($programId, $batchId) {
            $q->whereHas('batch', function ($bq) use ($programId, $batchId) {
                $bq->where('program_id', $programId);
                if ($batchId) {
                    $bq->where('id', $batchId);
                }
            });
        });

        $totalSessions = $query->count();

        if ($totalSessions === 0) {
            return 0;
        }

        $presentCount = Attendance::where('student_id', $studentId)
            ->where('status', 'present')
            ->whereHas('session', function ($q) use ($query) {
                $q->whereIn('id', $query->pluck('id'));
            })
            ->count();

        return ($presentCount / $totalSessions) * 100;
    }

    /**
     * Get gradebook for group
     */
    public function getForGroup(int $groupId): \Illuminate\Database\Eloquent\Collection
    {
        $group = \App\Models\Group::findOrFail($groupId);
        
        $students = $group->students;
        $entries = [];

        foreach ($students as $student) {
            $entry = GradebookEntry::where('student_id', $student->id)
                ->where('program_id', $group->batch->program_id)
                ->where('batch_id', $group->batch_id)
                ->first();

            if (!$entry) {
                $entry = $this->updateForStudent(
                    $student->id,
                    $group->batch->program_id,
                    $group->batch_id
                );
            }

            $entries[] = $entry;
        }

        return collect($entries);
    }
}

