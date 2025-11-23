<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Modules\LMS\Certificates\Models\Certificate;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Get student certificates
     */
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;

        $certificates = Certificate::where('student_id', $studentId)
            ->with(['program', 'template'])
            ->orderByDesc('issued_at')
            ->get();

        return ApiResponse::success($certificates, 'Certificates retrieved successfully');
    }

    /**
     * Download certificate
     */
    public function download(int $id, Request $request): \Illuminate\Http\Response
    {
        $studentId = $request->user()->id;

        $certificate = Certificate::where('student_id', $studentId)
            ->findOrFail($id);

        // TODO: Return PDF file
        // For now, return JSON
        return response()->json([
            'message' => 'Certificate download will be implemented with PDF generation',
            'certificate' => $certificate,
        ]);
    }
}

