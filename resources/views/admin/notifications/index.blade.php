@extends('admin.layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Notifications</h3>
        <div class="card-tools">
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i> Mark All Read
                </button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                    <tr class="{{ !$notification->is_read ? 'font-weight-bold' : '' }}">
                        <td>
                            <i class="fas fa-{{ $notification->type === 'contact' ? 'envelope' : ($notification->type === 'consultation' ? 'comments' : 'star') }}"></i>
                            {{ ucfirst($notification->type) }}
                        </td>
                        <td>{{ $notification->title }}</td>
                        <td>{{ Str::limit($notification->message, 50) }}</td>
                        <td>{{ $notification->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            @if($notification->is_read)
                                <span class="badge badge-success">Read</span>
                            @else
                                <span class="badge badge-warning">Unread</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.notifications.read', $notification->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No notifications found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $notifications->links('custom.pagination') }}
    </div>
</div>
@endsection