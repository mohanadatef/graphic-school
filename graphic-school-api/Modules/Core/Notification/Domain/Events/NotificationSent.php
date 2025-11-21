<?php

namespace Modules\Core\Notification\Domain\Events;

use App\Support\Events\BaseEvent;

class NotificationSent extends BaseEvent
{
    public function __construct(
        public string $type,
        public string $channel,
        public string $recipient,
        public ?int $userId = null
    ) {
        parent::__construct();
    }
}

