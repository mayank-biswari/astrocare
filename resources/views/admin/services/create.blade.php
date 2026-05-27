@extends('admin.layouts.app')

@section('title', 'Create Service')
@section('page-title', 'Create Service')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
<li class="breadcrumb-item active">Create Service</li>
@endsection

@section('content')
<form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Validation Error</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php $service = new \App\Models\Service(); @endphp
    @include('admin.services._form')
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from name (create only)
    var slugManuallyEdited = false;
    $('#slug').on('input', function() {
        slugManuallyEdited = $(this).val().length > 0;
    });

    $('#name').on('input', function() {
        if (!slugManuallyEdited) {
            var name = $(this).val();
            var slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_]+/g, '-')
                .replace(/^-+|-+$/g, '');
            $('#slug').val(slug);
        }
    });

    // Image upload preview
    $('#image').on('change', function() {
        var file = this.files[0];
        var label = $(this).next('.custom-file-label');
        label.text(file ? file.name : 'Choose file');

        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').html(
                    '<img src="' + e.target.result + '" alt="Preview" class="img-thumbnail" style="max-height: 150px;">'
                );
            };
            reader.readAsDataURL(file);
        }
    });

    // Dynamic Features - Add
    $('#add-feature-btn').on('click', function() {
        var html = '<div class="input-group mb-2 feature-item">' +
            '<input type="text" class="form-control" name="features[]" placeholder="Feature description">' +
            '<div class="input-group-append">' +
            '<button type="button" class="btn btn-danger remove-feature-btn"><i class="fas fa-times"></i></button>' +
            '</div></div>';
        $('#features-container').append(html);
    });

    // Dynamic Features - Remove
    $(document).on('click', '.remove-feature-btn', function() {
        var container = $('#features-container');
        if (container.find('.feature-item').length > 1) {
            $(this).closest('.feature-item').remove();
        } else {
            $(this).closest('.feature-item').find('input').val('');
        }
    });

    // Dynamic FAQ - Add
    var faqIndex = $('#faq-container .faq-item').length;
    $('#add-faq-btn').on('click', function() {
        var html = '<div class="card mb-2 faq-item"><div class="card-body p-3">' +
            '<div class="d-flex justify-content-between align-items-start mb-2">' +
            '<strong>FAQ #<span class="faq-number">' + (faqIndex + 1) + '</span></strong>' +
            '<button type="button" class="btn btn-sm btn-danger remove-faq-btn"><i class="fas fa-times"></i></button>' +
            '</div>' +
            '<div class="form-group mb-2">' +
            '<input type="text" class="form-control" name="faq[' + faqIndex + '][question]" placeholder="Question">' +
            '</div>' +
            '<div class="form-group mb-0">' +
            '<textarea class="form-control" name="faq[' + faqIndex + '][answer]" rows="2" placeholder="Answer"></textarea>' +
            '</div></div></div>';
        $('#faq-container').append(html);
        faqIndex++;
        updateFaqNumbers();
    });

    // Dynamic FAQ - Remove
    $(document).on('click', '.remove-faq-btn', function() {
        var container = $('#faq-container');
        if (container.find('.faq-item').length > 1) {
            $(this).closest('.faq-item').remove();
            updateFaqNumbers();
        } else {
            $(this).closest('.faq-item').find('input, textarea').val('');
        }
    });

    function updateFaqNumbers() {
        $('#faq-container .faq-item').each(function(index) {
            $(this).find('.faq-number').text(index + 1);
        });
    }

    // Toggle tier management section visibility
    $('#has_tiers').on('change', function() {
        if ($(this).is(':checked')) {
            $('#tier-management-section').slideDown();
        } else {
            $('#tier-management-section').slideUp();
        }
    });

    // Show validation errors tab
    @if($errors->any())
        var errorFields = {!! json_encode($errors->keys()) !!};
        var tabMapping = {
            'name': 'general', 'slug': 'general', 'type': 'general',
            'short_description': 'general', 'description': 'general',
            'image': 'general', 'icon': 'general',
            'base_price': 'pricing', 'currency': 'pricing', 'has_tiers': 'pricing',
            'meta_title': 'seo', 'meta_description': 'seo', 'meta_keywords': 'seo',
            'requires_auth': 'settings', 'requires_captcha': 'settings',
            'requires_shipping': 'settings', 'delivery_time': 'settings',
            'is_active': 'settings', 'sort_order': 'settings'
        };
        for (var i = 0; i < errorFields.length; i++) {
            var field = errorFields[i].split('.')[0];
            if (tabMapping[field]) {
                $('#' + tabMapping[field] + '-tab').tab('show');
                break;
            }
        }
    @endif
});
</script>
@endpush
