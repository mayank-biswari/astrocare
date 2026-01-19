@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Kundlis Management</h1>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control" onchange="window.location.href='?status='+this.value">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" onchange="window.location.href='?type='+this.value">
                        <option value="">All Types</option>
                        <option value="basic">Basic</option>
                        <option value="detailed">Detailed</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Birth Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kundlis as $kundli)
                            <tr>
                                <td>{{ $kundli->id }}</td>
                                <td>{{ $kundli->user->name }}</td>
                                <td>{{ $kundli->name }}</td>
                                <td>{{ ucfirst($kundli->type) }}</td>
                                <td>{{ \Carbon\Carbon::parse($kundli->birth_date)->format('M d, Y') }}</td>
                                <td>₹{{ number_format($kundli->amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $kundli->status == 'completed' ? 'success' : ($kundli->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($kundli->status) }}
                                    </span>
                                </td>
                                <td>{{ $kundli->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.kundlis.view', $kundli->id) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No kundlis found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $kundlis->links() }}
        </div>
    </div>
</div>
@endsection
