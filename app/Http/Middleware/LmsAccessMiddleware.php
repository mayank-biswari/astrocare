<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LmsAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->hasPermissionTo('access lms')) {
            abort(403, 'Unauthorized. You do not have LMS access.');
        }

        return $next($request);
    }
}
