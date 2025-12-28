@extends('admin.layouts.app')

@section('title', 'CMS Page Types')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">CMS Page Types</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Page Types</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Page Types</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.cms.page-types.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Page Type
                        </a>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="card-body border-bottom">
                    <form method="GET" class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or description..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary mr-2">Filter</button>
                            <a href="{{ route('admin.cms.page-types') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>
                </div>
                
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Pages</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pageTypes as $pageType)
                            <tr>
                                <td>{{ $pageType->name }}</td>
                                <td>{{ $pageType->slug }}</td>
                                <td>{{ $pageType->description }}</td>
                                <td>{{ $pageType->pages->count() }}</td>
                                <td>
                                    <span class="badge badge-{{ $pageType->is_active ? 'success' : 'secondary' }}">
                                        {{ $pageType->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.cms.page-types.edit', $pageType->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.cms.page-types.delete', $pageType->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $pageTypes->firstItem() ?? 0 }} to {{ $pageTypes->lastItem() ?? 0 }} of {{ $pageTypes->total() }} results
                    </div>
                    <div>
                        {{ $pageTypes->links('custom.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection