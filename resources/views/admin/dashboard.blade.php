@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['products'] }}</h3>
                    <p>Products</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <a href="{{ route('admin.products') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['orders'] }}</h3>
                    <p>Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('admin.orders') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['users'] }}</h3>
                    <p>Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['consultations'] }}</h3>
                    <p>Consultations</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('admin.consultations') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- New Features Stats -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['contact_submissions'] }}</h3>
                    <p>Contact Messages</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <a href="{{ route('admin.contact.submissions') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $stats['unread_notifications'] }}</h3>
                    <p>Unread Notifications</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bell"></i>
                </div>
                <a href="{{ route('admin.notifications') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ $stats['cms_pages'] }}</h3>
                    <p>CMS Pages</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <a href="{{ route('admin.cms.pages') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-light">
                <div class="inner">
                    <h3 class="text-dark">{{ \App\Models\Language::count() }}</h3>
                    <p class="text-dark">Languages</p>
                </div>
                <div class="icon">
                    <i class="fas fa-language text-dark"></i>
                </div>
                <a href="{{ route('admin.languages') }}" class="small-box-footer bg-dark">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Contact Messages</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($stats['recent_contacts'] as $contact)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $contact->name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($contact->subject, 30) }}</small>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted">{{ $contact->created_at->diffForHumans() }}</small><br>
                                    <a href="{{ route('admin.contact.view', $contact->id) }}" class="btn btn-sm btn-primary">View</a>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">No recent messages</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Notifications</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($stats['recent_notifications'] as $notification)
                        <li class="list-group-item {{ !$notification->is_read ? 'font-weight-bold' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-{{ $notification->type === 'contact' ? 'envelope' : 'bell' }} mr-2"></i>
                                    <strong>{{ $notification->title }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($notification->message, 40) }}</small>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small><br>
                                    <a href="{{ route('admin.notifications.read', $notification->id) }}" class="btn btn-sm btn-info">View</a>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">No recent notifications</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quick Actions</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.cms.create') }}" class="btn btn-success btn-block">
                        <i class="fas fa-file-plus"></i> Create CMS Page
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.contact.submissions') }}" class="btn btn-info btn-block">
                        <i class="fas fa-envelope"></i> View Messages
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.notifications') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-bell"></i> All Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection