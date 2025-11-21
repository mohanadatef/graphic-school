<?php

namespace Modules\Core\Notification\Services;

use Modules\Core\Notification\Models\InAppNotification;
use Modules\ACL\Users\Models\User;

/**
 * CHANGE-003: In-App Notifications Service
 */
class InAppNotificationService
{
    /**
     * Send notification to user
     */
    public function send(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $category = 'info',
        ?array $data = null
    ): InAppNotification {
        return InAppNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Send notification when enrollment is created
     */
    public function enrollmentCreated(User $student, $enrollment): void
    {
        $this->send(
            $student->id,
            'enrollment_created',
            'تم إنشاء طلب التسجيل',
            "تم إنشاء طلب التسجيل في الكورس: {$enrollment->course->title}",
            'info',
            [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
            ]
        );
    }

    /**
     * Send notification when enrollment is approved
     */
    public function enrollmentApproved(User $student, $enrollment): void
    {
        $this->send(
            $student->id,
            'enrollment_approved',
            'تم قبول طلب التسجيل',
            "تم قبول طلب التسجيل في الكورس: {$enrollment->course->title}",
            'success',
            [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
            ]
        );
    }

    /**
     * Send notification when enrollment is rejected
     */
    public function enrollmentRejected(User $student, $enrollment): void
    {
        $this->send(
            $student->id,
            'enrollment_rejected',
            'تم رفض طلب التسجيل',
            "تم رفض طلب التسجيل في الكورس: {$enrollment->course->title}",
            'error',
            [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
            ]
        );
    }

    /**
     * Send notification when payment is updated
     */
    public function paymentUpdated(User $student, $payment): void
    {
        $this->send(
            $student->id,
            'payment_updated',
            'تم تحديث حالة الدفع',
            "تم تحديث حالة الدفع للكورس: {$payment->course->title}",
            'info',
            [
                'payment_id' => $payment->id,
                'course_id' => $payment->course_id,
                'amount' => $payment->amount,
            ]
        );
    }

    /**
     * Send notification when quiz is created
     */
    public function quizCreated(User $student, $quiz): void
    {
        $this->send(
            $student->id,
            'quiz_created',
            'اختبار جديد',
            "تم إضافة اختبار جديد في الكورس: {$quiz->course->title}",
            'info',
            [
                'quiz_id' => $quiz->id,
                'course_id' => $quiz->course_id,
            ]
        );
    }

    /**
     * Send notification when quiz result is published
     */
    public function quizResultPublished(User $student, $quizAttempt): void
    {
        $this->send(
            $student->id,
            'quiz_result_published',
            'تم نشر نتائج الاختبار',
            "تم نشر نتائج الاختبار. النتيجة: {$quizAttempt->score}%",
            $quizAttempt->is_passed ? 'success' : 'warning',
            [
                'quiz_attempt_id' => $quizAttempt->id,
                'quiz_id' => $quizAttempt->quiz_id,
                'score' => $quizAttempt->score,
                'is_passed' => $quizAttempt->is_passed,
            ]
        );
    }

    /**
     * Send notification when new message is received
     */
    public function messageReceived(User $recipient, $message): void
    {
        $this->send(
            $recipient->id,
            'message_received',
            'رسالة جديدة',
            "لديك رسالة جديدة من {$message->sender->name}",
            'info',
            [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
            ]
        );
    }

    /**
     * Send notification when session is upcoming
     */
    public function sessionUpcoming(User $student, $session): void
    {
        $this->send(
            $student->id,
            'session_upcoming',
            'محاضرة قادمة',
            "لديك محاضرة قادمة: {$session->title} في {$session->session_date}",
            'info',
            [
                'session_id' => $session->id,
                'course_id' => $session->course_id,
                'session_date' => $session->session_date,
            ]
        );
    }

    /**
     * Send notification to admin when new ticket is created
     */
    public function ticketCreated(User $admin, $ticket): void
    {
        $this->send(
            $admin->id,
            'ticket_created',
            'تذكرة دعم جديدة',
            "تم إنشاء تذكرة دعم جديدة: {$ticket->title}",
            'info',
            [
                'ticket_id' => $ticket->id,
                'type' => $ticket->type,
            ]
        );
    }
}

