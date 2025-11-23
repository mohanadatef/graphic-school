<?php

namespace App\Http\Controllers;

use App\Support\Controllers\BaseController;
use App\Models\FAQ;
use App\Services\EntityTranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * CHANGE-002: CMS FAQ Management
 */
class FAQController extends BaseController
{
    /**
     * Get all FAQs (Public - only active)
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->attributes->get('locale') ?? app()->getLocale();
        $translationService = app(EntityTranslationService::class);

        $query = FAQ::active()->ordered();

        // Filter by category
        if ($request->has('category')) {
            $query->forCategory($request->input('category'));
        }

        $faqs = $query->get()->map(function ($faq) use ($translationService, $locale) {
            $faqData = $faq->toArray();
            $faqData['question'] = $translationService->getTranslatedField($faq, 'question', $locale, $faq->question);
            $faqData['answer'] = $translationService->getTranslatedField($faq, 'answer', $locale, $faq->answer);
            return $faqData;
        });

        return $this->success($faqs, 'FAQs retrieved successfully');
    }

    /**
     * Get all FAQs (Admin - all)
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = FAQ::query()->ordered();

        // Filter by category
        if ($request->has('category')) {
            $query->forCategory($request->input('category'));
        }

        $faqs = $query->paginate($request->input('per_page', 15));

        return $this->paginated($faqs, 'FAQs retrieved successfully');
    }

    /**
     * Create FAQ (Admin)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'category' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|in:ar,en',
            'translations.*.question' => 'nullable|string|max:500',
            'translations.*.answer' => 'nullable|string|max:2000',
        ]);

        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);

        $faq = FAQ::create($validated);

        // Save translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($faq, $translations);
        }

        // Load translations for response
        $faq->load('translations');

        return $this->created($faq, 'FAQ created successfully');
    }

    /**
     * Update FAQ (Admin)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $faq = FAQ::findOrFail($id);

        $validated = $request->validate([
            'question' => 'sometimes|string|max:500',
            'answer' => 'sometimes|string|max:2000',
            'category' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|in:ar,en',
            'translations.*.question' => 'nullable|string|max:500',
            'translations.*.answer' => 'nullable|string|max:2000',
        ]);

        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);

        $faq->update($validated);

        // Update translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($faq, $translations);
        }

        // Load translations for response
        $faq->load('translations');

        return $this->success($faq, 'FAQ updated successfully');
    }

    /**
     * Delete FAQ (Admin)
     */
    public function destroy(int $id): JsonResponse
    {
        $faq = FAQ::findOrFail($id);
        
        // Delete translations
        $translationService = app(EntityTranslationService::class);
        $translationService->deleteTranslations($faq);
        
        $faq->delete();

        return $this->noContent();
    }
}
