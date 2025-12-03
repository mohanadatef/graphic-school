<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Enrollments\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService
    ) {
    }

    /**
     * List enrollments
     */
    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::with(['student', 'course', 'group']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->has('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $enrollments = $query->orderByDesc('created_at')->paginate($request->get('per_page', 15));

        return ApiResponse::success($enrollments, 'Enrollments retrieved successfully');
    }

    /**
     * Approve enrollment and assign to group
     */
    public function approve(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'group_id' => 'nullable|exists:groups,id',
        ]);

        try {
            $enrollment = $this->enrollmentService->approveEnrollment(
                $id,
                $request->user()->id,
                $request->input('group_id')
            );

            return ApiResponse::success(
                $enrollment->load(['student', 'course', 'group']), 
                'Enrollment approved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    /**
     * Reject enrollment
     */
    public function reject(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $enrollment = $this->enrollmentService->rejectEnrollment(
            $id,
            $request->user()->id,
            $request->input('reason', '')
        );

        return ApiResponse::success(
            $enrollment->load(['student', 'course']), 
            'Enrollment rejected successfully'
        );
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

        return ApiResponse::success(
            $enrollment->load(['student', 'course', 'group']), 
            'Enrollment withdrawn successfully'
        );
    }
}
