<?php

namespace Modules\Core\Notification\Application\UseCases;

use App\Support\BaseUseCase;
use Modules\Core\Notification\Application\DTOs\SendNotificationDTO;
use Modules\Core\Notification\Domain\Events\NotificationSent;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;

class SendNotificationUseCase extends BaseUseCase
{
    protected function handle(mixed $input): bool
    {
        /** @var SendNotificationDTO $dto */
        $dto = $input;
        $dto->validate();

        try {
            switch ($dto->channel) {
                case 'email':
                    $this->sendEmail($dto);
                    break;
                case 'sms':
                    $this->sendSms($dto);
                    break;
                case 'push':
                    $this->sendPush($dto);
                    break;
            }

            // Dispatch domain event
            Event::dispatch(new NotificationSent(
                'notification',
                $dto->channel,
                $dto->recipient,
                $dto->userId
            ));

            return true;
        } catch (\Exception $e) {
            throw new \App\Exceptions\DomainException(
                'Failed to send notification: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    protected function sendEmail(SendNotificationDTO $dto): void
    {
        // Use Laravel Mail
        \Illuminate\Support\Facades\Mail::raw($dto->message, function ($message) use ($dto) {
            $message->to($dto->recipient)
                ->subject($dto->subject);
        });
    }

    protected function sendSms(SendNotificationDTO $dto): void
    {
        // Implement SMS sending logic
        // This would integrate with SMS provider (Twilio, etc.)
    }

    protected function sendPush(SendNotificationDTO $dto): void
    {
        // Implement push notification logic
        // This would integrate with FCM, APNS, etc.
    }
}

