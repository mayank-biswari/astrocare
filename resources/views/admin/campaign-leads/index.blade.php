@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Campaign Enquiries</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <!-- Search -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="Search by name, email, phone...">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
                <div class="col-md-2">
                    <select class="form-control" onchange="window.location.href='?source='+this.value">
                        <option value="">All Sources</option>
                        <option value="tarot-reading-campaign" {{ request('source') == 'tarot-reading-campaign' ? 'selected' : '' }}>Tarot Reading Campaign</option>
                    </select>
                </div>
                <div class="col-md-2 ml-auto text-right">
                    <span class="badge badge-info p-2">Total: {{ $leads->total() }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>DOB</th>
                            <th>Place</th>
                            <th>Source</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td>{{ $lead->id }}</td>
                                <td>{{ $lead->full_name }}</td>
                                <td>{{ $lead->email }}</td>
                                <td>{{ $lead->phone_number }}</td>
                                <td>{{ $lead->date_of_birth ? $lead->date_of_birth->format('M d, Y') : '-' }}</td>
                                <td>{{ $lead->place_of_birth }}</td>
                                <td><span class="badge badge-secondary">{{ $lead->source }}</span></td>
                                <td>{{ $lead->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.campaign-leads.view', $lead->id) }}" class="btn btn-sm btn-info">View</a>
                                    <form action="{{ route('admin.campaign-leads.delete', $lead->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this lead?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No campaign enquiries found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $leads->links() }}
        </div>
    </div>
</div>
@endsection
