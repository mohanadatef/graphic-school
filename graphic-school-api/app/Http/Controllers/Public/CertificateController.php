<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Modules\LMS\Certificates\Services\CertificateService;
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
     * Verify certificate by verification code (public route)
     */
    public function verify(string $code): JsonResponse
    {
        $certificate = $this->certificateService->verifyCertificate($code);
        
        if (!$certificate) {
            return ApiResponse::error('Certificate not found or invalid', [], 404);
        }

        return ApiResponse::success($certificate->load(['course', 'group', 'student', 'instructor']), 'Certificate verified successfully');
    }
}


