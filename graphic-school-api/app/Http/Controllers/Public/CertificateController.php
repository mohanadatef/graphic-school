<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CertificateService;
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
     * Verify certificate
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);

        $certificate = $this->certificateService->verifyCertificate($request->input('verification_code'));

        if (!$certificate) {
            return ApiResponse::error('Certificate not found', 404);
        }

        return ApiResponse::success($certificate->load(['student', 'program', 'template']), 'Certificate verified successfully');
    }
}

