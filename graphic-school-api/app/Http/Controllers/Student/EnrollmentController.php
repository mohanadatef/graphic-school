<?php

namespace App\Http\Controllers\Student;

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
     * Enroll in program
     */
    public function enroll(Request $request): JsonResponse
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'batch_id' => 'nullable|exists:batches,id',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $studentId = $request->user()->id;

        // Check if already enrolled
        $existing = Enrollment::where('student_id', $studentId)
            ->where('program_id', $request->program_id)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'withdrawn')
            ->first();

        if ($existing) {
            return ApiResponse::error('Already enrolled in this program', 422);
        }

        $enrollment = $this->enrollmentService->createEnrollment(
            $studentId,
            $request->program_id,
            $request->batch_id,
            $request->group_id
        );

        return ApiResponse::success($enrollment->load(['program', 'batch', 'group']), 'Enrollment request submitted successfully');
    }

    /**
     * Get student enrollments
     */
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;
        $query = Enrollment::where('student_id', $studentId)
            ->with(['program', 'batch', 'group']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderByDesc('created_at')->paginate($request->get('per_page', 15));

        return ApiResponse::success($enrollments, 'Enrollments retrieved successfully');
    }
}

