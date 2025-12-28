<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    protected $fillable = [
        'title', 'slug', 'body', 'image', 'meta_title', 'meta_description', 
        'meta_keywords', 'rating', 'rating_count', 'is_published', 'allow_comments', 
        'cms_category_id', 'cms_page_type_id', 'custom_fields', 'language_code'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'allow_comments' => 'boolean',
        'rating' => 'decimal:2',
        'custom_fields' => 'array'
    ];

    public function comments()
    {
        return $this->hasMany(CmsComment::class)->where('is_approved', true);
    }

    public function allComments()
    {
        return $this->hasMany(CmsComment::class);
    }

    public function category()
    {
        return $this->belongsTo(CmsCategory::class, 'cms_category_id');
    }

    public function pageType()
    {
        return $this->belongsTo(CmsPageType::class, 'cms_page_type_id');
    }

    public function translations()
    {
        return $this->hasMany(CmsPageTranslation::class);
    }

    public function getTranslation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = Language::getDefaultLanguage()->code ?? 'en';
        }
        
        return $this->translations()->where('language_code', $languageCode)->first();
    }

    public function getTranslatedTitle($languageCode = null)
    {
        $translation = $this->getTranslation($languageCode);
        return $translation ? $translation->title : $this->title;
    }

    public function getTranslatedBody($languageCode = null)
    {
        $translation = $this->getTranslation($languageCode);
        return $translation ? $translation->body : $this->body;
    }

    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function updateRating()
    {
        $comments = $this->comments()->whereNotNull('rating');
        $this->rating_count = $comments->count();
        $this->rating = $comments->avg('rating') ?? 0;
        $this->save();
    }
}
