<?php

namespace Modules\Operations\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\Operations\Reports\Services\StrategicReportService;
use Modules\Operations\Reports\Http\Requests\StrategicReportFilterRequest;
use Illuminate\Http\JsonResponse;

class StrategicReportController extends Controller
{
    public function __construct(private StrategicReportService $reportService)
    {
    }

    /**
     * Report 1: Performance Report (KPIs)
     */
    public function performance(StrategicReportFilterRequest $request): JsonResponse
    {
        $filters = $request->getFilters();
        $report = $this->reportService->performanceReport($filters);
        
        return ApiResponse::success(
            $report,
            'Performance report generated successfully'
        );
    }

    /**
     * Report 2: Profitability Report
     */
    public function profitability(StrategicReportFilterRequest $request): JsonResponse
    {
        $filters = $request->getFilters();
        $report = $this->reportService->profitabilityReport($filters);
        
        return ApiResponse::success(
            $report,
            'Profitability report generated successfully'
        );
    }

    /**
     * Report 3: Student Analytics Report
     */
    public function studentAnalytics(StrategicReportFilterRequest $request): JsonResponse
    {
        $filters = $request->getFilters();
        $report = $this->reportService->studentAnalyticsReport($filters);
        
        return ApiResponse::success(
            $report,
            'Student analytics report generated successfully'
        );
    }

    /**
     * Report 4: Instructor Performance Report
     */
    public function instructorPerformance(StrategicReportFilterRequest $request): JsonResponse
    {
        $filters = $request->getFilters();
        $report = $this->reportService->instructorPerformanceReport($filters);
        
        return ApiResponse::success(
            $report,
            'Instructor performance report generated successfully'
        );
    }

    /**
     * Report 5: Forecasting Report
     */
    public function forecasting(StrategicReportFilterRequest $request): JsonResponse
    {
        $filters = $request->getFilters();
        $report = $this->reportService->forecastingReport($filters);
        
        return ApiResponse::success(
            $report,
            'Forecasting report generated successfully'
        );
    }
}

