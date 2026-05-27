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
                        <div class="col-md-3 mb-2">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by name or email..."
                                   value="{{ request('search') }}" maxlength="100">
                        </div>
                        {{-- Role Dropdown --}}
                        <div class="col-md-2 mb-2">
                            <select name="role" class="form-control">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>admin</option>
                                <option value="expert" {{ request('role') == 'expert' ? 'selected' : '' }}>expert</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>user</option>
                            </select>
                        </div>
                        {{-- Date Range --}}
                        <div class="col-md-2 mb-2">
                            <input type="date" name="date_from" class="form-control"
                                   value="{{ request('date_from') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="date" name="date_to" class="form-control"
                                   value="{{ request('date_to') }}" max="{{ date('Y-m-d') }}">
                        </div>
                        {{-- Hidden sort fields to preserve sort state --}}
                        <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                        <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">
                        {{-- Buttons --}}
                        <div class="col-md-3 mb-2">
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
                    <div class="table-responsive">
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
                                <td>
                                    <a href="#" class="text-primary view-user-btn" data-toggle="modal" data-target="#viewUserModal"
                                       data-id="{{ $user->id }}"
                                       data-name="{{ $user->name }}"
                                       data-email="{{ $user->email }}"
                                       data-phone="{{ $user->phone ?? 'N/A' }}"
                                       data-role="{{ $user->roles->pluck('name')->implode(', ') ?: ($user->role ?? 'N/A') }}"
                                       data-user-code="{{ $user->user_code ?? 'N/A' }}"
                                       data-address="{{ $user->address ?? 'N/A' }}"
                                       data-city="{{ $user->city ?? 'N/A' }}"
                                       data-pincode="{{ $user->pincode ?? 'N/A' }}"
                                       data-dob="{{ $user->date_of_birth ?? 'N/A' }}"
                                       data-created="{{ $user->created_at->format('Y-m-d H:i') }}"
                                    >{{ $user->name }}</a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-info">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="text-nowrap">
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-info view-user-btn mr-1" data-toggle="modal" data-target="#viewUserModal"
                                           data-id="{{ $user->id }}"
                                           data-name="{{ $user->name }}"
                                           data-email="{{ $user->email }}"
                                           data-phone="{{ $user->phone ?? 'N/A' }}"
                                           data-role="{{ $user->roles->pluck('name')->implode(', ') ?: ($user->role ?? 'N/A') }}"
                                           data-user-code="{{ $user->user_code ?? 'N/A' }}"
                                           data-address="{{ $user->address ?? 'N/A' }}"
                                           data-city="{{ $user->city ?? 'N/A' }}"
                                           data-pincode="{{ $user->pincode ?? 'N/A' }}"
                                           data-dob="{{ $user->date_of_birth ?? 'N/A' }}"
                                           data-created="{{ $user->created_at->format('Y-m-d H:i') }}"
                                        ><i class="fas fa-eye"></i> View</button>
                                        <a href="{{ route('admin.user-management.edit', $user) }}" class="btn btn-sm btn-warning mr-1">Edit</a>
                                        <form action="{{ route('admin.user-management.destroy', $user) }}" method="POST" class="d-inline mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
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

    // View User Modal - populate data on click
    var viewButtons = document.querySelectorAll('.view-user-btn');
    for (var i = 0; i < viewButtons.length; i++) {
        viewButtons[i].addEventListener('click', function() {
            document.getElementById('modal-user-id').textContent = this.getAttribute('data-id');
            document.getElementById('modal-user-name').textContent = this.getAttribute('data-name');
            document.getElementById('modal-user-email').textContent = this.getAttribute('data-email');
            document.getElementById('modal-user-phone').textContent = this.getAttribute('data-phone');
            document.getElementById('modal-user-role').textContent = this.getAttribute('data-role');
            document.getElementById('modal-user-code').textContent = this.getAttribute('data-user-code');
            document.getElementById('modal-user-address').textContent = this.getAttribute('data-address');
            document.getElementById('modal-user-city').textContent = this.getAttribute('data-city');
            document.getElementById('modal-user-pincode').textContent = this.getAttribute('data-pincode');
            document.getElementById('modal-user-dob').textContent = this.getAttribute('data-dob');
            document.getElementById('modal-user-created').textContent = this.getAttribute('data-created');
        });
    }
})();
</script>

{{-- View User Modal --}}
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">User Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="text-muted">ID</th>
                                <td id="modal-user-id"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Name</th>
                                <td id="modal-user-name"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Email</th>
                                <td id="modal-user-email"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Phone</th>
                                <td id="modal-user-phone"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Role</th>
                                <td id="modal-user-role"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">User Code</th>
                                <td id="modal-user-code"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th class="text-muted">Address</th>
                                <td id="modal-user-address"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">City</th>
                                <td id="modal-user-city"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Pincode</th>
                                <td id="modal-user-pincode"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Date of Birth</th>
                                <td id="modal-user-dob"></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Joined</th>
                                <td id="modal-user-created"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
