<?php

namespace App\Http\Controllers\Admin;

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
     * Get attendance overview
     */
    public function index(Request $request): JsonResponse
    {
        $query = \App\Models\Attendance::with(['student', 'session.group', 'session.course', 'markedBy']);

        if ($request->has('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attendance = $query->orderByDesc('created_at')->paginate($request->get('per_page', 15));

        return ApiResponse::success($attendance, 'Attendance retrieved successfully');
    }
}

