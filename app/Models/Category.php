<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];
    
    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }
    
    public function getTranslatedName($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translation = $this->translations()->where('language_code', $locale)->first();
        return $translation ? $translation->name : $this->name;
    }
    
    public function getTranslatedDescription($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $translation = $this->translations()->where('language_code', $locale)->first();
        return $translation ? $translation->description : $this->description;
    }
}
