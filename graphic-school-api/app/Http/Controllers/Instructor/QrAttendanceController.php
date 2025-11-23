<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\QrAttendanceService;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QrAttendanceController extends Controller
{
    public function __construct(
        private QrAttendanceService $qrService
    ) {
    }

    /**
     * Generate QR token for session
     */
    public function generateQr(int $sessionId, Request $request): JsonResponse
    {
        $instructorId = $request->user()->id;

        // Verify instructor owns this session's group
        $session = Session::whereHas('group', function ($q) use ($instructorId) {
            $q->where('instructor_id', $instructorId);
        })->findOrFail($sessionId);

        $qrToken = $this->qrService->generateToken($sessionId);
        $qrUrl = $this->qrService->getQrCodeDataUrl($qrToken->token);

        return ApiResponse::success([
            'token' => $qrToken->token,
            'expires_at' => $qrToken->expires_at,
            'qr_url' => $qrUrl,
        ], 'QR token generated successfully');
    }
}

