<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Translation\ListTranslationRequest;
use App\Http\Requests\Admin\Translation\StoreTranslationRequest;
use App\Http\Requests\Admin\Translation\UpdateTranslationRequest;
use App\Http\Resources\TranslationResource;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TranslationController extends Controller
{
    public function __construct(private TranslationService $translationService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListTranslationRequest $request): JsonResponse
    {
        Log::info('Listing translations', $request->validated());

        $translations = $this->translationService->paginate(
            $request->validated(),
            $request->integer('per_page', 15)
        );

        $groups = $this->translationService->getGroups();
        $locales = $this->translationService->getLocales();

        return response()->json([
            'data' => TranslationResource::collection($translations),
            'meta' => [
                'groups' => $groups,
                'locales' => $locales,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTranslationRequest $request): JsonResponse
    {
        Log::info('Creating translation', $request->validated());

        $translation = $this->translationService->create($request->validated());

        return TranslationResource::make($translation)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Translation $translation): JsonResponse
    {
        return response()->json(TranslationResource::make($translation));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTranslationRequest $request, Translation $translation): JsonResponse
    {
        Log::info('Updating translation', [
            'id' => $translation->id,
            'data' => $request->validated(),
        ]);

        $translation = $this->translationService->update($translation, $request->validated());

        return response()->json(TranslationResource::make($translation));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Translation $translation): JsonResponse
    {
        Log::warning('Deleting translation', [
            'id' => $translation->id,
            'key' => $translation->key,
            'locale' => $translation->locale,
        ]);

        $this->translationService->delete($translation);

        return response()->json(['message' => 'Translation deleted successfully']);
    }

    /**
     * Get all groups
     */
    public function groups(): JsonResponse
    {
        $groups = $this->translationService->getGroups();
        return response()->json(['groups' => $groups]);
    }

    /**
     * Get all locales
     */
    public function locales(): JsonResponse
    {
        $locales = $this->translationService->getLocales();
        return response()->json(['locales' => $locales]);
    }

    /**
     * Clear translation cache
     */
    public function clearCache(): JsonResponse
    {
        Log::info('Clearing translation cache');
        $this->translationService->clearCache();
        return response()->json(['message' => 'Translation cache cleared successfully']);
    }
}
