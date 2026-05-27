@extends('admin.layouts.app')

@section('title', 'Edit Service')
@section('page-title', 'Edit Service')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
<li class="breadcrumb-item active">Edit: {{ $service->name }}</li>
@endsection

@section('content')
<form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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

    @include('admin.services._form')
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
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

    // ─── Tier Management ───────────────────────────────────────────

    // Toggle tier management section visibility
    $('#has_tiers').on('change', function() {
        if ($(this).is(':checked')) {
            $('#tier-management-section').slideDown();
        } else {
            $('#tier-management-section').slideUp();
        }
    });

    // Add New Tier
    $('#add-tier-btn').on('click', function() {
        var name = $('#new_tier_name').val().trim();
        var description = $('#new_tier_description').val().trim();
        var price = $('#new_tier_price').val();
        var isActive = $('#new_tier_is_active').is(':checked') ? 1 : 0;

        if (!name) {
            alert('Tier name is required.');
            $('#new_tier_name').focus();
            return;
        }
        if (!price || parseFloat(price) < 0) {
            alert('A valid price is required.');
            $('#new_tier_price').focus();
            return;
        }

        // Submit via a dynamically created form
        var form = $('<form>', {
            method: 'POST',
            action: '{{ isset($service) && $service->exists ? route("admin.services.tiers.store", $service->id) : "#" }}'
        });
        form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
        form.append($('<input>', { type: 'hidden', name: 'name', value: name }));
        form.append($('<input>', { type: 'hidden', name: 'description', value: description }));
        form.append($('<input>', { type: 'hidden', name: 'price', value: price }));
        if (isActive) {
            form.append($('<input>', { type: 'hidden', name: 'is_active', value: '1' }));
        }

        $('body').append(form);
        form.submit();
    });

    // Edit Tier - Open Modal
    $(document).on('click', '.edit-tier-btn', function() {
        var tierId = $(this).data('tier-id');
        var tierName = $(this).data('tier-name');
        var tierDescription = $(this).data('tier-description');
        var tierPrice = $(this).data('tier-price');
        var tierIsActive = $(this).data('tier-is-active');

        $('#edit_tier_id').val(tierId);
        $('#edit_tier_name').val(tierName);
        $('#edit_tier_description').val(tierDescription);
        $('#edit_tier_price').val(tierPrice);
        $('#edit_tier_is_active').prop('checked', tierIsActive == '1');

        $('#editTierModal').modal('show');
    });

    // Save Tier Edit
    $('#save-tier-btn').on('click', function() {
        var tierId = $('#edit_tier_id').val();
        var name = $('#edit_tier_name').val().trim();
        var description = $('#edit_tier_description').val().trim();
        var price = $('#edit_tier_price').val();
        var isActive = $('#edit_tier_is_active').is(':checked') ? 1 : 0;

        if (!name) {
            alert('Tier name is required.');
            $('#edit_tier_name').focus();
            return;
        }
        if (!price || parseFloat(price) < 0) {
            alert('A valid price is required.');
            $('#edit_tier_price').focus();
            return;
        }

        var serviceId = '{{ isset($service) && $service->exists ? $service->id : "" }}';
        var actionUrl = '/admin/services/' + serviceId + '/tiers/' + tierId;

        var form = $('<form>', {
            method: 'POST',
            action: actionUrl
        });
        form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
        form.append($('<input>', { type: 'hidden', name: '_method', value: 'PUT' }));
        form.append($('<input>', { type: 'hidden', name: 'name', value: name }));
        form.append($('<input>', { type: 'hidden', name: 'description', value: description }));
        form.append($('<input>', { type: 'hidden', name: 'price', value: price }));
        if (isActive) {
            form.append($('<input>', { type: 'hidden', name: 'is_active', value: '1' }));
        }

        $('body').append(form);
        form.submit();
    });

    // Delete Tier - Open Confirmation Modal
    $(document).on('click', '.delete-tier-btn', function() {
        var tierId = $(this).data('tier-id');
        var tierName = $(this).data('tier-name');

        $('#delete_tier_name').text(tierName);
        $('#confirm-delete-tier-btn').data('tier-id', tierId);

        $('#deleteTierModal').modal('show');
    });

    // Confirm Delete Tier
    $('#confirm-delete-tier-btn').on('click', function() {
        var tierId = $(this).data('tier-id');
        var serviceId = '{{ isset($service) && $service->exists ? $service->id : "" }}';
        var actionUrl = '/admin/services/' + serviceId + '/tiers/' + tierId;

        var form = $('<form>', {
            method: 'POST',
            action: actionUrl
        });
        form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
        form.append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }));

        $('body').append(form);
        form.submit();
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

    // ─── Form Field Builder ────────────────────────────────────────

    var serviceId = '{{ $service->id }}';
    var fieldTypes = ['select', 'radio', 'checkbox'];

    // Show/hide options builder based on field type
    $('#field_type').on('change', function() {
        var selectedType = $(this).val();
        if (fieldTypes.indexOf(selectedType) !== -1) {
            $('#options-builder-section').slideDown();
            if ($('#options-container .option-row').length === 0) {
                addOptionRow();
            }
        } else {
            $('#options-builder-section').slideUp();
        }
    });

    // Add option row
    $('#add-option-btn').on('click', function() {
        addOptionRow();
    });

    function addOptionRow(key, label) {
        key = key || '';
        label = label || '';
        var html = '<div class="input-group mb-2 option-row">' +
            '<input type="text" class="form-control form-control-sm option-key" placeholder="Value (key)" value="' + escapeHtml(key) + '">' +
            '<input type="text" class="form-control form-control-sm option-label" placeholder="Display Label" value="' + escapeHtml(label) + '">' +
            '<div class="input-group-append">' +
            '<button type="button" class="btn btn-sm btn-danger remove-option-btn"><i class="fas fa-times"></i></button>' +
            '</div></div>';
        $('#options-container').append(html);
    }

    // Remove option row
    $(document).on('click', '.remove-option-btn', function() {
        $(this).closest('.option-row').remove();
    });

    // Reset field modal
    function resetFieldModal() {
        $('#edit_field_id').val('');
        $('#field_name').val('');
        $('#field_label').val('');
        $('#field_type').val('text');
        $('#field_placeholder').val('');
        $('#field_validation_rules').val('');
        $('#field_is_required').prop('checked', false);
        $('#field_section').val('default');
        $('#field_section_label').val('');
        $('#field_help_text').val('');
        $('#options-container').empty();
        $('#options-builder-section').hide();
        $('#fieldModalLabel').text('Add Form Field');
    }

    // Open Add Field modal
    $('#add-field-btn').on('click', function() {
        resetFieldModal();
    });

    // Open Edit Field modal
    $(document).on('click', '.edit-field-btn', function() {
        resetFieldModal();

        var fieldId = $(this).data('field-id');
        var fieldName = $(this).data('field-name');
        var fieldLabel = $(this).data('field-label');
        var fieldType = $(this).data('field-type');
        var fieldPlaceholder = $(this).data('field-placeholder');
        var fieldValidation = $(this).data('field-validation');
        var fieldRequired = $(this).data('field-required');
        var fieldSection = $(this).data('field-section');
        var fieldSectionLabel = $(this).data('field-section-label');
        var fieldHelpText = $(this).data('field-help-text');
        var fieldOptions = $(this).data('field-options');

        $('#edit_field_id').val(fieldId);
        $('#field_name').val(fieldName);
        $('#field_label').val(fieldLabel);
        $('#field_type').val(fieldType);
        $('#field_placeholder').val(fieldPlaceholder);
        $('#field_validation_rules').val(fieldValidation);
        $('#field_is_required').prop('checked', fieldRequired == '1');
        $('#field_section').val(fieldSection || 'default');
        $('#field_section_label').val(fieldSectionLabel);
        $('#field_help_text').val(fieldHelpText);
        $('#fieldModalLabel').text('Edit Form Field');

        // Handle options for select/radio/checkbox
        if (fieldTypes.indexOf(fieldType) !== -1) {
            $('#options-builder-section').show();
            if (fieldOptions && typeof fieldOptions === 'object') {
                var options = Array.isArray(fieldOptions) ? fieldOptions : [];
                if (!Array.isArray(fieldOptions) && typeof fieldOptions === 'string') {
                    try { options = JSON.parse(fieldOptions); } catch(e) { options = []; }
                }
                options.forEach(function(opt) {
                    addOptionRow(opt.value || '', opt.label || '');
                });
            }
            if ($('#options-container .option-row').length === 0) {
                addOptionRow();
            }
        }

        $('#fieldModal').modal('show');
    });

    // Save Field (Create or Update)
    $('#save-field-btn').on('click', function() {
        var fieldId = $('#edit_field_id').val();
        var fieldName = $('#field_name').val().trim();
        var fieldLabel = $('#field_label').val().trim();
        var fieldType = $('#field_type').val();
        var placeholder = $('#field_placeholder').val().trim();
        var validationRules = $('#field_validation_rules').val().trim();
        var isRequired = $('#field_is_required').is(':checked') ? 1 : 0;
        var section = $('#field_section').val().trim() || 'default';
        var sectionLabel = $('#field_section_label').val().trim();
        var helpText = $('#field_help_text').val().trim();

        // Validation
        if (!fieldName) {
            alert('Field name is required.');
            $('#field_name').focus();
            return;
        }
        if (!fieldLabel) {
            alert('Field label is required.');
            $('#field_label').focus();
            return;
        }

        // Collect options if applicable
        var options = [];
        if (fieldTypes.indexOf(fieldType) !== -1) {
            $('#options-container .option-row').each(function() {
                var key = $(this).find('.option-key').val().trim();
                var label = $(this).find('.option-label').val().trim();
                if (key || label) {
                    options.push({ value: key, label: label || key });
                }
            });
        }

        var data = {
            _token: '{{ csrf_token() }}',
            field_name: fieldName,
            field_label: fieldLabel,
            field_type: fieldType,
            placeholder: placeholder,
            validation_rules: validationRules,
            is_required: isRequired,
            section: section,
            section_label: sectionLabel,
            help_text: helpText,
            options: options.length > 0 ? JSON.stringify(options) : null
        };

        var url, method;
        if (fieldId) {
            url = '/admin/services/' + serviceId + '/fields/' + fieldId;
            data._method = 'PUT';
        } else {
            url = '/admin/services/' + serviceId + '/fields';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(response) {
                $('#fieldModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var messages = [];
                    for (var key in errors) {
                        messages.push(errors[key][0]);
                    }
                    alert('Validation Error:\n' + messages.join('\n'));
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });

    // Delete Field - Open Confirmation Modal
    $(document).on('click', '.delete-field-btn', function() {
        var fieldId = $(this).data('field-id');
        var fieldName = $(this).data('field-name');

        $('#delete_field_name').text(fieldName);
        $('#confirm-delete-field-btn').data('field-id', fieldId);

        $('#deleteFieldModal').modal('show');
    });

    // Confirm Delete Field
    $('#confirm-delete-field-btn').on('click', function() {
        var fieldId = $(this).data('field-id');

        $.ajax({
            url: '/admin/services/' + serviceId + '/fields/' + fieldId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function(response) {
                $('#deleteFieldModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('An error occurred while deleting the field.');
            }
        });
    });

    // Move Field Up
    $(document).on('click', '.move-field-up', function() {
        var fieldId = $(this).data('field-id');
        var row = $(this).closest('tr');
        var prevRow = row.prev('tr');

        if (prevRow.length) {
            row.insertBefore(prevRow);
            saveFieldOrder();
        }
    });

    // Move Field Down
    $(document).on('click', '.move-field-down', function() {
        var fieldId = $(this).data('field-id');
        var row = $(this).closest('tr');
        var nextRow = row.next('tr');

        if (nextRow.length) {
            row.insertAfter(nextRow);
            saveFieldOrder();
        }
    });

    // Save field order via AJAX
    function saveFieldOrder() {
        var orderedIds = [];
        $('#fields-table tbody tr').each(function() {
            orderedIds.push($(this).data('field-id'));
        });

        $.ajax({
            url: '/admin/services/' + serviceId + '/fields/reorder',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                field_ids: orderedIds
            },
            success: function(response) {
                // Silently succeed
            },
            error: function(xhr) {
                alert('Failed to save field order. Please try again.');
            }
        });
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
});
</script>
@endpush
