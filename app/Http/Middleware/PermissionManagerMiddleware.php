<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Authorization logic:
     * 1. If user ID === 1 (Super Admin), allow immediately — bypass all checks.
     * 2. Otherwise, check if the user has the required granular permission via Spatie's hasPermissionTo().
     * 3. If not, abort with 403.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $permission  The required granular permission name
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();

        // Super Admin bypass — user ID 1 always has full access
        if ($user->id === 1) {
            return $next($request);
        }

        // Check granular permission via Spatie
        if ($user->hasPermissionTo($permission)) {
            return $next($request);
        }

        // Deny access
        abort(403, 'You do not have permission to access this section.');
    }
}
