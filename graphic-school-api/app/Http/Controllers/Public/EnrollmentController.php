<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Http\Responses\ApiResponse;
use Modules\ACL\Users\Models\User;
use Modules\ACL\Roles\Models\Role;
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
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'batch_id' => 'nullable|exists:batches,id',
            'group_id' => 'nullable|exists:groups,id',
        ]);

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
            $request->program_id,
            $request->batch_id,
            $request->group_id
        );

        return ApiResponse::success([
            'student' => $student,
            'enrollment' => $enrollment->load(['program']),
        ], 'Enrollment request submitted successfully');
    }
}

