<?php

namespace App\Http\Controllers\PageBuilder;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\PageBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageBuilderController extends Controller
{
    public function __construct(
        private PageBuilderService $pageBuilderService
    ) {
    }

    /**
     * List pages
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'language', 'search', 'per_page']);
        $pages = $this->pageBuilderService->getPages($request->user(), $filters);
        return ApiResponse::success($pages, 'Pages retrieved successfully');
    }

    /**
     * Get single page
     */
    public function show(int $id): JsonResponse
    {
        $page = \App\Models\PageBuilderPage::with('structure')
            ->where('academy_id', request()->user()->id)
            ->findOrFail($id);
        
        return ApiResponse::success($page, 'Page retrieved successfully');
    }

    /**
     * Create page
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'language' => 'nullable|string|in:en,ar',
            'status' => 'nullable|string|in:published,draft',
            'structure' => 'nullable|array',
        ]);

        $page = $this->pageBuilderService->createPage($request->user(), $validated);
        return ApiResponse::success($page, 'Page created successfully', 201);
    }

    /**
     * Update page
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'language' => 'sometimes|string|in:en,ar',
            'status' => 'sometimes|string|in:published,draft',
        ]);

        $page = $this->pageBuilderService->updatePage($id, $validated);
        return ApiResponse::success($page, 'Page updated successfully');
    }

    /**
     * Delete page
     */
    public function destroy(int $id): JsonResponse
    {
        $page = \App\Models\PageBuilderPage::where('academy_id', request()->user()->id)
            ->findOrFail($id);
        
        $page->delete();
        
        // Decrement usage
        $subscriptionService = app(\App\Services\SubscriptionService::class);
        $subscriptionService->decrementUsage(request()->user(), 'pages');
        
        return ApiResponse::success(null, 'Page deleted successfully', 204);
    }

    /**
     * Save structure
     */
    public function saveStructure(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'structure' => 'required|array',
        ]);

        $structure = $this->pageBuilderService->saveStructure($id, $validated['structure']);
        return ApiResponse::success($structure, 'Structure saved successfully');
    }

    /**
     * Publish page
     */
    public function publish(int $id): JsonResponse
    {
        $page = $this->pageBuilderService->publishPage($id);
        return ApiResponse::success($page, 'Page published successfully');
    }

    /**
     * Duplicate page
     */
    public function duplicate(int $id): JsonResponse
    {
        $page = $this->pageBuilderService->duplicatePage($id, request()->user());
        return ApiResponse::success($page, 'Page duplicated successfully', 201);
    }

    /**
     * Get templates
     */
    public function templates(): JsonResponse
    {
        $templates = \App\Models\PageBuilderTemplate::all();
        return ApiResponse::success($templates, 'Templates retrieved successfully');
    }

    /**
     * Apply template
     */
    public function applyTemplate(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'template_id' => 'required|exists:page_builder_templates,id',
        ]);

        $structure = $this->pageBuilderService->applyTemplate($id, $validated['template_id']);
        return ApiResponse::success($structure, 'Template applied successfully');
    }
}

