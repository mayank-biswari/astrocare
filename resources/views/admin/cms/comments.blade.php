@extends('admin.layouts.app')

@section('title', 'CMS Comments')
@section('page-title', 'CMS Comments')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Comments</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Comment</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comments as $comment)
                <tr>
                    <td>
                        <a href="{{ route('cms.show', $comment->page->slug) }}" target="_blank">
                            {{ Str::limit($comment->page->title, 30) }}
                        </a>
                    </td>
                    <td>{{ $comment->name }}</td>
                    <td>{{ $comment->email }}</td>
                    <td>{{ Str::limit($comment->comment, 50) }}</td>
                    <td>
                        @if($comment->rating)
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $comment->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        @else
                            <span class="text-muted">No rating</span>
                        @endif
                    </td>
                    <td>
                        @if($comment->is_approved)
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </td>
                    <td>{{ $comment->created_at->format('M d, Y') }}</td>
                    <td>
                        <form action="{{ route('admin.cms.comments.approve', $comment->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm {{ $comment->is_approved ? 'btn-warning' : 'btn-success' }}">
                                @if($comment->is_approved)
                                    <i class="fas fa-times"></i> Unapprove
                                @else
                                    <i class="fas fa-check"></i> Approve
                                @endif
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $comments->links() }}
    </div>
</div>
@endsection