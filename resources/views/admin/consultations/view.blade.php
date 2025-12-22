@extends('admin.layouts.app')

@section('title', 'View Consultation')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Consultation Details</h1>
                </div>
                <div class="col-sm-6">
                    <a href="{{ route('admin.consultations') }}" class="btn btn-secondary float-right">Back to Consultations</a>
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
                            <h3 class="card-title">Consultation Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Type:</strong> {{ ucfirst($consultation->type) }} Consultation<br>
                                    <strong>Duration:</strong> {{ $consultation->duration ?? 30 }} minutes<br>
                                    <strong>Amount:</strong> ₹{{ number_format($consultation->amount) }}<br>
                                    <strong>Status:</strong> 
                                    @if($consultation->status == 'completed')
                                        <span class="badge badge-success">Completed</span>
                                    @elseif($consultation->status == 'scheduled')
                                        <span class="badge badge-primary">Scheduled</span>
                                    @elseif($consultation->status == 'cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($consultation->status) }}</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Scheduled At:</strong> 
                                    @if($consultation->scheduled_at)
                                        {{ $consultation->scheduled_at->format('M d, Y g:i A') }}
                                    @else
                                        <span class="text-muted">Not scheduled</span>
                                    @endif<br>
                                    <strong>Booked On:</strong> {{ $consultation->created_at->format('M d, Y g:i A') }}<br>
                                    <strong>Last Updated:</strong> {{ $consultation->updated_at->format('M d, Y g:i A') }}
                                </div>
                            </div>

                            @if($consultation->notes)
                            <div class="mt-3">
                                <strong>Additional Notes:</strong>
                                <div class="bg-light p-3 rounded mt-2">
                                    {{ $consultation->notes }}
                                </div>
                            </div>
                            @endif

                            @if($consultation->reschedule_reason)
                            <div class="mt-3">
                                <strong>Reschedule Reason:</strong>
                                <div class="bg-warning p-3 rounded mt-2">
                                    {{ $consultation->reschedule_reason }}
                                </div>
                            </div>
                            @endif

                            @if($consultation->cancellation_reason)
                            <div class="mt-3">
                                <strong>Cancellation Reason:</strong>
                                <div class="bg-danger p-3 rounded mt-2 text-white">
                                    {{ $consultation->cancellation_reason }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User Information</h3>
                        </div>
                        <div class="card-body">
                            <strong>Name:</strong> {{ $consultation->user->name }}<br>
                            <strong>Email:</strong> {{ $consultation->user->email }}<br>
                            <strong>Phone:</strong> {{ $consultation->user->phone ?? 'Not provided' }}<br>
                            <strong>Member Since:</strong> {{ $consultation->user->created_at->format('M d, Y') }}
                        </div>
                    </div>

                    @if($consultation->status == 'scheduled')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Complete Consultation</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.consultations.status', $consultation->id) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">
                                
                                <div class="form-group">
                                    <label>Suggestions</label>
                                    <textarea name="suggestions" class="form-control" rows="4" placeholder="Enter astrological suggestions..." required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Remedies</label>
                                    <textarea name="remedies" class="form-control" rows="4" placeholder="Enter recommended remedies..." required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-block">
                                    Complete Consultation
                                </button>
                            </form>
                            
                            <div class="mt-3">
                                <button class="btn btn-danger btn-block" data-toggle="modal" data-target="#cancelModal">
                                    Cancel Consultation
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cancel Consultation</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.consultations.status', $consultation->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="cancelled">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason for Cancellation</label>
                        <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Please provide reason for cancellation..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Consultation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection