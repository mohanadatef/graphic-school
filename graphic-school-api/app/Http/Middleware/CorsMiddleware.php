<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight requests
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin($request))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Accept-Language, X-CSRF-TOKEN')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        try {
            $response = $next($request);
        } catch (\Exception $e) {
            // Even on error, add CORS headers
            $response = response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => app()->environment(['local', 'development']) ? $e->getTraceAsString() : null,
            ], 500);
        }

        // Add CORS headers to response (even on errors)
        return $response
            ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin($request))
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Accept-Language, X-CSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Expose-Headers', 'Authorization, X-Total-Count, X-Page, X-Per-Page');
    }

    /**
     * Get allowed origin for the request
     */
    private function getAllowedOrigin(Request $request): string
    {
        $origin = $request->header('Origin');
        
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:5173',
            'http://localhost:8080',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:5173',
            'http://127.0.0.1:8080',
            'http://graphic-school.test',
            'http://graphic-school-api.test',
            'http://graphic-school-frontend.test',
            env('FRONTEND_URL'),
        ];

        // Filter out empty values
        $allowedOrigins = array_filter($allowedOrigins);

        // If origin is in allowed list, return it; otherwise return wildcard
        if ($origin && in_array($origin, $allowedOrigins)) {
            return $origin;
        }

        // For development, always allow the origin if it exists
        if (app()->environment(['local', 'development', 'testing'])) {
            return $origin ?: '*';
        }

        // If origin is not in list but we're in dev, allow it anyway
        if (!$origin || in_array($origin, $allowedOrigins)) {
            return $origin ?: '*';
        }

        // For production, return first allowed origin or wildcard
        return !empty($allowedOrigins) ? reset($allowedOrigins) : '*';
    }
}

