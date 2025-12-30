@extends('admin.layouts.app')

@section('title', 'View Contact Submission')
@section('page-title', 'Contact Submission Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Contact Submission #{{ $submission->id }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.contact.submissions') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>Contact Information</h5>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $submission->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></td>
                    </tr>
                    @if($submission->phone)
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><a href="tel:{{ $submission->phone }}">{{ $submission->phone }}</a></td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Subject:</strong></td>
                        <td>{{ $submission->subject }}</td>
                    </tr>
                    <tr>
                        <td><strong>Submitted:</strong></td>
                        <td>{{ $submission->created_at->format('M d, Y at h:i A') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if($submission->is_read)
                                <span class="badge badge-success">Read</span>
                            @else
                                <span class="badge badge-warning">Unread</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Quick Actions</h5>
                <div class="btn-group-vertical w-100">
                    <a href="mailto:{{ $submission->email }}?subject=Re: {{ $submission->subject }}" class="btn btn-primary mb-2">
                        <i class="fas fa-reply"></i> Reply via Email
                    </a>
                    @if($submission->phone)
                    <a href="tel:{{ $submission->phone }}" class="btn btn-success mb-2">
                        <i class="fas fa-phone"></i> Call {{ $submission->phone }}
                    </a>
                    @endif
                    <form action="{{ route('admin.contact.delete', $submission->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this submission?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Delete Submission
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <hr>
        
        <div class="row">
            <div class="col-md-12">
                <h5>Message</h5>
                <div class="card">
                    <div class="card-body">
                        <p class="mb-0">{{ $submission->message }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection