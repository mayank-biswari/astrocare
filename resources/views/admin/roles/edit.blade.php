@extends('admin.layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Role Details</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" placeholder="Enter role name" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="guard_name">Guard Name</label>
                <input type="text" name="guard_name" id="guard_name" class="form-control @error('guard_name') is-invalid @enderror" value="{{ old('guard_name', $role->guard_name) }}" placeholder="web">
                @error('guard_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Permissions</h3>
        </div>
        <div class="card-body">
            @foreach($groupedPermissions as $group => $permissions)
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-header" data-toggle="collapse" data-target="#group-{{ Str::slug($group) }}" style="cursor: pointer;">
                        <h5 class="card-title mb-0">
                            <input type="checkbox" class="group-toggle mr-2" data-group="{{ Str::slug($group) }}">
                            {{ ucfirst($group) }} ({{ $permissions->count() }})
                        </h5>
                    </div>
                    <div id="group-{{ Str::slug($group) }}" class="collapse show">
                        <div class="card-body">
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" class="form-check-input permission-checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" data-group="{{ Str::slug($group) }}" {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Role
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(function() {
    // Group toggle: select/deselect all permissions in a group
    $('.group-toggle').on('change', function() {
        const group = $(this).data('group');
        const isChecked = $(this).prop('checked');
        $('.permission-checkbox[data-group="' + group + '"]').prop('checked', isChecked);
    });

    // Individual checkbox change: update group toggle state
    $('.permission-checkbox').on('change', function() {
        const group = $(this).data('group');
        updateGroupToggle(group);
    });

    // Initialize group toggle states on page load
    function updateGroupToggle(group) {
        const checkboxes = $('.permission-checkbox[data-group="' + group + '"]');
        const total = checkboxes.length;
        const checked = checkboxes.filter(':checked').length;
        const toggle = $('.group-toggle[data-group="' + group + '"]');

        if (checked === 0) {
            toggle.prop('checked', false);
            toggle.prop('indeterminate', false);
        } else if (checked === total) {
            toggle.prop('checked', true);
            toggle.prop('indeterminate', false);
        } else {
            toggle.prop('checked', false);
            toggle.prop('indeterminate', true);
        }
    }

    // Initialize all group toggles on page load
    $('.group-toggle').each(function() {
        updateGroupToggle($(this).data('group'));
    });

    // Prevent collapse toggle when clicking the group checkbox
    $('.group-toggle').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>
@endpush
