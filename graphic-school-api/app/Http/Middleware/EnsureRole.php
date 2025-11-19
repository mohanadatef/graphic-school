<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        $allowed = collect(explode('|', $roles))
            ->map(fn ($role) => trim($role))
            ->filter()
            ->toArray();

        if (! $user || empty($allowed) || ! in_array($user->role?->name, $allowed, true) || ! $user->is_active) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized role');
        }

        return $next($request);
    }
}
