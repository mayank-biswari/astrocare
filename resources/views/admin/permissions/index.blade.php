@extends('admin.layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Permissions')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Permissions</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Permissions</h3>
        <div class="card-tools">
            <form action="{{ route('admin.permissions.index') }}" method="GET" class="d-inline">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Search permissions..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm ml-2">
                <i class="fas fa-plus"></i> Create Permission
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($permissions->count())
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Roles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>{{ $permission->name }}</td>
                            <td>{{ $permission->guard_name }}</td>
                            <td>{{ $permission->roles_count }}</td>
                            <td>
                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline delete-form" data-roles-count="{{ $permission->roles_count }}">
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
                {{ $permissions->appends(request()->query())->links() }}
            </div>
        @else
            <p class="text-muted mb-0">No permissions have been created yet.</p>
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
        const rolesCount = form.data('roles-count');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This permission is used by ' + rolesCount + ' role(s). Are you sure you want to delete it?',
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
