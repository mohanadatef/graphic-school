<?php

namespace Modules\Core\FileStorage\Application\UseCases;

use App\Support\BaseUseCase;
use Modules\Core\FileStorage\Application\DTOs\UploadFileDTO;
use Modules\Core\FileStorage\Domain\Events\FileUploaded;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class UploadFileUseCase extends BaseUseCase
{
    protected function handle(mixed $input): array
    {
        /** @var UploadFileDTO $dto */
        $dto = $input;
        $dto->validate();

        $fileName = $dto->fileName ?? ($dto->generateUniqueName 
            ? Str::uuid() . '.' . $dto->file->getClientOriginalExtension()
            : $dto->file->getClientOriginalName());

        $path = $dto->directory 
            ? $dto->directory . '/' . $fileName
            : $fileName;

        $filePath = $dto->file->storeAs($dto->directory ?? '', $fileName, $dto->disk);
        $url = Storage::disk($dto->disk)->url($filePath);

        // Dispatch domain event
        Event::dispatch(new FileUploaded(
            $filePath,
            $fileName,
            $dto->disk,
            auth()->id()
        ));

        return [
            'path' => $filePath,
            'url' => $url,
            'name' => $fileName,
            'size' => $dto->file->getSize(),
            'mime_type' => $dto->file->getMimeType(),
        ];
    }
}

