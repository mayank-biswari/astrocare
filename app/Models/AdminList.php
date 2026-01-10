<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminList extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'type', 'method', 
        'configuration', 'is_active', 'is_template', 'template_name', 'template_category',
        'create_page', 'page_title', 'page_slug', 'page_description', 'items_per_page'
    ];

    protected $casts = [
        'configuration' => 'array',
        'is_active' => 'boolean',
        'is_template' => 'boolean',
        'create_page' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($list) {
            if (empty($list->slug)) {
                $list->slug = Str::slug($list->name);
            }
        });
    }

    public function getResults()
    {
        switch ($this->method) {
            case 'sql':
                return $this->getSqlResults();
            case 'manual':
                return $this->getManualResults();
            case 'query_builder':
                return $this->getQueryBuilderResults();
        }
        return collect();
    }

    private function getSqlResults()
    {
        try {
            return collect(\DB::select($this->configuration['sql']));
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getManualResults()
    {
        $ids = $this->configuration['selected_ids'] ?? [];
        if ($this->type === 'products') {
            return \App\Models\Product::whereIn('id', $ids)->get();
        } else {
            return \App\Models\CmsPage::whereIn('id', $ids)->get();
        }
    }

    private function getQueryBuilderResults()
    {
        $model = $this->type === 'products' ? \App\Models\Product::query() : \App\Models\CmsPage::query();
        
        foreach ($this->configuration['filters'] ?? [] as $filter) {
            if (empty($filter['field']) || empty($filter['operator']) || $filter['value'] === '') {
                continue;
            }
            
            $field = $filter['field'];
            $operator = $filter['operator'];
            $value = $filter['value'];
            
            // Handle specific field mappings
            if ($this->type === 'pages' && $field === 'page_type_id') {
                $field = 'cms_page_type_id';
            }
            
            if ($operator === 'like') {
                $model->where($field, 'like', '%' . $value . '%');
            } else {
                $model->where($field, $operator, $value);
            }
        }
        
        return $model->get();
    }
}