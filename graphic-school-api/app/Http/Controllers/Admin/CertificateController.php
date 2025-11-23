<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CertificateService;
use App\Models\CertificateTemplate;
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
     * List certificates
     */
    public function index(Request $request): JsonResponse
    {
        $query = Certificate::with(['student', 'program', 'template']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $certificates = $query->orderByDesc('issued_at')->paginate($request->get('per_page', 15));

        return ApiResponse::success($certificates, 'Certificates retrieved successfully');
    }

    /**
     * Issue certificate
     */
    public function issue(Request $request): JsonResponse
    {
        $request->validate([
            'enrollment_id' => 'required|exists:enrollments,id',
            'template_id' => 'nullable|exists:certificate_templates,id',
        ]);

        $certificate = $this->certificateService->issueCertificate(
            $request->input('enrollment_id'),
            $request->input('template_id')
        );

        return ApiResponse::success($certificate->load(['student', 'program', 'template']), 'Certificate issued successfully');
    }
}

