<?php

namespace Modules\Operations\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Operations\Reports\Http\Requests\ReportFilterRequest;
use Modules\Operations\Reports\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    /**
     * Get comprehensive courses report
     */
    public function courses(ReportFilterRequest $request): JsonResponse
    {
        $report = $this->reportService->coursesReport($request->validated());

        return response()->json([
            'data' => $report,
            'meta' => [
                'total' => $report->count(),
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Get comprehensive instructors report
     */
    public function instructors(ReportFilterRequest $request): JsonResponse
    {
        $report = $this->reportService->instructorsReport($request->validated());

        return response()->json([
            'data' => $report,
            'meta' => [
                'total' => $report->count(),
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Get financial report
     */
    public function financial(ReportFilterRequest $request): JsonResponse
    {
        $report = $this->reportService->financialReport($request->validated());

        return response()->json([
            'data' => $report,
            'meta' => [
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);
    }
}

