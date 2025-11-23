<?php

namespace App\Services;

use App\Models\GamificationEvent;
use App\Models\GamificationLevel;
use App\Models\GamificationPointsWallet;
use App\Models\GamificationRule;
use App\Models\GamificationUserBadge;
use App\Models\GamificationBadge;
use Modules\ACL\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GamificationService
{
    /**
     * Award points for an event
     */
    public function awardPointsForEvent(
        User $user,
        string $ruleCode,
        ?string $referenceTable = null,
        ?int $referenceId = null,
        array $meta = []
    ): GamificationEvent {
        return DB::transaction(function () use ($user, $ruleCode, $referenceTable, $referenceId, $meta) {
            $rule = GamificationRule::where('code', $ruleCode)
                ->where('active', true)
                ->first();

            if (!$rule) {
                Log::warning("Gamification rule not found: {$ruleCode}");
                throw new \Exception("Gamification rule not found: {$ruleCode}");
            }

            // Check max times per period if set
            if ($rule->max_times_per_period) {
                $recentCount = GamificationEvent::where('user_id', $user->id)
                    ->where('rule_id', $rule->id)
                    ->where('created_at', '>=', now()->subDay())
                    ->count();

                if ($recentCount >= $rule->max_times_per_period) {
                    Log::info("Max times per period reached for rule {$ruleCode} for user {$user->id}");
                    throw new \Exception("Max times per period reached for this rule");
                }
            }

            // Create event
            $event = GamificationEvent::create([
                'user_id' => $user->id,
                'rule_id' => $rule->id,
                'event_type' => $this->getEventTypeFromRuleCode($ruleCode),
                'reference_table' => $referenceTable,
                'reference_id' => $referenceId,
                'points_awarded' => $rule->points,
                'meta' => $meta,
            ]);

            // Update wallet
            $wallet = GamificationPointsWallet::firstOrCreate(
                ['user_id' => $user->id],
                ['total_points' => 0]
            );

            $wallet->incrementPoints($rule->points);

            // Recalculate level
            $this->recalculateUserLevel($user->id);

            // Check badges
            $this->checkAndAwardBadges($user->id);

            return $event;
        });
    }

    /**
     * Recalculate user level based on total points
     */
    public function recalculateUserLevel(int $userId): void
    {
        $wallet = GamificationPointsWallet::where('user_id', $userId)->first();
        if (!$wallet) {
            return;
        }

        $level = GamificationLevel::getLevelForPoints($wallet->total_points);
        $wallet->update(['level_id' => $level?->id]);
    }

    /**
     * Check and award badges based on conditions
     */
    public function checkAndAwardBadges(int $userId): void
    {
        $user = User::findOrFail($userId);
        $wallet = GamificationPointsWallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return;
        }

        $badges = GamificationBadge::where('active', true)->get();

        foreach ($badges as $badge) {
            // Check if user already has this badge
            $hasBadge = GamificationUserBadge::where('user_id', $userId)
                ->where('badge_id', $badge->id)
                ->exists();

            if ($hasBadge) {
                continue;
            }

            // Check badge conditions
            if ($this->checkBadgeCondition($badge, $user, $wallet)) {
                GamificationUserBadge::create([
                    'user_id' => $userId,
                    'badge_id' => $badge->id,
                    'awarded_at' => now(),
                    'meta' => [],
                ]);
            }
        }
    }

    /**
     * Check if badge condition is met
     */
    protected function checkBadgeCondition(GamificationBadge $badge, User $user, GamificationPointsWallet $wallet): bool
    {
        switch ($badge->code) {
            case 'perfect_attendance':
                // Check if user has perfect attendance for current month
                return $this->checkPerfectAttendance($user);
            
            case 'assignment_master':
                // Check if user has submitted all assignments with high scores
                return $this->checkAssignmentMaster($user);
            
            case 'top_student':
                // Check if user is in top 10% of leaderboard
                return $this->checkTopStudent($user);
            
            case 'early_bird':
                // Check if user was first to submit an assignment
                return $this->checkEarlyBird($user);
            
            case 'first_certificate':
                // Check if user has received their first certificate
                return $this->checkFirstCertificate($user);
            
            default:
                return false;
        }
    }

    protected function checkPerfectAttendance(User $user): bool
    {
        // Simplified: check if user has attended all sessions in current month
        // This is a placeholder - implement based on actual attendance logic
        return false;
    }

    protected function checkAssignmentMaster(User $user): bool
    {
        // Check if user has submitted assignments with average score > 90%
        // This is a placeholder - implement based on actual assignment logic
        return false;
    }

    protected function checkTopStudent(User $user): bool
    {
        $totalUsers = GamificationPointsWallet::count();
        if ($totalUsers < 10) {
            return false;
        }

        $userRank = $this->getUserRank($user->id);
        $topPercent = ceil($totalUsers * 0.1);
        
        return $userRank <= $topPercent;
    }

    protected function checkEarlyBird(User $user): bool
    {
        // Check if user was first to submit any assignment
        // This is a placeholder - implement based on actual assignment submission logic
        return false;
    }

    protected function checkFirstCertificate(User $user): bool
    {
        // Check if user has at least one certificate
        if (!class_exists(\App\Models\Certificate::class)) {
            return false;
        }
        
        return \App\Models\Certificate::where('student_id', $user->id)->exists();
    }

    /**
     * Get user gamification summary
     */
    public function getUserGamificationSummary(int $userId): array
    {
        $wallet = GamificationPointsWallet::with('level')
            ->where('user_id', $userId)
            ->first();

        if (!$wallet) {
            $wallet = GamificationPointsWallet::create([
                'user_id' => $userId,
                'total_points' => 0,
            ]);
            $wallet->load('level');
        }

        $badges = GamificationUserBadge::with('badge')
            ->where('user_id', $userId)
            ->orderBy('awarded_at', 'desc')
            ->get();

        $recentEvents = GamificationEvent::with('rule')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $rank = $this->getUserRank($userId);

        return [
            'wallet' => [
                'total_points' => $wallet->total_points,
                'level' => $wallet->level ? [
                    'id' => $wallet->level->id,
                    'name' => $wallet->level->name,
                    'color' => $wallet->level->color,
                    'icon' => $wallet->level->icon,
                ] : null,
            ],
            'badges' => $badges->map(function ($userBadge) {
                return [
                    'id' => $userBadge->badge->id,
                    'code' => $userBadge->badge->code,
                    'name' => $userBadge->badge->name,
                    'description' => $userBadge->badge->description,
                    'icon' => $userBadge->badge->icon,
                    'awarded_at' => $userBadge->awarded_at,
                ];
            }),
            'recent_events' => $recentEvents->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_type' => $event->event_type,
                    'points_awarded' => $event->points_awarded,
                    'rule' => $event->rule ? [
                        'code' => $event->rule->code,
                        'name' => $event->rule->name,
                    ] : null,
                    'created_at' => $event->created_at,
                ];
            }),
            'rank' => $rank,
        ];
    }

    /**
     * Get leaderboard
     */
    public function getLeaderboard(?int $programId = null, int $limit = 50): array
    {
        $query = GamificationPointsWallet::with(['user', 'level'])
            ->orderBy('total_points', 'desc')
            ->limit($limit);

        // If program_id is provided, filter by users enrolled in that program
        if ($programId) {
            $query->whereHas('user', function ($q) use ($programId) {
                $q->whereHas('enrollments', function ($enrollmentQuery) use ($programId) {
                    $enrollmentQuery->where('program_id', $programId)
                        ->where('status', 'approved');
                });
            });
        }

        $wallets = $query->get();

        return $wallets->map(function ($wallet, $index) {
            return [
                'rank' => $index + 1,
                'user' => [
                    'id' => $wallet->user->id,
                    'name' => $wallet->user->name,
                    'email' => $wallet->user->email,
                ],
                'total_points' => $wallet->total_points,
                'level' => $wallet->level ? [
                    'id' => $wallet->level->id,
                    'name' => $wallet->level->name,
                    'color' => $wallet->level->color,
                ] : null,
            ];
        })->toArray();
    }

    /**
     * Get user rank
     */
    public function getUserRank(int $userId): int
    {
        $wallet = GamificationPointsWallet::where('user_id', $userId)->first();
        if (!$wallet) {
            return 0;
        }

        $rank = GamificationPointsWallet::where('total_points', '>', $wallet->total_points)
            ->count();

        return $rank + 1;
    }

    /**
     * Get event type from rule code
     */
    protected function getEventTypeFromRuleCode(string $ruleCode): string
    {
        if (str_contains($ruleCode, 'enrollment')) {
            return 'enrollment';
        }
        if (str_contains($ruleCode, 'attendance')) {
            return 'attendance';
        }
        if (str_contains($ruleCode, 'assignment')) {
            return 'assignment';
        }
        if (str_contains($ruleCode, 'certificate')) {
            return 'certificate';
        }
        if (str_contains($ruleCode, 'payment')) {
            return 'payment';
        }
        if (str_contains($ruleCode, 'community')) {
            return 'community';
        }

        return 'other';
    }
}

