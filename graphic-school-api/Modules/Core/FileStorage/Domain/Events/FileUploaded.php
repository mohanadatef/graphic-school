<?php

namespace Modules\Core\FileStorage\Domain\Events;

use App\Support\Events\BaseEvent;

class FileUploaded extends BaseEvent
{
    public function __construct(
        public string $filePath,
        public string $fileName,
        public string $disk,
        public ?int $userId = null
    ) {
        parent::__construct();
    }
}

