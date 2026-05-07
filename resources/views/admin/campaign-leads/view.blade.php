@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Campaign Enquiry #{{ $lead->id }}</h1>
        <a href="{{ route('admin.campaign-leads') }}" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contact Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Full Name:</th>
                            <td>{{ $lead->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td><a href="tel:{{ $lead->phone_number }}">{{ $lead->phone_number }}</a></td>
                        </tr>
                        <tr>
                            <th>Date of Birth:</th>
                            <td>{{ $lead->date_of_birth ? $lead->date_of_birth->format('M d, Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Place of Birth:</th>
                            <td>{{ $lead->place_of_birth }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Enquiry Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Source:</th>
                            <td><span class="badge badge-secondary">{{ $lead->source }}</span></td>
                        </tr>
                        <tr>
                            <th>Submitted:</th>
                            <td>{{ $lead->created_at->format('M d, Y \a\t h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Message:</th>
                            <td>{{ $lead->message ?: 'No message provided' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="mailto:{{ $lead->email }}" class="btn btn-primary mr-2">
                        <i class="fas fa-envelope mr-1"></i> Send Email
                    </a>
                    <a href="tel:{{ $lead->phone_number }}" class="btn btn-success mr-2">
                        <i class="fas fa-phone mr-1"></i> Call
                    </a>
                    <form action="{{ route('admin.campaign-leads.delete', $lead->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
