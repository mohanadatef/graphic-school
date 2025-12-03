<?php

namespace App\Services;

use App\Models\Attendance;
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

        return $attendance;
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

