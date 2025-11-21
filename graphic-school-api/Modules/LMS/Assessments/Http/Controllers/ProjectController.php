<?php

namespace Modules\LMS\Assessments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Assessments\Models\StudentProject;
use Modules\LMS\Assessments\Http\Resources\StudentProjectResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;
        $projects = StudentProject::where('student_id', $studentId)
            ->with(['course', 'module', 'lesson'])
            ->orderByDesc('created_at')
            ->get();

        return ApiResponse::success(
            StudentProjectResource::collection($projects),
            'Projects retrieved successfully'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'module_id' => 'nullable|exists:course_modules,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'files' => 'nullable|array',
            'submission_note' => 'nullable|string',
        ]);

        $enrollment = \Modules\LMS\Enrollments\Models\Enrollment::where('student_id', $studentId)
            ->where('course_id', $validated['course_id'])
            ->where('status', 'approved')
            ->firstOrFail();

        $project = StudentProject::create([
            ...$validated,
            'student_id' => $studentId,
            'enrollment_id' => $enrollment->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return ApiResponse::success(
            new StudentProjectResource($project->load(['course', 'module', 'lesson'])),
            'Project submitted successfully',
            201
        );
    }

    public function show(Request $request, int $projectId): JsonResponse
    {
        $studentId = $request->user()->id;
        $project = StudentProject::where('student_id', $studentId)
            ->with(['course', 'module', 'lesson'])
            ->findOrFail($projectId);

        return ApiResponse::success(
            new StudentProjectResource($project),
            'Project retrieved successfully'
        );
    }
}

