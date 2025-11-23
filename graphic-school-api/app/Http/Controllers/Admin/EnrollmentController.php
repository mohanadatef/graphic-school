<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Enrollments\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService,
        private EnrollmentRepositoryInterface $enrollmentRepository
    ) {
    }

    /**
     * List enrollments
     */
    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::with(['student', 'program', 'batch', 'group']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $enrollments = $query->orderByDesc('created_at')->paginate($request->get('per_page', 15));

        return ApiResponse::success($enrollments, 'Enrollments retrieved successfully');
    }

    /**
     * Approve enrollment
     */
    public function approve(int $id, Request $request): JsonResponse
    {
        $enrollment = $this->enrollmentService->approveEnrollment(
            $id,
            $request->user()->id,
            $request->input('batch_id'),
            $request->input('group_id')
        );

        return ApiResponse::success($enrollment->load(['student', 'program', 'batch', 'group']), 'Enrollment approved successfully');
    }

    /**
     * Reject enrollment
     */
    public function reject(int $id, Request $request): JsonResponse
    {
        $enrollment = $this->enrollmentService->rejectEnrollment(
            $id,
            $request->user()->id,
            $request->input('reason', '')
        );

        return ApiResponse::success($enrollment->load(['student', 'program']), 'Enrollment rejected successfully');
    }

    /**
     * Withdraw enrollment
     */
    public function withdraw(int $id, Request $request): JsonResponse
    {
        $enrollment = $this->enrollmentService->withdrawEnrollment(
            $id,
            $request->user()->id
        );

        return ApiResponse::success($enrollment->load(['student', 'program']), 'Enrollment withdrawn successfully');
    }
}

