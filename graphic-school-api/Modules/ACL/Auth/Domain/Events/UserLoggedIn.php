<?php

namespace Modules\ACL\Auth\Domain\Events;

use App\Support\Events\BaseEvent;

class UserLoggedIn extends BaseEvent
{
    public function __construct(
        public int $userId,
        public string $email,
        public array $scopes
    ) {
        parent::__construct();
    }
}

