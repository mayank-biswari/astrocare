@extends('admin.layouts.app')

@section('title', 'Create Dynamic Page')

@section('content')
<div class="content-fluid">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Dynamic Page</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('admin.dynamic-pages.store') }}">
                @csrf

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Page Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>System Name *</label>
                                    <input type="text" name="system_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Title *</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>URL *</label>
                                    <input type="text" name="url" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input class="custom-control-input" type="checkbox" id="is_published" name="is_published">
                                        <label for="is_published" class="custom-control-label">Published</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Page Sections</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-success btn-sm" id="add-section">
                                <i class="fas fa-plus"></i> Add Section
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sections-container"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">SEO Settings</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control" placeholder="Page meta title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control" placeholder="keyword1, keyword2, keyword3">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" placeholder="Page meta description"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">External Assets</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>External CSS Files</label>
                                    <div id="css-files-container">
                                        <div class="input-group mb-2">
                                            <input type="url" name="external_css[]" class="form-control" placeholder="https://example.com/style.css">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success add-css-file"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>External JS Files</label>
                                    <div id="js-files-container">
                                        <div class="input-group mb-2">
                                            <input type="url" name="external_js[]" class="form-control" placeholder="https://example.com/script.js">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-success add-js-file"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Custom Styles & Scripts</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Custom CSS</label>
                                    <textarea name="custom_css" class="form-control" rows="10" placeholder="/* Custom CSS */"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Custom JavaScript</label>
                                    <textarea name="custom_js" class="form-control" rows="10" placeholder="// Custom JavaScript"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary">Create Page</button>
                        <a href="{{ route('admin.dynamic-pages.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@push('scripts')
<script>
let sectionIndex = 0;

$(function() {
    $('#sections-container').sortable({
        handle: '.card-header',
        update: function() {
            reindexSections();
        }
    });
});

document.getElementById('add-section').addEventListener('click', function() {
    const container = document.getElementById('sections-container');
    const sectionHtml = createSectionHtml(sectionIndex);
    container.insertAdjacentHTML('beforeend', sectionHtml);
    sectionIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-section') || e.target.closest('.remove-section')) {
        e.target.closest('.section-item').remove();
    }

    if (e.target.classList.contains('add-section-before')) {
        addSectionAt(e.target.closest('.section-item'), 'before');
    }

    if (e.target.classList.contains('add-section-after')) {
        addSectionAt(e.target.closest('.section-item'), 'after');
    }
});

function addSectionAt(targetSection, position) {
    const sectionHtml = createSectionHtml(sectionIndex);
    if (position === 'before') {
        targetSection.insertAdjacentHTML('beforebegin', sectionHtml);
    } else {
        targetSection.insertAdjacentHTML('afterend', sectionHtml);
    }
    sectionIndex++;
    reindexSections();
}

function createSectionHtml(index) {
    return `
        <div class="section-item border mb-3" data-index="${index}">
            <div class="card-header bg-light" style="cursor: move;" data-toggle="collapse" data-target="#section-${index}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-grip-vertical mr-2"></i>
                        <span class="section-title">Section ${index + 1}</span>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <div id="section-${index}" class="collapse">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Section Name</label>
                                <input type="text" name="sections[${index}][name]" class="form-control section-name" placeholder="Enter section name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Custom Template (Optional)</label>
                                <textarea name="sections[${index}][custom_template]" class="form-control" rows="5" placeholder="Enter HTML with @{{title@}}, @{{body@}}, @{{image@}}, @{{slug@}}, @{{url@}} OR enter view:filename to use a blade template"></textarea>
                                <small class="text-muted">HTML placeholders: @{{title@}}, @{{body@}}, @{{image@}}, @{{slug@}}, @{{url@}} OR use view:filename (e.g., view:testimonial) to load from resources/views/dynamic-pages/custom-templates/</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Section Classes</label>
                                <input type="text" name="sections[${index}][section_classes]" class="form-control" placeholder="e.g., container mx-auto px-4 py-8">
                                <small class="text-muted">Add custom CSS classes for this section wrapper</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Section Type</label>
                        <select name="sections[${index}][type]" class="form-control section-type">
                            <option value="list">List</option>
                            <option value="html">Custom HTML</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group list-field">
                        <label>Select List</label>
                        <select name="sections[${index}][list_id]" class="form-control">
                            <option value="">Select List</option>
                            @foreach($lists as $list)
                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group list-field">
                        <label>Layout</label>
                        <select name="sections[${index}][layout]" class="form-control layout-select">
                            <option value="grid">Grid</option>
                            <option value="list">List</option>
                            <option value="slider">Slider</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 list-field">
                    <div class="form-group">
                        <label>Grid Classes</label>
                        <input type="text" name="sections[${index}][grid_classes]" class="form-control" value="grid md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8" placeholder="grid md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
                        <small class="text-muted">For grid layout only</small>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group list-field">
                        <label>Columns/Limit</label>
                        <input type="number" name="sections[${index}][limit]" class="form-control" value="12" min="1">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group list-field">
                        <div class="custom-control custom-checkbox mt-4">
                            <input class="custom-control-input" type="checkbox" id="show_pagination_${index}" name="sections[${index}][show_pagination]">
                            <label for="show_pagination_${index}" class="custom-control-label">Pagination</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group list-field">
                        <label>Per Page</label>
                        <select name="sections[${index}][items_per_page]" class="form-control">
                            <option value="6">6</option>
                            <option value="9">9</option>
                            <option value="12" selected>12</option>
                            <option value="18">18</option>
                            <option value="24">24</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm mt-4 remove-section">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="slider-options" style="display: none;">
                <div class="row">
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="show_dots_${index}" name="sections[${index}][show_dots]" checked>
                            <label for="show_dots_${index}" class="custom-control-label">Show Dots</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="show_arrows_${index}" name="sections[${index}][show_arrows]" checked>
                            <label for="show_arrows_${index}" class="custom-control-label">Show Arrows</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="auto_rotate_${index}" name="sections[${index}][auto_rotate]" checked>
                            <label for="auto_rotate_${index}" class="custom-control-label">Auto Rotate</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Duration (ms)</label>
                            <input type="number" name="sections[${index}][duration]" class="form-control" value="3000" min="1000">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slides to Show</label>
                            <input type="number" name="sections[${index}][slides_to_show]" class="form-control" value="3" min="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slides to Scroll</label>
                            <input type="number" name="sections[${index}][slides_to_scroll]" class="form-control" value="1" min="1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="html-field" style="display: none;">
                <div class="form-group">
                    <label>Custom HTML</label>
                    <textarea name="sections[${index}][html]" class="form-control" rows="5"></textarea>
                </div>
            </div>
            <div class="list-options list-field" style="display: block;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="make_clickable_list_${index}" name="sections[${index}][make_clickable]" checked>
                            <label for="make_clickable_list_${index}" class="custom-control-label">Make Items Clickable</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="show_read_more_list_${index}" name="sections[${index}][show_read_more]" checked>
                            <label for="show_read_more_list_${index}" class="custom-control-label">Show Read More Button</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Read More Text</label>
                            <input type="text" name="sections[${index}][read_more_text]" class="form-control" value="Read More">
                        </div>
                    </div>
                </div>
            </div>
                    <div class="row mt-2">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-outline-primary btn-sm add-section-before">
                                <i class="fas fa-plus"></i> Add Before
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm ml-2 add-section-after">
                                <i class="fas fa-plus"></i> Add After
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function reindexSections() {
    const sections = document.querySelectorAll('.section-item');
    sections.forEach((section, index) => {
        section.setAttribute('data-index', index);
        section.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.name) {
                field.name = field.name.replace(/\[\d+\]/, `[${index}]`);
            }
        });
    });
}

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('section-name')) {
        const section = e.target.closest('.section-item');
        const title = section.querySelector('.section-title');
        const index = parseInt(section.getAttribute('data-index'));
        title.textContent = e.target.value || `Section ${index + 1}`;
    }
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('section-type')) {
        const section = e.target.closest('.section-item');
        const listFields = section.querySelectorAll('.list-field');
        const htmlField = section.querySelector('.html-field');
        const sliderOptions = section.querySelector('.slider-options');

        if (e.target.value === 'html') {
            listFields.forEach(field => field.style.display = 'none');
            htmlField.style.display = 'block';
            sliderOptions.style.display = 'none';
            const listOptions = section.querySelector('.list-options');
            if (listOptions) listOptions.style.display = 'none';
        } else {
            listFields.forEach(field => field.style.display = 'block');
            htmlField.style.display = 'none';
            const listOptions = section.querySelector('.list-options');
            if (listOptions) listOptions.style.display = 'block';
            const layoutSelect = section.querySelector('.layout-select');
            if (layoutSelect && layoutSelect.value === 'slider') {
                sliderOptions.style.display = 'block';
            } else {
                sliderOptions.style.display = 'none';
            }
        }
    }

    if (e.target.classList.contains('layout-select')) {
        const section = e.target.closest('.section-item');
        const sliderOptions = section.querySelector('.slider-options');
        const sectionType = section.querySelector('.section-type');

        if (e.target.value === 'slider' && sectionType.value === 'list') {
            sliderOptions.style.display = 'block';
        } else {
            sliderOptions.style.display = 'none';
        }
    }
});

// External assets handlers
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-css-file')) {
        const container = document.getElementById('css-files-container');
        const newField = `
            <div class="input-group mb-2">
                <input type="url" name="external_css[]" class="form-control" placeholder="https://example.com/style.css">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-css-file"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newField);
    }

    if (e.target.classList.contains('add-js-file')) {
        const container = document.getElementById('js-files-container');
        const newField = `
            <div class="input-group mb-2">
                <input type="url" name="external_js[]" class="form-control" placeholder="https://example.com/script.js">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-js-file"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newField);
    }

    if (e.target.classList.contains('remove-css-file') || e.target.classList.contains('remove-js-file')) {
        e.target.closest('.input-group').remove();
    }
});
</script>
@endpush
@endsection
