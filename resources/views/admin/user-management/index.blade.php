@extends('admin.layouts.app')

@section('content')
<style>
    .sortable-header a {
        color: inherit;
        text-decoration: none;
        cursor: pointer;
    }
    .sortable-header a:hover {
        text-decoration: underline;
        color: #007bff;
    }
    .sortable-header a i {
        margin-left: 4px;
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">User Management</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('admin.user-management.create') }}" class="btn btn-primary float-right">Add New User</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        {{-- Loading Overlay --}}
        <style>
            .loading-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.4);
                z-index: 9999;
                justify-content: center;
                align-items: center;
            }
            .loading-overlay.active {
                display: flex;
            }
            .loading-spinner {
                width: 50px;
                height: 50px;
                border: 5px solid #f3f3f3;
                border-top: 5px solid #007bff;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
        <div class="loading-overlay" id="loading-overlay">
            <div class="loading-spinner"></div>
        </div>
        {{-- Server Error Message --}}
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Filter Panel --}}
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.user-management.index') }}" id="filter-form">
                    <div class="row">
                        {{-- Search Input --}}
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by name or email..."
                                   value="{{ request('search') }}" maxlength="100">
                        </div>
                        {{-- Role Dropdown --}}
                        <div class="col-md-2">
                            <select name="role" class="form-control">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>admin</option>
                                <option value="expert" {{ request('role') == 'expert' ? 'selected' : '' }}>expert</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>user</option>
                            </select>
                        </div>
                        {{-- Date Range --}}
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control"
                                   value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control"
                                   value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        {{-- Hidden sort fields to preserve sort state --}}
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                        <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">
                        {{-- Buttons --}}
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.user-management.index') }}" class="btn btn-secondary">Reset Filters</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Result Count --}}
        <div class="mb-2">
            <span class="text-muted">Showing {{ $totalFiltered }} {{ Str::plural('user', $totalFiltered) }}</span>
        </div>

        {{-- Users Table --}}
        <div class="card">
            <div class="card-body">
                @if($users->isEmpty())
                    <p class="text-center text-muted my-4">No users found.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th class="sortable-header">
                                    <a href="{{ route('admin.user-management.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => (request('sort_by') == 'name' && request('sort_dir') == 'asc') ? 'desc' : 'asc'])) }}">
                                        Name
                                        @if(request('sort_by') == 'name')
                                            <i class="fas fa-arrow-{{ request('sort_dir') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="sortable-header">
                                    <a href="{{ route('admin.user-management.index', array_merge(request()->query(), ['sort_by' => 'email', 'sort_dir' => (request('sort_by') == 'email' && request('sort_dir') == 'asc') ? 'desc' : 'asc'])) }}">
                                        Email
                                        @if(request('sort_by') == 'email')
                                            <i class="fas fa-arrow-{{ request('sort_dir') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="sortable-header">
                                    <a href="{{ route('admin.user-management.index', array_merge(request()->query(), ['sort_by' => 'role', 'sort_dir' => (request('sort_by') == 'role' && request('sort_dir') == 'asc') ? 'desc' : 'asc'])) }}">
                                        Role
                                        @if(request('sort_by') == 'role')
                                            <i class="fas fa-arrow-{{ request('sort_dir') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="sortable-header">
                                    <a href="{{ route('admin.user-management.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_dir' => (request('sort_by') == 'created_at' && request('sort_dir') == 'asc') ? 'desc' : 'asc'])) }}">
                                        Created
                                        @if(request('sort_by') == 'created_at')
                                            <i class="fas fa-arrow-{{ request('sort_dir') == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-info">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.user-management.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.user-management.destroy', $user) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script>
(function() {
    var overlay = document.getElementById('loading-overlay');

    // Show loading overlay on filter form submit
    var filterForm = document.getElementById('filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            overlay.classList.add('active');
        });
    }

    // Show loading overlay on sortable column header link click
    var sortLinks = document.querySelectorAll('th a[href*="sort_by"]');
    for (var i = 0; i < sortLinks.length; i++) {
        sortLinks[i].addEventListener('click', function() {
            overlay.classList.add('active');
        });
    }

    // Hide loading overlay on page load (handles browser back/forward cache)
    window.addEventListener('pageshow', function() {
        overlay.classList.remove('active');
    });
})();
</script>
@endsection
