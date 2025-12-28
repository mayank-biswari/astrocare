@extends('admin.layouts.app')

@section('title', 'Edit CMS Page')
@section('page-title', 'Edit CMS Page')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Page: {{ $page->title }}</h3>
    </div>
    <form action="{{ route('admin.cms.update', $page->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $page->title }}" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Body Content</label>
                        <textarea name="body" rows="10" class="form-control" required>{{ $page->body }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Page Type</label>
                        <select name="cms_page_type_id" class="form-control" id="pageTypeSelect">
                            <option value="">Select Page Type</option>
                            @foreach($pageTypes as $pageType)
                                <option value="{{ $pageType->id }}" data-config="{{ json_encode($pageType->fields_config) }}" {{ $page->cms_page_type_id == $pageType->id ? 'selected' : '' }}>{{ $pageType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Language</label>
                        <select name="language_code" class="form-control" required>
                            @foreach($languages as $language)
                                <option value="{{ $language->code }}" {{ $page->language_code == $language->code ? 'selected' : '' }}>{{ $language->name }} ({{ $language->native_name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="cms_category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $page->cms_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Featured Image</label>
                        @if($page->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $page->image) }}" alt="Current image" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ $page->meta_title }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" rows="3" class="form-control">{{ $page->meta_description }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="{{ $page->meta_keywords }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_published" value="1" class="custom-control-input" id="published" {{ $page->is_published ? 'checked' : '' }}>
                            <label class="custom-control-label" for="published">Published</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="allow_comments" value="1" class="custom-control-input" id="comments" {{ $page->allow_comments ? 'checked' : '' }}>
                            <label class="custom-control-label" for="comments">Allow Comments</label>
                        </div>
                    </div>
                </div>
                
                <!-- Translations -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Translations</h5>
                        </div>
                        <div class="card-body">
                            @foreach($languages as $language)
                                @if($language->code !== $page->language_code)
                                    @php
                                        $translation = $page->translations->where('language_code', $language->code)->first();
                                    @endphp
                                    <div class="translation-section" data-lang="{{ $language->code }}">
                                        <h6>{{ $language->name }} ({{ $language->native_name }})</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" name="translations[{{ $language->code }}][title]" class="form-control" value="{{ $translation->title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Meta Title</label>
                                                    <input type="text" name="translations[{{ $language->code }}][meta_title]" class="form-control" value="{{ $translation->meta_title ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Body Content</label>
                                                    <textarea name="translations[{{ $language->code }}][body]" rows="5" class="form-control">{{ $translation->body ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Meta Description</label>
                                                    <textarea name="translations[{{ $language->code }}][meta_description]" rows="2" class="form-control">{{ $translation->meta_description ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Meta Keywords</label>
                                                    <input type="text" name="translations[{{ $language->code }}][meta_keywords]" class="form-control" value="{{ $translation->meta_keywords ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Dynamic Custom Fields -->
                <div id="customFields" class="col-md-12" style="display: none;">
                    <h5>Custom Fields</h5>
                    <div id="customFieldsContainer"></div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Page
            </button>
            <a href="{{ route('admin.cms.pages') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
const existingCustomFields = @json($page->custom_fields ?? []);

function loadCustomFields() {
    const selectedOption = document.getElementById('pageTypeSelect').options[document.getElementById('pageTypeSelect').selectedIndex];
    const config = selectedOption.dataset.config;
    const customFieldsDiv = document.getElementById('customFields');
    const container = document.getElementById('customFieldsContainer');
    
    if (config) {
        const fieldsConfig = JSON.parse(config);
        container.innerHTML = '';
        
        if (fieldsConfig.custom_fields && fieldsConfig.custom_fields.length > 0) {
            customFieldsDiv.style.display = 'block';
            
            fieldsConfig.custom_fields.forEach(field => {
                const fieldDiv = document.createElement('div');
                fieldDiv.className = 'form-group col-md-6';
                
                let fieldHtml = `<label>${field.label}${field.required ? ' *' : ''}</label>`;
                const existingValue = existingCustomFields[field.name] || '';
                
                if (field.type === 'select') {
                    fieldHtml += `<select name="custom_fields[${field.name}]" class="form-control" ${field.required ? 'required' : ''}>`;
                    fieldHtml += '<option value="">Select...</option>';
                    field.options.forEach(option => {
                        const selected = existingValue === option ? 'selected' : '';
                        fieldHtml += `<option value="${option}" ${selected}>${option}</option>`;
                    });
                    fieldHtml += '</select>';
                } else {
                    fieldHtml += `<input type="${field.type}" name="custom_fields[${field.name}]" class="form-control" value="${existingValue}" ${field.required ? 'required' : ''}>`;
                }
                
                fieldDiv.innerHTML = fieldHtml;
                container.appendChild(fieldDiv);
            });
        } else {
            customFieldsDiv.style.display = 'none';
        }
    } else {
        customFieldsDiv.style.display = 'none';
    }
}

// Load fields on page load
document.addEventListener('DOMContentLoaded', loadCustomFields);

// Load fields when page type changes
document.getElementById('pageTypeSelect').addEventListener('change', loadCustomFields);
</script>
@endsection