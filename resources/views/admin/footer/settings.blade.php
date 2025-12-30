@extends('admin.layouts.app')

@section('title', 'Footer Settings')
@section('page-title', 'Footer Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Footer Configuration</h3>
    </div>
    <form action="{{ route('admin.footer.settings.update') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', \App\Models\FooterSetting::get('company_name', 'AstroServices')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', \App\Models\FooterSetting::get('contact_email')) }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Company Description</label>
                        <textarea name="company_description" rows="3" class="form-control">{{ old('company_description', \App\Models\FooterSetting::get('company_description')) }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', \App\Models\FooterSetting::get('contact_phone')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="2" class="form-control">{{ old('address', \App\Models\FooterSetting::get('address')) }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Facebook URL</label>
                        <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url', \App\Models\FooterSetting::get('facebook_url')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Twitter URL</label>
                        <input type="url" name="twitter_url" class="form-control" value="{{ old('twitter_url', \App\Models\FooterSetting::get('twitter_url')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Instagram URL</label>
                        <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', \App\Models\FooterSetting::get('instagram_url')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>YouTube URL</label>
                        <input type="url" name="youtube_url" class="form-control" value="{{ old('youtube_url', \App\Models\FooterSetting::get('youtube_url')) }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Copyright Text</label>
                        <input type="text" name="copyright_text" class="form-control" value="{{ old('copyright_text', \App\Models\FooterSetting::get('copyright_text', '© 2024 AstroServices. All rights reserved.')) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection