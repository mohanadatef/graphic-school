<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Modules\LMS\Certificates\Services\CertificateService;
use Modules\LMS\Certificates\Models\Certificate;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(
        private CertificateService $certificateService
    ) {
    }

    /**
     * Get student certificates
     */
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;
        $certificates = $this->certificateService->getStudentCertificates($studentId);

        return ApiResponse::success($certificates, 'Certificates retrieved successfully');
    }

    /**
     * Get certificate details
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $studentId = $request->user()->id;

        $certificate = Certificate::where('student_id', $studentId)
            ->with(['course', 'group', 'instructor'])
            ->findOrFail($id);

        return ApiResponse::success($certificate, 'Certificate retrieved successfully');
    }
}
