<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\QrAttendanceService;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QrAttendanceController extends Controller
{
    public function __construct(
        private QrAttendanceService $qrService
    ) {
    }

    /**
     * Process QR check-in
     */
    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string|size:64',
        ]);

        $studentId = $request->user()->id;

        try {
            $attendance = $this->qrService->processCheckIn($request->token, $studentId);

            return ApiResponse::success([
                'attendance' => $attendance->load(['session', 'student']),
                'message' => 'Attendance confirmed successfully',
            ], 'Check-in successful');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
    }
}

