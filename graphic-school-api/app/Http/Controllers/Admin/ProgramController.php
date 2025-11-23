<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Repositories\Interfaces\ProgramRepositoryInterface;
use App\Services\EntityTranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function __construct(
        private ProgramRepositoryInterface $programRepository,
        private EntityTranslationService $translationService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'is_active', 'search']);
        $perPage = $request->integer('per_page', 15);
        
        $programs = $this->programRepository->paginateWithFilters($filters, $perPage);
        
        // Apply translations to each program
        $includeTranslations = $request->has('include_translations');
        $programs->getCollection()->transform(function ($program) use ($includeTranslations) {
            $locale = app()->getLocale();
            $programData = $program->toArray();
            $programData['title'] = $program->getTranslated('title', $locale);
            $programData['description'] = $program->getTranslated('description', $locale);
            $programData['meta_title'] = $program->getTranslated('meta_title', $locale);
            $programData['meta_description'] = $program->getTranslated('meta_description', $locale);
            
            if ($includeTranslations) {
                $programData['translations'] = $program->translations->map(fn($t) => $t->toArray());
            }
            
            return $programData;
        });
        
        return response()->json([
            'success' => true,
            'data' => $programs->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $programs->currentPage(),
                    'per_page' => $programs->perPage(),
                    'total' => $programs->total(),
                    'last_page' => $programs->lastPage(),
                ],
            ],
        ]);
    }

    public function show(Program $program, Request $request): JsonResponse
    {
        $program = $this->programRepository->loadRelations($program, ['translations', 'batches.groups']);
        
        $locale = app()->getLocale();
        $programData = $program->toArray();
        $programData['title'] = $program->getTranslated('title', $locale);
        $programData['description'] = $program->getTranslated('description', $locale);
        $programData['meta_title'] = $program->getTranslated('meta_title', $locale);
        $programData['meta_description'] = $program->getTranslated('meta_description', $locale);
        
        if ($request->has('include_translations')) {
            $programData['translations'] = $program->translations->map(fn($t) => $t->toArray());
        }
        
        return response()->json([
            'success' => true,
            'data' => $programData,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // Check subscription usage limit
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        try {
            $subscriptionService->blockIfOverLimit($request->user(), 'programs');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
        
        // Handle translations from JSON string if sent as FormData
        $translationsData = $request->input('translations');
        if (is_string($translationsData)) {
            $translationsData = json_decode($translationsData, true);
        }
        
        $validated = $request->validate([
            'type' => 'required|string|in:bootcamp,track,workshop,course',
            'duration_weeks' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'image' => 'nullable|file|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);
        
        // Validate translations separately
        if (empty($translationsData) || !is_array($translationsData)) {
            return response()->json([
                'success' => false,
                'message' => 'Translations are required',
            ], 422);
        }
        
        foreach ($translationsData as $trans) {
            $request->validate([
                'translations.*.locale' => 'required|string|in:en,ar',
                'translations.*.title' => 'required|string|max:255',
                'translations.*.description' => 'nullable|string',
                'translations.*.meta_title' => 'nullable|string|max:255',
                'translations.*.meta_description' => 'nullable|string',
            ]);
        }
        
        $translations = $translationsData;
        
        // Generate slug from first translation title
        $firstTranslation = collect($translations)->first();
        $validated['slug'] = Str::slug($firstTranslation['title']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('programs', 'public');
        }
        
        $program = $this->programRepository->create($validated);
        
        // Increment usage
        $subscriptionService->incrementUsage($request->user(), 'programs');
        
        if (!empty($translations)) {
            $this->translationService->saveTranslations($program, $translations);
        }
        
        $program->load('translations');
        
        // Apply translations to response
        $locale = app()->getLocale();
        $programData = $program->toArray();
        $programData['title'] = $program->getTranslated('title', $locale);
        $programData['description'] = $program->getTranslated('description', $locale);
        
        return response()->json([
            'success' => true,
            'message' => 'Program created successfully',
            'data' => $programData,
        ], 201);
    }

    public function update(Request $request, Program $program): JsonResponse
    {
        // Handle translations from JSON string if sent as FormData
        $translationsData = $request->input('translations');
        if (is_string($translationsData)) {
            $translationsData = json_decode($translationsData, true);
        }
        
        $validated = $request->validate([
            'type' => 'sometimes|string|in:bootcamp,track,workshop,course',
            'duration_weeks' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'image' => 'nullable|file|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);
        
        $translations = null;
        if (!empty($translationsData) && is_array($translationsData)) {
            foreach ($translationsData as $trans) {
                $request->validate([
                    'translations.*.locale' => 'required|string|in:en,ar',
                    'translations.*.title' => 'required|string|max:255',
                    'translations.*.description' => 'nullable|string',
                    'translations.*.meta_title' => 'nullable|string|max:255',
                    'translations.*.meta_description' => 'nullable|string',
                ]);
            }
            $translations = $translationsData;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            if ($program->image_path) {
                Storage::disk('public')->delete($program->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('programs', 'public');
        }
        
        $program = $this->programRepository->update($program, $validated);
        
        if ($translations) {
            $this->translationService->saveTranslations($program, $translations);
        }
        
        $program->load('translations');
        
        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully',
            'data' => $program,
        ]);
    }

    public function destroy(Program $program): JsonResponse
    {
        // Delete translations
        $program->translations()->delete();
        
        // Delete image if exists
        if ($program->image_path) {
            Storage::disk('public')->delete($program->image_path);
        }
        
        $this->programRepository->delete($program);
        
        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully',
        ]);
    }
}

