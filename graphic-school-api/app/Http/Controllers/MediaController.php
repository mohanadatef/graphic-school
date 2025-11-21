<?php

namespace App\Http\Controllers;

use App\Support\Controllers\BaseController;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * CHANGE-002: CMS Media Library
 */
class MediaController extends BaseController
{
    /**
     * Get all media (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Media::query();

        // Filter by type
        if ($request->has('type')) {
            $query->ofType($request->input('type'));
        }

        // Filter images only
        if ($request->boolean('images_only')) {
            $query->images();
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $media = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginated($media, 'Media retrieved successfully');
    }

    /**
     * Upload media (Admin)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');
        
        // Determine type
        $mimeType = $file->getMimeType();
        $type = 'document';
        if (str_starts_with($mimeType, 'image/')) {
            $type = 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $type = 'video';
        }

        // Get metadata for images
        $metadata = null;
        if ($type === 'image') {
            $imageInfo = getimagesize($file->getRealPath());
            $metadata = [
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null,
            ];
        }

        $media = Media::create([
            'name' => $file->getClientOriginalName(),
            'file_name' => $file->hashName(),
            'file_path' => $path,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'disk' => 'public',
            'type' => $type,
            'alt_text' => $validated['alt_text'] ?? null,
            'description' => $validated['description'] ?? null,
            'uploaded_by' => $request->user()->id,
            'metadata' => $metadata,
        ]);

        return $this->created($media, 'Media uploaded successfully');
    }

    /**
     * Update media (Admin)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $media = Media::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $media->update($validated);

        return $this->success($media, 'Media updated successfully');
    }

    /**
     * Delete media (Admin)
     */
    public function destroy(int $id): JsonResponse
    {
        $media = Media::findOrFail($id);

        // Delete file from storage
        Storage::disk($media->disk)->delete($media->file_path);

        $media->delete();

        return $this->noContent();
    }
}
