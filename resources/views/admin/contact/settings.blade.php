@extends('admin.layouts.app')

@section('title', 'Contact Settings')
@section('page-title', 'Contact Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Contact Form Settings</h3>
    </div>
    <form action="{{ route('admin.contact.settings.update') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Admin Email *</label>
                        <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" 
                               value="{{ old('admin_email', \App\Models\ContactSetting::get('admin_email')) }}" required>
                        <small class="form-text text-muted">Email address where contact form submissions will be sent</small>
                        @error('admin_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" 
                               value="{{ old('contact_phone', \App\Models\ContactSetting::get('contact_phone')) }}">
                        <small class="form-text text-muted">Phone number displayed on contact page</small>
                        @error('contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Contact Address</label>
                        <textarea name="contact_address" rows="3" class="form-control @error('contact_address') is-invalid @enderror">{{ old('contact_address', \App\Models\ContactSetting::get('contact_address')) }}</textarea>
                        <small class="form-text text-muted">Business address displayed on contact page</small>
                        @error('contact_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Business Hours</label>
                        <textarea name="business_hours" rows="2" class="form-control @error('business_hours') is-invalid @enderror">{{ old('business_hours', \App\Models\ContactSetting::get('business_hours')) }}</textarea>
                        <small class="form-text text-muted">Business hours displayed on contact page</small>
                        @error('business_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="show_contact_info" value="1" class="custom-control-input" id="show_contact_info" {{ \App\Models\ContactSetting::get('show_contact_info', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="show_contact_info">Show "Get in Touch" section on contact page</label>
                        </div>
                        <small class="form-text text-muted">Toggle visibility of contact information section</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
            <a href="{{ route('admin.contact.submissions') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Email Test</h3>
    </div>
    <div class="card-body">
        <p>To test if email notifications are working properly, submit a test message through the contact form on your website.</p>
        <a href="{{ route('contact') }}" target="_blank" class="btn btn-info">
            <i class="fas fa-external-link-alt"></i> Open Contact Form
        </a>
    </div>
</div>
@endsection