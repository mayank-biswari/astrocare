@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Payment Gateways</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Payment Gateways</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Supported Currencies</th>
                                <th>Status</th>
                                <th>Mode</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gateways as $gateway)
                            <tr>
                                <td>
                                    <strong>{{ $gateway->name }}</strong>
                                    <br><small class="text-muted">{{ $gateway->description }}</small>
                                </td>
                                <td>
                                    @foreach($gateway->supported_currencies as $currency)
                                        <span class="badge badge-info">{{ $currency }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge badge-{{ $gateway->is_active ? 'success' : 'danger' }}">
                                        {{ $gateway->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @if($gateway->code !== 'cod')
                                        <span class="badge badge-{{ $gateway->is_test_mode ? 'warning' : 'primary' }}">
                                            {{ $gateway->is_test_mode ? 'Test' : 'Live' }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editGateway({{ $gateway->id }}, '{{ $gateway->name }}', '{{ $gateway->code }}', {{ $gateway->is_active ? 'true' : 'false' }}, {{ $gateway->is_test_mode ? 'true' : 'false' }}, {{ json_encode($gateway->credentials) }})">
                                        <i class="fas fa-edit"></i> Configure
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
            <form id="gateway-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Configure Payment Gateway</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active">
                            <label class="form-check-label" for="is_active">Enable Gateway</label>
                        </div>
                    </div>
                    <div class="form-group" id="test-mode-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_test_mode" name="is_test_mode">
                            <label class="form-check-label" for="is_test_mode">Test Mode</label>
                        </div>
                    </div>
                    <div id="credentials-section"></div>
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
function editGateway(id, name, code, isActive, isTestMode, credentials) {
    document.getElementById('modal-title').textContent = 'Configure ' + name;
    document.getElementById('gateway-form').action = '/admin/payment-gateways/' + id;
    document.getElementById('is_active').checked = isActive;
    document.getElementById('is_test_mode').checked = isTestMode;
    
    // Hide test mode for COD
    if (code === 'cod') {
        document.getElementById('test-mode-group').style.display = 'none';
    } else {
        document.getElementById('test-mode-group').style.display = 'block';
    }
    
    // Build credentials fields
    const credentialsSection = document.getElementById('credentials-section');
    credentialsSection.innerHTML = '';
    
    if (credentials && code !== 'cod') {
        credentialsSection.innerHTML = '<h6 class="mt-3">API Credentials</h6>';
        for (const [key, value] of Object.entries(credentials)) {
            const fieldName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            credentialsSection.innerHTML += `
                <div class="form-group">
                    <label for="cred_${key}">${fieldName}</label>
                    <input type="text" class="form-control" id="cred_${key}" name="credentials[${key}]" value="${value || ''}" placeholder="Enter ${fieldName}">
                </div>
            `;
        }
    }
    
    $('#editModal').modal('show');
}
</script>
@endsection