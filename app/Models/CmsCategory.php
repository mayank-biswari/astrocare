<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function pages()
    {
        return $this->hasMany(CmsPage::class);
    }
}