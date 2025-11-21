<?php

namespace Modules\Core\ExportImport\Presentation\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\Core\ExportImport\Application\UseCases\ExportDataUseCase;
use Modules\Core\ExportImport\Application\DTOs\ExportDataDTO;
use Illuminate\Http\Request;

class ExportImportController extends BaseController
{
    public function export(Request $request, ExportDataUseCase $useCase)
    {
        $request->validate([
            'type' => 'required|string|in:excel,pdf,csv',
            'data' => 'required|array',
            'headings' => 'sometimes|array',
            'file_name' => 'sometimes|string|max:255',
        ]);

        $dto = ExportDataDTO::fromArray([
            'type' => $request->input('type'),
            'data' => $request->input('data'),
            'headings' => $request->input('headings', []),
            'fileName' => $request->input('file_name'),
        ]);

        $result = $useCase->execute($dto);

        return $this->success($result, 'Export completed successfully');
    }
}

