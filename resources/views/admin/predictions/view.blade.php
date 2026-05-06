@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Prediction #{{ $prediction->id }}</h1>
        <a href="{{ route('admin.predictions') }}" class="btn btn-secondary">Back to List</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Prediction Details -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Customer Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Name:</th>
                            <td>{{ $prediction->name }}</td>
                        </tr>
                        <tr>
                            <th>User Code:</th>
                            <td>{{ $prediction->user->user_code ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth:</th>
                            <td>{{ \Carbon\Carbon::parse($prediction->dob)->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Time of Birth:</th>
                            <td>{{ $prediction->time ?: 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Place of Birth:</th>
                            <td>{{ $prediction->place ?: 'Not provided' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Type:</th>
                            <td>
                                <span class="badge badge-{{ $prediction->type == 'yearly' ? 'primary' : 'info' }}">
                                    {{ ucfirst($prediction->type) }} Prediction
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Amount:</th>
                            <td>₹{{ number_format($prediction->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Payment:</th>
                            <td>
                                <span class="badge badge-{{ $prediction->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($prediction->payment_status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ $prediction->status == 'completed' ? 'success' : ($prediction->status == 'pending' ? 'warning' : ($prediction->status == 'processing' ? 'info' : 'danger')) }}">
                                    {{ ucfirst($prediction->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Ordered:</th>
                            <td>{{ $prediction->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Update Status & Report -->
        <div class="col-md-6">
            @if(auth()->user()->role === 'admin' || auth()->user()->hasPermissionTo('edit predictions'))
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Status & Report</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.predictions.status', $prediction->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Status</label>
                            <select name="status" class="form-control" id="predictionStatus">
                                <option value="pending" {{ $prediction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $prediction->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $prediction->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $prediction->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Prediction Report</label>
                            <textarea name="report" class="form-control" rows="10" placeholder="Enter the prediction report for the customer...">{{ $prediction->report }}</textarea>
                            <small class="text-muted">Required when marking as Completed. This report will be visible to the customer.</small>
                        </div>

                        @error('report')
                            <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror

                        <button type="submit" class="btn btn-primary">Update Prediction</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
