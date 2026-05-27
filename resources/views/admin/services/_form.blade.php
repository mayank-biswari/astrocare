{{-- Shared form partial for service create/edit --}}
<div class="card">
    <div class="card-header p-0">
        <ul class="nav nav-tabs" id="service-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                    <i class="fas fa-info-circle"></i> General
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pricing-tab" data-toggle="tab" href="#pricing" role="tab">
                    <i class="fas fa-rupee-sign"></i> Pricing
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="features-tab" data-toggle="tab" href="#features" role="tab">
                    <i class="fas fa-list"></i> Features & FAQ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab">
                    <i class="fas fa-search"></i> SEO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings" role="tab">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="form-fields-tab" data-toggle="tab" href="#form-fields" role="tab">
                    <i class="fas fa-wpforms"></i> Form Fields
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="service-tabs-content">

            {{-- General Tab --}}
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="name">Service Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name"
                                   value="{{ old('name', $service->name ?? '') }}"
                                   placeholder="Enter service name" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            @if(isset($service) && $service->exists)
                                <input type="text" class="form-control" id="slug"
                                       value="{{ $service->slug }}" readonly disabled>
                                <small class="form-text text-muted">Slug cannot be changed after creation.</small>
                            @else
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                       id="slug" name="slug"
                                       value="{{ old('slug') }}"
                                       placeholder="auto-generated-from-name">
                                <small class="form-text text-muted">Leave blank to auto-generate from name.</small>
                                @error('slug')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">Service Type <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Type</option>
                                @php
                                    $types = ['question' => 'Question', 'prediction' => 'Prediction', 'kundli' => 'Kundli', 'consultation' => 'Consultation', 'pooja' => 'Pooja', 'matching' => 'Matching', 'custom' => 'Custom'];
                                @endphp
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $service->type ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="icon">Icon Class</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                   id="icon" name="icon"
                                   value="{{ old('icon', $service->icon ?? '') }}"
                                   placeholder="e.g. fas fa-star">
                            @error('icon')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Font Awesome icon class.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="short_description">Short Description</label>
                    <input type="text" class="form-control @error('short_description') is-invalid @enderror"
                           id="short_description" name="short_description"
                           value="{{ old('short_description', $service->short_description ?? '') }}"
                           placeholder="Brief description for listings">
                    @error('short_description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Full Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="6"
                              placeholder="Detailed service description (HTML supported)">{{ old('description', $service->description ?? '') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Supports HTML content.</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="image">Service Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('image') is-invalid @enderror"
                                       id="image" name="image" accept="image/*">
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                            @error('image')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Accepted: jpeg, png, jpg, gif, webp. Max: 2MB.</small>
                            <div id="image-preview" class="mt-2">
                                @if(isset($service) && $service->image)
                                    <img src="{{ asset('storage/' . $service->image) }}" alt="Current image" class="img-thumbnail" style="max-height: 150px;">
                                    <br><small class="text-muted">Current image. Upload a new one to replace.</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pricing Tab --}}
            <div class="tab-pane fade" id="pricing" role="tabpanel">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="base_price">Base Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₹</span>
                                </div>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror"
                                       id="base_price" name="base_price"
                                       value="{{ old('base_price', $service->base_price ?? '0') }}"
                                       step="0.01" min="0" required>
                            </div>
                            @error('base_price')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <input type="text" class="form-control @error('currency') is-invalid @enderror"
                                   id="currency" name="currency"
                                   value="{{ old('currency', $service->currency ?? 'INR') }}"
                                   placeholder="INR">
                            @error('currency')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tiered Pricing</label>
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" class="custom-control-input" id="has_tiers" name="has_tiers" value="1"
                                       {{ old('has_tiers', $service->has_tiers ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="has_tiers">Enable tiered pricing</label>
                            </div>
                            <small class="form-text text-muted">When enabled, tiers can be managed after saving the service.</small>
                        </div>
                    </div>
                </div>

                {{-- Tier Management Section (only visible when editing an existing service with has_tiers enabled) --}}
                @if(isset($service) && $service->exists)
                <div id="tier-management-section" style="{{ old('has_tiers', $service->has_tiers ?? false) ? '' : 'display: none;' }}">
                    <hr>
                    <h5 class="mb-3"><i class="fas fa-layer-group"></i> Pricing Tiers</h5>

                    {{-- Existing Tiers Table --}}
                    @if($service->tiers && $service->tiers->count() > 0)
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service->tiers->sortBy('sort_order') as $tier)
                                <tr>
                                    <td>{{ $tier->name }}</td>
                                    <td>{{ Str::limit($tier->description, 50) }}</td>
                                    <td>₹{{ number_format($tier->price, 2) }}</td>
                                    <td>
                                        @if($tier->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-tier-btn"
                                                data-tier-id="{{ $tier->id }}"
                                                data-tier-name="{{ $tier->name }}"
                                                data-tier-description="{{ $tier->description }}"
                                                data-tier-price="{{ $tier->price }}"
                                                data-tier-is-active="{{ $tier->is_active ? '1' : '0' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-tier-btn"
                                                data-tier-id="{{ $tier->id }}"
                                                data-tier-name="{{ $tier->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No tiers defined yet. Add your first tier below.
                    </div>
                    @endif

                    {{-- Add New Tier Inline Form --}}
                    <div class="card card-outline card-success mb-0">
                        <div class="card-header py-2">
                            <h6 class="mb-0"><i class="fas fa-plus"></i> Add New Tier</h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label for="new_tier_name">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm" id="new_tier_name" placeholder="e.g. Premium">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-2">
                                        <label for="new_tier_description">Description</label>
                                        <input type="text" class="form-control form-control-sm" id="new_tier_description" placeholder="Brief description">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-2">
                                        <label for="new_tier_price">Price <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control form-control-sm" id="new_tier_price" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-2">
                                        <label>&nbsp;</label>
                                        <div class="custom-control custom-switch mt-1">
                                            <input type="checkbox" class="custom-control-input" id="new_tier_is_active" checked>
                                            <label class="custom-control-label" for="new_tier_is_active">Active</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-2">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-success" id="add-tier-btn">
                                                <i class="fas fa-plus"></i> Add Tier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit Tier Modal --}}
                <div class="modal fade" id="editTierModal" tabindex="-1" role="dialog" aria-labelledby="editTierModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTierModalLabel">Edit Tier</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="edit_tier_id">
                                <div class="form-group">
                                    <label for="edit_tier_name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_tier_name">
                                </div>
                                <div class="form-group">
                                    <label for="edit_tier_description">Description</label>
                                    <textarea class="form-control" id="edit_tier_description" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="edit_tier_price">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₹</span>
                                        </div>
                                        <input type="number" class="form-control" id="edit_tier_price" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="edit_tier_is_active">
                                        <label class="custom-control-label" for="edit_tier_is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="save-tier-btn">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delete Tier Confirmation Modal --}}
                <div class="modal fade" id="deleteTierModal" tabindex="-1" role="dialog" aria-labelledby="deleteTierModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteTierModalLabel">Delete Tier</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete the tier "<strong id="delete_tier_name"></strong>"?</p>
                                <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirm-delete-tier-btn">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div id="tier-management-section" style="{{ old('has_tiers') ? '' : 'display: none;' }}">
                    <hr>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Please save the service first before managing tiers.
                    </div>
                </div>
                @endif
            </div>

            {{-- Features & FAQ Tab --}}
            <div class="tab-pane fade" id="features" role="tabpanel">
                {{-- Features Section --}}
                <h5 class="mb-3"><i class="fas fa-check-circle"></i> Features</h5>
                <p class="text-muted">Add feature highlights for this service.</p>
                <div id="features-container">
                    @php
                        $features = old('features', $service->features ?? []);
                        if (!is_array($features)) $features = [];
                    @endphp
                    @forelse($features as $index => $feature)
                        <div class="input-group mb-2 feature-item">
                            <input type="text" class="form-control" name="features[]"
                                   value="{{ $feature }}" placeholder="Feature description">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger remove-feature-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="input-group mb-2 feature-item">
                            <input type="text" class="form-control" name="features[]" placeholder="Feature description">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger remove-feature-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-sm btn-success" id="add-feature-btn">
                    <i class="fas fa-plus"></i> Add Feature
                </button>

                <hr class="my-4">

                {{-- FAQ Section --}}
                <h5 class="mb-3"><i class="fas fa-question-circle"></i> FAQ</h5>
                <p class="text-muted">Add frequently asked questions for this service.</p>
                <div id="faq-container">
                    @php
                        $faqs = old('faq', $service->faq ?? []);
                        if (!is_array($faqs)) $faqs = [];
                    @endphp
                    @forelse($faqs as $index => $faq)
                        <div class="card mb-2 faq-item">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>FAQ #<span class="faq-number">{{ $index + 1 }}</span></strong>
                                    <button type="button" class="btn btn-sm btn-danger remove-faq-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="text" class="form-control" name="faq[{{ $index }}][question]"
                                           value="{{ $faq['question'] ?? '' }}" placeholder="Question">
                                </div>
                                <div class="form-group mb-0">
                                    <textarea class="form-control" name="faq[{{ $index }}][answer]"
                                              rows="2" placeholder="Answer">{{ $faq['answer'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card mb-2 faq-item">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>FAQ #<span class="faq-number">1</span></strong>
                                    <button type="button" class="btn btn-sm btn-danger remove-faq-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="text" class="form-control" name="faq[0][question]" placeholder="Question">
                                </div>
                                <div class="form-group mb-0">
                                    <textarea class="form-control" name="faq[0][answer]" rows="2" placeholder="Answer"></textarea>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-sm btn-success" id="add-faq-btn">
                    <i class="fas fa-plus"></i> Add FAQ
                </button>
            </div>

            {{-- SEO Tab --}}
            <div class="tab-pane fade" id="seo" role="tabpanel">
                <div class="form-group">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                           id="meta_title" name="meta_title"
                           value="{{ old('meta_title', $service->meta_title ?? '') }}"
                           placeholder="SEO title (leave blank to use service name)">
                    @error('meta_title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Recommended: 50-60 characters.</small>
                </div>

                <div class="form-group">
                    <label for="meta_description">Meta Description</label>
                    <textarea class="form-control @error('meta_description') is-invalid @enderror"
                              id="meta_description" name="meta_description" rows="3"
                              placeholder="SEO description for search engines">{{ old('meta_description', $service->meta_description ?? '') }}</textarea>
                    @error('meta_description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Recommended: 150-160 characters.</small>
                </div>

                <div class="form-group">
                    <label for="meta_keywords">Meta Keywords</label>
                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror"
                           id="meta_keywords" name="meta_keywords"
                           value="{{ old('meta_keywords', $service->meta_keywords ?? '') }}"
                           placeholder="keyword1, keyword2, keyword3">
                    @error('meta_keywords')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    <small class="form-text text-muted">Comma-separated keywords.</small>
                </div>
            </div>

            {{-- Settings Tab --}}
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Service Options</h5>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="requires_auth" name="requires_auth" value="1"
                                       {{ old('requires_auth', $service->requires_auth ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="requires_auth">Requires Authentication</label>
                            </div>
                            <small class="form-text text-muted">User must be logged in to access this service.</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="requires_captcha" name="requires_captcha" value="1"
                                       {{ old('requires_captcha', $service->requires_captcha ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="requires_captcha">Requires Captcha</label>
                            </div>
                            <small class="form-text text-muted">Show captcha verification on the form.</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="requires_shipping" name="requires_shipping" value="1"
                                       {{ old('requires_shipping', $service->requires_shipping ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="requires_shipping">Requires Shipping</label>
                            </div>
                            <small class="form-text text-muted">Collect shipping address during checkout.</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                            <small class="form-text text-muted">Only active services are visible on the frontend.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Display Settings</h5>

                        <div class="form-group">
                            <label for="delivery_time">Delivery Time</label>
                            <input type="text" class="form-control @error('delivery_time') is-invalid @enderror"
                                   id="delivery_time" name="delivery_time"
                                   value="{{ old('delivery_time', $service->delivery_time ?? '') }}"
                                   placeholder="e.g. 24-48 hours">
                            @error('delivery_time')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sort_order">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                   id="sort_order" name="sort_order"
                                   value="{{ old('sort_order', $service->sort_order ?? 0) }}"
                                   min="0">
                            @error('sort_order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Lower numbers appear first.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Fields Tab --}}
            <div class="tab-pane fade" id="form-fields" role="tabpanel">
                @if(isset($service) && $service->exists)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-wpforms"></i> Form Fields</h5>
                        <button type="button" class="btn btn-sm btn-success" id="add-field-btn" data-toggle="modal" data-target="#fieldModal">
                            <i class="fas fa-plus"></i> Add Field
                        </button>
                    </div>
                    <p class="text-muted">Configure the form fields that users will fill out when requesting this service.</p>

                    {{-- Existing Fields Table --}}
                    @if($service->formFields && $service->formFields->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" id="fields-table">
                            <thead>
                                <tr>
                                    <th width="40">Order</th>
                                    <th>Field Name</th>
                                    <th>Label</th>
                                    <th>Type</th>
                                    <th>Required</th>
                                    <th>Section</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service->formFields->sortBy('sort_order') as $index => $field)
                                <tr data-field-id="{{ $field->id }}">
                                    <td class="text-center">
                                        <div class="btn-group-vertical btn-group-sm">
                                            <button type="button" class="btn btn-xs btn-outline-secondary move-field-up" data-field-id="{{ $field->id }}" title="Move Up">
                                                <i class="fas fa-arrow-up"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary move-field-down" data-field-id="{{ $field->id }}" title="Move Down">
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td><code>{{ $field->field_name }}</code></td>
                                    <td>{{ $field->field_label }}</td>
                                    <td><span class="badge badge-info">{{ $field->field_type }}</span></td>
                                    <td>
                                        @if($field->is_required)
                                            <span class="badge badge-warning">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $field->section ?? 'default' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info edit-field-btn"
                                                data-field-id="{{ $field->id }}"
                                                data-field-name="{{ $field->field_name }}"
                                                data-field-label="{{ $field->field_label }}"
                                                data-field-type="{{ $field->field_type }}"
                                                data-field-placeholder="{{ $field->placeholder }}"
                                                data-field-validation="{{ $field->validation_rules }}"
                                                data-field-required="{{ $field->is_required ? '1' : '0' }}"
                                                data-field-section="{{ $field->section }}"
                                                data-field-section-label="{{ $field->section_label }}"
                                                data-field-help-text="{{ $field->help_text }}"
                                                data-field-options="{{ $field->options ? json_encode($field->options) : '' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-field-btn"
                                                data-field-id="{{ $field->id }}"
                                                data-field-name="{{ $field->field_name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No form fields defined yet. Click "Add Field" to create your first field.
                    </div>
                    @endif

                    {{-- Add/Edit Field Modal --}}
                    <div class="modal fade" id="fieldModal" tabindex="-1" role="dialog" aria-labelledby="fieldModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="fieldModalLabel">Add Form Field</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="edit_field_id" value="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="field_name">Field Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="field_name" placeholder="e.g. date_of_birth">
                                                <small class="form-text text-muted">System key (lowercase, underscores). Used as the form input name.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="field_label">Field Label <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="field_label" placeholder="e.g. Date of Birth">
                                                <small class="form-text text-muted">Display label shown to the user.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="field_type">Field Type <span class="text-danger">*</span></label>
                                                <select class="form-control" id="field_type">
                                                    <option value="text">Text</option>
                                                    <option value="email">Email</option>
                                                    <option value="tel">Telephone</option>
                                                    <option value="date">Date</option>
                                                    <option value="time">Time</option>
                                                    <option value="datetime">Date & Time</option>
                                                    <option value="select">Select (Dropdown)</option>
                                                    <option value="textarea">Textarea</option>
                                                    <option value="radio">Radio Buttons</option>
                                                    <option value="checkbox">Checkbox</option>
                                                    <option value="hidden">Hidden</option>
                                                    <option value="file">File Upload</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="field_placeholder">Placeholder</label>
                                                <input type="text" class="form-control" id="field_placeholder" placeholder="e.g. Enter your date of birth">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="field_validation_rules">Validation Rules</label>
                                                <input type="text" class="form-control" id="field_validation_rules" placeholder="e.g. required|date|before:today">
                                                <small class="form-text text-muted">Pipe-separated Laravel validation rules.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div class="custom-control custom-switch mt-2">
                                                    <input type="checkbox" class="custom-control-input" id="field_is_required">
                                                    <label class="custom-control-label" for="field_is_required">Required Field</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="field_section">Section</label>
                                                <input type="text" class="form-control" id="field_section" placeholder="e.g. personal_details" value="default">
                                                <small class="form-text text-muted">Group key for organizing fields into sections.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="field_section_label">Section Label</label>
                                                <input type="text" class="form-control" id="field_section_label" placeholder="e.g. Personal Details">
                                                <small class="form-text text-muted">Heading displayed above the section.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="field_help_text">Help Text</label>
                                        <input type="text" class="form-control" id="field_help_text" placeholder="e.g. Please enter your exact date of birth for accurate predictions">
                                        <small class="form-text text-muted">Displayed below the field as guidance for the user.</small>
                                    </div>

                                    {{-- Options Builder (for select/radio/checkbox) --}}
                                    <div id="options-builder-section" style="display: none;">
                                        <hr>
                                        <h6><i class="fas fa-list-ul"></i> Field Options</h6>
                                        <p class="text-muted small">Define the available options for this field.</p>
                                        <div id="options-container">
                                            {{-- Options will be added dynamically --}}
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-success" id="add-option-btn">
                                            <i class="fas fa-plus"></i> Add Option
                                        </button>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="save-field-btn">
                                        <i class="fas fa-save"></i> Save Field
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Field Confirmation Modal --}}
                    <div class="modal fade" id="deleteFieldModal" tabindex="-1" role="dialog" aria-labelledby="deleteFieldModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteFieldModalLabel">Delete Form Field</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete the field "<strong id="delete_field_name"></strong>"?</p>
                                    <p class="text-danger mb-0"><small>This action cannot be undone. Existing submission data for this field will not be affected.</small></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" id="confirm-delete-field-btn">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Please save the service first before managing form fields.
                    </div>
                @endif
            </div>

        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ isset($service) && $service->exists ? 'Update Service' : 'Create Service' }}
        </button>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</div>
