<?php

namespace Modules\ACL\Users\Domain\Events;

use App\Support\Events\BaseEvent;

class UserUpdated extends BaseEvent
{
    public function __construct(
        public int $userId,
        public array $changes
    ) {
        parent::__construct();
    }
}

