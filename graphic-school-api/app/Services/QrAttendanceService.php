<?php

namespace App\Services;

use App\Models\QrToken;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use Modules\LMS\Sessions\Models\Session;
use Modules\ACL\Users\Models\User;
use Illuminate\Support\Facades\DB;

class QrAttendanceService
{
    /**
     * Generate QR token for session
     */
    public function generateToken(int $sessionId, int $expiryMinutes = 5): QrToken
    {
        return QrToken::generate($sessionId, $expiryMinutes);
    }

    /**
     * Process QR check-in
     */
    public function processCheckIn(string $token, int $studentId): Attendance
    {
        return DB::transaction(function () use ($token, $studentId) {
            $qrToken = QrToken::where('token', $token)->firstOrFail();

            // Validate token
            if ($qrToken->isExpired()) {
                throw new \Exception('QR token has expired');
            }

            $session = $qrToken->session;

            // Verify student is in the group
            if (!$session->group_id) {
                throw new \Exception('Session has no group assigned');
            }

            $group = $session->group;
            if (!$group->students()->where('users.id', $studentId)->exists()) {
                throw new \Exception('Student is not enrolled in this group');
            }

            // Get or create attendance
            $attendance = Attendance::firstOrCreate(
                [
                    'session_id' => $session->id,
                    'student_id' => $studentId,
                ],
                [
                    'status' => 'present',
                    'timestamp' => now(),
                ]
            );

            // Update if already exists
            if ($attendance->wasRecentlyCreated === false) {
                $attendance->status = 'present';
                $attendance->timestamp = now();
                $attendance->save();
            }

            // Log QR scan
            AttendanceLog::create([
                'attendance_id' => $attendance->id,
                'qr_token_id' => $qrToken->id,
                'student_id' => $studentId,
                'action' => 'qr_scan',
                'metadata' => [
                    'token' => $token,
                    'scanned_at' => now()->toIso8601String(),
                ],
            ]);

            return $attendance;
        });
    }

    /**
     * Get QR code data URL (for frontend rendering)
     */
    public function getQrCodeDataUrl(string $token): string
    {
        // Simple QR code generation using a library or API
        // For now, return a placeholder URL
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($token);
        return $qrUrl;
    }
}

