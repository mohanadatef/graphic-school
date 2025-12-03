<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Get page by slug (public)
     * GET /api/public/pages/{slug}
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->header('Accept-Language') 
            ?? $request->query('locale') 
            ?? app()->getLocale();

        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->with(['blocks' => function ($query) {
                $query->where('is_enabled', true)->orderBy('sort_order');
            }])
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        }

        // Format response with localized content
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->getTitle($locale),
                'content' => $page->getContent($locale),
                'meta_description' => $page->getMetaDescription($locale),
                'blocks' => $page->enabledBlocks->map(function ($block) use ($locale) {
                    return [
                        'id' => $block->id,
                        'type' => $block->type,
                        'title' => $block->getTitle($locale),
                        'content' => $block->getContent($locale),
                        'config' => $block->config,
                    ];
                }),
            ],
        ]);
    }
}

