<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    /**
     * Handle an incoming request.
     * 
     * Supports multiple permissions: permission:view_courses|manage_courses
     * User needs at least one of the specified permissions.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated');
        }

        // Super Admin bypasses all permission checks
        if ($user->role?->name === 'super_admin') {
            return $next($request);
        }

        // Admin bypasses most checks (can be restricted later if needed)
        if ($user->role?->name === 'admin' && ! $user->is_active) {
            abort(Response::HTTP_FORBIDDEN, 'Account is inactive');
        }

        // Parse multiple permissions (separated by |)
        $requiredPermissions = collect(explode('|', $permissions))
            ->map(fn ($perm) => trim($perm))
            ->filter()
            ->toArray();

        if (empty($requiredPermissions)) {
            abort(Response::HTTP_FORBIDDEN, 'No permission specified');
        }

        // Check if user has at least one of the required permissions
        $hasPermission = false;
        foreach ($requiredPermissions as $permission) {
            if ($user->hasPermission($permission)) {
                $hasPermission = true;
                break;
            }
        }

        // Admin has all permissions by default (unless explicitly restricted)
        if (! $hasPermission && $user->role?->name === 'admin') {
            $hasPermission = true;
        }

        if (! $hasPermission) {
            abort(Response::HTTP_FORBIDDEN, 'Permission denied: ' . implode(' or ', $requiredPermissions));
        }

        return $next($request);
    }
}
