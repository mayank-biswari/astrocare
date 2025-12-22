@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Currency Management</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Currencies</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Symbol</th>
                                <th>Exchange Rate (to INR)</th>
                                <th>Status</th>
                                <th>Default</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currencies as $currency)
                            <tr>
                                <td><strong>{{ $currency->code }}</strong></td>
                                <td>{{ $currency->name }}</td>
                                <td>{{ $currency->symbol }}</td>
                                <td>{{ $currency->exchange_rate }}</td>
                                <td>
                                    <span class="badge badge-{{ $currency->is_active ? 'success' : 'danger' }}">
                                        {{ $currency->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @if($currency->is_default)
                                        <span class="badge badge-primary">Default</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.currencies.set-default', $currency->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Set Default</button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editCurrency({{ $currency->id }}, '{{ $currency->code }}', '{{ $currency->name }}', '{{ $currency->exchange_rate }}', {{ $currency->is_active ? 'true' : 'false' }})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="currency-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Edit Currency</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Currency: <strong id="currency-name"></strong></label>
                    </div>
                    <div class="form-group">
                        <label for="exchange_rate">Exchange Rate (1 INR = ? <span id="currency-code"></span>)</label>
                        <input type="number" step="0.0001" class="form-control" id="exchange_rate" name="exchange_rate" required>
                        <small class="text-muted">Enter how much 1 INR equals in this currency</small>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active">
                            <label class="form-check-label" for="is_active">Enable Currency</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCurrency(id, code, name, exchangeRate, isActive) {
    document.getElementById('currency-form').action = '/admin/currencies/' + id;
    document.getElementById('currency-name').textContent = name + ' (' + code + ')';
    document.getElementById('currency-code').textContent = code;
    document.getElementById('exchange_rate').value = exchangeRate;
    document.getElementById('is_active').checked = isActive;
    
    $('#editModal').modal('show');
}
</script>
@endsection