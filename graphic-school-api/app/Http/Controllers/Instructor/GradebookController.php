<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Services\GradebookService;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
    public function __construct(
        private GradebookService $gradebookService
    ) {
    }

    /**
     * Get gradebook for group
     */
    public function getForGroup(int $groupId, Request $request): JsonResponse
    {
        $instructorId = $request->user()->id;

        // Verify instructor owns this group
        $group = \App\Models\Group::where('instructor_id', $instructorId)
            ->findOrFail($groupId);

        $entries = $this->gradebookService->getForGroup($groupId);

        return ApiResponse::success($entries, 'Gradebook retrieved successfully');
    }
}

