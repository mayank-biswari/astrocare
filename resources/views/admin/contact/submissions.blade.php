@extends('admin.layouts.app')

@section('title', 'Contact Submissions')
@section('page-title', 'Contact Submissions')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Contact Form Submissions</h3>
    </div>
    <div class="card-body">
        <!-- Search and Filter Form -->
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, or subject..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Unread</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
                <div class="col-md-3 text-right">
                    <a href="{{ route('admin.contact.settings') }}" class="btn btn-info">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                    <tr class="{{ !$submission->is_read ? 'font-weight-bold' : '' }}">
                        <td>{{ $submission->name }}</td>
                        <td>{{ $submission->email }}</td>
                        <td>{{ Str::limit($submission->subject, 50) }}</td>
                        <td>
                            @if($submission->is_read)
                                <span class="badge badge-success">Read</span>
                            @else
                                <span class="badge badge-warning">Unread</span>
                            @endif
                        </td>
                        <td>{{ $submission->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <a href="{{ route('admin.contact.view', $submission->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <form action="{{ route('admin.contact.delete', $submission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No submissions found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $submissions->links('custom.pagination') }}
    </div>
</div>
@endsection