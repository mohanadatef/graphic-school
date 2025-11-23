<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AssignmentService;
use App\Models\Assignment;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function __construct(
        private AssignmentService $assignmentService
    ) {
    }

    /**
     * Get student assignments
     */
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;

        // Get assignments for student's enrolled programs/groups
        $assignments = Assignment::whereHas('group.students', function ($q) use ($studentId) {
            $q->where('users.id', $studentId);
        })
        ->orWhereHas('program', function ($q) use ($studentId) {
            $q->whereHas('batches.groups.students', function ($sq) use ($studentId) {
                $sq->where('users.id', $studentId);
            });
        })
        ->with(['program', 'group', 'submissions' => function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        }])
        ->orderBy('due_date')
        ->paginate($request->get('per_page', 15));

        return ApiResponse::success($assignments, 'Assignments retrieved successfully');
    }

    /**
     * Show assignment details
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $studentId = $request->user()->id;

        $assignment = Assignment::with(['program', 'group', 'submissions' => function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        }])->findOrFail($id);

        return ApiResponse::success($assignment, 'Assignment retrieved successfully');
    }

    /**
     * Submit assignment
     */
    public function submit(int $assignmentId, Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'nullable|file|max:10240', // 10MB max
            'text_submission' => 'nullable|string',
        ]);

        $studentId = $request->user()->id;
        $fileUrl = null;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('assignment-submissions', 'public');
            $fileUrl = Storage::url($path);
        }

        $submission = $this->assignmentService->submit(
            $assignmentId,
            $studentId,
            $fileUrl,
            $request->text_submission
        );

        return ApiResponse::success($submission->load(['assignment']), 'Assignment submitted successfully');
    }
}

