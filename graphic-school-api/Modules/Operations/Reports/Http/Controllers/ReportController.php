<?php

namespace Modules\Operations\Reports\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
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

        return ApiResponse::success(
            [
                'report' => $report,
                'summary' => [
                    'total_courses' => $report->count(),
                    'total_students' => $report->sum('statistics.students_enrolled'),
                    'total_revenue' => $report->sum('financial.paid_total'),
                    'average_rating' => round($report->avg('performance.average_rating') ?? 0, 2),
                ],
            ],
            'Courses report generated successfully',
            200,
            [
                'total' => $report->count(),
                'generated_at' => now()->toDateTimeString(),
            ]
        );
    }

    /**
     * Get comprehensive instructors report
     */
    public function instructors(ReportFilterRequest $request): JsonResponse
    {
        $report = $this->reportService->instructorsReport($request->validated());

        return ApiResponse::success(
            [
                'report' => $report,
                'summary' => [
                    'total_instructors' => $report->count(),
                    'total_courses' => $report->sum('statistics.courses_count'),
                    'total_students' => $report->sum('statistics.total_students'),
                    'total_revenue' => $report->sum('financial.total_revenue'),
                    'average_rating' => round($report->avg('performance.average_rating') ?? 0, 2),
                ],
            ],
            'Instructors report generated successfully',
            200,
            [
                'total' => $report->count(),
                'generated_at' => now()->toDateTimeString(),
            ]
        );
    }

    /**
     * Get financial report
     */
    public function financial(ReportFilterRequest $request): JsonResponse
    {
        $report = $this->reportService->financialReport($request->validated());

        return ApiResponse::success(
            $report,
            'Financial report generated successfully',
            200,
            [
                'generated_at' => now()->toDateTimeString(),
            ]
        );
    }
}

