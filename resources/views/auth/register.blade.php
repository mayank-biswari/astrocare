@extends('layouts.app')

@section('title', 'Register - AstroServices')

@section('content')
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-r from-indigo-900 to-purple-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                    <p class="text-gray-600">Join AstroServices for spiritual guidance</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-6" id="registerForm">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">
                            <button type="button" onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="password-strength" class="mt-2 text-sm"></div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm
                            Password</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 pr-10">
                            <button type="button" onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i id="password_confirmation-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" id="submitBtn" disabled
                        class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-bold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Create Account
                    </button>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-medium">Sign
                                in</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            let passwordStrength = 0;

            function togglePassword(fieldId) {
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

            document.getElementById('password').addEventListener('input', function(e) {
                const password = e.target.value;
                const strengthDiv = document.getElementById('password-strength');
                const submitBtn = document.getElementById('submitBtn');

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
                    strengthDiv.innerHTML =
                        `<span class="${colors[result.strength-1]}">Password Strength: ${labels[result.strength-1]}</span>`;
                    if (result.feedback.length > 0) {
                        strengthDiv.innerHTML +=
                            `<br><span class="text-red-600 text-xs">Add: ${result.feedback.join(', ')}</span>`;
                    }
                } else {
                    strengthDiv.innerHTML =
                        `<span class="${colors[result.strength]}">Password Strength: ${labels[result.strength]}</span>`;
                    if (result.feedback.length > 0) {
                        strengthDiv.innerHTML +=
                            `<br><span class="text-red-600 text-xs">Add: ${result.feedback.join(', ')}</span>`;
                    }
                }

                // Enable submit only if password is strong (strength = 4)
                submitBtn.disabled = result.strength < 4;
            });

            document.getElementById('registerForm').addEventListener('submit', function(e) {
                if (passwordStrength < 4) {
                    e.preventDefault();
                    alert('Please use a strong password with at least 8 characters, uppercase and lowercase letters, numbers, and special characters.');
                }
            });
        </script>
    @endpush
@endsection
