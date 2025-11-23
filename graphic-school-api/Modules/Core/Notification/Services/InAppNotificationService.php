<?php

namespace Modules\Core\Notification\Services;

use Modules\Core\Notification\Models\InAppNotification;
use Modules\ACL\Users\Models\User;
use Modules\Core\Localization\Services\TranslationService;
use App\Services\EntityTranslationService;

/**
 * CHANGE-003: In-App Notifications Service
 */
class InAppNotificationService
{
    /**
     * Send notification to user
     * If title/message are translation keys, they will be translated based on user locale
     */
    public function send(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $category = 'info',
        ?array $data = null,
        ?string $locale = null
    ): InAppNotification {
        $user = User::find($userId);
        $locale = $locale ?? $user?->locale ?? app()->getLocale();

        // Try to translate if title/message look like translation keys
        $translationService = app(TranslationService::class);
        
        // If title starts with 'notifications.', treat it as a translation key
        $translatedTitle = str_starts_with($title, 'notifications.') 
            ? $translationService->get($title, [], $locale, 'messages') 
            : $title;
        
        // If message starts with 'notifications.', treat it as a translation key
        $translatedMessage = str_starts_with($message, 'notifications.') 
            ? $translationService->get($message, $data ?? [], $locale, 'messages') 
            : $message;

        return InAppNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'category' => $category,
            'title' => $translatedTitle,
            'message' => $translatedMessage,
            'data' => array_merge($data ?? [], ['locale' => $locale]),
        ]);
    }

    /**
     * Send notification when enrollment is created
     */
    public function enrollmentCreated(User $student, $enrollment): void
    {
        $locale = $student->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);
        $entityTranslationService = app(EntityTranslationService::class);

        // Get translated course title
        $courseTitle = $entityTranslationService->getTranslatedField($enrollment->course, 'title', $locale, $enrollment->course->title);

        $this->send(
            $student->id,
            'enrollment_created',
            $translationService->get('notifications.enrollment_created.title', [], $locale, 'messages') ?: 'Enrollment Created',
            $translationService->get('notifications.enrollment_created.message', ['course' => $courseTitle], $locale, 'messages') ?: "Enrollment request created for course: {$courseTitle}",
            'info',
            [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
            ],
            $locale
        );
    }

    /**
     * Send notification when enrollment is approved
     */
    public function enrollmentApproved(User $student, $enrollment): void
    {
        $locale = $student->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);
        $entityTranslationService = app(EntityTranslationService::class);

        $courseTitle = $entityTranslationService->getTranslatedField($enrollment->course, 'title', $locale, $enrollment->course->title);

        $this->send(
            $student->id,
            'enrollment_approved',
            $translationService->get('notifications.enrollment_approved.title', [], $locale, 'messages') ?: 'Enrollment Approved',
            $translationService->get('notifications.enrollment_approved.message', ['course' => $courseTitle], $locale, 'messages') ?: "Enrollment approved for course: {$courseTitle}",
            'success',
            [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
            ],
            $locale
        );
    }

    /**
     * Send notification when enrollment is rejected
     */
    public function enrollmentRejected(User $student, $enrollment): void
    {
        $locale = $student->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);
        $entityTranslationService = app(EntityTranslationService::class);

        $courseTitle = $entityTranslationService->getTranslatedField($enrollment->course, 'title', $locale, $enrollment->course->title);

        $this->send(
            $student->id,
            'enrollment_rejected',
            $translationService->get('notifications.enrollment_rejected.title', [], $locale, 'messages') ?: 'Enrollment Rejected',
            $translationService->get('notifications.enrollment_rejected.message', ['course' => $courseTitle], $locale, 'messages') ?: "Enrollment rejected for course: {$courseTitle}",
            'error',
            [
                'enrollment_id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
            ],
            $locale
        );
    }

    /**
     * Send notification when payment is updated
     */
    public function paymentUpdated(User $student, $payment): void
    {
        $locale = $student->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);
        $entityTranslationService = app(EntityTranslationService::class);

        $courseTitle = $entityTranslationService->getTranslatedField($payment->course, 'title', $locale, $payment->course->title);

        $this->send(
            $student->id,
            'payment_updated',
            $translationService->get('notifications.payment_updated.title', [], $locale, 'messages') ?: 'Payment Updated',
            $translationService->get('notifications.payment_updated.message', ['course' => $courseTitle, 'amount' => $payment->amount], $locale, 'messages') ?: "Payment updated for course: {$courseTitle}",
            'info',
            [
                'payment_id' => $payment->id,
                'course_id' => $payment->course_id,
                'amount' => $payment->amount,
            ],
            $locale
        );
    }

    /**
     * Send notification when quiz is created
     */
    public function quizCreated(User $student, $quiz): void
    {
        $locale = $student->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);
        $entityTranslationService = app(EntityTranslationService::class);

        $courseTitle = $entityTranslationService->getTranslatedField($quiz->course, 'title', $locale, $quiz->course->title);

        $this->send(
            $student->id,
            'quiz_created',
            $translationService->get('notifications.quiz_created.title', [], $locale, 'messages') ?: 'New Quiz',
            $translationService->get('notifications.quiz_created.message', ['course' => $courseTitle], $locale, 'messages') ?: "New quiz added in course: {$courseTitle}",
            'info',
            [
                'quiz_id' => $quiz->id,
                'course_id' => $quiz->course_id,
            ],
            $locale
        );
    }

    /**
     * Send notification when quiz result is published
     */
    public function quizResultPublished(User $student, $quizAttempt): void
    {
        $locale = $student->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);

        $this->send(
            $student->id,
            'quiz_result_published',
            $translationService->get('notifications.quiz_result_published.title', [], $locale, 'messages') ?: 'Quiz Results Published',
            $translationService->get('notifications.quiz_result_published.message', ['score' => $quizAttempt->score], $locale, 'messages') ?: "Quiz results published. Score: {$quizAttempt->score}%",
            $quizAttempt->is_passed ? 'success' : 'warning',
            [
                'quiz_attempt_id' => $quizAttempt->id,
                'quiz_id' => $quizAttempt->quiz_id,
                'score' => $quizAttempt->score,
                'is_passed' => $quizAttempt->is_passed,
            ],
            $locale
        );
    }

    /**
     * Send notification when new message is received
     */
    public function messageReceived(User $recipient, $message): void
    {
        $locale = $recipient->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);

        $this->send(
            $recipient->id,
            'message_received',
            $translationService->get('notifications.message_received.title', [], $locale, 'messages') ?: 'New Message',
            $translationService->get('notifications.message_received.message', ['sender' => $message->sender->name], $locale, 'messages') ?: "You have a new message from {$message->sender->name}",
            'info',
            [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
            ],
            $locale
        );
    }

    /**
     * Send notification when session is upcoming
     */
    public function sessionUpcoming(User $student, $session): void
    {
        $locale = $student->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);
        $entityTranslationService = app(EntityTranslationService::class);

        $sessionTitle = $entityTranslationService->getTranslatedField($session, 'title', $locale, $session->title);

        $this->send(
            $student->id,
            'session_upcoming',
            $translationService->get('notifications.session_upcoming.title', [], $locale, 'messages') ?: 'Upcoming Session',
            $translationService->get('notifications.session_upcoming.message', ['session' => $sessionTitle, 'date' => $session->session_date], $locale, 'messages') ?: "You have an upcoming session: {$sessionTitle} on {$session->session_date}",
            'info',
            [
                'session_id' => $session->id,
                'course_id' => $session->course_id,
                'session_date' => $session->session_date,
            ],
            $locale
        );
    }

    /**
     * Send notification to admin when new ticket is created
     */
    public function ticketCreated(User $admin, $ticket): void
    {
        $locale = $admin->locale ?? app()->getLocale();
        $translationService = app(TranslationService::class);

        $this->send(
            $admin->id,
            'ticket_created',
            $translationService->get('notifications.ticket_created.title', [], $locale, 'messages') ?: 'New Support Ticket',
            $translationService->get('notifications.ticket_created.message', ['title' => $ticket->title], $locale, 'messages') ?: "New support ticket created: {$ticket->title}",
            'info',
            [
                'ticket_id' => $ticket->id,
                'type' => $ticket->type,
            ],
            $locale
        );
    }
}

