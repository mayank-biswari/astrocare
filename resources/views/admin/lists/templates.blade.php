@extends('admin.layouts.app')

@section('title', 'List Templates')

@section('content')
<div class="content-fluid">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">List Templates</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Templates</h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <div class="input-group input-group-sm mr-2">
                                <input type="text" name="search" class="form-control" placeholder="Search templates..." value="{{ request('search') }}">
                            </div>
                            <div class="input-group input-group-sm mr-2">
                                <select name="type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="products" {{ request('type') === 'products' ? 'selected' : '' }}>Products</option>
                                    <option value="pages" {{ request('type') === 'pages' ? 'selected' : '' }}>Pages</option>
                                </select>
                            </div>
                            <div class="input-group input-group-sm mr-2">
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <option value="content" {{ request('category') === 'content' ? 'selected' : '' }}>Content Management</option>
                                    <option value="status" {{ request('category') === 'status' ? 'selected' : '' }}>Status Filters</option>
                                    <option value="type" {{ request('category') === 'type' ? 'selected' : '' }}>Type Filters</option>
                                    <option value="custom" {{ request('category') === 'custom' ? 'selected' : '' }}>Custom Filters</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Template Name</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Method</th>
                                <th>Filters</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                            <tr>
                                <td>
                                    <strong>{{ $template->template_name ?: $template->name }}</strong>
                                    @if($template->description)
                                        <br><small class="text-muted">{{ $template->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $template->type === 'products' ? 'primary' : 'info' }}">
                                        {{ ucfirst($template->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($template->template_category)
                                        <span class="badge badge-secondary">{{ ucfirst($template->template_category) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $template->method === 'sql' ? 'warning' : ($template->method === 'manual' ? 'info' : 'success') }}">
                                        {{ ucfirst(str_replace('_', ' ', $template->method)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($template->method === 'query_builder' && isset($template->configuration['filters']))
                                        {{ count($template->configuration['filters']) }} filters
                                    @elseif($template->method === 'manual' && isset($template->configuration['selected_ids']))
                                        {{ count($template->configuration['selected_ids']) }} items
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.lists.create', $template->type) }}?template={{ $template->id }}" class="btn btn-sm btn-success">Use Template</a>
                                    <form method="POST" action="{{ route('admin.lists.templates.delete', $template) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No templates found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($templates->hasPages())
                <div class="card-footer">
                    {{ $templates->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
