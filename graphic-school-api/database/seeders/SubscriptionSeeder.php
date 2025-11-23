<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\AcademySubscription;
use App\Models\SubscriptionUsageTracker;
use App\Services\SubscriptionService;
use Modules\ACL\Users\Models\User;
use Carbon\Carbon;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPlans();
        $this->seedSubscriptions();
    }

    protected function seedPlans(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'code' => 'basic',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'currency' => 'EGP',
                'description' => 'Perfect for small academies getting started',
                'features' => [
                    'Up to 50 students',
                    '5 programs',
                    '10 batches',
                    '20 groups',
                    '500 MB storage',
                    '100 community posts',
                    'Basic support',
                ],
                'limits' => [
                    'students' => 50,
                    'programs' => 5,
                    'batches' => 10,
                    'groups' => 20,
                    'storage_mb' => 500,
                    'community_posts' => 100,
                    'certificates' => 50,
                    'assignment_submissions' => 500,
                    'pages' => 3,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Standard',
                'code' => 'standard',
                'price_monthly' => 500,
                'price_yearly' => 5000,
                'currency' => 'EGP',
                'description' => 'Ideal for growing academies',
                'features' => [
                    'Up to 200 students',
                    '20 programs',
                    '50 batches',
                    '100 groups',
                    '5 GB storage',
                    '1000 community posts',
                    'Priority support',
                    'Advanced analytics',
                ],
                'limits' => [
                    'students' => 200,
                    'programs' => 20,
                    'batches' => 50,
                    'groups' => 100,
                    'storage_mb' => 5000,
                    'community_posts' => 1000,
                    'certificates' => 500,
                    'assignment_submissions' => 5000,
                    'pages' => 10,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Premium',
                'code' => 'premium',
                'price_monthly' => 1500,
                'price_yearly' => 15000,
                'currency' => 'EGP',
                'description' => 'For established academies with high demand',
                'features' => [
                    'Up to 1000 students',
                    'Unlimited programs',
                    'Unlimited batches',
                    'Unlimited groups',
                    '50 GB storage',
                    'Unlimited community posts',
                    '24/7 support',
                    'Advanced analytics',
                    'Custom branding',
                    'API access',
                ],
                'limits' => [
                    'students' => 1000,
                    'programs' => 999999, // Unlimited
                    'batches' => 999999,
                    'groups' => 999999,
                    'storage_mb' => 50000,
                    'community_posts' => 999999,
                    'certificates' => 999999,
                    'assignment_submissions' => 999999,
                    'pages' => 999999, // Unlimited
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'code' => 'enterprise',
                'price_monthly' => 5000,
                'price_yearly' => 50000,
                'currency' => 'EGP',
                'description' => 'For large organizations with custom needs',
                'features' => [
                    'Unlimited students',
                    'Unlimited programs',
                    'Unlimited batches',
                    'Unlimited groups',
                    'Unlimited storage',
                    'Unlimited community posts',
                    'Dedicated support',
                    'Advanced analytics',
                    'Custom branding',
                    'API access',
                    'White-label option',
                    'Custom integrations',
                ],
                'limits' => [
                    'students' => 999999,
                    'programs' => 999999,
                    'batches' => 999999,
                    'groups' => 999999,
                    'storage_mb' => 999999,
                    'community_posts' => 999999,
                    'certificates' => 999999,
                    'assignment_submissions' => 999999,
                    'pages' => 999999, // Unlimited
                ],
                'is_active' => true,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['code' => $planData['code']],
                $planData
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }

    protected function seedSubscriptions(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        
        // Get admin users (academies)
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))
            ->take(3)
            ->get();

        if ($admins->isEmpty()) {
            $this->command->warn('No admin users found. Skipping subscription seeding.');
            return;
        }

        $plans = SubscriptionPlan::all();

        foreach ($admins as $index => $admin) {
            $plan = $plans->get($index % $plans->count());
            
            // Subscribe academy to plan with 14-day trial
            $subscription = $subscriptionService->subscribeAcademyToPlan($admin, $plan, 14);

            // Set some usage for demo
            if ($index === 0) {
                // First academy: 60% usage
                $this->setUsage($admin->id, 'students', $plan->getLimit('students', 0) * 0.6);
                $this->setUsage($admin->id, 'programs', $plan->getLimit('programs', 0) * 0.6);
                $this->setUsage($admin->id, 'community_posts', $plan->getLimit('community_posts', 0) * 0.6);
            }
        }

        $this->command->info('Academy subscriptions seeded successfully!');
    }

    protected function setUsage(int $academyId, string $key, float $used): void
    {
        $tracker = SubscriptionUsageTracker::where('academy_id', $academyId)
            ->where('key', $key)
            ->first();

        if ($tracker) {
            $tracker->update(['used' => (int) $used]);
        }
    }
}

