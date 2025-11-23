<?php

namespace App\Http\Controllers\Instructor;

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
     * List assignments for instructor
     */
    public function index(Request $request): JsonResponse
    {
        $instructorId = $request->user()->id;

        $assignments = Assignment::where('created_by', $instructorId)
            ->with(['program', 'group', 'session'])
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return ApiResponse::success($assignments, 'Assignments retrieved successfully');
    }

    /**
     * Create assignment
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'group_id' => 'nullable|exists:groups,id',
            'session_id' => 'nullable|exists:sessions,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:now',
            'max_grade' => 'required|numeric|min:0|max:100',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max
        ]);

        $data = $request->only([
            'program_id', 'batch_id', 'group_id', 'session_id',
            'title', 'description', 'due_date', 'max_grade',
        ]);
        $data['created_by'] = $request->user()->id;

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('assignments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'url' => Storage::url($path),
                ];
            }
        }

        $assignment = $this->assignmentService->create($data, $attachments);

        return ApiResponse::success($assignment->load(['program', 'group']), 'Assignment created successfully');
    }

    /**
     * Get assignment submissions
     */
    public function submissions(int $assignmentId): JsonResponse
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Verify instructor owns this assignment
        if ($assignment->created_by !== request()->user()->id) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $submissions = $assignment->submissions()
            ->with(['student'])
            ->orderByDesc('submitted_at')
            ->get();

        return ApiResponse::success($submissions, 'Submissions retrieved successfully');
    }

    /**
     * Grade submission
     */
    public function gradeSubmission(int $submissionId, Request $request): JsonResponse
    {
        $request->validate([
            'grade' => 'required|numeric|min:0',
            'feedback' => 'nullable|string',
        ]);

        $submission = \App\Models\AssignmentSubmission::findOrFail($submissionId);
        $assignment = $submission->assignment;

        // Verify instructor owns this assignment
        if ($assignment->created_by !== $request->user()->id) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $graded = $this->assignmentService->grade(
            $submissionId,
            $request->grade,
            $request->feedback,
            $request->user()->id
        );

        return ApiResponse::success($graded->load(['student', 'grader']), 'Submission graded successfully');
    }
}

