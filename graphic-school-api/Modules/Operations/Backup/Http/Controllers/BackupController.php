<?php

namespace Modules\Operations\Backup\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Operations\Backup\Services\BackupService;
use Modules\Operations\Backup\Models\Backup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(private BackupService $backupService)
    {
    }

    /**
     * Get all backups
     */
    public function index(Request $request): JsonResponse
    {
        $backups = Backup::with('creator')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json($backups);
    }

    /**
     * Create database backup
     */
    public function createDatabaseBackup(Request $request): JsonResponse
    {
        $backup = $this->backupService->createDatabaseBackup($request->user()?->id);

        return response()->json($backup, 201);
    }

    /**
     * Create files backup
     */
    public function createFilesBackup(Request $request): JsonResponse
    {
        $backup = $this->backupService->createFilesBackup($request->user()?->id);

        return response()->json($backup, 201);
    }

    /**
     * Create full backup
     */
    public function createFullBackup(Request $request): JsonResponse
    {
        $backup = $this->backupService->createFullBackup($request->user()?->id);

        return response()->json($backup, 201);
    }

    /**
     * Download backup
     */
    public function download(Backup $backup): BinaryFileResponse|Response
    {
        if ($backup->status !== 'completed') {
            return response()->json(['message' => 'Backup not completed'], 400);
        }

        $path = $this->backupService->getBackupPath($backup);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Backup file not found'], 404);
        }

        return response()->download($path);
    }

    /**
     * Delete backup
     */
    public function destroy(Backup $backup): JsonResponse
    {
        $path = $this->backupService->getBackupPath($backup);
        
        if (file_exists($path)) {
            unlink($path);
        }

        $backup->delete();

        return response()->json(['message' => 'Backup deleted']);
    }
}

