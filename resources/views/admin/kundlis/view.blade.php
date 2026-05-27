@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Kundli Details</h1>
        <a href="{{ route('admin.kundlis') }}" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Kundli Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Name:</strong> {{ $kundli->name }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Type:</strong> {{ ucfirst($kundli->type) }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Birth Date:</strong> {{ \Carbon\Carbon::parse($kundli->birth_date)->format('M d, Y') }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Birth Time:</strong> {{ $kundli->birth_time }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Birth Place:</strong> {{ $kundli->birth_place }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Amount:</strong> ₹{{ number_format($kundli->amount, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>User Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $kundli->user->name }}</p>
                    <p><strong>Email:</strong> {{ $kundli->user->email }}</p>
                    <p><strong>Phone:</strong> {{ $kundli->user->phone }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Update Status & Report</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.kundlis.status', $kundli->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $kundli->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $kundli->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $kundli->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Upload Kundli Report PDF</label>
                            @if($kundli->pdf_path)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $kundli->pdf_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf"></i> View Current PDF
                                    </a>
                                    <small class="text-muted ml-2">Upload a new file to replace.</small>
                                </div>
                            @endif
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="pdf_file" name="pdf_file" accept=".pdf">
                                <label class="custom-file-label" for="pdf_file">Choose PDF file</label>
                            </div>
                            <small class="text-muted">Accepted: PDF only. Max size: 10MB.</small>
                            @error('pdf_file')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Report Notes (Optional)</label>
                            <textarea name="report" class="form-control" rows="4" placeholder="Enter any notes or summary for the kundli report...">{{ $kundli->report }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Update Kundli</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
