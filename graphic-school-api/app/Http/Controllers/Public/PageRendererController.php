<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PageBuilderService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageRendererController extends Controller
{
    public function __construct(
        private PageBuilderService $pageBuilderService
    ) {
    }

    /**
     * Render public page
     */
    public function render(string $academySlug, string $pageSlug): View|Response
    {
        $language = request()->get('lang', app()->getLocale());
        $page = $this->pageBuilderService->getPublicPage($academySlug, $pageSlug, $language);

        if (!$page) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return \App\Http\Responses\ApiResponse::notFound('Page not found');
            }
            abort(404, 'Page not found');
        }

        // Get branding for academy
        $branding = app(\App\Services\BrandingService::class)->getForFrontend();

        return view('page-builder.render', [
            'page' => $page,
            'structure' => $page->structure->structure ?? [],
            'branding' => $branding,
            'language' => $language,
        ]);
    }
}

