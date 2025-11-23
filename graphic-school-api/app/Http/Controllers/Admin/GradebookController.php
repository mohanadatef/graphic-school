<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradebookEntry;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
    /**
     * Get gradebook overview
     */
    public function index(Request $request): JsonResponse
    {
        $query = GradebookEntry::with(['student', 'program', 'batch']);

        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->has('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        $entries = $query->orderByDesc('overall_grade')->paginate($request->get('per_page', 15));

        return ApiResponse::success($entries, 'Gradebook retrieved successfully');
    }
}

