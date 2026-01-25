@extends('dashboard.layout')

@section('title', 'Account Settings - Dashboard')

@section('dashboard-content')
@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 sm:px-6 py-4 rounded-lg mb-4 sm:mb-6 shadow-sm text-sm sm:text-base">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif

<div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-4 sm:mb-6" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <h1 class="text-xl sm:text-2xl font-bold">Account Settings</h1>
    <p class="text-white/90 mt-1 text-sm sm:text-base">Manage your profile and preferences</p>
</div>

<!-- Profile Photo Section -->
<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mb-4 sm:mb-6">
    <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6">Profile Photo</h2>
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
        <div class="relative">
            @if(auth()->user()->profile_photo)
                <img src="{{ auth()->user()->profile_photo }}" alt="Profile" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover">
            @else
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-2xl sm:text-3xl">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            @endif
        </div>
        <div class="flex-1 w-full">
            <form action="{{ route('dashboard.profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                @csrf
                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button type="button" onclick="document.getElementById('profile_photo').click()" class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
                        <i class="fas fa-{{ auth()->user()->profile_photo ? 'edit' : 'upload' }} mr-2"></i>{{ auth()->user()->profile_photo ? 'Change Photo' : 'Upload Photo' }}
                    </button>
                    @if(auth()->user()->profile_photo)
                        <button type="button" onclick="confirmDelete()" class="px-3 sm:px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm sm:text-base">
                            <i class="fas fa-trash mr-2"></i>Delete Photo
                        </button>
                        <form action="{{ route('dashboard.profile.photo.delete') }}" method="POST" id="deletePhotoForm" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </form>
            <p class="text-xs sm:text-sm text-gray-500 mt-2">JPG, PNG or GIF. Max size 2MB.</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6">Profile Information</h2>
        
        <form action="{{ route('dashboard.profile.update') }}" method="POST" class="space-y-3 sm:space-y-4">
            @csrf
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" required
                       class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base">
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" required
                       class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base">
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" name="phone" value="{{ auth()->user()->phone }}" placeholder="Enter phone number"
                       class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base">
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ auth()->user()->date_of_birth }}"
                       class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base">
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
                Update Profile
            </button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6">Change Password</h2>
        
        <form action="{{ route('dashboard.password.update') }}" method="POST" class="space-y-3 sm:space-y-4" id="passwordForm">
            @csrf
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" name="current_password" required
                       class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base">
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">New Password</label>
                <div class="relative">
                    <input type="password" name="password" id="new_password" required
                           class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 pr-10 text-sm sm:text-base">
                    <button type="button" onclick="togglePasswordField('new_password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <i id="new_password-icon" class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="new-password-strength" class="mt-2 text-xs sm:text-sm"></div>
            </div>

            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 pr-10 text-sm sm:text-base">
                    <button type="button" onclick="togglePasswordField('password_confirmation')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <i id="password_confirmation-icon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="passwordSubmitBtn" disabled class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-sm sm:text-base">
                Change Password
            </button>
        </form>
    </div>
</div>

<!-- Preferences -->
<div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 mt-4 sm:mt-6">
    <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6">Preferences</h2>
    
    <form action="{{ route('dashboard.preferences.update') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            <div>
                <h3 class="font-bold mb-3 sm:mb-4 text-sm sm:text-base">Notifications</h3>
                <div class="space-y-2 sm:space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="email_notifications" value="1" 
                               {{ auth()->user()->email_notifications ? 'checked' : '' }} class="mr-2 sm:mr-3">
                        <span class="text-xs sm:text-sm">Email notifications for order updates</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="sms_notifications" value="1" 
                               {{ auth()->user()->sms_notifications ? 'checked' : '' }} class="mr-2 sm:mr-3">
                        <span class="text-xs sm:text-sm">SMS notifications for consultations</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="marketing_emails" value="1" 
                               {{ auth()->user()->marketing_emails ? 'checked' : '' }} class="mr-2 sm:mr-3">
                        <span class="text-xs sm:text-sm">Marketing emails</span>
                    </label>
                </div>
            </div>
            
            <div>
                <h3 class="font-bold mb-3 sm:mb-4 text-sm sm:text-base">Language & Currency</h3>
                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Preferred Language</label>
                        <select name="language" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm">
                            <option {{ auth()->user()->language == 'English' ? 'selected' : '' }}>English</option>
                            <option {{ auth()->user()->language == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                            <option {{ auth()->user()->language == 'Sanskrit' ? 'selected' : '' }}>Sanskrit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm">
                            <option {{ auth()->user()->currency == 'INR (₹)' ? 'selected' : '' }}>INR (₹)</option>
                            <option {{ auth()->user()->currency == 'USD ($)' ? 'selected' : '' }}>USD ($)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 sm:mt-6">
            <button type="submit" class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm sm:text-base">
                Save Preferences
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Delete Profile Photo?',
        text: "Are you sure you want to delete your profile photo?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deletePhotoForm').submit();
        }
    });
}

let passwordStrength = 0;

function togglePasswordField(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = [];

    if (password.length >= 8) strength++;
    else feedback.push('at least 8 characters');

    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    else feedback.push('uppercase and lowercase letters');

    if (/\d/.test(password)) strength++;
    else feedback.push('numbers');

    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    else feedback.push('special characters');

    return { strength, feedback };
}

document.getElementById('new_password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strengthDiv = document.getElementById('new-password-strength');
    const submitBtn = document.getElementById('passwordSubmitBtn');

    if (password.length === 0) {
        strengthDiv.innerHTML = '';
        passwordStrength = 0;
        submitBtn.disabled = true;
        return;
    }

    const result = checkPasswordStrength(password);
    passwordStrength = result.strength;

    const colors = ['text-red-600', 'text-orange-600', 'text-yellow-600', 'text-green-600'];
    const labels = ['Weak', 'Fair', 'Good', 'Strong'];

    if (result.strength > 0) {
        strengthDiv.innerHTML = `<span class="${colors[result.strength-1]}">Password Strength: ${labels[result.strength-1]}</span>`;
        if (result.feedback.length > 0) {
            strengthDiv.innerHTML += `<br><span class="text-red-600 text-xs">Add: ${result.feedback.join(', ')}</span>`;
        }
    } else {
        strengthDiv.innerHTML = `<span class="${colors[result.strength]}">Password Strength: ${labels[result.strength]}</span>`;
        if (result.feedback.length > 0) {
            strengthDiv.innerHTML += `<br><span class="text-red-600 text-xs">Add: ${result.feedback.join(', ')}</span>`;
        }
    }

    submitBtn.disabled = result.strength < 4;
});

document.getElementById('passwordForm').addEventListener('submit', function(e) {
    if (passwordStrength < 4) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Please use a strong password with at least 8 characters, uppercase and lowercase letters, numbers, and special characters.'
        });
    }
});
</script>
@endpush
