<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    /**
     * Get all pages
     * GET /api/admin/pages
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $query = Page::query();

        // Apply filters
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhereJsonContains('title', $search);
            });
        }

        // Order by sort_order, then slug
        $query->orderBy('sort_order')->orderBy('slug');

        $pages = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $pages->items(),
            'meta' => [
                'pagination' => [
                    'current_page' => $pages->currentPage(),
                    'per_page' => $pages->perPage(),
                    'total' => $pages->total(),
                    'last_page' => $pages->lastPage(),
                ],
            ],
        ]);
    }

    /**
     * Get single page with blocks
     * GET /api/admin/pages/{id}
     */
    public function show(Page $page): JsonResponse
    {
        $page->load('blocks');
        
        return response()->json([
            'success' => true,
            'data' => $page,
        ]);
    }

    /**
     * Get page by slug
     * GET /api/admin/pages/slug/{slug}
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->with('blocks')->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $page,
        ]);
    }

    /**
     * Create new page
     * POST /api/admin/pages
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slug' => 'required|string|max:255|unique:pages,slug',
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'content' => 'nullable|array',
            'meta_description' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $page = Page::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully',
            'data' => $page,
        ], 201);
    }

    /**
     * Update page
     * PUT /api/admin/pages/{id}
     */
    public function update(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', Rule::unique('pages')->ignore($page->id)],
            'title' => 'required|array',
            'title.*' => 'required|string|max:255',
            'content' => 'nullable|array',
            'meta_description' => 'nullable|array',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $page->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully',
            'data' => $page->fresh('blocks'),
        ]);
    }

    /**
     * Delete page
     * DELETE /api/admin/pages/{id}
     */
    public function destroy(Page $page): JsonResponse
    {
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully',
        ]);
    }

    /**
     * Update page blocks
     * PUT /api/admin/pages/{id}/blocks
     */
    public function updateBlocks(Request $request, Page $page): JsonResponse
    {
        $validated = $request->validate([
            'blocks' => 'required|array',
            'blocks.*.id' => 'nullable|exists:page_blocks,id',
            'blocks.*.type' => 'required|string',
            'blocks.*.title' => 'nullable|array',
            'blocks.*.content' => 'nullable|array',
            'blocks.*.config' => 'nullable|array',
            'blocks.*.is_enabled' => 'boolean',
            'blocks.*.sort_order' => 'nullable|integer|min:0',
        ]);

        // Delete blocks that are not in the request
        $blockIds = collect($validated['blocks'])->pluck('id')->filter();
        $page->blocks()->whereNotIn('id', $blockIds)->delete();

        // Update or create blocks
        foreach ($validated['blocks'] as $blockData) {
            if (isset($blockData['id'])) {
                $block = $page->blocks()->find($blockData['id']);
                if ($block) {
                    $block->update($blockData);
                }
            } else {
                $page->blocks()->create($blockData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Page blocks updated successfully',
            'data' => $page->fresh('blocks'),
        ]);
    }
}

