<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            abort(403, 'Access denied.');
        }

        $user = auth()->user();

        // Allow admins full access
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Allow users with any admin-level prediction permission
        if ($user->hasAnyPermission(['view predictions', 'edit predictions', 'delete predictions'])) {
            return $next($request);
        }

        abort(403, 'Access denied. Admin privileges required.');
    }
}
