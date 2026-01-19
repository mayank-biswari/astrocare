@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Question Details</h1>
        <a href="{{ route('admin.questions') }}" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Question Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Category:</strong> {{ ucfirst($question->category) }}
                    </div>
                    <div class="mb-3">
                        <strong>Question:</strong>
                        <p class="mt-2">{{ $question->question }}</p>
                    </div>
                    @if($question->answer)
                        <div class="mb-3">
                            <strong>Answer:</strong>
                            <p class="mt-2 bg-light p-3 rounded">{{ $question->answer }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>User Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $question->name }}</p>
                    <p><strong>Email:</strong> {{ $question->email }}</p>
                    <p><strong>Phone:</strong> {{ $question->phone }}</p>
                    <p><strong>DOB:</strong> {{ \Carbon\Carbon::parse($question->dob)->format('M d, Y') }}</p>
                    @if($question->time)
                        <p><strong>Birth Time:</strong> {{ $question->time }}</p>
                    @endif
                    @if($question->place)
                        <p><strong>Birth Place:</strong> {{ $question->place }}</p>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Update Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.questions.status', $question->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ $question->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ $question->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $question->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="answer" class="form-control" rows="6" required>{{ $question->answer }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
