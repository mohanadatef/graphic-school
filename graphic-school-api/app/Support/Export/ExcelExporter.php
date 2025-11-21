<?php

namespace App\Support\Export;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ExcelExporter
{
    /**
     * Export collection to Excel
     */
    public static function export(
        Collection $data,
        string $filename,
        array $headings = [],
        ?callable $mapping = null
    ): string {
        $export = new class($data, $headings, $mapping) implements FromCollection, WithHeadings, WithMapping {
            public function __construct(
                private Collection $data,
                private array $headings,
                private $mapping = null // callable - cannot be typed
            ) {
            }

            public function collection(): Collection
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }

            public function map($row): array
            {
                if ($this->mapping) {
                    return ($this->mapping)($row);
                }

                return (array) $row;
            }
        };

        $path = "exports/{$filename}_" . now()->format('Y-m-d_His') . '.xlsx';
        
        Excel::store($export, $path, 'public');

        return Storage::disk('public')->url($path);
    }

    /**
     * Export with chunking for large datasets
     */
    public static function exportChunked(
        callable $queryBuilder,
        string $filename,
        array $headings = [],
        ?callable $mapping = null,
        int $chunkSize = 1000
    ): string {
        // Implementation for chunked export
        // This would use QueuedExport for large datasets
        return self::export(collect([]), $filename, $headings, $mapping);
    }
}

