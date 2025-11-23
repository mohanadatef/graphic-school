<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\AcademySubscription;
use App\Models\SubscriptionUsageTracker;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPayment;
use Modules\ACL\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionService
{
    protected $notificationService;

    public function __construct()
    {
        // Notification service will be injected when available
    }

    /**
     * Subscribe academy to a plan
     */
    public function subscribeAcademyToPlan(User $academy, SubscriptionPlan $plan, int $trialDays = 14): AcademySubscription
    {
        return DB::transaction(function () use ($academy, $plan, $trialDays) {
            // Cancel existing subscription if any
            $existing = AcademySubscription::where('academy_id', $academy->id)
                ->where('status', '!=', 'canceled')
                ->first();

            if ($existing) {
                $existing->update(['status' => 'canceled']);
            }

            $now = now();
            $trialEndsAt = $trialDays > 0 ? $now->copy()->addDays($trialDays) : null;
            $expiresAt = $trialEndsAt ? $trialEndsAt->copy() : $now->copy()->addMonth();

            $subscription = AcademySubscription::create([
                'academy_id' => $academy->id,
                'plan_id' => $plan->id,
                'status' => $trialDays > 0 ? 'trial' : 'active',
                'started_at' => $now,
                'expires_at' => $expiresAt,
                'trial_ends_at' => $trialEndsAt,
                'auto_renew' => true,
                'next_billing_date' => $trialEndsAt ? $trialEndsAt->copy() : $now->copy()->addMonth(),
            ]);

            // Initialize usage trackers from plan limits
            $this->initializeUsageTrackers($academy->id, $plan);

            // Notify academy
            $this->notifySubscriptionCreated($subscription);

            return $subscription->load(['plan']);
        });
    }

    /**
     * Renew subscription
     */
    public function renewSubscription(User $academy): AcademySubscription
    {
        return DB::transaction(function () use ($academy) {
            $subscription = AcademySubscription::where('academy_id', $academy->id)
                ->where('status', '!=', 'canceled')
                ->firstOrFail();

            if (!$subscription->auto_renew) {
                throw new \Exception('Auto-renew is disabled for this subscription');
            }

            $plan = $subscription->plan;
            $billingPeriod = $subscription->next_billing_date ? 
                ($subscription->expires_at->diffInMonths($subscription->started_at) >= 12 ? 'yearly' : 'monthly') 
                : 'monthly';

            // Generate invoice
            $invoice = $this->generateInvoice($academy, $plan, $billingPeriod);

            // For now, assume payment is successful (mock)
            // In production, this would be handled by payment gateway
            $this->recordPayment($invoice, [
                'status' => 'success',
                'reference_code' => 'RENEW-' . uniqid(),
                'amount' => $invoice->amount,
            ]);

            // Update subscription
            $newExpiresAt = $subscription->expires_at->copy();
            if ($billingPeriod === 'yearly') {
                $newExpiresAt->addYear();
            } else {
                $newExpiresAt->addMonth();
            }

            $subscription->update([
                'status' => 'active',
                'expires_at' => $newExpiresAt,
                'next_billing_date' => $newExpiresAt->copy(),
            ]);

            // Update usage trackers if plan changed
            $this->updateUsageTrackers($academy->id, $plan);

            $this->notifySubscriptionRenewed($subscription);

            return $subscription->fresh();
        });
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(User $academy): AcademySubscription
    {
        $subscription = AcademySubscription::where('academy_id', $academy->id)
            ->where('status', '!=', 'canceled')
            ->firstOrFail();

        $subscription->update([
            'status' => 'canceled',
            'auto_renew' => false,
        ]);

        $this->notifySubscriptionCanceled($subscription);

        return $subscription->fresh();
    }

    /**
     * Suspend subscription (HQ control)
     */
    public function suspendSubscription(User $academy): AcademySubscription
    {
        $subscription = AcademySubscription::where('academy_id', $academy->id)
            ->where('status', '!=', 'canceled')
            ->firstOrFail();

        $subscription->update(['status' => 'suspended']);

        $this->notifySubscriptionSuspended($subscription);

        return $subscription->fresh();
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(User $academy): AcademySubscription
    {
        $subscription = AcademySubscription::where('academy_id', $academy->id)
            ->whereIn('status', ['suspended', 'expired'])
            ->firstOrFail();

        $status = $subscription->expires_at > now() ? 'active' : 'expired';
        $subscription->update(['status' => $status]);

        $this->notifySubscriptionResumed($subscription);

        return $subscription->fresh();
    }

    /**
     * Generate invoice
     */
    public function generateInvoice(User $academy, SubscriptionPlan $plan, string $billingPeriod): SubscriptionInvoice
    {
        $amount = $billingPeriod === 'yearly' ? $plan->price_yearly : $plan->price_monthly;

        $invoice = SubscriptionInvoice::create([
            'academy_id' => $academy->id,
            'plan_id' => $plan->id,
            'amount' => $amount,
            'currency' => $plan->currency,
            'status' => 'unpaid',
            'billing_period' => $billingPeriod,
        ]);

        $this->notifyInvoiceCreated($invoice);

        return $invoice;
    }

    /**
     * Record payment
     */
    public function recordPayment(SubscriptionInvoice $invoice, array $paymentData): SubscriptionPayment
    {
        $payment = SubscriptionPayment::create([
            'invoice_id' => $invoice->id,
            'method_id' => $paymentData['method_id'] ?? null,
            'status' => $paymentData['status'] ?? 'pending',
            'reference_code' => $paymentData['reference_code'] ?? null,
            'amount' => $paymentData['amount'] ?? $invoice->amount,
        ]);

        if ($payment->isSuccessful()) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $this->notifyPaymentSuccessful($invoice);
        } else {
            $invoice->update(['status' => 'failed']);
            $this->notifyPaymentFailed($invoice);
        }

        return $payment;
    }

    /**
     * Check usage limit
     */
    public function checkUsageLimit(User $academy, string $key): bool
    {
        $tracker = SubscriptionUsageTracker::where('academy_id', $academy->id)
            ->where('key', $key)
            ->first();

        if (!$tracker) {
            return true; // No limit set
        }

        return !$tracker->isExceeded();
    }

    /**
     * Get usage tracker
     */
    public function getUsageTracker(User $academy, string $key): ?SubscriptionUsageTracker
    {
        return SubscriptionUsageTracker::where('academy_id', $academy->id)
            ->where('key', $key)
            ->first();
    }

    /**
     * Increment usage
     */
    public function incrementUsage(User $academy, string $key, int $amount = 1): void
    {
        $tracker = SubscriptionUsageTracker::where('academy_id', $academy->id)
            ->where('key', $key)
            ->first();

        if ($tracker) {
            $tracker->incrementUsage($amount);
            
            // Check if approaching limit (80%)
            if ($tracker->getUsagePercentage() >= 80 && $tracker->getUsagePercentage() < 100) {
                $this->notifyUsageLimitWarning($academy, $key, $tracker);
            }
        }
    }

    /**
     * Decrement usage
     */
    public function decrementUsage(User $academy, string $key, int $amount = 1): void
    {
        $tracker = SubscriptionUsageTracker::where('academy_id', $academy->id)
            ->where('key', $key)
            ->first();

        if ($tracker) {
            $tracker->decrementUsage($amount);
        }
    }

    /**
     * Block action if over limit
     */
    public function blockIfOverLimit(User $academy, string $key): void
    {
        if (!$this->checkUsageLimit($academy, $key)) {
            $tracker = $this->getUsageTracker($academy, $key);
            throw new \Exception("Limit exceeded for {$key}. Current: {$tracker->used}/{$tracker->limit}. Please upgrade your plan.");
        }
    }

    /**
     * Check if academy can perform action
     */
    public function canPerformAction(User $academy, string $feature): bool
    {
        $subscription = AcademySubscription::where('academy_id', $academy->id)
            ->where('status', '!=', 'canceled')
            ->first();

        if (!$subscription) {
            return false;
        }

        if ($subscription->isExpired() || $subscription->status === 'suspended') {
            return false;
        }

        $plan = $subscription->plan;
        return $plan->hasFeature($feature);
    }

    /**
     * Initialize usage trackers from plan limits
     */
    protected function initializeUsageTrackers(int $academyId, SubscriptionPlan $plan): void
    {
        if (!$plan->limits || !is_array($plan->limits)) {
            return;
        }

        foreach ($plan->limits as $key => $limit) {
            SubscriptionUsageTracker::updateOrCreate(
                [
                    'academy_id' => $academyId,
                    'key' => $key,
                ],
                [
                    'limit' => $limit,
                    'used' => 0,
                ]
            );
        }
    }

    /**
     * Update usage trackers when plan changes
     */
    protected function updateUsageTrackers(int $academyId, SubscriptionPlan $plan): void
    {
        if (!$plan->limits || !is_array($plan->limits)) {
            return;
        }

        foreach ($plan->limits as $key => $limit) {
            SubscriptionUsageTracker::updateOrCreate(
                [
                    'academy_id' => $academyId,
                    'key' => $key,
                ],
                [
                    'limit' => $limit,
                ]
            );
        }
    }

    /**
     * Get academy subscription
     */
    public function getAcademySubscription(User $academy): ?AcademySubscription
    {
        return AcademySubscription::where('academy_id', $academy->id)
            ->where('status', '!=', 'canceled')
            ->with(['plan', 'usageTrackers'])
            ->first();
    }

    /**
     * Get usage overview
     */
    public function getUsageOverview(User $academy): array
    {
        $subscription = $this->getAcademySubscription($academy);
        if (!$subscription) {
            return [];
        }

        $trackers = SubscriptionUsageTracker::where('academy_id', $academy->id)->get();

        return $trackers->map(function ($tracker) {
            return [
                'key' => $tracker->key,
                'used' => $tracker->used,
                'limit' => $tracker->limit,
                'percentage' => $tracker->getUsagePercentage(),
                'is_exceeded' => $tracker->isExceeded(),
            ];
        })->toArray();
    }

    /**
     * Check and handle expired subscriptions
     */
    public function checkExpiredSubscriptions(): void
    {
        $expired = AcademySubscription::where('expires_at', '<=', now())
            ->whereIn('status', ['active', 'trial'])
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);
            $this->notifySubscriptionExpired($subscription);
        }
    }

    /**
     * Check and handle trial ending soon
     */
    public function checkTrialEndingSoon(): void
    {
        $endingSoon = AcademySubscription::where('status', 'trial')
            ->where('trial_ends_at', '<=', now()->addDays(3))
            ->where('trial_ends_at', '>', now())
            ->get();

        foreach ($endingSoon as $subscription) {
            $this->notifyTrialEndingSoon($subscription);
        }
    }

    /**
     * Notification methods
     */
    protected function notifySubscriptionCreated(AcademySubscription $subscription): void
    {
        $this->sendNotification($subscription->academy_id, 'subscription_created', 'Subscription Created', 'Your subscription has been activated.');
    }

    protected function notifySubscriptionRenewed(AcademySubscription $subscription): void
    {
        $this->sendNotification($subscription->academy_id, 'subscription_renewed', 'Subscription Renewed', 'Your subscription has been renewed successfully.');
    }

    protected function notifySubscriptionCanceled(AcademySubscription $subscription): void
    {
        $this->sendNotification($subscription->academy_id, 'subscription_canceled', 'Subscription Canceled', 'Your subscription has been canceled.');
    }

    protected function notifySubscriptionSuspended(AcademySubscription $subscription): void
    {
        $this->sendNotification($subscription->academy_id, 'subscription_suspended', 'Subscription Suspended', 'Your subscription has been suspended by admin.');
    }

    protected function notifySubscriptionResumed(AcademySubscription $subscription): void
    {
        $this->sendNotification($subscription->academy_id, 'subscription_resumed', 'Subscription Resumed', 'Your subscription has been resumed.');
    }

    protected function notifySubscriptionExpired(AcademySubscription $subscription): void
    {
        $this->sendNotification($subscription->academy_id, 'subscription_expired', 'Subscription Expired', 'Your subscription has expired. Please renew to continue using the platform.');
    }

    protected function notifyTrialEndingSoon(AcademySubscription $subscription): void
    {
        $days = $subscription->daysUntilTrialEnds();
        $this->sendNotification($subscription->academy_id, 'trial_ending_soon', 'Trial Ending Soon', "Your trial period ends in {$days} days. Please upgrade to continue.");
    }

    protected function notifyInvoiceCreated(SubscriptionInvoice $invoice): void
    {
        $this->sendNotification($invoice->academy_id, 'invoice_created', 'New Invoice', "A new invoice has been generated for {$invoice->amount} {$invoice->currency}.");
    }

    protected function notifyPaymentSuccessful(SubscriptionInvoice $invoice): void
    {
        $this->sendNotification($invoice->academy_id, 'payment_successful', 'Payment Successful', 'Your payment has been processed successfully.');
    }

    protected function notifyPaymentFailed(SubscriptionInvoice $invoice): void
    {
        $this->sendNotification($invoice->academy_id, 'payment_failed', 'Payment Failed', 'Your payment could not be processed. Please try again.');
    }

    protected function notifyUsageLimitWarning(User $academy, string $key, SubscriptionUsageTracker $tracker): void
    {
        $this->sendNotification($academy->id, 'usage_limit_warning', 'Usage Limit Warning', "You have used {$tracker->getUsagePercentage()}% of your {$key} limit.");
    }

    protected function sendNotification(int $userId, string $type, string $title, string $message): void
    {
        try {
            if (class_exists(\Modules\Core\Notification\Services\NotificationService::class)) {
                $notificationService = app(\Modules\Core\Notification\Services\NotificationService::class);
                $notificationService->create([
                    'user_id' => $userId,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification', ['error' => $e->getMessage()]);
        }
    }
}

