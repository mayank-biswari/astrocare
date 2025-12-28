<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPageTranslation extends Model
{
    protected $fillable = [
        'cms_page_id',
        'language_code',
        'title',
        'body',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    public function cmsPage()
    {
        return $this->belongsTo(CmsPage::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_code', 'code');
    }
}