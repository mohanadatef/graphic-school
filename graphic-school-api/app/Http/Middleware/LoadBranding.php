<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\BrandingService;
use Symfony\Component\HttpFoundation\Response;

class LoadBranding
{
    public function __construct(private BrandingService $brandingService)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Load branding settings
        $branding = $this->brandingService->all();
        
        // Make branding available globally
        config(['branding' => $branding]);
        
        // Add branding to response headers (for frontend to access)
        $response = $next($request);
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            // Branding is already available via /api/branding/frontend endpoint
            // No need to add to every response
        }
        
        return $response;
    }
}

