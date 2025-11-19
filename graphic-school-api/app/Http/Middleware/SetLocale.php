<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from header, query parameter, or default
        $locale = $request->header('Accept-Language') 
            ?? $request->query('locale') 
            ?? $request->cookie('locale')
            ?? config('app.locale', 'en');

        // Validate locale (only allow en or ar)
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale', 'en');
        }

        app()->setLocale($locale);

        $response = $next($request);

        // Set locale cookie for future requests
        $response->cookie('locale', $locale, 60 * 24 * 30); // 30 days

        return $response;
    }
}
