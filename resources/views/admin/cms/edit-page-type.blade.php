@extends('admin.layouts.app')

@section('title', 'Edit Page Type')

@section('content')
<div class="content-fluid">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $pageType->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input type="text" name="description" class="form-control" value="{{ $pageType->description }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Template</label>
                                    <select name="template" class="form-control">
                                        <option value="" {{ !$pageType->template ? 'selected' : '' }}>Default Template</option>
                                        @php
                                            $templatesPath = resource_path('views/dynamic-pages/custom-templates');
                                            $templates = File::exists($templatesPath) ? File::files($templatesPath) : [];
                                        @endphp
                                        @foreach($templates as $template)
                                            @php
                                                $filename = $template->getFilename();
                                                $name = str_replace(['.blade.php', '-', '_'], ['', ' ', ' '], pathinfo($filename, PATHINFO_FILENAME));
                                                $name = ucwords($name);
                                            @endphp
                                            <option value="{{ $filename }}" {{ $pageType->template == $filename ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Custom template file for this page type</small>
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
                                            <select name="custom_fields[{{ $index }}][type]" class="form-control" onchange="toggleFieldOptions({{ $index }})" required>
                                                <option value="text" {{ $field['type'] == 'text' ? 'selected' : '' }}>Text</option>
                                                <option value="number" {{ $field['type'] == 'number' ? 'selected' : '' }}>Number</option>
                                                <option value="select" {{ $field['type'] == 'select' ? 'selected' : '' }}>Select</option>
                                                <option value="textarea" {{ $field['type'] == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                <option value="image" {{ $field['type'] == 'image' ? 'selected' : '' }}>Image(s)</option>
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
                                    <div class="row mt-2" id="number_options_{{ $index }}" style="display: {{ $field['type'] == 'number' ? 'block' : 'none' }};">
                                        <div class="col-md-4">
                                            <label>Min Value</label>
                                            <input type="number" name="custom_fields[{{ $index }}][min]" class="form-control" value="{{ $field['min'] ?? '' }}" placeholder="e.g., 0">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Max Value</label>
                                            <input type="number" name="custom_fields[{{ $index }}][max]" class="form-control" value="{{ $field['max'] ?? '' }}" placeholder="e.g., 100">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Step</label>
                                            <input type="number" name="custom_fields[{{ $index }}][step]" class="form-control" value="{{ $field['step'] ?? 1 }}" placeholder="e.g., 1">
                                        </div>
                                    </div>
                                    <div class="row mt-2" id="image_options_{{ $index }}" style="display: {{ $field['type'] == 'image' ? 'block' : 'none' }};">
                                        <div class="col-md-3">
                                            <label>Allow Multiple</label>
                                            <select name="custom_fields[{{ $index }}][multiple]" class="form-control">
                                                <option value="0" {{ !($field['multiple'] ?? false) ? 'selected' : '' }}>Single Image</option>
                                                <option value="1" {{ ($field['multiple'] ?? false) ? 'selected' : '' }}>Multiple Images</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Max Size (MB)</label>
                                            <input type="number" name="custom_fields[{{ $index }}][max_size]" class="form-control" value="{{ $field['max_size'] ?? 2 }}" min="1" max="10">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Max Images (if multiple)</label>
                                            <input type="number" name="custom_fields[{{ $index }}][max_images]" class="form-control" value="{{ $field['max_images'] ?? 5 }}" min="1" max="20">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Allowed Types</label>
                                            <select name="custom_fields[{{ $index }}][allowed_types][]" class="form-control" multiple size="3">
                                                @php $allowedTypes = $field['allowed_types'] ?? ['jpg', 'png']; @endphp
                                                <option value="jpg" {{ in_array('jpg', $allowedTypes) ? 'selected' : '' }}>JPG</option>
                                                <option value="png" {{ in_array('png', $allowedTypes) ? 'selected' : '' }}>PNG</option>
                                                <option value="gif" {{ in_array('gif', $allowedTypes) ? 'selected' : '' }}>GIF</option>
                                                <option value="webp" {{ in_array('webp', $allowedTypes) ? 'selected' : '' }}>WebP</option>
                                                <option value="svg" {{ in_array('svg', $allowedTypes) ? 'selected' : '' }}>SVG</option>
                                            </select>
                                            <small class="text-muted">Hold Ctrl to select multiple</small>
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
                <select name="custom_fields[${fieldIndex}][type]" class="form-control" onchange="toggleFieldOptions(${fieldIndex})" required>
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="select">Select</option>
                    <option value="textarea">Textarea</option>
                    <option value="image">Image(s)</option>
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
        <div class="row mt-2" id="number_options_${fieldIndex}" style="display: none;">
            <div class="col-md-4">
                <label>Min Value</label>
                <input type="number" name="custom_fields[${fieldIndex}][min]" class="form-control" placeholder="e.g., 0">
            </div>
            <div class="col-md-4">
                <label>Max Value</label>
                <input type="number" name="custom_fields[${fieldIndex}][max]" class="form-control" placeholder="e.g., 100">
            </div>
            <div class="col-md-4">
                <label>Step</label>
                <input type="number" name="custom_fields[${fieldIndex}][step]" class="form-control" placeholder="e.g., 1" value="1">
            </div>
        </div>
        <div class="row mt-2" id="image_options_${fieldIndex}" style="display: none;">
            <div class="col-md-3">
                <label>Allow Multiple</label>
                <select name="custom_fields[${fieldIndex}][multiple]" class="form-control">
                    <option value="0">Single Image</option>
                    <option value="1">Multiple Images</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Max Size (MB)</label>
                <input type="number" name="custom_fields[${fieldIndex}][max_size]" class="form-control" value="2" min="1" max="10">
            </div>
            <div class="col-md-3">
                <label>Max Images (if multiple)</label>
                <input type="number" name="custom_fields[${fieldIndex}][max_images]" class="form-control" value="5" min="1" max="20">
            </div>
            <div class="col-md-3">
                <label>Allowed Types</label>
                <select name="custom_fields[${fieldIndex}][allowed_types][]" class="form-control" multiple size="3">
                    <option value="jpg" selected>JPG</option>
                    <option value="png" selected>PNG</option>
                    <option value="gif">GIF</option>
                    <option value="webp">WebP</option>
                    <option value="svg">SVG</option>
                </select>
                <small class="text-muted">Hold Ctrl to select multiple</small>
            </div>
        </div>
    `;
    container.appendChild(fieldDiv);
    fieldIndex++;
}

function removeCustomField(button) {
    button.closest('.border').remove();
}

function toggleFieldOptions(index) {
    const typeSelect = document.querySelector(`select[name="custom_fields[${index}][type]"]`);
    const optionsDiv = document.getElementById(`options_${index}`);
    const numberOptionsDiv = document.getElementById(`number_options_${index}`);
    const imageOptionsDiv = document.getElementById(`image_options_${index}`);

    if (typeSelect && optionsDiv && imageOptionsDiv) {
        optionsDiv.style.display = 'none';
        if (numberOptionsDiv) numberOptionsDiv.style.display = 'none';
        imageOptionsDiv.style.display = 'none';

        if (typeSelect.value === 'select') {
            optionsDiv.style.display = 'block';
        } else if (typeSelect.value === 'number') {
            if (numberOptionsDiv) numberOptionsDiv.style.display = 'block';
        } else if (typeSelect.value === 'image') {
            imageOptionsDiv.style.display = 'block';
        }
    }
}
</script>
@endsection
