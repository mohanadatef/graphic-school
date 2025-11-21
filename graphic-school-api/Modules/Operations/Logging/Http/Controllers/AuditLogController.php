<?php

namespace Modules\Operations\Logging\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\Operations\Logging\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * CHANGE-008: Full Audit Log System
 */
class AuditLogController extends BaseController
{
    /**
     * Get all audit logs (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->has('user_id')) {
            $query->forUser($request->input('user_id'));
        }

        // Filter by action
        if ($request->has('action')) {
            $query->forAction($request->input('action'));
        }

        // Filter by model type
        if ($request->has('model_type')) {
            $query->forModel($request->input('model_type'), $request->input('model_id'));
        }

        // Filter by date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->inDateRange($request->input('from_date'), $request->input('to_date'));
        }

        $logs = $query->paginate($request->input('per_page', 15));

        return $this->paginated($logs, 'Audit logs retrieved successfully');
    }

    /**
     * Get audit log details
     */
    public function show(int $id): JsonResponse
    {
        $log = ActivityLog::with(['user', 'model'])->findOrFail($id);

        return $this->success($log, 'Audit log retrieved successfully');
    }

    /**
     * Get audit logs for a specific entity
     */
    public function forEntity(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $logs = ActivityLog::forModel($modelType, $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginated($logs, 'Audit logs retrieved successfully');
    }
}

