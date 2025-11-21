<?php

namespace App\Support\Export;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PdfExporter
{
    /**
     * Export collection to PDF
     */
    public static function export(
        Collection $data,
        string $filename,
        string $view,
        array $viewData = []
    ): string {
        $pdf = Pdf::loadView($view, array_merge([
            'data' => $data,
        ], $viewData));

        $path = "exports/{$filename}_" . now()->format('Y-m-d_His') . '.pdf';
        
        Storage::disk('public')->put($path, $pdf->output());

        return Storage::disk('public')->url($path);
    }

    /**
     * Export report to PDF
     */
    public static function exportReport(
        array $reportData,
        string $title,
        string $filename
    ): string {
        return self::export(
            collect($reportData),
            $filename,
            'exports.report',
            ['title' => $title]
        );
    }
}

