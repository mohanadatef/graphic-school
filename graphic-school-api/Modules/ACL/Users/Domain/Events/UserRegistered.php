<?php

namespace Modules\ACL\Users\Domain\Events;

use App\Support\Events\BaseEvent;

class UserRegistered extends BaseEvent
{
    public function __construct(
        public int $userId,
        public string $email,
        public string $name,
        public ?int $roleId = null
    ) {
        parent::__construct();
    }
}

