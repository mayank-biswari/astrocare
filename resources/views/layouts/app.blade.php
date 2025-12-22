<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Astrology Services')</title>
    @if(\App\Models\SiteSetting::get('site_icon'))
        <link rel="icon" type="image/x-icon" href="{{ asset(\App\Models\SiteSetting::get('site_icon')) }}">
    @endif
    @stack('meta')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-indigo-900 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        @if(\App\Models\SiteSetting::get('site_logo'))
                            <img src="{{ asset(\App\Models\SiteSetting::get('site_logo')) }}" alt="Logo" class="h-8 w-auto">
                        @else
                            <span class="text-2xl font-bold">🔮 AstroServices</span>
                        @endif
                    </a>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('home') }}" class="hover:text-yellow-300">{{ __('messages.home') }}</a>
                    <div class="relative" id="services-dropdown">
                        <button onclick="toggleDropdown()" class="hover:text-yellow-300 flex items-center">
                            {{ __('messages.services') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="dropdown-menu" class="absolute hidden bg-white text-gray-800 mt-2 py-2 w-48 rounded shadow-lg z-50">
                            <a href="{{ route('services.index') }}" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.all_services') }}</a>
                            <a href="{{ route('consultations.index') }}" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.consultations') }}</a>
                            <a href="{{ route('kundli.index') }}" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.kundli_reading') }}</a>
                            <a href="{{ route('horoscope.matching') }}" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.horoscope_matching') }}</a>
                            <a href="{{ route('ask.question') }}" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.ask_question') }}</a>
                            <a href="{{ route('predictions.index') }}" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.predictions') }}</a>
                        </div>
                    </div>
                    <a href="{{ route('pooja.index') }}" class="hover:text-yellow-300">{{ __('messages.pooja_rituals') }}</a>
                    <a href="{{ route('shop.index') }}" class="hover:text-yellow-300">{{ __('messages.shop') }}</a>
                    <a href="{{ route('cart.index') }}" class="hover:text-yellow-300 relative">
                        {{ __('messages.cart') }}
                        @php $cartCount = count(session()->get('cart', [])); @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-yellow-500 text-indigo-900 text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('about') }}" class="hover:text-yellow-300">{{ __('messages.about') }}</a>
                    <div class="relative" id="language-dropdown">
                        <button onclick="toggleLanguageDropdown()" class="hover:text-yellow-300 flex items-center">
                            {{ __('messages.language') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="language-menu" class="absolute hidden bg-white text-gray-800 mt-2 py-2 w-32 rounded shadow-lg z-50">
                            @foreach(\App\Models\Language::getActiveLanguages() as $language)
                                <a href="{{ route('lang.switch', $language->code) }}" class="block px-4 py-2 hover:bg-gray-100">{{ $language->native_name }}</a>
                            @endforeach
                        </div>
                    </div>
                    <div class="relative" id="currency-dropdown">
                        <button onclick="toggleCurrencyDropdown()" class="hover:text-yellow-300 flex items-center">
                            @php $currentCurrency = \App\Models\Currency::where('code', session('currency', \App\Models\Currency::getDefaultCurrency()->code))->first(); @endphp
                            {{ $currentCurrency->symbol }} {{ $currentCurrency->code }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="currency-menu" class="absolute hidden bg-white text-gray-800 mt-2 py-2 w-32 rounded shadow-lg z-50">
                            @foreach(\App\Models\Currency::getActiveCurrencies() as $currency)
                                <a href="{{ route('currency.switch', $currency->code) }}" class="block px-4 py-2 hover:bg-gray-100">{{ $currency->symbol }} {{ $currency->code }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-yellow-300">{{ __('messages.dashboard') }}</a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-yellow-300">{{ __('messages.admin') }}</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-yellow-300">{{ __('messages.logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-yellow-300">{{ __('messages.login') }}</a>
                        <a href="{{ route('register') }}" class="bg-yellow-500 text-indigo-900 px-4 py-2 rounded hover:bg-yellow-400">{{ __('messages.register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    @if(\App\Models\SiteSetting::get('site_logo'))
                        <img src="{{ asset(\App\Models\SiteSetting::get('site_logo')) }}" alt="Logo" class="h-8 w-auto mb-4">
                    @else
                        <h3 class="text-xl font-bold mb-4">🔮 AstroServices</h3>
                    @endif
                    <p class="text-gray-300">{{ __('messages.trusted_partner') }}</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">{{ __('messages.services') }}</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="{{ route('consultations.index') }}" class="hover:text-white">{{ __('messages.consultations') }}</a></li>
                        <li><a href="{{ route('kundli.index') }}" class="hover:text-white">{{ __('messages.kundli_reading') }}</a></li>
                        <li><a href="{{ route('pooja.index') }}" class="hover:text-white">{{ __('messages.pooja_rituals') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">{{ __('messages.shop') }}</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="{{ route('shop.category', 'gemstones') }}" class="hover:text-white">{{ __('messages.gemstones') }}</a></li>
                        <li><a href="{{ route('shop.category', 'rudraksha') }}" class="hover:text-white">{{ __('messages.rudraksha') }}</a></li>
                        <li><a href="{{ route('shop.category', 'yantras') }}" class="hover:text-white">{{ __('messages.yantras') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">{{ __('messages.contact') }}</h4>
                    <p class="text-gray-300">{{ __('messages.email') }}: info@astroservices.com</p>
                    <p class="text-gray-300">{{ __('messages.phone') }}: +91 9876543210</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; 2024 AstroServices. {{ __('messages.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>
<script>
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown-menu');
    dropdown.classList.toggle('hidden');
}

function toggleLanguageDropdown() {
    const dropdown = document.getElementById('language-menu');
    dropdown.classList.toggle('hidden');
}

function toggleCurrencyDropdown() {
    const dropdown = document.getElementById('currency-menu');
    dropdown.classList.toggle('hidden');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const servicesDropdown = document.getElementById('services-dropdown');
    const servicesMenu = document.getElementById('dropdown-menu');
    const languageDropdown = document.getElementById('language-dropdown');
    const languageMenu = document.getElementById('language-menu');
    const currencyDropdown = document.getElementById('currency-dropdown');
    const currencyMenu = document.getElementById('currency-menu');
    
    if (!servicesDropdown.contains(event.target)) {
        servicesMenu.classList.add('hidden');
    }
    
    if (!languageDropdown.contains(event.target)) {
        languageMenu.classList.add('hidden');
    }
    
    if (!currencyDropdown.contains(event.target)) {
        currencyMenu.classList.add('hidden');
    }
});
</script>
</body>
</html>
