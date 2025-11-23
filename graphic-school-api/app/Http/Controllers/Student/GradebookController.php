<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GradebookEntry;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
    /**
     * Get student gradebook
     */
    public function index(Request $request): JsonResponse
    {
        $studentId = $request->user()->id;

        $entries = GradebookEntry::where('student_id', $studentId)
            ->with(['program', 'batch'])
            ->orderByDesc('updated_at')
            ->get();

        return ApiResponse::success($entries, 'Gradebook retrieved successfully');
    }
}

