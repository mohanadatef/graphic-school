<?php

namespace Modules\ACL\Auth\Domain\Events;

use App\Support\Events\BaseEvent;

class UserLoggedOut extends BaseEvent
{
    public function __construct(
        public int $userId,
        public string $email
    ) {
        parent::__construct();
    }
}

