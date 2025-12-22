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
                <div class="small-box-footer">Consultations</div>
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
                <div class="col-md-4">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.orders') }}" class="btn btn-success btn-block">
                        <i class="fas fa-eye"></i> View Orders
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.users') }}" class="btn btn-info btn-block">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection