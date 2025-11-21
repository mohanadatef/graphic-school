<?php

namespace Modules\Core\FileStorage\Application\UseCases;

use App\Support\BaseUseCase;
use Modules\Core\FileStorage\Domain\Events\FileDeleted;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;

class DeleteFileUseCase extends BaseUseCase
{
    protected function handle(mixed $input): void
    {
        $filePath = $input['path'];
        $disk = $input['disk'] ?? 'public';

        if (Storage::disk($disk)->exists($filePath)) {
            Storage::disk($disk)->delete($filePath);

            // Dispatch domain event
            Event::dispatch(new FileDeleted($filePath, $disk));
        }
    }
}

