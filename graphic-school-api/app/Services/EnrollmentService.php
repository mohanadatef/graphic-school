<?php

namespace App\Services;

use Modules\LMS\Enrollments\Models\Enrollment;
use App\Models\EnrollmentLog;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Attendance;
use Modules\LMS\Sessions\Models\Session;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{

    /**
     * Create enrollment for a program
     */
    public function createEnrollment(int $studentId, int $programId, ?int $batchId = null, ?int $groupId = null): Enrollment
    {
        return DB::transaction(function () use ($studentId, $programId, $batchId, $groupId) {
            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'program_id' => $programId,
                'batch_id' => $batchId,
                'group_id' => $groupId,
                'status' => 'pending',
                'payment_status' => 'not_paid',
            ]);

            // Log enrollment creation
            EnrollmentLog::create([
                'enrollment_id' => $enrollment->id,
                'action' => 'created',
                'metadata' => [
                    'program_id' => $programId,
                    'batch_id' => $batchId,
                    'group_id' => $groupId,
                ],
            ]);

            return $enrollment;
        });
    }

    /**
     * Approve enrollment
     */
    public function approveEnrollment(int $enrollmentId, ?int $adminId = null, ?int $batchId = null, ?int $groupId = null): Enrollment
    {
        return DB::transaction(function () use ($enrollmentId, $adminId, $batchId, $groupId) {
            $enrollment = Enrollment::findOrFail($enrollmentId);

            // Auto-assign batch/group if not set
            if (!$enrollment->batch_id && $batchId) {
                $enrollment->batch_id = $batchId;
            }
            if (!$enrollment->group_id && $groupId) {
                $enrollment->group_id = $groupId;
            }

            $enrollment->status = 'approved';
            $enrollment->can_attend = true;
            $enrollment->approved_by = $adminId;
            $enrollment->approved_at = now();
            $enrollment->save();

            // Log approval
            EnrollmentLog::create([
                'enrollment_id' => $enrollment->id,
                'action' => 'approved',
                'admin_id' => $adminId,
                'metadata' => [
                    'batch_id' => $enrollment->batch_id,
                    'group_id' => $enrollment->group_id,
                ],
            ]);

            // Create invoice
            $this->createInvoiceForEnrollment($enrollment);

            // Create attendance slots for all sessions in the group
            if ($enrollment->group_id) {
                $this->createAttendanceSlots($enrollment);
            }

            // Award gamification points for enrollment
            try {
                $gamificationService = app(\App\Services\GamificationService::class);
                $student = $enrollment->student;
                $gamificationService->awardPointsForEvent(
                    $student,
                    'enrollment_first_program',
                    'enrollments',
                    $enrollment->id,
                    [
                        'program_id' => $enrollment->program_id,
                        'batch_id' => $enrollment->batch_id,
                        'group_id' => $enrollment->group_id,
                    ]
                );
            } catch (\Exception $e) {
                // Log but don't fail enrollment if gamification fails
                \Illuminate\Support\Facades\Log::warning('Gamification failed for enrollment', [
                    'enrollment_id' => $enrollment->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return $enrollment;
        });
    }

    /**
     * Reject enrollment
     */
    public function rejectEnrollment(int $enrollmentId, ?int $adminId = null, string $reason = ''): Enrollment
    {
        return DB::transaction(function () use ($enrollmentId, $adminId, $reason) {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            $enrollment->status = 'rejected';
            $enrollment->save();

            EnrollmentLog::create([
                'enrollment_id' => $enrollment->id,
                'action' => 'rejected',
                'admin_id' => $adminId,
                'metadata' => ['reason' => $reason],
            ]);

            return $enrollment;
        });
    }

    /**
     * Withdraw enrollment
     */
    public function withdrawEnrollment(int $enrollmentId, ?int $adminId = null): Enrollment
    {
        return DB::transaction(function () use ($enrollmentId, $adminId) {
            $enrollment = Enrollment::findOrFail($enrollmentId);
            $enrollment->status = 'withdrawn';
            $enrollment->can_attend = false;
            $enrollment->save();

            EnrollmentLog::create([
                'enrollment_id' => $enrollment->id,
                'action' => 'withdrawn',
                'admin_id' => $adminId,
            ]);

            return $enrollment;
        });
    }

    /**
     * Create invoice for approved enrollment
     */
    protected function createInvoiceForEnrollment(Enrollment $enrollment): Invoice
    {
        $program = $enrollment->program;
        $totalAmount = $program ? $program->price : 0;

        $invoice = Invoice::create([
            'enrollment_id' => $enrollment->id,
            'total_amount' => $totalAmount,
            'due_date' => now()->addDays(30),
            'status' => 'unpaid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'title' => $program ? $program->getTranslated('title') : 'Program Fee',
            'amount' => $totalAmount,
            'quantity' => 1,
        ]);

        EnrollmentLog::create([
            'enrollment_id' => $enrollment->id,
            'action' => 'payment-created',
            'metadata' => ['invoice_id' => $invoice->id],
        ]);

        return $invoice;
    }

    /**
     * Create attendance slots for all sessions in the group
     */
    protected function createAttendanceSlots(Enrollment $enrollment): void
    {
        if (!$enrollment->group_id) {
            return;
        }

        $sessions = Session::where('group_id', $enrollment->group_id)->get();

        foreach ($sessions as $session) {
            Attendance::firstOrCreate(
                [
                    'session_id' => $session->id,
                    'student_id' => $enrollment->student_id,
                ],
                [
                    'status' => 'absent',
                ]
            );
        }
    }
}

