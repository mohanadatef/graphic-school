<?php

namespace Modules\Operations\Analytics\Http\Middleware;

use Modules\Operations\Analytics\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    public function __construct(private AnalyticsService $analyticsService)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $response = $next($request);

        // Only track successful responses
        if ($response->getStatusCode() === 200) {
            $this->track($request, $type);
        }

        return $response;
    }

    /**
     * Track the visit
     */
    protected function track(Request $request, string $type): void
    {
        $id = $request->route($type); // course, instructor, etc.

        if ($id) {
            $this->analyticsService->trackVisit(
                $this->getModelClass($type),
                $id,
                $request->user()?->id,
                $request->ip(),
                $request->userAgent(),
                $request->header('referer')
            );
        }
    }

    /**
     * Get model class for type
     */
    protected function getModelClass(string $type): string
    {
        return match ($type) {
            'course' => \Modules\LMS\Courses\Models\Course::class,
            'instructor' => \Modules\ACL\Users\Models\User::class,
            default => throw new \InvalidArgumentException("Unknown type: {$type}"),
        };
    }
}

