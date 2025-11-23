<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = SubscriptionPlan::orderBy('price_monthly')->get();
        return ApiResponse::success($plans, 'Plans retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subscription_plans,code',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan = SubscriptionPlan::create($validated);
        return ApiResponse::success($plan, 'Plan created successfully', 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:subscription_plans,code,' . $id,
            'price_monthly' => 'sometimes|numeric|min:0',
            'price_yearly' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan->update($validated);
        return ApiResponse::success($plan->fresh(), 'Plan updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->delete();
        return ApiResponse::success(null, 'Plan deleted successfully', 204);
    }
}

