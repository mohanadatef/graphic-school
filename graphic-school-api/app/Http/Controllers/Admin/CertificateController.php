<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Certificates\Services\CertificateService;
use Modules\LMS\Certificates\Models\Certificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(
        private CertificateService $certificateService
    ) {
    }

    /**
     * List all certificates
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['course_id', 'student_id', 'group_id', 'per_page']);
        $certificates = $this->certificateService->getAllCertificates($filters);

        return ApiResponse::success($certificates, 'Certificates retrieved successfully');
    }

    /**
     * Issue a new certificate
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'group_id' => 'nullable|exists:groups,id',
            'instructor_id' => 'nullable|exists:users,id',
        ]);

        try {
            $certificate = $this->certificateService->issueCertificate(
                $request->student_id,
                $request->course_id,
                $request->group_id,
                $request->instructor_id
            );

            return ApiResponse::success(
                $certificate->load(['course', 'group', 'student', 'instructor']),
                'Certificate issued successfully',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 400);
        }
    }

    /**
     * Get certificate details
     */
    public function show(int $id): JsonResponse
    {
        $certificate = Certificate::with(['course', 'group', 'student', 'instructor'])
            ->findOrFail($id);

        return ApiResponse::success($certificate, 'Certificate retrieved successfully');
    }

    /**
     * Delete certificate
     */
    public function destroy(int $id): JsonResponse
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->delete();

        return ApiResponse::success(null, 'Certificate deleted successfully');
    }
}


