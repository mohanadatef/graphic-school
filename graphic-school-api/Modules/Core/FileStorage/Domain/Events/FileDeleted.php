<?php

namespace Modules\Core\FileStorage\Domain\Events;

use App\Support\Events\BaseEvent;

class FileDeleted extends BaseEvent
{
    public function __construct(
        public string $filePath,
        public string $disk
    ) {
        parent::__construct();
    }
}

