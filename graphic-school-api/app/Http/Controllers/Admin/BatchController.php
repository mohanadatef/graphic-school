<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Repositories\Interfaces\BatchRepositoryInterface;
use App\Services\EntityTranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BatchController extends Controller
{
    public function __construct(
        private BatchRepositoryInterface $batchRepository,
        private EntityTranslationService $translationService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['program_id', 'is_active', 'start_date_from', 'start_date_to']);
        $perPage = $request->integer('per_page', 15);
        
        $batches = $this->batchRepository->paginateWithFilters($filters, $perPage);
        
        $includeTranslations = $request->has('include_translations');
        $batches->getCollection()->transform(function ($batch) use ($includeTranslations) {
            $locale = app()->getLocale();
            $batchData = $batch->toArray();
            $batchData['name'] = $batch->getTranslated('name', $locale);
            $batchData['description'] = $batch->getTranslated('description', $locale);
            
            if ($includeTranslations) {
                $batchData['translations'] = $batch->translations->map(fn($t) => $t->toArray());
            }
            
            return $batchData;
        });
        
        return response()->json([
            'success' => true,
            'data' => $batches->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $batches->currentPage(),
                    'per_page' => $batches->perPage(),
                    'total' => $batches->total(),
                    'last_page' => $batches->lastPage(),
                ],
            ],
        ]);
    }

    public function show(Batch $batch, Request $request): JsonResponse
    {
        $batch = $this->batchRepository->loadRelations($batch, ['translations', 'program', 'groups']);
        
        $locale = app()->getLocale();
        $batchData = $batch->toArray();
        $batchData['name'] = $batch->getTranslated('name', $locale);
        $batchData['description'] = $batch->getTranslated('description', $locale);
        
        if ($request->has('include_translations')) {
            $batchData['translations'] = $batch->translations->map(fn($t) => $t->toArray());
        }
        
        return response()->json([
            'success' => true,
            'data' => $batchData,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'code' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|in:en,ar',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);
        
        $translations = $validated['translations'];
        unset($validated['translations']);
        
        $batch = $this->batchRepository->create($validated);
        $this->translationService->saveTranslations($batch, $translations);
        
        $batch->load('translations');
        
        return response()->json([
            'success' => true,
            'message' => 'Batch created successfully',
            'data' => $batch,
        ], 201);
    }

    public function update(Request $request, Batch $batch): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_students' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'translations' => 'sometimes|array',
            'translations.*.locale' => 'required|string|in:en,ar',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);
        
        $translations = $validated['translations'] ?? null;
        if (isset($validated['translations'])) {
            unset($validated['translations']);
        }
        
        $batch = $this->batchRepository->update($batch, $validated);
        
        if ($translations) {
            $this->translationService->saveTranslations($batch, $translations);
        }
        
        $batch->load('translations');
        
        return response()->json([
            'success' => true,
            'message' => 'Batch updated successfully',
            'data' => $batch,
        ]);
    }

    public function destroy(Batch $batch): JsonResponse
    {
        $batch->translations()->delete();
        $this->batchRepository->delete($batch);
        
        return response()->json([
            'success' => true,
            'message' => 'Batch deleted successfully',
        ]);
    }
}

