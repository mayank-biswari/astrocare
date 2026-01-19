@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Questions Management</h1>

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
                    <select class="form-control" onchange="window.location.href='?category='+this.value">
                        <option value="">All Categories</option>
                        <option value="career">Career & Business</option>
                        <option value="love">Love & Relationships</option>
                        <option value="marriage">Marriage & Family</option>
                        <option value="health">Health & Wellness</option>
                        <option value="finance">Finance & Money</option>
                        <option value="education">Education & Studies</option>
                        <option value="general">General</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Question</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>{{ $question->user->name }}</td>
                                <td>{{ ucfirst($question->category) }}</td>
                                <td>{{ Str::limit($question->question, 50) }}</td>
                                <td>₹{{ number_format($question->amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $question->status == 'completed' ? 'success' : ($question->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($question->status) }}
                                    </span>
                                </td>
                                <td>{{ $question->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.questions.view', $question->id) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No questions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $questions->links() }}
        </div>
    </div>
</div>
@endsection
