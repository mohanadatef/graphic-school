<?php

namespace App\Contracts\Events;

interface UserEvents
{
    // Events that can be dispatched by User module
    public const USER_REGISTERED = 'user.registered';
    public const USER_UPDATED = 'user.updated';
    public const USER_DELETED = 'user.deleted';
}

