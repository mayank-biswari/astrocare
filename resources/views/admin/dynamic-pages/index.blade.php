@extends('admin.layouts.app')

@section('title', 'Dynamic Pages')

@section('content')
<div class="content-fluid">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dynamic Pages</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('admin.dynamic-pages.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New Page
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
                                <th>System Name</th>
                                <th>Title</th>
                                <th>URL</th>
                                <th>Status</th>
                                <th>Sections</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                            <tr>
                                <td>{{ $page->system_name }}</td>
                                <td>{{ $page->title }}</td>
                                <td>
                                    <code>/{{ $page->url }}</code>
                                    @if($page->is_published)
                                        <a href="/{{ $page->url }}" target="_blank" class="btn btn-xs btn-outline-primary ml-2">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $page->is_published ? 'success' : 'secondary' }}">
                                        {{ $page->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>{{ count($page->sections ?? []) }}</td>
                                <td>
                                    <a href="{{ route('admin.dynamic-pages.edit', $page) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form method="POST" action="{{ route('admin.dynamic-pages.delete', $page) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No dynamic pages found</td>
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
