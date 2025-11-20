<?php

namespace Modules\Operations\Logging\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Operations\Logging\Services\LoggingService;
use Modules\Operations\Logging\Http\Requests\ListActivityLogRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ActivityLogController extends Controller
{
    public function __construct(private LoggingService $loggingService)
    {
    }

    /**
     * Get activity logs
     */
    public function index(ListActivityLogRequest $request): JsonResponse
    {
        $logs = $this->loggingService->getLogs(
            $request->validated(),
            $request->integer('per_page', 50)
        );

        return response()->json($logs);
    }

    /**
     * Get logs for a specific model
     */
    public function getModelLogs(string $modelType, int $modelId): JsonResponse
    {
        $logs = $this->loggingService->getModelLogs($modelType, $modelId);

        return response()->json($logs);
    }

    /**
     * Get logs for a specific user
     */
    public function getUserLogs(int $userId): JsonResponse
    {
        $logs = $this->loggingService->getUserLogs($userId);

        return response()->json($logs);
    }

    /**
     * Export logs to CSV
     */
    public function export(ListActivityLogRequest $request): Response
    {
        $csv = $this->loggingService->exportToCsv($request->validated());

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity_logs_' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}

