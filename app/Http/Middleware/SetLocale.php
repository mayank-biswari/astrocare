<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Language;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $activeLanguages = Language::getActiveLanguages()->pluck('code')->toArray();
        $defaultLanguage = Language::getDefaultLanguage();
        
        $locale = session('locale', $defaultLanguage ? $defaultLanguage->code : config('app.locale'));
        
        if (in_array($locale, $activeLanguages)) {
            App::setLocale($locale);
        } else {
            App::setLocale($defaultLanguage ? $defaultLanguage->code : config('app.locale'));
        }
        
        return $next($request);
    }
}