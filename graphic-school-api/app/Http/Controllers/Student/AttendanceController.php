<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {
    }

    /**
     * Get student attendance
     */
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;
        $groupId = $request->input('group_id');

        $attendance = $this->attendanceService->getStudentAttendance($studentId, $groupId);

        return ApiResponse::success($attendance, 'Attendance retrieved successfully');
    }
}

