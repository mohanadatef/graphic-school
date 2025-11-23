<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentLog;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class EnrollmentLogController extends Controller
{
    public function index(int $enrollmentId): JsonResponse
    {
        $logs = EnrollmentLog::where('enrollment_id', $enrollmentId)
            ->with('admin')
            ->orderByDesc('created_at')
            ->get();

        return ApiResponse::success($logs, 'Enrollment logs retrieved successfully');
    }
}

