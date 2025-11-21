<?php

namespace Modules\Core\ExportImport\Application\DTOs;

use App\Support\DTOs\BaseDTO;

class ExportDataDTO extends BaseDTO
{
    public string $type; // excel, pdf, csv
    public array $data;
    public array $headings;
    public ?string $fileName = null;
    public $mapping = null; // callable - cannot be typed

    public function validate(): void
    {
        if (!in_array($this->type, ['excel', 'pdf', 'csv'])) {
            throw new \App\Exceptions\DomainException(
                'Invalid export type',
                ['type' => 'Type must be excel, pdf, or csv'],
                422
            );
        }

        if (empty($this->data)) {
            throw new \App\Exceptions\DomainException(
                'Data is required',
                ['data' => 'Data array cannot be empty'],
                422
            );
        }
    }
}

