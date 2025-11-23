<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\EntityTranslationService;

class SetLocale
{
    public function __construct(
        private EntityTranslationService $translationService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detect locale using EntityTranslationService
        $locale = $this->translationService->detectLocale(
            $request->query('locale'),
            $request->header('Accept-Language'),
            $request->user()?->locale ?? $request->cookie('locale')
        );

        // Set application locale
        app()->setLocale($locale);

        // Store locale in request for later use
        $request->attributes->set('locale', $locale);

        $response = $next($request);

        // Set locale cookie for future requests
        $response->cookie('locale', $locale, 60 * 24 * 30); // 30 days

        return $response;
    }
}
