<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService
    ) {
    }

    /**
     * Enroll in course
     */
    public function enroll(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $studentId = $request->user()->id;
        $course = Course::findOrFail($request->course_id);

        // Verify course is published
        if (!$course->is_published) {
            return ApiResponse::error('Course is not available for enrollment', 403);
        }

        // Verify group belongs to course if provided
        if ($request->group_id) {
            $group = \App\Models\Group::where('id', $request->group_id)
                ->where('course_id', $course->id)
                ->first();
            if (!$group) {
                return ApiResponse::error('Invalid group for this course', 422);
            }
        }

        // Check if already enrolled
        $existing = Enrollment::where('student_id', $studentId)
            ->where('course_id', $request->course_id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return ApiResponse::error('Already enrolled in this course', 422);
        }

        $enrollment = $this->enrollmentService->createEnrollment(
            $studentId,
            $request->course_id,
            $request->group_id
        );

        return ApiResponse::success(
            $enrollment->load(['course', 'group']), 
            'Enrollment request submitted successfully'
        );
    }

    /**
     * Get student enrollments
     */
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;
        $query = Enrollment::where('student_id', $studentId)
            ->with(['course', 'group']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderByDesc('created_at')->paginate($request->get('per_page', 15));

        return ApiResponse::success($enrollments, 'Enrollments retrieved successfully');
    }
}
