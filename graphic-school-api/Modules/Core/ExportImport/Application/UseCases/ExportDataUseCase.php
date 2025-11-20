<?php

namespace Modules\Core\ExportImport\Application\UseCases;

use App\Support\BaseUseCase;
use Modules\Core\ExportImport\Application\DTOs\ExportDataDTO;
use Modules\Core\ExportImport\Domain\Events\ExportCompleted;
use App\Support\Export\ExcelExporter;
use App\Support\Export\PdfExporter;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Collection;

class ExportDataUseCase extends BaseUseCase
{
    protected function handle(mixed $input): array
    {
        /** @var ExportDataDTO $dto */
        $dto = $input;
        $dto->validate();

        $fileName = $dto->fileName ?? 'export_' . now()->format('Y-m-d_His');
        $collection = collect($dto->data);

        switch ($dto->type) {
            case 'excel':
                $url = ExcelExporter::export(
                    $collection,
                    $fileName,
                    $dto->headings,
                    $dto->mapping
                );
                break;

            case 'pdf':
                $url = PdfExporter::export(
                    $collection,
                    $fileName,
                    'exports.report',
                    ['title' => $fileName]
                );
                break;

            case 'csv':
                $url = $this->exportCsv($collection, $fileName, $dto->headings, $dto->mapping);
                break;

            default:
                throw new \App\Exceptions\DomainException('Unsupported export type', [], 422);
        }

        // Dispatch domain event
        Event::dispatch(new ExportCompleted(
            $dto->type,
            $url,
            $fileName
        ));

        return [
            'url' => $url,
            'file_name' => $fileName,
            'type' => $dto->type,
        ];
    }

    protected function exportCsv(Collection $data, string $fileName, array $headings, ?callable $mapping): string
    {
        $path = storage_path('app/public/exports/' . $fileName . '_' . now()->format('Y-m-d_His') . '.csv');
        
        $file = fopen($path, 'w');
        
        // Write headings
        if (!empty($headings)) {
            fputcsv($file, $headings);
        }
        
        // Write data
        foreach ($data as $row) {
            $rowData = $mapping ? $mapping($row) : (array) $row;
            fputcsv($file, $rowData);
        }
        
        fclose($file);
        
        return \Illuminate\Support\Facades\Storage::disk('public')->url('exports/' . basename($path));
    }
}

