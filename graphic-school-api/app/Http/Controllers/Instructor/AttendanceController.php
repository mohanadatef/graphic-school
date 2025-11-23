<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {
    }

    /**
     * Get sessions for instructor's groups
     */
    public function sessions(Request $request): JsonResponse
    {
        $instructorId = $request->user()->id;

        $sessions = Session::whereHas('group', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })
        ->with(['group', 'course', 'attendance'])
        ->orderBy('scheduled_at')
        ->get();

        return ApiResponse::success($sessions, 'Sessions retrieved successfully');
    }

    /**
     * Get attendance for a session
     */
    public function attendance(int $sessionId, Request $request): JsonResponse
    {
        $instructorId = $request->user()->id;

        // Verify instructor owns this session's group
        $session = Session::whereHas('group', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->findOrFail($sessionId);

        $attendance = $this->attendanceService->getSessionAttendance($sessionId);

        return ApiResponse::success($attendance, 'Attendance retrieved successfully');
    }

    /**
     * Update attendance
     */
    public function updateAttendance(int $sessionId, Request $request): JsonResponse
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:users,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string',
        ]);

        $instructorId = $request->user()->id;

        // Verify instructor owns this session's group
        $session = Session::whereHas('group', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->findOrFail($sessionId);

        $results = $this->attendanceService->bulkUpdateAttendance(
            $sessionId,
            $request->attendance,
            $instructorId
        );

        return ApiResponse::success($results, 'Attendance updated successfully');
    }
}

