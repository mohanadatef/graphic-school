<?php

namespace Modules\Core\ExportImport\Domain\Events;

use App\Support\Events\BaseEvent;

class ExportCompleted extends BaseEvent
{
    public function __construct(
        public string $type, // excel, pdf, csv
        public string $filePath,
        public string $fileName
    ) {
        parent::__construct();
    }
}

