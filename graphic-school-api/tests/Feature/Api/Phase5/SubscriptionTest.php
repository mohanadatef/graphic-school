<?php

namespace Tests\Feature\Api\Phase5;

use Tests\TestCase;
use App\Models\SubscriptionPlan;
use App\Models\AcademySubscription;
use App\Models\SubscriptionUsageTracker;
use App\Models\SubscriptionInvoice;
use App\Services\SubscriptionService;
use Modules\ACL\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'SubscriptionSeeder']);
    }

    public function test_subscription_plans_are_seeded(): void
    {
        $plans = SubscriptionPlan::all();
        $this->assertGreaterThanOrEqual(4, $plans->count());
        $this->assertDatabaseHas('subscription_plans', ['code' => 'basic']);
        $this->assertDatabaseHas('subscription_plans', ['code' => 'premium']);
    }

    public function test_hq_can_list_plans(): void
    {
        $hqUser = User::whereHas('role', fn($q) => $q->where('name', 'hq'))->first();
        if (!$hqUser) {
            $this->markTestSkipped('No HQ user found');
            return;
        }

        $response = $this->actingAs($hqUser, 'api')
            ->getJson('/api/hq/plans');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'code', 'price_monthly', 'price_yearly'],
            ],
        ]);
    }

    public function test_hq_can_create_plan(): void
    {
        $hqUser = User::whereHas('role', fn($q) => $q->where('name', 'hq'))->first();
        if (!$hqUser) {
            $this->markTestSkipped('No HQ user found');
            return;
        }

        $response = $this->actingAs($hqUser, 'api')
            ->postJson('/api/hq/plans', [
                'name' => 'Test Plan',
                'code' => 'test_plan',
                'price_monthly' => 100,
                'price_yearly' => 1000,
                'currency' => 'EGP',
                'description' => 'Test plan description',
                'limits' => [
                    'students' => 100,
                    'programs' => 10,
                ],
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('subscription_plans', ['code' => 'test_plan']);
    }

    public function test_subscription_service_can_subscribe_academy(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        $plan = SubscriptionPlan::where('code', 'basic')->first();
        if (!$plan) {
            $this->markTestSkipped('No basic plan found');
            return;
        }

        $subscriptionService = app(SubscriptionService::class);
        $subscription = $subscriptionService->subscribeAcademyToPlan($admin, $plan, 14);

        $this->assertNotNull($subscription);
        $this->assertEquals('trial', $subscription->status);
        $this->assertDatabaseHas('academy_subscriptions', [
            'academy_id' => $admin->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_usage_tracker_is_created_on_subscription(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        $plan = SubscriptionPlan::where('code', 'basic')->first();
        if (!$plan) {
            $this->markTestSkipped('No basic plan found');
            return;
        }

        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribeAcademyToPlan($admin, $plan, 0);

        $tracker = SubscriptionUsageTracker::where('academy_id', $admin->id)
            ->where('key', 'students')
            ->first();

        $this->assertNotNull($tracker);
        $this->assertEquals($plan->getLimit('students', 0), $tracker->limit);
    }

    public function test_usage_limit_check_blocks_when_exceeded(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        $plan = SubscriptionPlan::where('code', 'basic')->first();
        if (!$plan) {
            $this->markTestSkipped('No basic plan found');
            return;
        }

        $subscriptionService = app(SubscriptionService::class);
        $subscriptionService->subscribeAcademyToPlan($admin, $plan, 0);

        // Set usage to limit
        $tracker = SubscriptionUsageTracker::where('academy_id', $admin->id)
            ->where('key', 'programs')
            ->first();
        $tracker->update(['used' => $tracker->limit]);

        // Try to check limit - should fail
        $this->expectException(\Exception::class);
        $subscriptionService->blockIfOverLimit($admin, 'programs');
    }

    public function test_invoice_is_generated_on_renewal(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found');
            return;
        }

        $plan = SubscriptionPlan::where('code', 'standard')->first();
        if (!$plan) {
            $this->markTestSkipped('No standard plan found');
            return;
        }

        $subscriptionService = app(SubscriptionService::class);
        $subscription = $subscriptionService->subscribeAcademyToPlan($admin, $plan, 0);
        
        // Renew subscription
        $subscriptionService->renewSubscription($admin);

        $invoice = SubscriptionInvoice::where('academy_id', $admin->id)
            ->where('plan_id', $plan->id)
            ->first();

        $this->assertNotNull($invoice);
        $this->assertEquals($plan->price_monthly, $invoice->amount);
    }
}

