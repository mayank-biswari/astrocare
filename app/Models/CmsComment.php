<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsComment extends Model
{
    protected $fillable = [
        'cms_page_id', 'name', 'email', 'comment', 'rating', 'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean'
    ];

    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }

    public static function boot()
    {
        parent::boot();
        
        static::created(function ($comment) {
            $comment->page->updateRating();
        });
        
        static::updated(function ($comment) {
            $comment->page->updateRating();
        });
    }
}
