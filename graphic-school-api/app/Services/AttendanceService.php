<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\GradebookEntry;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Models\User;

class AttendanceService
{
    /**
     * Update attendance for a session
     */
    public function updateAttendance(int $sessionId, int $studentId, string $status, ?int $markedBy = null, ?string $notes = null): Attendance
    {
        $attendance = Attendance::firstOrNew([
            'session_id' => $sessionId,
            'student_id' => $studentId,
        ]);

        $attendance->status = $status;
        $attendance->marked_by = $markedBy;
        $attendance->notes = $notes;

        if ($status === 'present' && !$attendance->timestamp) {
            $attendance->timestamp = now();
        }

        $attendance->save();

        // Update gradebook
        $this->updateGradebookForAttendance($attendance);

        // Award gamification points for attendance
        if ($status === 'present') {
            try {
                $gamificationService = app(\App\Services\GamificationService::class);
                $student = User::findOrFail($studentId);
                $gamificationService->awardPointsForEvent(
                    $student,
                    'attendance_present',
                    'attendance',
                    $attendance->id,
                    [
                        'session_id' => $sessionId,
                        'status' => $status,
                    ]
                );
            } catch (\Exception $e) {
                // Log but don't fail attendance if gamification fails
                \Illuminate\Support\Facades\Log::warning('Gamification failed for attendance', [
                    'attendance_id' => $attendance->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $attendance;
    }

    /**
     * Update gradebook after attendance change
     */
    protected function updateGradebookForAttendance(Attendance $attendance): void
    {
        $session = $attendance->session;
        if (!$session->group_id) {
            return;
        }

        $group = $session->group;
        if (!$group->batch || !$group->batch->program_id) {
            return;
        }

        $gradebookService = app(\App\Services\GradebookService::class);
        $gradebookService->updateForStudent(
            $attendance->student_id,
            $group->batch->program_id,
            $group->batch_id
        );
    }

    /**
     * Bulk update attendance for a session
     */
    public function bulkUpdateAttendance(int $sessionId, array $attendanceData, ?int $markedBy = null): array
    {
        $results = [];

        foreach ($attendanceData as $data) {
            $results[] = $this->updateAttendance(
                $sessionId,
                $data['student_id'],
                $data['status'],
                $markedBy,
                $data['notes'] ?? null
            );
        }

        return $results;
    }

    /**
     * Get attendance for a student
     */
    public function getStudentAttendance(int $studentId, ?int $groupId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Attendance::where('student_id', $studentId)
            ->with(['session.group', 'session.course']);

        if ($groupId) {
            $query->whereHas('session', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            });
        }

        return $query->orderByDesc('created_at')->get();
    }

    /**
     * Get attendance for a session
     */
    public function getSessionAttendance(int $sessionId): \Illuminate\Database\Eloquent\Collection
    {
        return Attendance::where('session_id', $sessionId)
            ->with(['student', 'markedBy'])
            ->get();
    }
}

