<?php

namespace Tests\Feature\Api\Phase5;

use Tests\TestCase;
use App\Models\GamificationRule;
use App\Models\GamificationLevel;
use App\Models\GamificationBadge;
use App\Models\GamificationPointsWallet;
use App\Services\GamificationService;
use Modules\ACL\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GamificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'GamificationSeeder']);
    }

    public function test_gamification_levels_are_seeded(): void
    {
        $levels = GamificationLevel::all();
        $this->assertGreaterThan(0, $levels->count());
        $this->assertTrue($levels->contains('name', 'Bronze'));
        $this->assertTrue($levels->contains('name', 'Gold'));
    }

    public function test_gamification_rules_are_seeded(): void
    {
        $rules = GamificationRule::where('active', true)->get();
        $this->assertGreaterThan(0, $rules->count());
        $this->assertTrue($rules->contains('code', 'enrollment_first_program'));
        $this->assertTrue($rules->contains('code', 'attendance_present'));
    }

    public function test_gamification_badges_are_seeded(): void
    {
        $badges = GamificationBadge::where('active', true)->get();
        $this->assertGreaterThan(0, $badges->count());
        $this->assertTrue($badges->contains('code', 'top_student'));
    }

    public function test_award_points_for_enrollment(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found in database');
            return;
        }

        $rule = GamificationRule::where('code', 'enrollment_first_program')->first();
        $this->assertNotNull($rule);

        $service = app(GamificationService::class);
        $event = $service->awardPointsForEvent(
            $student,
            'enrollment_first_program',
            'enrollments',
            1,
            ['program_id' => 1]
        );

        $this->assertNotNull($event);
        $this->assertEquals($rule->points, $event->points_awarded);

        $wallet = GamificationPointsWallet::where('user_id', $student->id)->first();
        $this->assertNotNull($wallet);
        $this->assertEquals($rule->points, $wallet->total_points);
    }

    public function test_user_level_is_calculated(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found in database');
            return;
        }

        $service = app(GamificationService::class);
        
        // Award enough points to reach Silver level (500 points)
        for ($i = 0; $i < 50; $i++) {
            try {
                $service->awardPointsForEvent(
                    $student,
                    'attendance_present',
                    'attendance',
                    $i + 1,
                    []
                );
            } catch (\Exception $e) {
                // Skip if max times reached
            }
        }

        $wallet = GamificationPointsWallet::where('user_id', $student->id)->first();
        $this->assertNotNull($wallet);
        $this->assertNotNull($wallet->level_id);
        
        $level = $wallet->level;
        $this->assertNotNull($level);
        $this->assertGreaterThanOrEqual(500, $wallet->total_points);
    }

    public function test_student_can_get_gamification_summary(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found in database');
            return;
        }

        $response = $this->actingAs($student, 'api')
            ->getJson('/api/student/gamification/summary');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'wallet',
                'badges',
                'recent_events',
                'rank',
            ],
        ]);
    }

    public function test_student_can_get_leaderboard(): void
    {
        $student = User::whereHas('role', fn($q) => $q->where('name', 'student'))->first();
        if (!$student) {
            $this->markTestSkipped('No student users found in database');
            return;
        }

        $response = $this->actingAs($student, 'api')
            ->getJson('/api/student/gamification/leaderboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'rank',
                    'user',
                    'total_points',
                    'level',
                ],
            ],
        ]);
    }

    public function test_admin_can_list_gamification_rules(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if (!$admin) {
            $this->markTestSkipped('No admin users found in database');
            return;
        }

        $response = $this->actingAs($admin, 'api')
            ->getJson('/api/admin/gamification/rules');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'code',
                        'name',
                        'points',
                        'active',
                    ],
                ],
            ],
        ]);
    }
}

