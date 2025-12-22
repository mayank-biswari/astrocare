@extends('admin.layouts.app')

@section('title', 'Manage Admin Users')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Admin Users</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admin Users</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Current Admin Users</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Since</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admins as $admin)
                                    <tr>
                                        <td><strong>{{ $admin->name }}</strong></td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($admin->id !== auth()->id())
                                                <form action="{{ route('admin.admins.demote', $admin) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Demote this admin to regular user?')">
                                                        <i class="fas fa-arrow-down"></i> Demote
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge badge-info">You</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Promote User to Admin</h3>
                    </div>
                    <div class="card-body">
                        @if($users->count() > 0)
                            @foreach($users as $user)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                    <div>
                                        <strong>{{ $user->name }}</strong><br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    <form action="{{ route('admin.admins.promote', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Promote {{ $user->name }} to admin?')">
                                            <i class="fas fa-arrow-up"></i> Promote
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No regular users available to promote.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection