@extends('admin.layouts.app')

@section('title', 'View Order')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Order Details - {{ $order->order_number }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders') }}">Orders</a></li>
                    <li class="breadcrumb-item active">View Order</li>
                </ol>
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
                        <h3 class="card-title">Order Items</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $items = is_array($order->items) ? $order->items : json_decode($order->items, true);
                            // Determine if items is a list of products or a single plan info object
                            // Check if any item has a 'name' key (product items) or if it's numerically indexed
                            $isItemList = $items && (isset($items[0]) || (is_array($items) && !isset($items['plan_id']) && !isset($items['plan_name']) && count($items) > 0 && isset(array_values($items)[0]['name'])));
                        @endphp
                        @if($items && $isItemList)
                            @foreach($items as $item)
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                    <div class="d-flex align-items-center">
                                        @if(isset($item['image']) && $item['image'])
                                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="img-thumbnail mr-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 60px; height: 60px; border-radius: 4px;">
                                                <span class="font-weight-bold">{{ substr($item['name'] ?? '??', 0, 2) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-1">{{ $item['name'] ?? 'Item' }}</h6>
                                            <small class="text-muted">Quantity: {{ $item['quantity'] ?? 1 }}</small>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <strong>{{ getCurrencySymbol($order->currency) }}{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        @elseif($items)
                            {{-- Single plan/service order (campaign orders) --}}
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 60px; height: 60px; border-radius: 4px;">
                                        <span class="font-weight-bold">{{ substr($items['plan_name'] ?? 'PL', 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $items['plan_name'] ?? 'Plan' }}</h6>
                                        @if(isset($items['description']))
                                            <small class="text-muted">{{ $items['description'] }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <strong>{{ getCurrencySymbol($order->currency) }}{{ number_format($order->total_amount, 2) }}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between">
                                <strong>Total Amount:</strong>
                                <strong class="text-primary">{{ getCurrencySymbol($order->currency) }}{{ number_format($order->total_amount, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Order Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Order Number:</strong></td>
                                <td>{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge
                                        @if($order->status == 'delivered') badge-success
                                        @elseif($order->status == 'shipped') badge-info
                                        @elseif($order->status == 'processing') badge-warning
                                        @elseif($order->status == 'cancelled') badge-danger
                                        @else badge-secondary @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Payment:</strong></td>
                                <td>{{ ucfirst($order->payment_method) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Customer Details</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> {{ $order->user->name }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email }}</p>
                        <p><strong>Phone:</strong> {{ $order->phone }}</p>
                        <p><strong>Address:</strong><br>{{ is_array($order->shipping_address) ? implode(', ', array_filter($order->shipping_address)) : ($order->shipping_address ?? 'N/A') }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Status</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
