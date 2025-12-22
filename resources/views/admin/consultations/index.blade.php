@extends('admin.layouts.app')

@section('title', 'Consultations Management')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Consultations Management</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Consultations</h3>
                </div>
                
                <!-- Filters -->
                <div class="card-body">
                    <form method="GET" class="row mb-3">
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="chat" {{ request('type') == 'chat' ? 'selected' : '' }}>Chat</option>
                                <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                                <option value="phone" {{ request('type') == 'phone' ? 'selected' : '' }}>Phone</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by user name or email" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Scheduled At</th>
                                    <th>Duration</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($consultations as $consultation)
                                <tr>
                                    <td>{{ $consultation->id }}</td>
                                    <td>
                                        <strong>{{ $consultation->user->name }}</strong><br>
                                        <small class="text-muted">{{ $consultation->user->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($consultation->type) }}</span>
                                    </td>
                                    <td>
                                        @if($consultation->scheduled_at)
                                            {{ $consultation->scheduled_at->format('M d, Y g:i A') }}
                                        @else
                                            <span class="text-muted">Not scheduled</span>
                                        @endif
                                    </td>
                                    <td>{{ $consultation->duration ?? 30 }} min</td>
                                    <td>₹{{ number_format($consultation->amount) }}</td>
                                    <td>
                                        @if($consultation->status == 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($consultation->status == 'scheduled')
                                            <span class="badge badge-primary">Scheduled</span>
                                        @elseif($consultation->status == 'cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($consultation->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.consultations.view', $consultation->id) }}" class="btn btn-sm btn-info">View</a>
                                        
                                        @if($consultation->status == 'scheduled')
                                        <form method="POST" action="{{ route('admin.consultations.status', $consultation->id) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark as completed?')">Complete</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.consultations.status', $consultation->id) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this consultation?')">Cancel</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No consultations found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $consultations->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection