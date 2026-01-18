<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\DynamicPage;

class CheckDynamicPage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');

        $path = $path ? $path : "<home>";

        // Check if a dynamic page exists for this URL
        $dynamicPage = DynamicPage::where('url', $path)
            ->where('is_published', true)
            ->first();

        if ($dynamicPage) {
            return response()->view('dynamic-page', ['page' => $dynamicPage]);
        }

        return $next($request);
    }
}
