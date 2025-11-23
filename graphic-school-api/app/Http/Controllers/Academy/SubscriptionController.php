<?php

namespace App\Http\Controllers\Academy;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\SubscriptionService;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $subscription = $this->subscriptionService->getAcademySubscription($request->user());
        
        if (!$subscription) {
            return ApiResponse::error('No active subscription found', [], 404);
        }

        return ApiResponse::success($subscription->load(['plan', 'usageTrackers']), 'Subscription retrieved successfully');
    }

    public function usage(Request $request): JsonResponse
    {
        $usage = $this->subscriptionService->getUsageOverview($request->user());
        return ApiResponse::success($usage, 'Usage retrieved successfully');
    }

    public function changePlan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
        $subscription = $this->subscriptionService->subscribeAcademyToPlan($request->user(), $plan, 0);

        return ApiResponse::success($subscription, 'Plan changed successfully');
    }

    public function cancel(Request $request): JsonResponse
    {
        $subscription = $this->subscriptionService->cancelSubscription($request->user());
        return ApiResponse::success($subscription, 'Subscription canceled successfully');
    }

    public function renew(Request $request): JsonResponse
    {
        $subscription = $this->subscriptionService->renewSubscription($request->user());
        return ApiResponse::success($subscription, 'Subscription renewed successfully');
    }

    public function invoices(Request $request): JsonResponse
    {
        $subscription = $this->subscriptionService->getAcademySubscription($request->user());
        
        if (!$subscription) {
            return ApiResponse::error('No active subscription found', [], 404);
        }

        $invoices = $subscription->invoices()
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return ApiResponse::success($invoices, 'Invoices retrieved successfully');
    }
}

