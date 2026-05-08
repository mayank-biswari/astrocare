@extends('admin.layouts.app')

@section('title', 'Edit User Roles')
@section('page-title', 'Edit User Roles')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.user-roles.index') }}">User Roles</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form action="{{ route('admin.user-roles.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Editing roles for: {{ $user->name }} ({{ $user->email }})</h3>
        </div>
        <div class="card-body">
            @if($roles->count())
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4 col-sm-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" name="roles[]" value="{{ $role->id }}" id="role-{{ $role->id }}" {{ in_array($role->id, old('roles', $userRoleIds)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">No roles available in the system.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Roles
            </button>
            <a href="{{ route('admin.user-roles.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</form>
@endsection
