<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsPage;
use App\Models\CmsComment;
use App\Models\Language;

class CmsController extends Controller
{
    public function index(Request $request)
    {
        $languageCode = $request->get('lang', Language::getDefaultLanguage()->code ?? 'en');
        $pages = CmsPage::where('is_published', true)
                       ->where('language_code', $languageCode)
                       ->latest()
                       ->paginate(10);
        return view('cms.index', compact('pages', 'languageCode'));
    }

    public function show($slug, Request $request)
    {
        $languageCode = $request->get('lang', session('locale', Language::getDefaultLanguage()->code ?? 'en'));

        // First try to find page in requested language
        $page = CmsPage::where('slug', $slug)
                      ->where('is_published', true)
                      ->where('language_code', $languageCode)
                      ->first();

        // If not found, try to find the base page and get its translation
        if (!$page) {
            $basePage = CmsPage::where('slug', $slug)
                              ->where('is_published', true)
                              ->first();

            if ($basePage) {
                $translation = $basePage->getTranslation($languageCode);
                if ($translation) {
                    // Create a virtual page object with translated content
                    $page = clone $basePage;
                    $page->title = $translation->title;
                    $page->body = $translation->body;
                    $page->meta_title = $translation->meta_title;
                    $page->meta_description = $translation->meta_description;
                    $page->meta_keywords = $translation->meta_keywords;
                } else {
                    $page = $basePage; // Fallback to original language
                }
            }
        }

        if (!$page) {
            abort(404);
        }

        $comments = $page->comments()->latest()->get();
        return view('cms.show', compact('page', 'comments', 'languageCode'));
    }

    public function testimonials(Request $request)
    {
        $languageCode = $request->get('lang', Language::getDefaultLanguage()->code ?? 'en');

        // Get testimonial page type
        $testimonialPageType = \App\Models\CmsPageType::where('name', 'Testimonials')->first();

        $testimonials = CmsPage::where('is_published', true)
                              ->where('language_code', $languageCode)
                              ->when($testimonialPageType, function($query) use ($testimonialPageType) {
                                  return $query->where('cms_page_type_id', $testimonialPageType->id);
                              })
                              ->latest()
                              ->paginate(9);

        return view('testimonials', compact('testimonials', 'languageCode'));
    }

    public function storeComment(Request $request, $slug)
    {
        $page = CmsPage::where('slug', $slug)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'comment' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'captcha' => 'required|captcha'
        ], [
            'captcha.required' => 'Please enter the security code.',
            'captcha.captcha' => 'The security code is incorrect. Please try again.'
        ]);

        CmsComment::create([
            'cms_page_id' => $page->id,
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
            'rating' => $request->rating
        ]);

        return redirect()->back()->with('success', 'Comment submitted for approval!');
    }

    public function blogs(Request $request)
    {
        $languageCode = $request->get('lang', Language::getDefaultLanguage()->code ?? 'en');

        $blogPageType = \App\Models\CmsPageType::where('name', 'Blogs')->first();

        $blogs = CmsPage::where('is_published', true)
                       ->where('language_code', $languageCode)
                       ->when($blogPageType, function($query) use ($blogPageType) {
                           return $query->where('cms_page_type_id', $blogPageType->id);
                       })
                       ->latest()
                       ->paginate(12);

        return view('blogs', compact('blogs', 'languageCode'));
    }

    public function viewListPage($slug, Request $request)
    {
        $list = \App\Models\AdminList::where('page_slug', $slug)
                                    ->where('create_page', true)
                                    ->where('is_active', true)
                                    ->firstOrFail();

        $perPage = $list->items_per_page ?: 12;

        if ($list->method === 'query_builder') {
            $model = $list->type === 'products' ? \App\Models\Product::query() : \App\Models\CmsPage::query();

            foreach ($list->configuration['filters'] ?? [] as $filter) {
                if (empty($filter['field']) || empty($filter['operator']) || $filter['value'] === '') {
                    continue;
                }

                $field = $filter['field'];
                $operator = $filter['operator'];
                $value = $filter['value'];

                if ($list->type === 'pages' && $field === 'page_type_id') {
                    $field = 'cms_page_type_id';
                }

                if ($operator === 'like') {
                    $model->where($field, 'like', '%' . $value . '%');
                } else {
                    $model->where($field, $operator, $value);
                }
            }

            $items = $model->paginate($perPage);
        } else {
            $allItems = $list->getResults();
            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $allItems->forPage($request->get('page', 1), $perPage),
                $allItems->count(),
                $perPage,
                $request->get('page', 1),
                ['path' => $request->url(), 'pageName' => 'page']
            );
        }

        return view('list-page', compact('list', 'items'));
    }

    public function viewDynamicPage($url)
    {
        $page = \App\Models\DynamicPage::where('url', $url)
                                      ->where('is_published', true)
                                      ->firstOrFail();

        return view('dynamic-page', compact('page'));
    }
}
