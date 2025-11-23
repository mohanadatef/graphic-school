<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\GamificationRule;
use App\Models\GamificationLevel;
use App\Models\GamificationBadge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    /**
     * List gamification rules
     */
    public function rules(Request $request): JsonResponse
    {
        $query = GamificationRule::query();

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $rules = $query->orderBy('code')->paginate($request->get('per_page', 15));

        return ApiResponse::success($rules, 'Gamification rules retrieved successfully');
    }

    /**
     * Create gamification rule
     */
    public function createRule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:gamification_rules,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'points' => 'required|integer|min:0',
            'max_times_per_period' => 'nullable|integer|min:1',
            'scope' => 'nullable|string|in:global,per_program',
            'active' => 'boolean',
        ]);

        $rule = GamificationRule::create($validated);

        return ApiResponse::success($rule, 'Gamification rule created successfully', 201);
    }

    /**
     * Update gamification rule
     */
    public function updateRule(int $id, Request $request): JsonResponse
    {
        $rule = GamificationRule::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|unique:gamification_rules,code,' . $id,
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'points' => 'sometimes|integer|min:0',
            'max_times_per_period' => 'nullable|integer|min:1',
            'scope' => 'sometimes|string|in:global,per_program',
            'active' => 'boolean',
        ]);

        $rule->update($validated);

        return ApiResponse::success($rule, 'Gamification rule updated successfully');
    }

    /**
     * Delete gamification rule
     */
    public function deleteRule(int $id): JsonResponse
    {
        $rule = GamificationRule::findOrFail($id);
        $rule->delete();

        return ApiResponse::success(null, 'Gamification rule deleted successfully');
    }

    /**
     * List gamification levels
     */
    public function levels(Request $request): JsonResponse
    {
        $levels = GamificationLevel::orderBy('min_points')->get();

        return ApiResponse::success($levels, 'Gamification levels retrieved successfully');
    }

    /**
     * Create gamification level
     */
    public function createLevel(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'min_points' => 'required|integer|min:0',
            'max_points' => 'nullable|integer|min:0',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $level = GamificationLevel::create($validated);

        return ApiResponse::success($level, 'Gamification level created successfully', 201);
    }

    /**
     * Update gamification level
     */
    public function updateLevel(int $id, Request $request): JsonResponse
    {
        $level = GamificationLevel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'min_points' => 'sometimes|integer|min:0',
            'max_points' => 'nullable|integer|min:0',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $level->update($validated);

        return ApiResponse::success($level, 'Gamification level updated successfully');
    }

    /**
     * Delete gamification level
     */
    public function deleteLevel(int $id): JsonResponse
    {
        $level = GamificationLevel::findOrFail($id);
        $level->delete();

        return ApiResponse::success(null, 'Gamification level deleted successfully');
    }

    /**
     * List gamification badges
     */
    public function badges(Request $request): JsonResponse
    {
        $query = GamificationBadge::query();

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $badges = $query->orderBy('code')->paginate($request->get('per_page', 15));

        return ApiResponse::success($badges, 'Gamification badges retrieved successfully');
    }

    /**
     * Create gamification badge
     */
    public function createBadge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:gamification_badges,code',
            'name' => 'required|string',
            'description' => 'required|string',
            'icon' => 'nullable|string',
            'condition_type' => 'nullable|string|in:rule_based,manual,composite',
            'active' => 'boolean',
        ]);

        $badge = GamificationBadge::create($validated);

        return ApiResponse::success($badge, 'Gamification badge created successfully', 201);
    }

    /**
     * Update gamification badge
     */
    public function updateBadge(int $id, Request $request): JsonResponse
    {
        $badge = GamificationBadge::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|unique:gamification_badges,code,' . $id,
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'icon' => 'nullable|string',
            'condition_type' => 'sometimes|string|in:rule_based,manual,composite',
            'active' => 'boolean',
        ]);

        $badge->update($validated);

        return ApiResponse::success($badge, 'Gamification badge updated successfully');
    }

    /**
     * Delete gamification badge
     */
    public function deleteBadge(int $id): JsonResponse
    {
        $badge = GamificationBadge::findOrFail($id);
        $badge->delete();

        return ApiResponse::success(null, 'Gamification badge deleted successfully');
    }
}

