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
            <!-- Basic Details Section -->
            <div class="card mb-3">
                <div class="card-header" data-toggle="collapse" data-target="#basicDetails" style="cursor: pointer;">
                    <h5 class="mb-0">
                        <i class="fas fa-chevron-down"></i> Basic Details
                    </h5>
                </div>
                <div id="basicDetails" class="collapse show">
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
                                    <div id="editor" style="height: 300px;"></div>
                                    <textarea name="body" id="body-content" style="display: none;" required>{{ $page->body }}</textarea>
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
                                    <label>Created By</label>
                                    <select name="created_by" class="form-control select2">
                                        @if($page->createdBy)
                                            <option value="{{ $page->created_by }}" selected data-text="{{ $page->createdBy->name }} ({{ $page->createdBy->email }})">{{ $page->createdBy->name }} ({{ $page->createdBy->email }})</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Slug</label>
                                    <input type="text" name="slug" class="form-control" value="{{ $page->slug }}">
                                    <small class="form-text text-muted">URL-friendly version of the title</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Featured Image</label>
                                    @if($page->image)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $page->image) }}" alt="Current image" class="img-thumbnail" style="max-height: 100px;">
                                            <div class="mt-2">
                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="delete_image" value="1" class="custom-control-input">
                                                    <span class="custom-control-label">Delete current image</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                    <small class="form-text text-muted">Upload new image or check delete to remove current image</small>
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Translations Section -->
            <div class="card mb-3">
                <div class="card-header" data-toggle="collapse" data-target="#translations" style="cursor: pointer;">
                    <h5 class="mb-0">
                        <i class="fas fa-chevron-down"></i> Translations
                    </h5>
                </div>
                <div id="translations" class="collapse">
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
                                                    <div id="editor-{{ $language->code }}" style="height: 200px;"></div>
                                                    <textarea name="translations[{{ $language->code }}][body]" id="body-content-{{ $language->code }}" style="display: none;">{{ $translation->body ?? '' }}</textarea>
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
                
            <!-- Custom Fields Section -->
            <div class="card mb-3" id="customFieldsSection" style="display: none;">
                <div class="card-header" data-toggle="collapse" data-target="#customFields" style="cursor: pointer;">
                    <h5 class="mb-0">
                        <i class="fas fa-chevron-down"></i> Custom Fields
                    </h5>
                </div>
                <div id="customFields" class="collapse show">
                    <div class="card-body">
                        <div class="row" id="customFieldsContainer"></div>
                    </div>
                </div>
            </div>

            <!-- Product Information Section -->
            <div class="card mb-3" id="productFieldsSection" style="display: none;">
                <div class="card-header" data-toggle="collapse" data-target="#productFieldsCollapse" style="cursor: pointer;">
                    <h5 class="mb-0">
                        <i class="fas fa-chevron-down"></i> Product Information
                    </h5>
                </div>
                <div id="productFieldsCollapse" class="collapse show">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Price *</label>
                                    <input type="number" name="product[price]" class="form-control" step="0.01" min="0" value="{{ $page->product->price ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Sale Price</label>
                                    <input type="number" name="product[sale_price]" class="form-control" step="0.01" min="0" value="{{ $page->product->sale_price ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>SKU</label>
                                    <input type="text" name="product[sku]" class="form-control" value="{{ $page->product->sku ?? '' }}">
                                </div>
                            </div>
                            
                            <div class="col-md-12"><hr><h6>Currency-Specific Pricing</h6></div>
                            @foreach($currencies as $currency)
                            @php
                                $currencyPrice = $page->product->currency_prices[$currency->code]['price'] ?? '';
                                $currencySalePrice = $page->product->currency_prices[$currency->code]['sale_price'] ?? '';
                            @endphp
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ $currency->name }} ({{ $currency->symbol }}) Price</label>
                                    <input type="number" name="product[currency_prices][{{ $currency->code }}][price]" class="form-control" step="0.01" min="0" value="{{ $currencyPrice }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ $currency->name }} Sale Price</label>
                                    <input type="number" name="product[currency_prices][{{ $currency->code }}][sale_price]" class="form-control" step="0.01" min="0" value="{{ $currencySalePrice }}">
                                </div>
                            </div>
                            @endforeach
                            
                            <div class="col-md-12"><hr></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Min Quantity</label>
                                    <input type="number" name="product[min_quantity]" class="form-control" value="{{ $page->product->min_quantity ?? 1 }}" min="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quantity Step</label>
                                    <input type="number" name="product[quantity_step]" class="form-control" value="{{ $page->product->quantity_step ?? 1 }}" min="1">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Quantity Unit</label>
                                    <input type="text" name="product[quantity_unit]" class="form-control" value="{{ $page->product->quantity_unit ?? 'item' }}" placeholder="e.g., item, kg, hour">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="product[stock_quantity]" class="form-control" value="{{ $page->product->stock_quantity ?? 0 }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" name="product[manage_stock]" value="1" {{ ($page->product->manage_stock ?? true) ? 'checked' : '' }} class="custom-control-input" id="manageStock">
                                        <label class="custom-control-label" for="manageStock">Manage Stock</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" name="product[is_featured]" value="1" {{ ($page->product->is_featured ?? false) ? 'checked' : '' }} class="custom-control-input" id="isFeatured">
                                        <label class="custom-control-label" for="isFeatured">Featured Product</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product Variants -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <hr>
                                <h6>Product Variants <button type="button" class="btn btn-sm btn-success" onclick="addVariant()"><i class="fas fa-plus"></i> Add Variant</button></h6>
                                <div id="variantsContainer">
                                    @if($page->product && $page->product->variants->count() > 0)
                                        @foreach($page->product->variants as $index => $variant)
                                        <div class="variant-item card mb-3" data-index="{{ $index }}">
                                            <div class="card-body">
                                                <button type="button" class="btn btn-sm btn-danger float-right" onclick="removeVariant(this)"><i class="fas fa-trash"></i></button>
                                                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Variant Name *</label>
                                                            <input type="text" name="variants[{{ $index }}][name]" class="form-control" value="{{ $variant->name }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Price *</label>
                                                            <input type="number" name="variants[{{ $index }}][price]" class="form-control" step="0.01" value="{{ $variant->price }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Sale Price</label>
                                                            <input type="number" name="variants[{{ $index }}][sale_price]" class="form-control" step="0.01" value="{{ $variant->sale_price }}">
                                                        </div>
                                                    </div>
                                                    @foreach($currencies as $currency)
                                                    @php
                                                        $vCurrencyPrice = $variant->currency_prices[$currency->code]['price'] ?? '';
                                                        $vCurrencySalePrice = $variant->currency_prices[$currency->code]['sale_price'] ?? '';
                                                    @endphp
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>{{ $currency->symbol }} Price</label>
                                                            <input type="number" name="variants[{{ $index }}][currency_prices][{{ $currency->code }}][price]" class="form-control" step="0.01" value="{{ $vCurrencyPrice }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>{{ $currency->symbol }} Sale</label>
                                                            <input type="number" name="variants[{{ $index }}][currency_prices][{{ $currency->code }}][sale_price]" class="form-control" step="0.01" value="{{ $vCurrencySalePrice }}">
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Stock</label>
                                                            <input type="number" name="variants[{{ $index }}][stock_quantity]" class="form-control" value="{{ $variant->stock_quantity }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Min Qty</label>
                                                            <input type="number" name="variants[{{ $index }}][min_quantity]" class="form-control" value="{{ $variant->min_quantity ?? 1 }}" min="1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Qty Step</label>
                                                            <input type="number" name="variants[{{ $index }}][quantity_step]" class="form-control" value="{{ $variant->quantity_step ?? 1 }}" min="1">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Qty Unit</label>
                                                            <input type="text" name="variants[{{ $index }}][quantity_unit]" class="form-control" value="{{ $variant->quantity_unit ?? 'min' }}" placeholder="min">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="custom-control custom-checkbox mt-4">
                                                            <input type="checkbox" name="variants[{{ $index }}][is_active]" value="1" {{ $variant->is_active ? 'checked' : '' }} class="custom-control-input" id="variantActive{{ $index }}">
                                                            <label class="custom-control-label" for="variantActive{{ $index }}">Active</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Preferences Section -->
            <div class="card mb-3">
                <div class="card-header" data-toggle="collapse" data-target="#pagePreferences" style="cursor: pointer;">
                    <h5 class="mb-0">
                        <i class="fas fa-chevron-down"></i> Page Preferences
                    </h5>
                </div>
                <div id="pagePreferences" class="collapse show">
                    <div class="card-body">
                        <div class="row">
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
                        </div>
                    </div>
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

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
const existingCustomFields = @json($page->custom_fields ?? []);

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

// Set initial content
quill.root.innerHTML = {!! json_encode($page->body) !!};

// Initialize translation editors
var translationEditors = {};
@foreach($languages as $language)
    @if($language->code !== $page->language_code)
        @php
            $translation = $page->translations->where('language_code', $language->code)->first();
        @endphp
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
        translationEditors['{{ $language->code }}'].root.innerHTML = {!! json_encode($translation->body ?? '') !!};
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

function loadCustomFields() {
    const selectedOption = document.getElementById('pageTypeSelect').options[document.getElementById('pageTypeSelect').selectedIndex];
    const config = selectedOption.dataset.config;
    const customFieldsDiv = document.getElementById('customFields');
    const container = document.getElementById('customFieldsContainer');
    const productFieldsSection = document.getElementById('productFieldsSection');
    const priceInput = document.querySelector('input[name="product[price]"]');
    
    if (config) {
        const fieldsConfig = JSON.parse(config);
        container.innerHTML = '';
        
        if (fieldsConfig.custom_fields && fieldsConfig.custom_fields.length > 0) {
            document.getElementById('customFieldsSection').style.display = 'block';
            
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
            document.getElementById('customFieldsSection').style.display = 'none';
        }
    } else {
        document.getElementById('customFieldsSection').style.display = 'none';
    }
    
    // Check if page type has product fields
    const pageTypeId = document.getElementById('pageTypeSelect').value;
    if (pageTypeId) {
        fetch(`/admin/cms/page-types/${pageTypeId}/check-product-fields`)
            .then(response => response.json())
            .then(data => {
                if (data.has_product_fields) {
                    productFieldsSection.style.display = 'block';
                    if (priceInput) priceInput.required = true;
                } else {
                    productFieldsSection.style.display = 'none';
                    if (priceInput) priceInput.required = false;
                }
            });
    } else {
        productFieldsSection.style.display = 'none';
        if (priceInput) priceInput.required = false;
    }
}

// Load fields on page load
document.addEventListener('DOMContentLoaded', loadCustomFields);

// Load fields when page type changes
document.getElementById('pageTypeSelect').addEventListener('change', loadCustomFields);

let variantIndex = {{ $page->product && $page->product->variants->count() > 0 ? $page->product->variants->count() : 0 }};

function addVariant() {
    const container = document.getElementById('variantsContainer');
    const currencies = @json($currencies);
    
    // Get values from last variant to copy
    const lastVariant = container.querySelector('.variant-item:last-child');
    let copyValues = {
        price: '',
        sale_price: '',
        stock_quantity: '0',
        min_quantity: '1',
        quantity_step: '1',
        quantity_unit: 'min',
        currency_prices: {}
    };
    
    if (lastVariant) {
        const index = lastVariant.dataset.index;
        copyValues.price = lastVariant.querySelector(`input[name="variants[${index}][price]"]`)?.value || '';
        copyValues.sale_price = lastVariant.querySelector(`input[name="variants[${index}][sale_price]"]`)?.value || '';
        copyValues.stock_quantity = lastVariant.querySelector(`input[name="variants[${index}][stock_quantity]"]`)?.value || '0';
        copyValues.min_quantity = lastVariant.querySelector(`input[name="variants[${index}][min_quantity]"]`)?.value || '1';
        copyValues.quantity_step = lastVariant.querySelector(`input[name="variants[${index}][quantity_step]"]`)?.value || '1';
        copyValues.quantity_unit = lastVariant.querySelector(`input[name="variants[${index}][quantity_unit]"]`)?.value || 'min';
        
        currencies.forEach(currency => {
            const priceInput = lastVariant.querySelector(`input[name="variants[${index}][currency_prices][${currency.code}][price]"]`);
            const salePriceInput = lastVariant.querySelector(`input[name="variants[${index}][currency_prices][${currency.code}][sale_price]"]`);
            copyValues.currency_prices[currency.code] = {
                price: priceInput?.value || '',
                sale_price: salePriceInput?.value || ''
            };
        });
    }
    
    let currencyFields = '';
    currencies.forEach(currency => {
        const currPrice = copyValues.currency_prices[currency.code]?.price || '';
        const currSalePrice = copyValues.currency_prices[currency.code]?.sale_price || '';
        currencyFields += `
            <div class="col-md-2">
                <div class="form-group">
                    <label>${currency.symbol} Price</label>
                    <input type="number" name="variants[${variantIndex}][currency_prices][${currency.code}][price]" class="form-control" step="0.01" value="${currPrice}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>${currency.symbol} Sale</label>
                    <input type="number" name="variants[${variantIndex}][currency_prices][${currency.code}][sale_price]" class="form-control" step="0.01" value="${currSalePrice}">
                </div>
            </div>
        `;
    });
    
    const variantHtml = `
        <div class="variant-item card mb-3" data-index="${variantIndex}">
            <div class="card-body">
                <button type="button" class="btn btn-sm btn-danger float-right" onclick="removeVariant(this)"><i class="fas fa-trash"></i></button>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Variant Name *</label>
                            <input type="text" name="variants[${variantIndex}][name]" class="form-control" placeholder="e.g., Chat, Call" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Price *</label>
                            <input type="number" name="variants[${variantIndex}][price]" class="form-control" step="0.01" value="${copyValues.price}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Sale Price</label>
                            <input type="number" name="variants[${variantIndex}][sale_price]" class="form-control" step="0.01" value="${copyValues.sale_price}">
                        </div>
                    </div>
                    ${currencyFields}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Stock</label>
                            <input type="number" name="variants[${variantIndex}][stock_quantity]" class="form-control" value="${copyValues.stock_quantity}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Min Qty</label>
                            <input type="number" name="variants[${variantIndex}][min_quantity]" class="form-control" value="${copyValues.min_quantity}" min="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Qty Step</label>
                            <input type="number" name="variants[${variantIndex}][quantity_step]" class="form-control" value="${copyValues.quantity_step}" min="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Qty Unit</label>
                            <input type="text" name="variants[${variantIndex}][quantity_unit]" class="form-control" value="${copyValues.quantity_unit}" placeholder="min">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="custom-control custom-checkbox mt-4">
                            <input type="checkbox" name="variants[${variantIndex}][is_active]" value="1" checked class="custom-control-input" id="variantActive${variantIndex}">
                            <label class="custom-control-label" for="variantActive${variantIndex}">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', variantHtml);
    variantIndex++;
}

function removeVariant(button) {
    if (confirm('Are you sure you want to remove this variant?')) {
        button.closest('.variant-item').remove();
    }
}
</script>
@endsection

@push('scripts')
<script>
// Initialize Select2 with AJAX for user search
$(document).ready(function() {
    $('.select2').select2({
        ajax: {
            url: '{{ route('api.users.search') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            },
            cache: true
        },
        placeholder: 'Search user by name or email...',
        allowClear: true,
        minimumInputLength: 2,
        templateResult: function(user) {
            if (user.loading) return user.text;
            return $('<span>' + user.text + '</span>');
        },
        templateSelection: function(user) {
            return user.text;
        }
    });
});
</script>
@endpush