<?php

namespace Modules\Core\FileStorage\Application\DTOs;

use App\Support\DTOs\BaseDTO;
use Illuminate\Http\UploadedFile;

class UploadFileDTO extends BaseDTO
{
    public UploadedFile $file;
    public string $disk = 'public';
    public ?string $directory = null;
    public ?string $fileName = null;
    public bool $generateUniqueName = true;

    public function validate(): void
    {
        if (!$this->file || !$this->file->isValid()) {
            throw new \App\Exceptions\DomainException(
                'Invalid file',
                ['file' => 'File is required and must be valid'],
                422
            );
        }
    }
}

