<?php

namespace App\Contracts\Services;

use Illuminate\Http\UploadedFile;

/**
 * Interface for file storage service
 * Follows Dependency Inversion Principle - UseCases depend on abstraction, not concrete implementation
 */
interface FileStorageInterface
{
    /**
     * Upload and store an uploaded file
     */
    public function upload(UploadedFile $file, string $path, string $disk = 'public'): string;

    /**
     * Delete a file
     */
    public function delete(string $path, string $disk = 'public'): bool;

    /**
     * Get file URL
     */
    public function url(string $path, string $disk = 'public'): string;
}

