<?php

namespace Modules\Core\Notification\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class SendNotificationDTO extends BaseDTO
{
    public string $channel; // email, sms, push
    public string $recipient;
    public string $subject;
    public string $message;
    public ?array $data = null;
    public ?int $userId = null;

    public function validate(): void
    {
        if (!in_array($this->channel, ['email', 'sms', 'push'])) {
            throw new \App\Exceptions\DomainException(
                'Invalid notification channel',
                ['channel' => 'Channel must be email, sms, or push'],
                422
            );
        }

        if (empty($this->recipient)) {
            throw new \App\Exceptions\DomainException(
                'Recipient is required',
                ['recipient' => 'Recipient is required'],
                422
            );
        }
    }
}

