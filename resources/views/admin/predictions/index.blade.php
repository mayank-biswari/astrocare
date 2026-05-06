@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Predictions Management</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control" onchange="window.location.href='?status='+this.value+'&type={{ request('type') }}'">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" onchange="window.location.href='?type='+this.value+'&status={{ request('status') }}'">
                        <option value="">All Types</option>
                        <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ request('type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>DOB</th>
                            <th>Place</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($predictions as $prediction)
                            <tr>
                                <td>{{ $prediction->id }}</td>
                                <td>
                                    {{ $prediction->name }}
                                    <br><small class="text-muted">{{ $prediction->email }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $prediction->type == 'yearly' ? 'primary' : 'info' }}">
                                        {{ ucfirst($prediction->type) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($prediction->dob)->format('M d, Y') }}</td>
                                <td>{{ $prediction->place ?? '-' }}</td>
                                <td>₹{{ number_format($prediction->amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $prediction->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($prediction->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $prediction->status == 'completed' ? 'success' : ($prediction->status == 'pending' ? 'warning' : ($prediction->status == 'processing' ? 'info' : 'danger')) }}">
                                        {{ ucfirst($prediction->status) }}
                                    </span>
                                </td>
                                <td>{{ $prediction->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if(auth()->user()->role === 'admin' || auth()->user()->hasPermissionTo('view predictions'))
                                        <a href="{{ route('admin.predictions.view', $prediction->id) }}" class="btn btn-sm btn-info">View</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No predictions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $predictions->links() }}
        </div>
    </div>
</div>
@endsection
