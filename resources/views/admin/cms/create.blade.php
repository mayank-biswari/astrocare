@extends('admin.layouts.app')

@section('title', 'Create CMS Page')
@section('page-title', 'Create CMS Page')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Page</h3>
    </div>
    <form action="{{ route('admin.cms.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Body Content</label>
                        <div id="editor" style="height: 300px;"></div>
                        <textarea name="body" id="body-content" style="display: none;" required></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Page Type</label>
                        <select name="cms_page_type_id" class="form-control" id="pageTypeSelect">
                            <option value="">Select Page Type</option>
                            @foreach($pageTypes as $pageType)
                                <option value="{{ $pageType->id }}" data-config="{{ json_encode($pageType->fields_config) }}">{{ $pageType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Language</label>
                        <select name="language_code" class="form-control" required>
                            @foreach($languages as $language)
                                <option value="{{ $language->code }}" {{ $language->is_default ? 'selected' : '' }}>{{ $language->name }} ({{ $language->native_name }})</option>
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
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Featured Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Meta Description</label>
                        <textarea name="meta_description" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_published" value="1" class="custom-control-input" id="published">
                            <label class="custom-control-label" for="published">Published</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="allow_comments" value="1" checked class="custom-control-input" id="comments">
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
                                @if(!$language->is_default)
                                <div class="translation-section" data-lang="{{ $language->code }}">
                                    <h6>{{ $language->name }} ({{ $language->native_name }})</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Title</label>
                                                <input type="text" name="translations[{{ $language->code }}][title]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Meta Title</label>
                                                <input type="text" name="translations[{{ $language->code }}][meta_title]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Body Content</label>
                                                <div id="editor-{{ $language->code }}" style="height: 200px;"></div>
                                                <textarea name="translations[{{ $language->code }}][body]" id="body-content-{{ $language->code }}" style="display: none;"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Meta Description</label>
                                                <textarea name="translations[{{ $language->code }}][meta_description]" rows="2" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Meta Keywords</label>
                                                <input type="text" name="translations[{{ $language->code }}][meta_keywords]" class="form-control">
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
                <i class="fas fa-save"></i> Create Page
            </button>
            <a href="{{ route('admin.cms.pages') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
// Initialize Quill editor
var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['link', 'image'],
            ['clean']
        ]
    }
});

// Initialize translation editors
var translationEditors = {};
@foreach($languages as $language)
    @if(!$language->is_default)
        translationEditors['{{ $language->code }}'] = new Quill('#editor-{{ $language->code }}', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link']
                ]
            }
        });
    @endif
@endforeach

// Update hidden textarea on content change and form submit
quill.on('text-change', function() {
    document.querySelector('#body-content').value = quill.root.innerHTML;
});

// Update translation textareas on content change
Object.keys(translationEditors).forEach(function(lang) {
    translationEditors[lang].on('text-change', function() {
        document.querySelector('#body-content-' + lang).value = translationEditors[lang].root.innerHTML;
    });
});

// Ensure content is synced on form submit
document.querySelector('form').addEventListener('submit', function() {
    document.querySelector('#body-content').value = quill.root.innerHTML;
    
    // Update translation textareas
    Object.keys(translationEditors).forEach(function(lang) {
        document.querySelector('#body-content-' + lang).value = translationEditors[lang].root.innerHTML;
    });
});

document.getElementById('pageTypeSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
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
                
                if (field.type === 'select') {
                    fieldHtml += `<select name="custom_fields[${field.name}]" class="form-control" ${field.required ? 'required' : ''}>`;
                    fieldHtml += '<option value="">Select...</option>';
                    field.options.forEach(option => {
                        fieldHtml += `<option value="${option}">${option}</option>`;
                    });
                    fieldHtml += '</select>';
                } else {
                    fieldHtml += `<input type="${field.type}" name="custom_fields[${field.name}]" class="form-control" ${field.required ? 'required' : ''}>`;
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
});
</script>
@endsection