@extends('admin.layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Roles</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Roles</h3>
        <div class="card-tools">
            <form action="{{ route('admin.roles.index') }}" method="GET" class="d-inline">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Search roles..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm ml-2">
                <i class="fas fa-plus"></i> Create Role
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($roles->count())
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->guard_name }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>{{ $role->permissions_count }}</td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline delete-form" data-users-count="{{ $role->users_count }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger btn-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $roles->appends(request()->query())->links() }}
            </div>
        @else
            <p class="text-muted mb-0">No roles have been created yet.</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const usersCount = form.data('users-count');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This role is assigned to ' + usersCount + ' user(s). Are you sure you want to delete it?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
