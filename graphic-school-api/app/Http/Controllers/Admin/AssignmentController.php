<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * List all assignments
     */
    public function index(Request $request): JsonResponse
    {
        $query = Assignment::with(['program', 'group', 'creator']);

        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $assignments = $query->orderByDesc('created_at')->paginate($request->get('per_page', 15));

        return ApiResponse::success($assignments, 'Assignments retrieved successfully');
    }
}

