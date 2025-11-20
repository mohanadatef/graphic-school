<?php

namespace App\Support\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Support\Events\EventLogger;

abstract class BaseEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        EventLogger::dispatch(get_class($this), $this->toArray());
    }

    /**
     * Convert event to array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

