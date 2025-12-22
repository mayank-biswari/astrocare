@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Language Management</h1>
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
                            <h3 class="card-title">Languages</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Native Name</th>
                                        <th>Status</th>
                                        <th>Default</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($languages as $language)
                                    <tr>
                                        <td>{{ $language->code }}</td>
                                        <td>{{ $language->name }}</td>
                                        <td>{{ $language->native_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $language->is_active ? 'success' : 'danger' }}">
                                                {{ $language->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($language->is_default)
                                                <span class="badge badge-primary">Default</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.languages.set-default', $language->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Set Default</button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editLanguage({{ $language->id }}, '{{ $language->code }}', '{{ $language->name }}', '{{ $language->native_name }}', {{ $language->is_active ? 'true' : 'false' }})">Edit</button>
                                            @if(!$language->is_default)
                                            <form method="POST" action="{{ route('admin.languages.delete', $language->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" id="form-title">Add Language</h3>
                        </div>
                        <form id="language-form" method="POST" action="{{ route('admin.languages.store') }}">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="code">Language Code</label>
                                    <input type="text" class="form-control" id="code" name="code" placeholder="e.g., en, hi, fr" required>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g., English" required>
                                </div>
                                <div class="form-group">
                                    <label for="native_name">Native Name</label>
                                    <input type="text" class="form-control" id="native_name" name="native_name" placeholder="e.g., English, हिंदी" required>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" id="submit-btn">Add Language</button>
                                <button type="button" class="btn btn-secondary" id="cancel-btn" onclick="resetForm()" style="display: none;">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
let editingId = null;

function editLanguage(id, code, name, nativeName, isActive) {
    editingId = id;
    document.getElementById('form-title').textContent = 'Edit Language';
    document.getElementById('language-form').action = `/admin/languages/${id}`;
    document.getElementById('language-form').innerHTML += '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('code').value = code;
    document.getElementById('name').value = name;
    document.getElementById('native_name').value = nativeName;
    document.getElementById('is_active').checked = isActive;
    document.getElementById('submit-btn').textContent = 'Update Language';
    document.getElementById('cancel-btn').style.display = 'inline-block';
}

function resetForm() {
    editingId = null;
    document.getElementById('form-title').textContent = 'Add Language';
    document.getElementById('language-form').action = '{{ route("admin.languages.store") }}';
    document.getElementById('language-form').querySelector('input[name="_method"]')?.remove();
    document.getElementById('code').value = '';
    document.getElementById('name').value = '';
    document.getElementById('native_name').value = '';
    document.getElementById('is_active').checked = true;
    document.getElementById('submit-btn').textContent = 'Add Language';
    document.getElementById('cancel-btn').style.display = 'none';
}
</script>
@endsection