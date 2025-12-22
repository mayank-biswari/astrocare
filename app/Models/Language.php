<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_active',
        'is_default',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    public static function getActiveLanguages()
    {
        return self::where('is_active', true)->orderBy('sort_order')->get();
    }

    public static function getDefaultLanguage()
    {
        return self::where('is_default', true)->first();
    }

    public static function getLanguageByCode($code)
    {
        return self::where('code', $code)->where('is_active', true)->first();
    }
}
