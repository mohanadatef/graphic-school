<?php

namespace Modules\LMS\Certificates\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Certificates\Services\CertificateService;
use Modules\LMS\Certificates\Http\Resources\CertificateResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(private CertificateService $certificateService)
    {
    }

    public function issueCertificate(Request $request, int $enrollmentId): JsonResponse
    {
        try {
            $certificate = $this->certificateService->issueCertificate($enrollmentId);
            return ApiResponse::success(new CertificateResource($certificate->load(['course', 'student'])), 'Certificate issued successfully', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 400);
        }
    }

    public function verifyCertificate(string $verificationCode): JsonResponse
    {
        $certificate = $this->certificateService->verifyCertificate($verificationCode);
        
        if (!$certificate) {
            return ApiResponse::error('Certificate not found or invalid', [], 404);
        }

        return ApiResponse::success(new CertificateResource($certificate), 'Certificate verified successfully');
    }

    public function getMyCertificates(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;
        $certificates = $this->certificateService->getStudentCertificates($studentId);
        return ApiResponse::success($certificates, 'Certificates retrieved successfully');
    }
}

