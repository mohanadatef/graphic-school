<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GamificationLevel;
use App\Models\GamificationRule;
use App\Models\GamificationBadge;
use App\Models\GamificationPointsWallet;
use App\Services\GamificationService;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Enrollments\Models\Enrollment;
use Modules\LMS\Sessions\Models\Session;
use App\Models\Attendance;
use App\Models\AssignmentSubmission;
use App\Models\PaymentTransaction;
use Modules\LMS\Certificates\Models\Certificate;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLevels();
        $this->seedRules();
        $this->seedBadges();
        // Award points to students - disabled for now, points will be awarded automatically via service integration
        // $this->awardPointsToStudents();
        $this->command->info('Gamification levels, rules, and badges seeded successfully.');
        $this->command->info('Points will be awarded automatically when students perform actions (enrollment, attendance, assignments, payments, certificates).');
    }

    protected function seedLevels(): void
    {
        $levels = [
            [
                'name' => 'Bronze',
                'min_points' => 0,
                'max_points' => 499,
                'color' => '#cd7f32',
                'icon' => '🥉',
            ],
            [
                'name' => 'Silver',
                'min_points' => 500,
                'max_points' => 999,
                'color' => '#c0c0c0',
                'icon' => '🥈',
            ],
            [
                'name' => 'Gold',
                'min_points' => 1000,
                'max_points' => 1999,
                'color' => '#ffd700',
                'icon' => '🥇',
            ],
            [
                'name' => 'Platinum',
                'min_points' => 2000,
                'max_points' => 3499,
                'color' => '#e5e4e2',
                'icon' => '💎',
            ],
            [
                'name' => 'Elite',
                'min_points' => 3500,
                'max_points' => null,
                'color' => '#b9f2ff',
                'icon' => '👑',
            ],
        ];

        foreach ($levels as $level) {
            GamificationLevel::updateOrCreate(
                ['name' => $level['name']],
                $level
            );
        }

        $this->command->info('Gamification levels seeded successfully!');
    }

    protected function seedRules(): void
    {
        $rules = [
            [
                'code' => 'enrollment_first_program',
                'name' => 'First Program Enrollment',
                'description' => 'Awarded when a student enrolls in their first program',
                'points' => 100,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
            [
                'code' => 'attendance_present',
                'name' => 'Session Attendance',
                'description' => 'Awarded for attending a session',
                'points' => 10,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
            [
                'code' => 'assignment_submitted',
                'name' => 'Assignment Submission',
                'description' => 'Awarded for submitting an assignment',
                'points' => 25,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
            [
                'code' => 'assignment_perfect_score',
                'name' => 'Perfect Assignment Score',
                'description' => 'Awarded for achieving 95% or higher on an assignment',
                'points' => 50,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
            [
                'code' => 'payment_made',
                'name' => 'Payment Made',
                'description' => 'Awarded when a payment is successfully processed',
                'points' => 30,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
            [
                'code' => 'certificate_issued',
                'name' => 'Certificate Earned',
                'description' => 'Awarded when a certificate is issued',
                'points' => 200,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
            // Phase 5.2: Community rules
            [
                'code' => 'community_post',
                'name' => 'Community Post Created',
                'description' => 'Awarded for creating a community post',
                'points' => 20,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
            [
                'code' => 'community_comment',
                'name' => 'Community Comment Added',
                'description' => 'Awarded for adding a comment to a post',
                'points' => 10,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
            [
                'code' => 'community_reply',
                'name' => 'Community Reply Added',
                'description' => 'Awarded for replying to a comment',
                'points' => 5,
                'max_times_per_period' => null,
                'scope' => 'global',
                'active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            GamificationRule::updateOrCreate(
                ['code' => $rule['code']],
                $rule
            );
        }

        $this->command->info('Gamification rules seeded successfully!');
    }

    protected function seedBadges(): void
    {
        $badges = [
            [
                'code' => 'perfect_attendance',
                'name' => 'Perfect Attendance',
                'description' => 'Achieved perfect attendance for a full month',
                'icon' => '✅',
                'condition_type' => 'rule_based',
                'active' => true,
            ],
            [
                'code' => 'assignment_master',
                'name' => 'Assignment Master',
                'description' => 'Submitted all assignments with average score above 90%',
                'icon' => '📝',
                'condition_type' => 'rule_based',
                'active' => true,
            ],
            [
                'code' => 'top_student',
                'name' => 'Top Student',
                'description' => 'Ranked in top 10% of all students',
                'icon' => '🏆',
                'condition_type' => 'rule_based',
                'active' => true,
            ],
            [
                'code' => 'early_bird',
                'name' => 'Early Bird',
                'description' => 'First to submit an assignment',
                'icon' => '🐦',
                'condition_type' => 'rule_based',
                'active' => true,
            ],
            [
                'code' => 'first_certificate',
                'name' => 'First Certificate',
                'description' => 'Earned your first certificate',
                'icon' => '🎓',
                'condition_type' => 'rule_based',
                'active' => true,
            ],
        ];

        foreach ($badges as $badge) {
            GamificationBadge::updateOrCreate(
                ['code' => $badge['code']],
                $badge
            );
        }

        $this->command->info('Gamification badges seeded successfully!');
    }

    protected function awardPointsToStudents(): void
    {
        $gamificationService = app(GamificationService::class);
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))
            ->take(20)
            ->get();

        if ($students->isEmpty()) {
            $this->command->warn('No students found to award points to');
            return;
        }

        foreach ($students as $index => $student) {
            // Award enrollment points
            $enrollments = Enrollment::where('student_id', $student->id)
                ->where('status', 'approved')
                ->whereNotNull('program_id')
                ->take(1)
                ->get();

            foreach ($enrollments as $enrollment) {
                if (!$enrollment->id) continue;
                try {
                    $gamificationService->awardPointsForEvent(
                        $student,
                        'enrollment_first_program',
                        'enrollments',
                        (int) $enrollment->id,
                        ['program_id' => $enrollment->program_id]
                    );
                } catch (\Exception $e) {
                    // Skip if already awarded
                }
            }

            // Award attendance points (5-15 sessions)
            $attendances = Attendance::where('student_id', $student->id)
                ->where('status', 'present')
                ->whereNotNull('id')
                ->take(rand(5, 15))
                ->get();

            foreach ($attendances as $attendance) {
                $attendanceId = $attendance->id ?? null;
                if (empty($attendanceId) || $attendanceId <= 0) continue;
                try {
                    $gamificationService->awardPointsForEvent(
                        $student,
                        'attendance_present',
                        'attendance',
                        (int) $attendanceId,
                        ['session_id' => $attendance->session_id ?? null]
                    );
                } catch (\Exception $e) {
                    // Skip if already awarded or rule not found
                    if (str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'Max times')) {
                        continue;
                    }
                }
            }

            // Award assignment submission points (2-5 assignments)
            $submissions = AssignmentSubmission::where('student_id', $student->id)
                ->where('status', '!=', 'draft')
                ->take(rand(2, 5))
                ->get();

            foreach ($submissions as $submission) {
                if (!$submission->id) continue;
                try {
                    $gamificationService->awardPointsForEvent(
                        $student,
                        'assignment_submitted',
                        'assignment_submissions',
                        (int) $submission->id,
                        ['assignment_id' => $submission->assignment_id]
                    );

                    // Award perfect score if applicable
                    if ($submission->grade && $submission->assignment && 
                        $submission->grade >= $submission->assignment->max_grade * 0.95) {
                        try {
                            $gamificationService->awardPointsForEvent(
                                $student,
                                'assignment_perfect_score',
                                'assignment_submissions',
                                (int) $submission->id,
                                ['grade' => $submission->grade]
                            );
                        } catch (\Exception $e) {
                            // Skip if already awarded
                        }
                    }
                } catch (\Exception $e) {
                    // Skip if already awarded
                }
            }

            // Award payment points (1-2 payments)
            $payments = PaymentTransaction::whereHas('invoice', function($q) use ($student) {
                    $q->whereHas('enrollment', function($eq) use ($student) {
                        $eq->where('student_id', $student->id);
                    });
                })
                ->where('status', 'success')
                ->take(rand(1, 2))
                ->get();

            foreach ($payments as $payment) {
                if (!$payment->id) continue;
                try {
                    $gamificationService->awardPointsForEvent(
                        $student,
                        'payment_made',
                        'payment_transactions',
                        (int) $payment->id,
                        ['invoice_id' => $payment->invoice_id]
                    );
                } catch (\Exception $e) {
                    // Skip if already awarded
                }
            }

            // Award certificate points (if any)
            $certificates = Certificate::where('student_id', $student->id)
                ->take(1)
                ->get();

            foreach ($certificates as $certificate) {
                if (!$certificate->id) continue;
                try {
                    $gamificationService->awardPointsForEvent(
                        $student,
                        'certificate_issued',
                        'certificates',
                        (int) $certificate->id,
                        ['course_id' => $certificate->course_id]
                    );
                } catch (\Exception $e) {
                    // Skip if already awarded
                }
            }

            // Recalculate level and check badges
            $gamificationService->recalculateUserLevel($student->id);
            $gamificationService->checkAndAwardBadges($student->id);
        }

        $this->command->info('Gamification points awarded to ' . $students->count() . ' students!');
    }
}

