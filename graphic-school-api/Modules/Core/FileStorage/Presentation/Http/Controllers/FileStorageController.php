<?php

namespace Modules\Core\FileStorage\Presentation\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\Core\FileStorage\Application\UseCases\UploadFileUseCase;
use Modules\Core\FileStorage\Application\UseCases\DeleteFileUseCase;
use Modules\Core\FileStorage\Application\DTOs\UploadFileDTO;
use Illuminate\Http\Request;

class FileStorageController extends BaseController
{
    public function upload(Request $request, UploadFileUseCase $useCase)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'disk' => 'sometimes|string|in:public,local,s3',
            'directory' => 'sometimes|string|max:255',
        ]);

        $dto = UploadFileDTO::fromArray([
            'file' => $request->file('file'),
            'disk' => $request->input('disk', 'public'),
            'directory' => $request->input('directory'),
            'generateUniqueName' => $request->boolean('generate_unique_name', true),
        ]);

        $result = $useCase->execute($dto);

        return $this->created($result, 'File uploaded successfully');
    }

    public function delete(Request $request, DeleteFileUseCase $useCase)
    {
        $request->validate([
            'path' => 'required|string',
            'disk' => 'sometimes|string|in:public,local,s3',
        ]);

        $useCase->execute([
            'path' => $request->input('path'),
            'disk' => $request->input('disk', 'public'),
        ]);

        return $this->success(null, 'File deleted successfully');
    }
}

