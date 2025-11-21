<?php

namespace Modules\Core\Notification\Listeners;

use Modules\Core\Notification\Services\InAppNotificationService;
use Modules\LMS\Enrollments\Enums\EnrollmentStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * CHANGE-003: Send notifications for enrollment events
 */
class SendEnrollmentNotification
{
    public function __construct(
        private InAppNotificationService $notificationService
    ) {}

    /**
     * Handle enrollment status changes
     */
    public function handle($event): void
    {
        $enrollment = $event->enrollment ?? null;
        
        if (! $enrollment || ! $enrollment->student) {
            return;
        }

        $status = $enrollment->status;

        if ($status === EnrollmentStatus::APPROVED->value) {
            $this->notificationService->enrollmentApproved($enrollment->student, $enrollment);
        } elseif ($status === EnrollmentStatus::REJECTED->value) {
            $this->notificationService->enrollmentRejected($enrollment->student, $enrollment);
        } else {
            $this->notificationService->enrollmentCreated($enrollment->student, $enrollment);
        }
    }
}

