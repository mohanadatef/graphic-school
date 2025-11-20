<?php

namespace Modules\Operations\Backup\Services;

use Modules\Operations\Backup\Models\Backup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class BackupService
{
    protected string $backupPath = 'backups';

    /**
     * Create database backup
     */
    public function createDatabaseBackup(?int $userId = null): Backup
    {
        $backup = Backup::create([
            'type' => 'database',
            'path' => '',
            'status' => 'pending',
            'created_by' => $userId,
        ]);

        try {
            $filename = 'database_' . now()->format('Y-m-d_His') . '.sql';
            $path = $this->backupPath . '/' . $filename;

            // Get database configuration
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            // Create backup directory if it doesn't exist
            $fullPath = storage_path('app/' . $this->backupPath);
            if (!File::exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }

            // Execute mysqldump
            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($fullPath . '/' . $filename)
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('Database backup failed');
            }

            $size = File::size($fullPath . '/' . $filename);

            $backup->update([
                'path' => $path,
                'size' => $size,
                'status' => 'completed',
            ]);

            return $backup;
        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Create files backup
     */
    public function createFilesBackup(?int $userId = null): Backup
    {
        $backup = Backup::create([
            'type' => 'files',
            'path' => '',
            'status' => 'pending',
            'created_by' => $userId,
        ]);

        try {
            $filename = 'files_' . now()->format('Y-m-d_His') . '.zip';
            $path = $this->backupPath . '/' . $filename;
            $fullPath = storage_path('app/' . $path);

            // Create backup directory if it doesn't exist
            $backupDir = storage_path('app/' . $this->backupPath);
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Cannot create zip file');
            }

            // Add storage/app/public to zip
            $publicPath = storage_path('app/public');
            if (File::exists($publicPath)) {
                $this->addDirectoryToZip($zip, $publicPath, 'public');
            }

            $zip->close();

            $size = File::size($fullPath);

            $backup->update([
                'path' => $path,
                'size' => $size,
                'status' => 'completed',
            ]);

            return $backup;
        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Create full backup (database + files)
     */
    public function createFullBackup(?int $userId = null): Backup
    {
        $backup = Backup::create([
            'type' => 'full',
            'path' => '',
            'status' => 'pending',
            'created_by' => $userId,
        ]);

        try {
            $dbBackup = $this->createDatabaseBackup($userId);
            $filesBackup = $this->createFilesBackup($userId);

            $filename = 'full_' . now()->format('Y-m-d_His') . '.zip';
            $path = $this->backupPath . '/' . $filename;
            $fullPath = storage_path('app/' . $path);

            $zip = new ZipArchive();
            if ($zip->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Cannot create zip file');
            }

            // Add database backup
            $zip->addFile(storage_path('app/' . $dbBackup->path), 'database.sql');

            // Add files backup
            $zip->addFile(storage_path('app/' . $filesBackup->path), 'files.zip');

            $zip->close();

            $size = File::size($fullPath);

            $backup->update([
                'path' => $path,
                'size' => $size,
                'status' => 'completed',
            ]);

            return $backup;
        } catch (\Exception $e) {
            $backup->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Add directory to zip recursively
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $dir, string $zipPath): void
    {
        $files = File::allFiles($dir);

        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    /**
     * Get backup file path
     */
    public function getBackupPath(Backup $backup): string
    {
        return storage_path('app/' . $backup->path);
    }

    /**
     * Delete old backups (older than specified days)
     */
    public function deleteOldBackups(int $days = 30): int
    {
        $cutoffDate = now()->subDays($days);

        $backups = Backup::where('created_at', '<', $cutoffDate)->get();

        $deleted = 0;
        foreach ($backups as $backup) {
            $path = $this->getBackupPath($backup);
            if (File::exists($path)) {
                File::delete($path);
            }
            $backup->delete();
            $deleted++;
        }

        return $deleted;
    }
}

