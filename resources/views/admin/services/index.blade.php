@extends('admin.layouts.app')

@section('title', 'Manage Services')
@section('page-title', 'Services')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Services</li>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Service
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Sort Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>{{ $service->id }}</td>
                        <td>{{ $service->name }}</td>
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst($service->type) }}
                            </span>
                        </td>
                        <td>₹{{ number_format($service->base_price, 2) }}</td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input service-toggle"
                                       id="service-toggle-{{ $service->id }}"
                                       data-id="{{ $service->id }}"
                                       {{ $service->is_active ? 'checked' : '' }}>
                                <label class="custom-control-label" for="service-toggle-{{ $service->id }}">
                                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                        </td>
                        <td>{{ $service->sort_order }}</td>
                        <td>
                            <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger delete-btn">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No services found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    {{ $services->links('pagination::bootstrap-4') }}
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.service-toggle').on('change', function() {
        var serviceId = $(this).data('id');
        var label = $(this).next('label');

        $.ajax({
            url: '/admin/services/' + serviceId + '/toggle',
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
                    text: 'Failed to update service status.',
                });
                // Revert the toggle
                var checkbox = $('#service-toggle-' + serviceId);
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
        });
    });
});
</script>
@endpush
