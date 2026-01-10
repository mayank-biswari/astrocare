@extends('admin.layouts.app')

@section('title', 'Product Lists')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Product Lists</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('admin.lists.create', 'products') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Items Count</th>
                                <th>Page Link</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lists as $list)
                            <tr>
                                <td>{{ $list->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $list->method === 'sql' ? 'warning' : ($list->method === 'manual' ? 'info' : 'success') }}">
                                        {{ ucfirst(str_replace('_', ' ', $list->method)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $list->is_active ? 'success' : 'secondary' }}">
                                        {{ $list->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $list->getResults()->count() }}</td>
                                <td>
                                    @if($list->create_page && $list->page_slug)
                                        <a href="/view/{{ $list->page_slug }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt"></i> View Page
                                        </a>
                                    @else
                                        <span class="text-muted">No page</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.lists.edit', $list) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form method="POST" action="{{ route('admin.lists.delete', $list) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No lists found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection