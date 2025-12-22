@extends('admin.layouts.app')

@section('title', 'Manage Categories')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Categories</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categories</li>
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
                        <h3 class="card-title">All Categories</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td><strong>{{ $category->name }}</strong></td>
                                        <td><code>{{ $category->slug }}</code></td>
                                        <td>
                                            <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', {{ $category->is_active ? 'true' : 'false' }})">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
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
                        <h3 class="card-title" id="form-title">Add New Category</h3>
                    </div>
                    <form id="category-form" action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            
                            <h5>Translations</h5>
                            @foreach(\App\Models\Language::getActiveLanguages() as $language)
                                <div class="form-group">
                                    <label for="translations_{{ $language->code }}_name">Name ({{ $language->native_name }})</label>
                                    <input type="text" class="form-control" id="translations_{{ $language->code }}_name" name="translations[{{ $language->code }}][name]">
                                </div>
                                <div class="form-group">
                                    <label for="translations_{{ $language->code }}_description">Description ({{ $language->native_name }})</label>
                                    <textarea class="form-control" id="translations_{{ $language->code }}_description" name="translations[{{ $language->code }}][description]" rows="2"></textarea>
                                </div>
                            @endforeach
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" id="submit-btn">Create Category</button>
                            <button type="button" class="btn btn-secondary" id="cancel-btn" onclick="resetForm()" style="display: none;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let editingId = null;

function editCategory(id, name, description, isActive, translations = {}) {
    editingId = id;
    document.getElementById('form-title').textContent = 'Edit Category';
    document.getElementById('category-form').action = '/admin/categories/' + id;
    if (!document.querySelector('input[name="_method"]')) {
        document.getElementById('category-form').innerHTML += '<input type="hidden" name="_method" value="PUT">';
    }
    document.getElementById('name').value = name;
    document.getElementById('description').value = description;
    
    // Clear and set translations
    @foreach(\App\Models\Language::getActiveLanguages() as $language)
        const {{ $language->code }}Name = document.getElementById('translations_{{ $language->code }}_name');
        const {{ $language->code }}Desc = document.getElementById('translations_{{ $language->code }}_description');
        if ({{ $language->code }}Name) {{ $language->code }}Name.value = translations['{{ $language->code }}'] ? translations['{{ $language->code }}'].name || '' : '';
        if ({{ $language->code }}Desc) {{ $language->code }}Desc.value = translations['{{ $language->code }}'] ? translations['{{ $language->code }}'].description || '' : '';
    @endforeach
    
    document.getElementById('is_active').checked = isActive;
    document.getElementById('submit-btn').textContent = 'Update Category';
    document.getElementById('cancel-btn').style.display = 'inline-block';
}

function resetForm() {
    editingId = null;
    document.getElementById('form-title').textContent = 'Add New Category';
    document.getElementById('category-form').action = '{{ route("admin.categories.store") }}';
    document.querySelector('input[name="_method"]')?.remove();
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
    
    // Clear translations
    @foreach(\App\Models\Language::getActiveLanguages() as $language)
        const {{ $language->code }}Name = document.getElementById('translations_{{ $language->code }}_name');
        const {{ $language->code }}Desc = document.getElementById('translations_{{ $language->code }}_description');
        if ({{ $language->code }}Name) {{ $language->code }}Name.value = '';
        if ({{ $language->code }}Desc) {{ $language->code }}Desc.value = '';
    @endforeach
    
    document.getElementById('is_active').checked = true;
    document.getElementById('submit-btn').textContent = 'Create Category';
    document.getElementById('cancel-btn').style.display = 'none';
}
</script>
@endsection