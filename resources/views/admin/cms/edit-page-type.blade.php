@extends('admin.layouts.app')

@section('title', 'Edit Page Type')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Page Type: {{ $pageType->name }}</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <form action="{{ route('admin.cms.page-types.update', $pageType->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $pageType->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" name="description" class="form-control" value="{{ $pageType->description }}">
                                </div>
                            </div>
                        </div>
                        
                        <h5>Field Configuration</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="fields_config[show_comments]" class="form-check-input" value="1" {{ $pageType->fields_config['show_comments'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label">Show Comments</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="fields_config[show_posted_date]" class="form-check-input" value="1" {{ $pageType->fields_config['show_posted_date'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label">Show Posted Date</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="fields_config[show_author]" class="form-check-input" value="1" {{ $pageType->fields_config['show_author'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label">Show Author</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" name="fields_config[show_rating]" class="form-check-input" value="1" {{ $pageType->fields_config['show_rating'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label">Show Rating</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mt-3">
                            <input type="checkbox" name="is_active" class="form-check-input" {{ $pageType->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                        
                        <h5 class="mt-4">Custom Fields</h5>
                        <div id="customFieldsList">
                            @if(isset($pageType->fields_config['custom_fields']))
                                @foreach($pageType->fields_config['custom_fields'] as $index => $field)
                                <div class="border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Field Name</label>
                                            <input type="text" name="custom_fields[{{ $index }}][name]" class="form-control" value="{{ $field['name'] }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Field Label</label>
                                            <input type="text" name="custom_fields[{{ $index }}][label]" class="form-control" value="{{ $field['label'] }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Field Type</label>
                                            <select name="custom_fields[{{ $index }}][type]" class="form-control" onchange="toggleOptions({{ $index }})" required>
                                                <option value="text" {{ $field['type'] == 'text' ? 'selected' : '' }}>Text</option>
                                                <option value="number" {{ $field['type'] == 'number' ? 'selected' : '' }}>Number</option>
                                                <option value="select" {{ $field['type'] == 'select' ? 'selected' : '' }}>Select</option>
                                                <option value="textarea" {{ $field['type'] == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Required</label>
                                            <select name="custom_fields[{{ $index }}][required]" class="form-control">
                                                <option value="0" {{ !($field['required'] ?? false) ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ ($field['required'] ?? false) ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-block" onclick="removeCustomField(this)">Remove</button>
                                        </div>
                                    </div>
                                    <div class="row mt-2" id="options_{{ $index }}" style="display: {{ $field['type'] == 'select' ? 'block' : 'none' }};">
                                        <div class="col-md-12">
                                            <label>Options (comma separated)</label>
                                            <input type="text" name="custom_fields[{{ $index }}][options]" class="form-control" value="{{ isset($field['options']) ? implode(', ', $field['options']) : '' }}">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-success" onclick="addCustomField()">Add Custom Field</button>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.cms.page-types') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
let fieldIndex = {{ isset($pageType->fields_config['custom_fields']) ? count($pageType->fields_config['custom_fields']) : 0 }};

function addCustomField() {
    const container = document.getElementById('customFieldsList');
    const fieldDiv = document.createElement('div');
    fieldDiv.className = 'border p-3 mb-3';
    fieldDiv.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <label>Field Name</label>
                <input type="text" name="custom_fields[${fieldIndex}][name]" class="form-control" placeholder="field_name" required>
            </div>
            <div class="col-md-3">
                <label>Field Label</label>
                <input type="text" name="custom_fields[${fieldIndex}][label]" class="form-control" placeholder="Field Label" required>
            </div>
            <div class="col-md-2">
                <label>Field Type</label>
                <select name="custom_fields[${fieldIndex}][type]" class="form-control" onchange="toggleOptions(${fieldIndex})" required>
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="select">Select</option>
                    <option value="textarea">Textarea</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Required</label>
                <select name="custom_fields[${fieldIndex}][required]" class="form-control">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger btn-block" onclick="removeCustomField(this)">Remove</button>
            </div>
        </div>
        <div class="row mt-2" id="options_${fieldIndex}" style="display: none;">
            <div class="col-md-12">
                <label>Options (comma separated)</label>
                <input type="text" name="custom_fields[${fieldIndex}][options]" class="form-control" placeholder="Option 1, Option 2, Option 3">
            </div>
        </div>
    `;
    container.appendChild(fieldDiv);
    fieldIndex++;
}

function removeCustomField(button) {
    button.closest('.border').remove();
}

function toggleOptions(index) {
    const typeSelect = document.querySelector(`select[name="custom_fields[${index}][type]"]`);
    const optionsDiv = document.getElementById(`options_${index}`);
    
    if (typeSelect && optionsDiv) {
        if (typeSelect.value === 'select') {
            optionsDiv.style.display = 'block';
        } else {
            optionsDiv.style.display = 'none';
        }
    }
}
</script>
@endsection