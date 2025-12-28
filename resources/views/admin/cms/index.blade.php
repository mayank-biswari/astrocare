@extends('admin.layouts.app')

@section('title', 'CMS Pages')
@section('page-title', 'CMS Pages')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Pages</h3>
        <div class="card-tools">
            <a href="{{ route('admin.cms.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Create New Page
            </a>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card-body border-bottom">
        <form method="GET" class="row">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search by title or content..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Published</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="page_type" class="form-control">
                    <option value="">All Page Types</option>
                    @foreach($pageTypes as $pageType)
                        <option value="{{ $pageType->id }}" {{ request('page_type') == $pageType->id ? 'selected' : '' }}>{{ $pageType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                <a href="{{ route('admin.cms.pages') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
    
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Page Type</th>
                    <th>Language</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pages as $page)
                <tr>
                    <td>{{ $page->title }}</td>
                    <td>{{ $page->category->name ?? 'No Category' }}</td>
                    <td>{{ $page->pageType->name ?? 'No Type' }}</td>
                    <td><span class="badge badge-info">{{ strtoupper($page->language_code) }}</span></td>
                    <td>
                        @if($page->is_published)
                            <span class="badge badge-success">Published</span>
                        @else
                            <span class="badge badge-secondary">Draft</span>
                        @endif
                    </td>
                    <td>
                        @if($page->rating > 0)
                            {{ number_format($page->rating, 1) }}/5 ({{ $page->rating_count }})
                        @else
                            <span class="text-muted">No ratings</span>
                        @endif
                    </td>
                    <td>{{ $page->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.cms.edit', $page->id) }}" class="btn btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('cms.show', $page->slug) }}" target="_blank" class="btn btn-success">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.cms.delete', $page->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger delete-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="text-muted">
            Showing {{ $pages->firstItem() ?? 0 }} to {{ $pages->lastItem() ?? 0 }} of {{ $pages->total() }} results
        </div>
        <div>
            {{ $pages->links('custom.pagination') }}
        </div>
    </div>
</div>
@endsection