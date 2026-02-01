<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPageType extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'template', 'has_product_fields', 'fields_config', 'is_active'];

    protected $casts = [
        'fields_config' => 'array',
        'is_active' => 'boolean',
        'has_product_fields' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($pageType) {
            if (empty($pageType->slug)) {
                $pageType->slug = Str::slug($pageType->name);
            }
        });
    }

    public function pages()
    {
        return $this->hasMany(CmsPage::class);
    }
}