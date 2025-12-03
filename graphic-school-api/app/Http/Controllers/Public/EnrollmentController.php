<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Http\Responses\ApiResponse;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
use Modules\LMS\Courses\Models\Course;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(
        private EnrollmentService $enrollmentService
    ) {
    }

    /**
     * Public enrollment (creates student + enrollment)
     */
    public function enroll(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        // Verify course exists and is published
        $course = Course::findOrFail($request->course_id);
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

        // Create student user
        $studentRole = Role::where('name', 'student')->firstOrFail();
        
        $student = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make(Str::random(16)), // Random password, user will reset
            'role_id' => $studentRole->id,
        ]);

        // Create enrollment
        $enrollment = $this->enrollmentService->createEnrollment(
            $student->id,
            $request->course_id,
            $request->group_id
        );

        return ApiResponse::success([
            'student' => $student,
            'enrollment' => $enrollment->load(['course', 'group']),
        ], 'Enrollment request submitted successfully');
    }
}
