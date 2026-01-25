<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\CmsCategory;
use App\Models\CmsPageType;

class ListController extends Controller
{
    public function productLists(Request $request)
    {
        $query = \App\Models\AdminList::where('type', 'products');
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $lists = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.lists.products', compact('lists'));
    }

    public function pageLists(Request $request)
    {
        $query = \App\Models\AdminList::where('type', 'pages');
        
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $lists = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.lists.pages', compact('lists'));
    }

    public function create($type)
    {
        if (!in_array($type, ['products', 'pages'])) {
            abort(404);
        }
        
        $items = $type === 'products' ? Product::all() : \App\Models\CmsPage::all();
        $categories = $type === 'products' ? Category::where('is_active', true)->get() : CmsCategory::where('is_active', true)->get();
        $pageTypes = $type === 'pages' ? CmsPageType::where('is_active', true)->get() : collect();
        
        return view('admin.lists.create', compact('type', 'items', 'categories', 'pageTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:products,pages',
            'method' => 'required|in:sql,manual,query_builder',
            'description' => 'nullable|string'
        ]);

        $configuration = [];
        
        switch ($request->method) {
            case 'sql':
                $request->validate(['sql_query' => 'required|string']);
                $configuration['sql'] = $request->sql_query;
                break;
                
            case 'manual':
                $request->validate(['selected_ids' => 'required|array']);
                $configuration['selected_ids'] = $request->selected_ids;
                break;
                
            case 'query_builder':
                $configuration['filters'] = $request->filters ?? [];
                break;
        }

        \App\Models\AdminList::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'method' => $request->method,
            'configuration' => $configuration,
            'is_active' => $request->has('is_active'),
            'is_template' => $request->has('is_template'),
            'template_name' => $request->template_name,
            'template_category' => $request->template_category,
            'create_page' => $request->has('create_page'),
            'page_title' => $request->page_title,
            'page_slug' => $request->page_slug,
            'page_description' => $request->page_description,
            'items_per_page' => $request->items_per_page ?: 12
        ]);

        $route = $request->type === 'products' ? 'admin.lists.products' : 'admin.lists.pages';
        return redirect()->route($route)->with('success', 'List created successfully!');
    }

    public function edit(\App\Models\AdminList $list)
    {
        $items = $list->type === 'products' ? Product::all() : \App\Models\CmsPage::all();
        $categories = $list->type === 'products' ? Category::where('is_active', true)->get() : CmsCategory::where('is_active', true)->get();
        $pageTypes = $list->type === 'pages' ? CmsPageType::where('is_active', true)->get() : collect();
        
        return view('admin.lists.edit', compact('list', 'items', 'categories', 'pageTypes'));
    }

    public function update(Request $request, \App\Models\AdminList $list)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'method' => 'required|in:sql,manual,query_builder',
            'description' => 'nullable|string'
        ]);

        $configuration = [];
        
        switch ($request->method) {
            case 'sql':
                $request->validate(['sql_query' => 'required|string']);
                $configuration['sql'] = $request->sql_query;
                break;
                
            case 'manual':
                $request->validate(['selected_ids' => 'required|array']);
                $configuration['selected_ids'] = $request->selected_ids;
                break;
                
            case 'query_builder':
                $configuration['filters'] = $request->filters ?? [];
                break;
        }

        $list->update([
            'name' => $request->name,
            'description' => $request->description,
            'method' => $request->method,
            'configuration' => $configuration,
            'is_active' => $request->has('is_active'),
            'is_template' => $request->has('is_template'),
            'create_page' => $request->has('create_page'),
            'page_title' => $request->page_title,
            'page_slug' => $request->page_slug,
            'page_description' => $request->page_description,
            'items_per_page' => $request->items_per_page ?: 12
        ]);

        $route = $list->type === 'products' ? 'admin.lists.products' : 'admin.lists.pages';
        return redirect()->route($route)->with('success', 'List updated successfully!');
    }

    public function destroy(\App\Models\AdminList $list)
    {
        $list->delete();
        $route = $list->type === 'products' ? 'admin.lists.products' : 'admin.lists.pages';
        return redirect()->route($route)->with('success', 'List deleted successfully!');
    }

    public function templates(Request $request)
    {
        $query = \App\Models\AdminList::where('is_template', true);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('template_name', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->category) {
            $query->where('template_category', $request->category);
        }
        
        $templates = $query->latest()->paginate(10)->appends($request->query());
        return view('admin.lists.templates', compact('templates'));
    }

    public function deleteTemplate(\App\Models\AdminList $template)
    {
        $template->delete();
        return redirect()->route('admin.lists.templates')->with('success', 'Template deleted successfully!');
    }

    public function preview(Request $request)
    {
        $configuration = [];
        
        switch ($request->method) {
            case 'sql':
                try {
                    $results = collect(\DB::select($request->sql_query));
                    return response()->json(['count' => $results->count(), 'results' => $results->take(5)]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid SQL query: ' . $e->getMessage()], 400);
                }
                
            case 'manual':
                $count = count($request->selected_ids ?? []);
                return response()->json(['count' => $count]);
                
            case 'query_builder':
                $model = $request->type === 'products' ? Product::query() : \App\Models\CmsPage::query();
                
                foreach ($request->filters ?? [] as $filter) {
                    if (empty($filter['field']) || empty($filter['operator']) || $filter['value'] === '') {
                        continue;
                    }
                    
                    $field = $filter['field'];
                    $operator = $filter['operator'];
                    $value = $filter['value'];
                    
                    if ($request->type === 'pages' && $field === 'page_type_id') {
                        $field = 'cms_page_type_id';
                    }
                    
                    if ($operator === 'like') {
                        $model->where($field, 'like', '%' . $value . '%');
                    } else {
                        $model->where($field, $operator, $value);
                    }
                }
                
                $results = $model->get();
                return response()->json(['count' => $results->count(), 'results' => $results->take(5)]);
        }
        
        return response()->json(['count' => 0]);
    }
}
