<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\GamificationService;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    public function __construct(
        private GamificationService $gamificationService
    ) {
    }

    /**
     * Get group leaderboard
     */
    public function groupLeaderboard(Request $request): JsonResponse
    {
        $groupId = $request->get('group_id');
        
        if (!$groupId) {
            return ApiResponse::error('Group ID is required', [], 422);
        }

        $group = Group::findOrFail($groupId);
        
        // Get students in the group
        $studentIds = $group->students->pluck('id')->toArray();
        
        // Get leaderboard filtered by these students
        $allLeaderboard = $this->gamificationService->getLeaderboard(null, 1000);
        
        $groupLeaderboard = array_filter($allLeaderboard, function ($entry) use ($studentIds) {
            return in_array($entry['user']['id'], $studentIds);
        });
        
        // Re-rank
        $groupLeaderboard = array_values($groupLeaderboard);
        foreach ($groupLeaderboard as $index => &$entry) {
            $entry['rank'] = $index + 1;
        }

        return ApiResponse::success($groupLeaderboard, 'Group leaderboard retrieved successfully');
    }
}

