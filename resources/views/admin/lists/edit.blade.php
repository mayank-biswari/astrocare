@extends('admin.layouts.app')

@section('title', 'Edit ' . ucfirst($list->type) . ' List')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="content-fluid">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit {{ ucfirst($list->type) }} List</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('admin.lists.update', $list) }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List Configuration</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>List Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ $list->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" name="description" class="form-control" value="{{ $list->description }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>List Type *</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="method_query_builder" name="method" value="query_builder" {{ $list->method === 'query_builder' ? 'checked' : '' }}>
                                        <label for="method_query_builder" class="custom-control-label">Query Builder</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="method_manual" name="method" value="manual" {{ $list->method === 'manual' ? 'checked' : '' }}>
                                        <label for="method_manual" class="custom-control-label">Manual Selection</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="method_sql" name="method" value="sql" {{ $list->method === 'sql' ? 'checked' : '' }}>
                                        <label for="method_sql" class="custom-control-label">SQL Query</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Query Builder -->
                <div class="card method-config" id="query_builder_config" style="{{ $list->method === 'query_builder' ? '' : 'display: none;' }}">
                    <div class="card-header">
                        <h3 class="card-title">Query Builder</h3>
                    </div>
                    <div class="card-body">
                        <div id="filters-container">
                            @foreach($list->configuration['filters'] ?? [['field' => '', 'operator' => '=', 'value' => '']] as $index => $filter)
                            <div class="filter-row mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="filters[{{ $index }}][field]" class="form-control">
                                            @if($list->type === 'products')
                                                <option value="name" {{ ($filter['field'] ?? '') === 'name' ? 'selected' : '' }}>Name</option>
                                                <option value="category" {{ ($filter['field'] ?? '') === 'category' ? 'selected' : '' }}>Category</option>
                                                <option value="price" {{ ($filter['field'] ?? '') === 'price' ? 'selected' : '' }}>Price</option>
                                                <option value="is_active" {{ ($filter['field'] ?? '') === 'is_active' ? 'selected' : '' }}>Status</option>
                                            @else
                                                <option value="title" {{ ($filter['field'] ?? '') === 'title' ? 'selected' : '' }}>Title</option>
                                                <option value="cms_category_id" {{ ($filter['field'] ?? '') === 'cms_category_id' ? 'selected' : '' }}>Category</option>
                                                <option value="page_type_id" {{ ($filter['field'] ?? '') === 'page_type_id' ? 'selected' : '' }}>Page Type</option>
                                                <option value="is_published" {{ ($filter['field'] ?? '') === 'is_published' ? 'selected' : '' }}>Status</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="filters[{{ $index }}][operator]" class="form-control">
                                            <option value="=" {{ ($filter['operator'] ?? '') === '=' ? 'selected' : '' }}>=</option>
                                            <option value="like" {{ ($filter['operator'] ?? '') === 'like' ? 'selected' : '' }}>Contains</option>
                                            <option value=">" {{ ($filter['operator'] ?? '') === '>' ? 'selected' : '' }}>&gt;</option>
                                            <option value="<" {{ ($filter['operator'] ?? '') === '<' ? 'selected' : '' }}>&lt;</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="filters[{{ $index }}][value]" class="form-control filter-value" value="{{ $filter['value'] ?? '' }}" placeholder="Value">
                                        <select name="filters[{{ $index }}][value]" class="form-control filter-select" style="display: none;">
                                            <!-- Options will be populated dynamically -->
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-filter">Remove</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-success" id="add-filter">Add Filter</button>
                        <button type="button" class="btn btn-info" id="preview-query">Preview Results</button>
                        <div id="preview-results" class="mt-3" style="display: none;">
                            <h5>Preview Results (<span id="preview-count">0</span> items)</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>{{ $list->type === 'products' ? 'Name' : 'Title' }}</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview-items">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manual Selection -->
                <div class="card method-config" id="manual_config" style="{{ $list->method === 'manual' ? '' : 'display: none;' }}">
                    <div class="card-header">
                        <h3 class="card-title">Manual Selection</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($items as $item)
                            <div class="col-md-4 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="item_{{ $item->id }}" name="selected_ids[]" value="{{ $item->id }}" {{ in_array($item->id, $list->configuration['selected_ids'] ?? []) ? 'checked' : '' }}>
                                    <label for="item_{{ $item->id }}" class="custom-control-label">
                                        {{ $list->type === 'products' ? $item->name : $item->title }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- SQL Query -->
                <div class="card method-config" id="sql_config" style="{{ $list->method === 'sql' ? '' : 'display: none;' }}">
                    <div class="card-header">
                        <h3 class="card-title">SQL Query</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <textarea name="sql_query" class="form-control" rows="10" placeholder="SELECT * FROM {{ $list->type === 'products' ? 'products' : 'cms_pages' }} WHERE...">{{ $list->configuration['sql'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" {{ $list->is_active ? 'checked' : '' }}>
                                    <label for="is_active" class="custom-control-label">Active</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="is_template" name="is_template" {{ $list->is_template ? 'checked' : '' }}>
                                    <label for="is_template" class="custom-control-label">Save as Template</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" id="create_page" name="create_page" {{ $list->create_page ? 'checked' : '' }}>
                                    <label for="create_page" class="custom-control-label">Create Page from List</label>
                                </div>
                            </div>
                        </div>

                        <div id="page-creation-fields" class="row mt-3" style="{{ $list->create_page ? '' : 'display: none;' }}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Page Title</label>
                                    <input type="text" name="page_title" class="form-control" value="{{ $list->page_title }}" placeholder="e.g., Our Services">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Page URL Slug</label>
                                    <input type="text" name="page_slug" class="form-control" value="{{ $list->page_slug }}" placeholder="e.g., our-services">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Page Description</label>
                                    <textarea name="page_description" class="form-control" rows="3" placeholder="Brief description of the page content">{{ $list->page_description }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Items per Page</label>
                                    <select name="items_per_page" class="form-control">
                                        <option value="6" {{ $list->items_per_page == 6 ? 'selected' : '' }}>6 items</option>
                                        <option value="9" {{ $list->items_per_page == 9 ? 'selected' : '' }}>9 items</option>
                                        <option value="12" {{ $list->items_per_page == 12 ? 'selected' : '' }}>12 items</option>
                                        <option value="18" {{ $list->items_per_page == 18 ? 'selected' : '' }}>18 items</option>
                                        <option value="24" {{ $list->items_per_page == 24 ? 'selected' : '' }}>24 items</option>
                                        <option value="50" {{ $list->items_per_page == 50 ? 'selected' : '' }}>50 items</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    The page will be created at: <strong id="page-url-preview">/view/{{ $list->page_slug ?: '' }}</strong>
                                    @if($list->page_slug)
                                        <a href="/view/{{ $list->page_slug }}" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
                                            <i class="fas fa-external-link-alt"></i> Open Page
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Update List</button>
                            <a href="{{ route('admin.lists.' . $list->type) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const methodRadios = document.querySelectorAll('input[name="method"]');
    const configs = document.querySelectorAll('.method-config');

    // Categories and Page Types data
    const categories = @json($categories ?? []);
    const pageTypes = @json($pageTypes ?? []);
    const type = '{{ $list->type }}';

    methodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            configs.forEach(config => config.style.display = 'none');
            document.getElementById(this.value + '_config').style.display = 'block';
        });
    });

    // Handle field change to show appropriate value input
    function handleFieldChange(fieldSelect) {
        const row = fieldSelect.closest('.filter-row');
        const valueInput = row.querySelector('.filter-value');
        const valueSelect = row.querySelector('.filter-select');
        const field = fieldSelect.value;
        const currentValue = valueInput.value || valueSelect.value;

        if ((type === 'products' && field === 'category') || (type === 'pages' && field === 'cms_category_id')) {
            valueInput.style.display = 'none';
            valueSelect.style.display = 'block';
            valueSelect.name = valueInput.name;
            valueInput.name = '';

            // Populate categories
            valueSelect.innerHTML = '<option value="">Select Category</option>';
            categories.forEach(cat => {
                const selected = currentValue == cat.id ? 'selected' : '';
                valueSelect.innerHTML += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
            });
        } else if ((type === 'products' && field === 'is_active') || (type === 'pages' && field === 'is_published')) {
            valueInput.style.display = 'none';
            valueSelect.style.display = 'block';
            valueSelect.name = valueInput.name;
            valueInput.name = '';

            // Populate status options
            valueSelect.innerHTML = '<option value="">Select Status</option>';
            const statusOptions = [
                { value: '1', label: 'Active' },
                { value: '0', label: 'Inactive' }
            ];
            statusOptions.forEach(option => {
                const selected = currentValue == option.value ? 'selected' : '';
                valueSelect.innerHTML += `<option value="${option.value}" ${selected}>${option.label}</option>`;
            });
        } else if (type === 'pages' && field === 'page_type_id') {
            valueInput.style.display = 'none';
            valueSelect.style.display = 'block';
            valueSelect.name = valueInput.name;
            valueInput.name = '';

            // Populate page types
            valueSelect.innerHTML = '<option value="">Select Page Type</option>';
            pageTypes.forEach(pt => {
                const selected = currentValue == pt.id ? 'selected' : '';
                valueSelect.innerHTML += `<option value="${pt.id}" ${selected}>${pt.name}</option>`;
            });
        } else {
            valueInput.style.display = 'block';
            valueSelect.style.display = 'none';
            valueInput.name = valueSelect.name;
            valueSelect.name = '';
            if (currentValue && valueInput.value !== currentValue) {
                valueInput.value = currentValue;
            }
        }
    }

    // Initial setup for existing field selects
    document.querySelectorAll('select[name*="[field]"]').forEach(handleFieldChange);

    // Add event listeners to existing field selects
    document.querySelectorAll('select[name*="[field]"]').forEach(select => {
        select.addEventListener('change', () => handleFieldChange(select));
    });

    let filterIndex = {{ count($list->configuration['filters'] ?? []) }};
    document.getElementById('add-filter').addEventListener('click', function() {
        const container = document.getElementById('filters-container');
        const newFilter = container.querySelector('.filter-row').cloneNode(true);

        newFilter.querySelectorAll('input, select').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, '[' + filterIndex + ']');
            input.value = '';
            if (input.type === 'checkbox') input.checked = false;
        });

        // Reset value field to input type
        const valueInput = newFilter.querySelector('.filter-value');
        const valueSelect = newFilter.querySelector('.filter-select');
        valueInput.style.display = 'block';
        valueSelect.style.display = 'none';
        valueInput.name = valueInput.name || valueSelect.name;
        valueSelect.name = '';

        container.appendChild(newFilter);

        // Add event listener to new field select
        const newFieldSelect = newFilter.querySelector('select[name*="[field]"]');
        newFieldSelect.addEventListener('change', () => handleFieldChange(newFieldSelect));

        filterIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-filter')) {
            if (document.querySelectorAll('.filter-row').length > 1) {
                e.target.closest('.filter-row').remove();
            }
        }
    });

    // Preview functionality
    document.getElementById('preview-query').addEventListener('click', function() {
        const filters = [];
        document.querySelectorAll('.filter-row').forEach(row => {
            const field = row.querySelector('select[name*="[field]"]').value;
            const operator = row.querySelector('select[name*="[operator]"]').value;
            const valueInput = row.querySelector('.filter-value');
            const valueSelect = row.querySelector('.filter-select');
            const value = valueInput.style.display !== 'none' ? valueInput.value : valueSelect.value;

            if (field && operator && value) {
                filters.push({ field, operator, value });
            }
        });

        fetch('{{ route("admin.lists.preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: '{{ $list->type }}',
                method: 'query_builder',
                filters: filters
            })
        })
        .then(response => response.json())
        .then(data => {
            const previewDiv = document.getElementById('preview-results');
            const previewCount = document.getElementById('preview-count');
            const previewItems = document.getElementById('preview-items');

            if (data.error) {
                alert('Error: ' + data.error);
                previewDiv.style.display = 'none';
            } else {
                previewCount.textContent = data.count;
                previewItems.innerHTML = '';

                if (data.results && data.results.length > 0) {
                    data.results.forEach(item => {
                        const title = '{{ $list->type }}' === 'products' ? item.name : item.title;
                        const status = '{{ $list->type }}' === 'products' ?
                            (item.is_active ? 'Active' : 'Inactive') :
                            (item.is_published ? 'Published' : 'Draft');
                        const statusClass = ('{{ $list->type }}' === 'products' ? item.is_active : item.is_published) ? 'success' : 'secondary';

                        previewItems.innerHTML += `
                            <tr>
                                <td>${item.id}</td>
                                <td>${title}</td>
                                <td><span class="badge badge-${statusClass}">${status}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    previewItems.innerHTML = '<tr><td colspan="3" class="text-center">No items found</td></tr>';
                }

                previewDiv.style.display = 'block';
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    });

    // Show/hide page creation fields
    document.getElementById('create_page').addEventListener('change', function() {
        document.getElementById('page-creation-fields').style.display = this.checked ? 'block' : 'none';
    });

    // Auto-generate slug from title
    document.querySelector('input[name="page_title"]').addEventListener('input', function() {
        const title = this.value;
        const slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        document.querySelector('input[name="page_slug"]').value = slug;
        document.getElementById('page-url-preview').textContent = '/view/' + slug;
    });
});
</script>
@endsection
