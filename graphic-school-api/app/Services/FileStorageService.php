<?php

namespace App\Services;

use App\Contracts\Services\FileStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * File storage service implementation
 * Follows Single Responsibility Principle - only responsible for file storage operations
 */
class FileStorageService implements FileStorageInterface
{
    /**
     * Upload and store an uploaded file
     */
    public function upload(UploadedFile $file, string $path, string $disk = 'public'): string
    {
        return $file->store($path, $disk);
    }

    /**
     * Delete a file
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Get file URL
     */
    public function url(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }
}

