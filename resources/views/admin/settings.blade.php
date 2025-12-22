@extends('admin.layouts.app')

@section('title', 'Site Settings')
@section('page-title', 'Settings')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Site Logo & Icon</h3>
    </div>
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="logo">Site Logo</label>
                        @if(\App\Models\SiteSetting::get('site_logo'))
                            <div class="mb-2">
                                <img src="{{ \App\Models\SiteSetting::get('site_logo') }}" alt="Current Logo" style="max-height: 60px;">
                            </div>
                        @endif
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <small class="form-text text-muted">Upload a new logo (JPEG, PNG, JPG, GIF, max 2MB)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="icon">Site Icon/Favicon</label>
                        @if(\App\Models\SiteSetting::get('site_icon'))
                            <div class="mb-2">
                                <img src="{{ \App\Models\SiteSetting::get('site_icon') }}" alt="Current Icon" style="max-height: 32px;">
                            </div>
                        @endif
                        <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                        <small class="form-text text-muted">Upload a new icon (JPEG, PNG, JPG, GIF, ICO, max 1MB)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Settings
            </button>
        </div>
    </form>
</div>
@endsection