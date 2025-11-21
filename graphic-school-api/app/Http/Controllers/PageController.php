<?php

namespace App\Http\Controllers;

use App\Support\Controllers\BaseController;
use App\Models\Page;
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
    public function show(string $slug): JsonResponse
    {
        $page = Page::findBySlug($slug);

        if (! $page) {
            return $this->notFound('Page not found');
        }

        return $this->success($page, 'Page retrieved successfully');
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
        ]);

        $page = Page::create($validated);

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
        ]);

        $page->update($validated);

        return $this->success($page, 'Page updated successfully');
    }

    /**
     * Delete page (Admin)
     */
    public function destroy(int $id): JsonResponse
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return $this->noContent();
    }
}
