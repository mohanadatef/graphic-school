<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\AcademySubscription;
use App\Services\SubscriptionService;
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
        $subscriptions = AcademySubscription::with(['academy', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return ApiResponse::success($subscriptions, 'Subscriptions retrieved successfully');
    }

    public function suspend(int $id): JsonResponse
    {
        $subscription = AcademySubscription::findOrFail($id);
        $subscription = $this->subscriptionService->suspendSubscription($subscription->academy);
        return ApiResponse::success($subscription, 'Subscription suspended successfully');
    }

    public function resume(int $id): JsonResponse
    {
        $subscription = AcademySubscription::findOrFail($id);
        $subscription = $this->subscriptionService->resumeSubscription($subscription->academy);
        return ApiResponse::success($subscription, 'Subscription resumed successfully');
    }

    public function usage(int $id): JsonResponse
    {
        $subscription = AcademySubscription::findOrFail($id);
        $usage = $this->subscriptionService->getUsageOverview($subscription->academy);
        return ApiResponse::success($usage, 'Usage retrieved successfully');
    }
}

