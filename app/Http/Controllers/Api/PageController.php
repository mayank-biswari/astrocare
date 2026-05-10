<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * Return a published CMS page by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $page = CmsPage::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$page) {
            return response()->json([
                'message' => 'Page not found.',
            ], 404);
        }

        return response()->json([
            'title' => $page->title,
            'body' => $page->body,
        ], 200);
    }
}
