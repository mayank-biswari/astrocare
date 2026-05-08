@extends('admin.layouts.app')

@section('title', 'User Roles')
@section('page-title', 'User Roles')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">User Roles</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Users</h3>
        <div class="card-tools">
            <form action="{{ route('admin.user-roles.index') }}" method="GET" class="d-inline">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control float-right" placeholder="Search by name or email..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        @if($users->count())
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">No roles assigned</span>
                                @endforelse
                            </td>
                            <td>
                                <a href="{{ route('admin.user-roles.edit', $user) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @else
            <p class="text-muted mb-0">No users found.</p>
        @endif
    </div>
</div>
@endsection
