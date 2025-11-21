<?php

namespace Modules\Operations\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\Operations\Dashboard\Http\Requests\DashboardFilterRequest;
use Modules\Operations\Dashboard\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function __invoke(DashboardFilterRequest $request)
    {
        try {
            $data = $this->dashboardService->getDashboardData($request->validated());
            
            return ApiResponse::success(
                $data,
                'Dashboard data retrieved successfully'
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Re-throw to let Handler process it
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in DashboardController: ' . $e->getMessage());
            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : 'Error loading dashboard data',
                config('app.debug') ? ['exception' => get_class($e), 'file' => $e->getFile(), 'line' => $e->getLine()] : [],
                500
            );
        }
    }
}

