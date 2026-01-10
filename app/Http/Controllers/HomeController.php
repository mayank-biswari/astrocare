<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsPage;
use App\Models\CmsPageType;

class HomeController extends Controller
{
    public function index()
    {
        // Get Home Hero Pages
        $heroPagesType = CmsPageType::where('name', 'Home Hero Pages')->first();
        $heroPages = collect();
        if ($heroPagesType) {
            $heroPages = CmsPage::where('cms_page_type_id', $heroPagesType->id)
                ->where('is_published', true)
                ->latest()
                ->get();
        }

        // Get testimonials page type
        $testimonialsPageType = CmsPageType::where('name', 'Testimonials')->first();

        $testimonials = collect();
        if ($testimonialsPageType) {
            $testimonials = CmsPage::where('cms_page_type_id', $testimonialsPageType->id)
                ->where('is_published', true)
                ->latest()
                ->limit(6)
                ->get();
        }

        // Get services page type
        $servicesPageType = CmsPageType::where('name', 'Services')->first();

        $services = collect();
        if ($servicesPageType) {
            $services = CmsPage::where('cms_page_type_id', $servicesPageType->id)
                ->where('is_published', true)
                ->orderBy('created_at')
                ->get();
        }

        // Get sacred products page type
        $productsPageType = CmsPageType::where('name', 'Sacred Products')->first();

        $products = collect();
        if ($productsPageType) {
            $products = CmsPage::where('cms_page_type_id', $productsPageType->id)
                ->where('is_published', true)
                ->orderBy('created_at')
                ->get();
        }

        return view('home', compact('testimonials', 'services', 'products', 'heroPages'));
    }

    public function about()
    {
        return view('about');
    }

    public function terms()
    {
        return view('terms');
    }
}
