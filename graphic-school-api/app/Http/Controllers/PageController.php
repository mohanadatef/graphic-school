<?php

namespace App\Http\Controllers;

use App\Support\Controllers\BaseController;
use App\Models\Page;
use App\Services\EntityTranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * CHANGE-002: CMS Page Builder
 */
class PageController extends BaseController
{
    /**
     * Get all pages (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Page::query();

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filter by is_active
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $pages = $query->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return $this->paginated($pages, 'Pages retrieved successfully');
    }

    /**
     * Get page by slug (Public)
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $page = Page::where('slug', $slug)->where('is_active', true)->first();

        if (! $page) {
            return $this->notFound('Page not found');
        }

        // Get locale from request
        $locale = $request->attributes->get('locale') ?? app()->getLocale();
        $translationService = app(EntityTranslationService::class);

        // Get translated fields
        $title = $translationService->getTranslatedField($page, 'title', $locale, $page->title);
        $content = $translationService->getTranslatedField($page, 'content', $locale, $page->content);
        $metaTitle = $translationService->getTranslatedField($page, 'meta_title', $locale);
        $metaDescription = $translationService->getTranslatedField($page, 'meta_description', $locale);
        $sections = $translationService->getTranslatedField($page, 'sections', $locale, $page->sections);

        // Include sections in response
        $pageData = $page->toArray();
        $pageData['title'] = $title;
        $pageData['content'] = $content;
        $pageData['meta_title'] = $metaTitle;
        $pageData['meta_description'] = $metaDescription;
        $pageData['sections'] = $sections ?? [
            'slider' => true,
            'testimonials' => true,
            'featured_courses' => true,
            'statistics' => true,
            'faq' => true,
        ];

        // Include translations if requested (admin)
        if ($request->has('include_translations')) {
            $pageData['translations'] = $page->translations()->get()->map(function ($translation) {
                return [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'content' => $translation->content,
                    'meta_title' => $translation->meta_title,
                    'meta_description' => $translation->meta_description,
                    'sections' => $translation->sections,
                ];
            });
        }

        return $this->success($pageData, 'Page retrieved successfully');
    }

    /**
     * Create page (Admin)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => 'required|string|unique:pages,slug|max:255',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'template' => 'nullable|string|max:255',
            'sections' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|in:ar,en',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'translations.*.sections' => 'nullable|array',
        ]);

        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);

        $page = Page::create($validated);

        // Save translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($page, $translations);
        }

        // Load translations for response
        $page->load('translations');

        return $this->created($page, 'Page created successfully');
    }

    /**
     * Update page (Admin)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'slug' => 'sometimes|string|unique:pages,slug,' . $id . '|max:255',
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'template' => 'nullable|string|max:255',
            'sections' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|in:ar,en',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.content' => 'nullable|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string',
            'translations.*.sections' => 'nullable|array',
        ]);

        $translations = $validated['translations'] ?? [];
        unset($validated['translations']);

        $page->update($validated);

        // Update translations if provided
        if (!empty($translations)) {
            $translationService = app(EntityTranslationService::class);
            $translationService->saveTranslations($page, $translations);
        }

        // Load translations for response
        $page->load('translations');

        return $this->success($page, 'Page updated successfully');
    }

    /**
     * Delete page (Admin)
     */
    public function destroy(int $id): JsonResponse
    {
        $page = Page::findOrFail($id);
        
        // Delete translations (cascade should handle this, but explicit for clarity)
        $translationService = app(EntityTranslationService::class);
        $translationService->deleteTranslations($page);
        
        $page->delete();

        return $this->noContent();
    }
}
