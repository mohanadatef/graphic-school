<?php

namespace Modules\LMS\Progress\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\LMS\Progress\Services\ProgressService;
use Modules\LMS\Progress\Http\Requests\UpdateProgressRequest;
use Modules\LMS\Progress\Http\Resources\ProgressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __construct(private ProgressService $progressService)
    {
    }

    public function getProgress(Request $request, int $enrollmentId): JsonResponse
    {
        $studentId = $request->user()->id;
        $progress = $this->progressService->getStudentProgress($studentId, $enrollmentId);
        return ApiResponse::success($progress, 'Progress retrieved successfully');
    }

    public function markLessonComplete(Request $request, int $enrollmentId, int $lessonId): JsonResponse
    {
        $studentId = $request->user()->id;
        $progress = $this->progressService->markLessonComplete($studentId, $enrollmentId, $lessonId);
        return ApiResponse::success(new ProgressResource($progress), 'Lesson marked as completed');
    }

    public function updateProgress(UpdateProgressRequest $request, int $enrollmentId, int $lessonId): JsonResponse
    {
        $studentId = $request->user()->id;
        $progress = $this->progressService->updateLessonProgress(
            $studentId,
            $enrollmentId,
            $lessonId,
            $request->input('percentage', 0),
            $request->input('time_spent', 0)
        );
        return ApiResponse::success(new ProgressResource($progress), 'Progress updated successfully');
    }
}

