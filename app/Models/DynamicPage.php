<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicPage extends Model
{
    protected $fillable = [
        'system_name', 'title', 'url', 'sections', 'custom_css', 'custom_js', 'is_published',
        'external_css', 'external_js'
    ];

    protected $casts = [
        'sections' => 'array',
        'is_published' => 'boolean',
        'external_css' => 'array',
        'external_js' => 'array'
    ];

    public function getSectionData($section)
    {
        if ($section['type'] === 'list' && !empty($section['list_id'])) {
            $list = \App\Models\AdminList::find($section['list_id']);
            if ($list) {
                $results = $list->getResults();
                $limit = $section['limit'] ?? 10;
                return $results->take($limit);
            }
        }
        return collect();
    }
}