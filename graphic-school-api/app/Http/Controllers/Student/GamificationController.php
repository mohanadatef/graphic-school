<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    public function __construct(
        private GamificationService $gamificationService
    ) {
    }

    /**
     * Get student gamification summary
     */
    public function summary(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $summary = $this->gamificationService->getUserGamificationSummary($userId);

        return ApiResponse::success($summary, 'Gamification summary retrieved successfully');
    }

    /**
     * Get student gamification events
     */
    public function events(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $perPage = $request->get('per_page', 20);

        $events = \App\Models\GamificationEvent::where('user_id', $userId)
            ->with('rule')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return ApiResponse::success($events, 'Gamification events retrieved successfully');
    }

    /**
     * Get leaderboard
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $programId = $request->get('program_id');
        $limit = $request->get('limit', 50);

        $leaderboard = $this->gamificationService->getLeaderboard($programId, $limit);

        return ApiResponse::success($leaderboard, 'Leaderboard retrieved successfully');
    }
}

