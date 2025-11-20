<?php

namespace Modules\ACL\Users\Domain\Events;

use App\Support\Events\BaseEvent;

class UserDeleted extends BaseEvent
{
    public function __construct(
        public int $userId,
        public string $email
    ) {
        parent::__construct();
    }
}

