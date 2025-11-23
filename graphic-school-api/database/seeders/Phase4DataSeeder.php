<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QrToken;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CalendarEvent;
use App\Models\GradebookEntry;
use App\Models\Program;
use App\Models\Group;
use Modules\ACL\Users\Models\User;
use Modules\LMS\Sessions\Models\Session;
use Carbon\Carbon;

class Phase4DataSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::with('batches.groups')->get();
        $students = User::whereHas('role', fn($q) => $q->where('name', 'student'))->get();
        $instructors = User::whereHas('role', fn($q) => $q->where('name', 'instructor'))->get();

        if ($programs->isEmpty() || $students->isEmpty()) {
            $this->command->warn('No programs or students found. Please run DynamicLearningSeeder first.');
            return;
        }

        // 1. Create Assignments (4 per program)
        $assignments = [];
        foreach ($programs as $program) {
            $groups = $program->batches->flatMap->groups;
            if ($groups->isEmpty()) continue;

            $group = $groups->first();
            $instructor = $instructors->random();

            for ($i = 0; $i < 4; $i++) {
                $assignment = Assignment::create([
                    'program_id' => $program->id,
                    'batch_id' => $group->batch_id,
                    'group_id' => $group->id,
                    'title' => "Assignment " . ($i + 1) . " - " . ($program->getTranslated('title') ?? 'Program'),
                    'description' => 'Complete this assignment and submit before the due date.',
                    'due_date' => Carbon::now()->addDays(rand(7, 30)),
                    'max_grade' => 100,
                    'created_by' => $instructor->id,
                    'is_active' => true,
                ]);

                $assignments[] = $assignment;

                // Create calendar event
                CalendarEvent::create([
                    'user_id' => null,
                    'event_type' => 'assignment',
                    'reference_id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'start_datetime' => $assignment->due_date,
                    'end_datetime' => $assignment->due_date,
                    'color' => '#ef4444',
                ]);
            }
        }

        // 2. Create Submissions (30 total)
        $submissionCount = 0;
        foreach ($assignments as $assignment) {
            $groupStudents = $assignment->group ? $assignment->group->students : collect();
            if ($groupStudents->isEmpty()) continue;

            $submissionStudents = $groupStudents->random(min(3, $groupStudents->count()));
            
            foreach ($submissionStudents as $student) {
                if ($submissionCount >= 30) break;

                $isLate = rand(0, 10) < 2; // 20% chance of being late
                $submittedAt = $isLate 
                    ? $assignment->due_date->copy()->addDays(rand(1, 5))
                    : $assignment->due_date->copy()->subDays(rand(1, 7));

                $submission = AssignmentSubmission::create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id,
                    'submitted_at' => $submittedAt,
                    'text_submission' => 'This is my submission for the assignment.',
                    'status' => $isLate ? 'late' : 'submitted',
                ]);

                $submissionCount++;

                // Grade 2 submissions
                if ($submissionCount <= 2) {
                    $submission->grade = rand(70, 100);
                    $submission->feedback = 'Good work! Keep it up.';
                    $submission->graded_by = $assignment->created_by;
                    $submission->graded_at = $submittedAt->copy()->addDays(rand(1, 3));
                    $submission->status = 'graded';
                    $submission->save();
                }
            }
        }

        // 3. Create QR Tokens (10 total - mix of expired and valid)
        $sessions = Session::whereHas('group')->take(10)->get();
        foreach ($sessions as $index => $session) {
            $isExpired = $index < 5; // First 5 are expired
            QrToken::create([
                'session_id' => $session->id,
                'token' => \Illuminate\Support\Str::random(64),
                'expires_at' => $isExpired 
                    ? Carbon::now()->subMinutes(10)
                    : Carbon::now()->addMinutes(5),
            ]);
        }

        // 4. Create Calendar Events (20 total)
        // Sessions events
        $sessions = Session::whereNotNull('session_date')->take(10)->get();
        foreach ($sessions as $session) {
            if (!$session->session_date) continue;
            
            CalendarEvent::updateOrCreate(
                [
                    'event_type' => 'session',
                    'reference_id' => $session->id,
                ],
                [
                    'user_id' => null,
                    'title' => $session->title ?? 'Session',
                    'description' => $session->note ?? '',
                    'start_datetime' => Carbon::parse($session->session_date)->setTimeFromTimeString($session->start_time ?? '09:00:00'),
                    'end_datetime' => Carbon::parse($session->session_date)->setTimeFromTimeString($session->end_time ?? '11:00:00'),
                    'color' => '#3b82f6',
                ]
            );
        }

        // Custom events
        for ($i = 0; $i < 10; $i++) {
            CalendarEvent::create([
                'user_id' => $instructors->random()->id,
                'event_type' => 'custom',
                'title' => 'Custom Event ' . ($i + 1),
                'description' => 'This is a custom calendar event.',
                'start_datetime' => Carbon::now()->addDays(rand(1, 30)),
                'end_datetime' => Carbon::now()->addDays(rand(1, 30))->addHours(2),
                'color' => '#10b981',
            ]);
        }

        // 5. Create Gradebook Entries (for 10 students)
        $selectedStudents = $students->take(10);
        foreach ($selectedStudents as $student) {
            $enrollments = \Modules\LMS\Enrollments\Models\Enrollment::where('student_id', $student->id)
                ->where('status', 'approved')
                ->whereNotNull('program_id')
                ->get();

            foreach ($enrollments as $enrollment) {
                $entry = GradebookEntry::firstOrNew([
                    'student_id' => $student->id,
                    'program_id' => $enrollment->program_id,
                    'batch_id' => $enrollment->batch_id,
                ]);

                // Calculate grades
                $submissions = AssignmentSubmission::whereHas('assignment', function ($q) use ($enrollment) {
                    $q->where('program_id', $enrollment->program_id);
                })
                ->where('student_id', $student->id)
                ->where('status', 'graded')
                ->get();

                $entry->assignment_grade = $submissions->count() > 0 ? $submissions->avg('grade') : 0;

                // Calculate attendance
                $sessions = Session::whereHas('group', function ($q) use ($enrollment) {
                    $q->where('batch_id', $enrollment->batch_id);
                })->count();

                $presentCount = \App\Models\Attendance::where('student_id', $student->id)
                    ->where('status', 'present')
                    ->whereHas('session', function ($q) use ($enrollment) {
                        $q->whereHas('group', function ($gq) use ($enrollment) {
                            $gq->where('batch_id', $enrollment->batch_id);
                        });
                    })
                    ->count();

                $entry->attendance_percentage = $sessions > 0 ? ($presentCount / $sessions) * 100 : 0;
                if (!$entry->participation_grade) {
                    $entry->participation_grade = rand(70, 100);
                }
                $entry->save();
                $entry->calculateOverallGrade();
            }
        }

        $this->command->info('Phase 4 demo data seeded successfully!');
        $this->command->info('- Assignments: ' . Assignment::count());
        $this->command->info('- Submissions: ' . AssignmentSubmission::count());
        $this->command->info('- QR Tokens: ' . QrToken::count());
        $this->command->info('- Calendar Events: ' . CalendarEvent::count());
        $this->command->info('- Gradebook Entries: ' . GradebookEntry::count());
    }
}

