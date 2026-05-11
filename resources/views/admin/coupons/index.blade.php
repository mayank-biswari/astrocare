@extends('admin.layouts.app')

@section('title', 'Manage Coupons')
@section('page-title', 'Coupons')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Coupons</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Coupon
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount Type</th>
                    <th>Value</th>
                    <th>Validity Period</th>
                    <th>Usage</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td><strong>{{ $coupon->code }}</strong></td>
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst($coupon->discount_type) }}
                            </span>
                        </td>
                        <td>
                            @if($coupon->discount_type === 'percentage')
                                {{ $coupon->discount_value }}%
                            @else
                                ₹{{ number_format($coupon->discount_value, 2) }}
                            @endif
                        </td>
                        <td>
                            {{ $coupon->start_date->format('d M Y') }} — {{ $coupon->end_date->format('d M Y') }}
                        </td>
                        <td>
                            {{ $coupon->usage_count }} / {{ $coupon->usage_limit == 0 ? '∞' : $coupon->usage_limit }}
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input coupon-toggle"
                                       id="coupon-toggle-{{ $coupon->id }}"
                                       data-id="{{ $coupon->id }}"
                                       {{ $coupon->is_active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="coupon-toggle-{{ $coupon->id }}">
                                    {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.coupons.delete', $coupon->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger delete-btn" onclick="return confirm('Are you sure you want to delete this coupon?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No coupons found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    {{ $coupons->links('pagination::bootstrap-4') }}
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.coupon-toggle').on('change', function() {
        var couponId = $(this).data('id');
        var label = $(this).next('label');

        $.ajax({
            url: '/admin/coupons/' + couponId + '/toggle',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    label.text(response.is_active ? 'Active' : 'Inactive');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update coupon status.',
                });
                // Revert the toggle
                var checkbox = $('#coupon-toggle-' + couponId);
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
        });
    });
});
</script>
@endpush
