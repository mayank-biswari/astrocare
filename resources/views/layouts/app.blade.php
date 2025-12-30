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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saffron': '#FF9933',
                        'deep-saffron': '#FF6600',
                        'sacred-orange': '#FF8C00',
                        'temple-red': '#DC143C',
                        'divine-gold': '#FFD700',
                        'sacred-maroon': '#800000',
                        'holy-yellow': '#FFFF00',
                        'spiritual-purple': '#4B0082',
                        'om-blue': '#000080'
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #FF9933 0%, #FF6600 50%, #DC143C 100%);
        }
        .om-shadow {
            box-shadow: 0 4px 20px rgba(255, 153, 51, 0.3);
        }
        .divine-glow {
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.4);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 via-yellow-50 to-red-50">
    <!-- Navigation -->
    <nav class="gradient-bg text-white shadow-lg om-shadow">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        @if(\App\Models\SiteSetting::get('site_logo'))
                            <img src="{{ asset(\App\Models\SiteSetting::get('site_logo')) }}" alt="Logo" class="h-8 w-auto">
                        @else
                            <span class="text-xl md:text-2xl font-bold flex items-center">
                                <span class="text-divine-gold mr-2">🕉️</span>
                                <span class="bg-gradient-to-r from-divine-gold to-holy-yellow bg-clip-text text-transparent">AstroServices</span>
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex space-x-6">
                    <a href="{{ route('home') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center">
                        <i class="fas fa-home mr-1"></i>{{ __('messages.home') }}
                    </a>
                    <div class="relative" id="services-dropdown">
                        <button onclick="toggleDropdown()" class="hover:text-divine-gold flex items-center transition-colors duration-300">
                            <i class="fas fa-star-and-crescent mr-1"></i>{{ __('messages.services') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="dropdown-menu" class="absolute hidden bg-white text-gray-800 mt-2 py-2 w-48 rounded-lg shadow-xl border border-saffron/20 z-50">
                            <a href="{{ route('services.index') }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ __('messages.all_services') }}</a>
                            <a href="{{ route('consultations.index') }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ __('messages.consultations') }}</a>
                            <a href="{{ route('kundli.index') }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ __('messages.kundli_reading') }}</a>
                            <a href="{{ route('horoscope.matching') }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ __('messages.horoscope_matching') }}</a>
                            <a href="{{ route('ask.question') }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ __('messages.ask_question') }}</a>
                            <a href="{{ route('predictions.index') }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ __('messages.predictions') }}</a>
                        </div>
                    </div>
                    <a href="{{ route('pooja.index') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center">
                        <i class="fas fa-fire mr-1"></i>{{ __('messages.pooja_rituals') }}
                    </a>
                    <a href="{{ route('shop.index') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center">
                        <i class="fas fa-gem mr-1"></i>{{ __('messages.shop') }}
                    </a>
                    <a href="{{ route('blogs.index') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center">
                        <i class="fas fa-scroll mr-1"></i>{{ __('messages.blogs') }}
                    </a>
                    <a href="{{ route('testimonials') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center">
                        <i class="fas fa-quote-left mr-1"></i>{{ __('messages.testimonials') }}
                    </a>
                    <a href="{{ route('contact') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center">
                        <i class="fas fa-phone mr-1"></i>{{ __('messages.contact') }}
                    </a>
                    <a href="{{ route('about') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>{{ __('messages.about') }}
                    </a>
                </div>

                <!-- Desktop Right Menu -->
                <div class="hidden lg:flex items-center space-x-4">
                    <a href="{{ route('cart.index') }}" class="hover:text-divine-gold relative transition-colors duration-300">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span class="hidden xl:inline ml-1">{{ __('messages.cart') }}</span>
                        @php $cartCount = count(session()->get('cart', [])); @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-divine-gold text-temple-red text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <div class="relative" id="language-dropdown">
                        <button onclick="toggleLanguageDropdown()" class="hover:text-divine-gold flex items-center transition-colors duration-300">
                            <i class="fas fa-language"></i>
                            <span class="hidden xl:inline ml-1">{{ __('messages.language') }}</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="language-menu" class="absolute hidden bg-white text-gray-800 mt-2 py-2 w-32 rounded-lg shadow-xl border border-saffron/20 z-50 right-0">
                            @foreach(\App\Models\Language::getActiveLanguages() as $language)
                                <a href="{{ route('lang.switch', $language->code) }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ $language->native_name }}</a>
                            @endforeach
                        </div>
                    </div>
                    <div class="relative" id="currency-dropdown">
                        <button onclick="toggleCurrencyDropdown()" class="hover:text-divine-gold flex items-center transition-colors duration-300">
                            @php $currentCurrency = \App\Models\Currency::where('code', session('currency', \App\Models\Currency::getDefaultCurrency()->code))->first(); @endphp
                            <i class="fas fa-coins"></i>
                            <span class="hidden xl:inline ml-1">{{ $currentCurrency->code }}</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="currency-menu" class="absolute hidden bg-white text-gray-800 mt-2 py-2 w-32 rounded-lg shadow-xl border border-saffron/20 z-50 right-0">
                            @foreach(\App\Models\Currency::getActiveCurrencies() as $currency)
                                <a href="{{ route('currency.switch', $currency->code) }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ $currency->symbol }} {{ $currency->code }}</a>
                            @endforeach
                        </div>
                    </div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-divine-gold transition-colors duration-300">
                            <i class="fas fa-user-circle"></i>
                            <span class="hidden xl:inline ml-1">{{ __('messages.dashboard') }}</span>
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-divine-gold transition-colors duration-300">
                                <i class="fas fa-crown"></i>
                                <span class="hidden xl:inline ml-1">{{ __('messages.admin') }}</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-divine-gold transition-colors duration-300">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="hidden xl:inline ml-1">{{ __('messages.logout') }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-divine-gold transition-colors duration-300">{{ __('messages.login') }}</a>
                        <a href="{{ route('register') }}" class="bg-divine-gold text-temple-red px-3 py-2 rounded-lg hover:bg-holy-yellow transition-colors duration-300 text-sm font-semibold divine-glow">{{ __('messages.register') }}</a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center space-x-2">
                    <a href="{{ route('cart.index') }}" class="hover:text-divine-gold relative">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        @php $cartCount = count(session()->get('cart', [])); @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-divine-gold text-temple-red text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <button onclick="toggleMobileMenu()" class="text-white hover:text-divine-gold focus:outline-none transition-colors duration-300">
                        <svg id="mobile-menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg id="mobile-close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="lg:hidden hidden bg-gradient-to-r from-deep-saffron to-temple-red border-t border-divine-gold/30">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('home') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                        <i class="fas fa-home mr-2"></i>{{ __('messages.home') }}
                    </a>
                    
                    <!-- Mobile Services Dropdown -->
                    <div class="block">
                        <button onclick="toggleMobileServices()" class="w-full text-left px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg flex items-center justify-between transition-colors duration-300">
                            <span><i class="fas fa-star-and-crescent mr-2"></i>{{ __('messages.services') }}</span>
                            <svg id="mobile-services-icon" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="mobile-services-menu" class="hidden ml-4 mt-1 space-y-1">
                            <a href="{{ route('services.index') }}" class="block px-3 py-2 text-gray-200 hover:text-divine-gold hover:bg-white/10 rounded-lg text-sm transition-colors duration-300">{{ __('messages.all_services') }}</a>
                            <a href="{{ route('consultations.index') }}" class="block px-3 py-2 text-gray-200 hover:text-divine-gold hover:bg-white/10 rounded-lg text-sm transition-colors duration-300">{{ __('messages.consultations') }}</a>
                            <a href="{{ route('kundli.index') }}" class="block px-3 py-2 text-gray-200 hover:text-divine-gold hover:bg-white/10 rounded-lg text-sm transition-colors duration-300">{{ __('messages.kundli_reading') }}</a>
                            <a href="{{ route('horoscope.matching') }}" class="block px-3 py-2 text-gray-200 hover:text-divine-gold hover:bg-white/10 rounded-lg text-sm transition-colors duration-300">{{ __('messages.horoscope_matching') }}</a>
                            <a href="{{ route('ask.question') }}" class="block px-3 py-2 text-gray-200 hover:text-divine-gold hover:bg-white/10 rounded-lg text-sm transition-colors duration-300">{{ __('messages.ask_question') }}</a>
                            <a href="{{ route('predictions.index') }}" class="block px-3 py-2 text-gray-200 hover:text-divine-gold hover:bg-white/10 rounded-lg text-sm transition-colors duration-300">{{ __('messages.predictions') }}</a>
                        </div>
                    </div>
                    
                    <a href="{{ route('pooja.index') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                        <i class="fas fa-fire mr-2"></i>{{ __('messages.pooja_rituals') }}
                    </a>
                    <a href="{{ route('shop.index') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                        <i class="fas fa-gem mr-2"></i>{{ __('messages.shop') }}
                    </a>
                    <a href="{{ route('blogs.index') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                        <i class="fas fa-scroll mr-2"></i>{{ __('messages.blogs') }}
                    </a>
                    <a href="{{ route('testimonials') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                        <i class="fas fa-quote-left mr-2"></i>{{ __('messages.testimonials') }}
                    </a>
                    <a href="{{ route('contact') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                        <i class="fas fa-phone mr-2"></i>{{ __('messages.contact') }}
                    </a>
                    <a href="{{ route('about') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                        <i class="fas fa-info-circle mr-2"></i>{{ __('messages.about') }}
                    </a>
                    
                    <hr class="border-divine-gold/30 my-2">
                    
                    <!-- Mobile Language/Currency -->
                    <div class="flex space-x-4 px-3 py-2">
                        <div class="relative" id="mobile-language-dropdown">
                            <button onclick="toggleMobileLanguage()" class="text-white hover:text-divine-gold flex items-center transition-colors duration-300">
                                <i class="fas fa-language mr-2"></i>{{ __('messages.language') }}
                            </button>
                            <div id="mobile-language-menu" class="hidden absolute bg-white text-gray-800 mt-2 py-2 w-32 rounded-lg shadow-xl border border-saffron/20 z-50">
                                @foreach(\App\Models\Language::getActiveLanguages() as $language)
                                    <a href="{{ route('lang.switch', $language->code) }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ $language->native_name }}</a>
                                @endforeach
                            </div>
                        </div>
                        <div class="relative" id="mobile-currency-dropdown">
                            <button onclick="toggleMobileCurrency()" class="text-white hover:text-divine-gold flex items-center transition-colors duration-300">
                                @php $currentCurrency = \App\Models\Currency::where('code', session('currency', \App\Models\Currency::getDefaultCurrency()->code))->first(); @endphp
                                <i class="fas fa-coins mr-2"></i>{{ $currentCurrency->code }}
                            </button>
                            <div id="mobile-currency-menu" class="hidden absolute bg-white text-gray-800 mt-2 py-2 w-32 rounded-lg shadow-xl border border-saffron/20 z-50">
                                @foreach(\App\Models\Currency::getActiveCurrencies() as $currency)
                                    <a href="{{ route('currency.switch', $currency->code) }}" class="block px-4 py-2 hover:bg-saffron/10 hover:text-temple-red transition-colors">{{ $currency->symbol }} {{ $currency->code }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <hr class="border-divine-gold/30 my-2">
                    
                    <!-- Mobile Auth -->
                    @auth
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                            <i class="fas fa-user-circle mr-2"></i>{{ __('messages.dashboard') }}
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                                <i class="fas fa-crown mr-2"></i>{{ __('messages.admin') }}
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">
                                <i class="fas fa-sign-out-alt mr-2"></i>{{ __('messages.logout') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 text-white hover:text-divine-gold hover:bg-white/10 rounded-lg transition-colors duration-300">{{ __('messages.login') }}</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 bg-divine-gold text-temple-red hover:bg-holy-yellow rounded-lg transition-colors duration-300 font-semibold divine-glow">{{ __('messages.register') }}</a>
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
    <footer class="bg-gradient-to-r from-sacred-maroon via-temple-red to-deep-saffron text-white py-8 mt-12 om-shadow">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    @if(\App\Models\SiteSetting::get('site_logo'))
                        <img src="{{ asset(\App\Models\SiteSetting::get('site_logo')) }}" alt="Logo" class="h-8 w-auto mb-4">
                    @else
                        <h3 class="text-xl font-bold mb-4 flex items-center">
                            <span class="text-divine-gold mr-2">🕉️</span>
                            {{ \App\Models\FooterSetting::get('company_name', 'AstroServices') }}
                        </h3>
                    @endif
                    <p class="text-orange-100">{{ \App\Models\FooterSetting::get('company_description', __('messages.trusted_partner')) }}</p>
                    <div class="mt-4 flex items-center text-divine-gold">
                        <i class="fas fa-om mr-2"></i>
                        <span class="text-sm">Blessed by Divine Grace</span>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-divine-gold flex items-center">
                        <i class="fas fa-star-and-crescent mr-2"></i>{{ __('messages.services') }}
                    </h4>
                    <ul class="space-y-2 text-orange-100">
                        <li><a href="{{ route('consultations.index') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center"><i class="fas fa-user-astronaut mr-2 text-xs"></i>{{ __('messages.consultations') }}</a></li>
                        <li><a href="{{ route('kundli.index') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center"><i class="fas fa-chart-pie mr-2 text-xs"></i>{{ __('messages.kundli_reading') }}</a></li>
                        <li><a href="{{ route('pooja.index') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center"><i class="fas fa-fire mr-2 text-xs"></i>{{ __('messages.pooja_rituals') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-divine-gold flex items-center">
                        <i class="fas fa-gem mr-2"></i>{{ __('messages.shop') }}
                    </h4>
                    <ul class="space-y-2 text-orange-100">
                        <li><a href="{{ route('shop.category', 'gemstones') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center"><i class="fas fa-gem mr-2 text-xs"></i>{{ __('messages.gemstones') }}</a></li>
                        <li><a href="{{ route('shop.category', 'rudraksha') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center"><i class="fas fa-circle mr-2 text-xs"></i>{{ __('messages.rudraksha') }}</a></li>
                        <li><a href="{{ route('shop.category', 'yantras') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center"><i class="fas fa-dharmachakra mr-2 text-xs"></i>{{ __('messages.yantras') }}</a></li>
                    </ul>

                    <hr class="my-5 border-divine-gold/30">
                    <ul class="space-y-2 text-orange-100">
                        <li><a href="{{ route('cms.show', 'privacy-policy') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center"><i class="fas fa-shield-alt mr-2 text-xs"></i>Privacy Policy</a></li>
                        <li><a href="{{ route('cms.show', 'refund-policy') }}" class="hover:text-divine-gold transition-colors duration-300 flex items-center"><i class="fas fa-undo mr-2 text-xs"></i>Refund Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4 text-divine-gold flex items-center">
                        <i class="fas fa-phone mr-2"></i>{{ __('messages.contact') }}
                    </h4>
                    @if(\App\Models\FooterSetting::get('contact_email'))
                        <p class="text-orange-100 flex items-center mb-2">
                            <i class="fas fa-envelope mr-2 text-divine-gold"></i>
                            {{ \App\Models\FooterSetting::get('contact_email') }}
                        </p>
                    @endif
                    @if(\App\Models\FooterSetting::get('contact_phone'))
                        <p class="text-orange-100 flex items-center mb-2">
                            <i class="fas fa-phone mr-2 text-divine-gold"></i>
                            {{ \App\Models\FooterSetting::get('contact_phone') }}
                        </p>
                    @endif
                    @if(\App\Models\FooterSetting::get('address'))
                        <p class="text-orange-100 mt-2 flex items-start">
                            <i class="fas fa-map-marker-alt mr-2 text-divine-gold mt-1"></i>
                            <span>{!! nl2br(e(\App\Models\FooterSetting::get('address'))) !!}</span>
                        </p>
                    @endif

                    @php
                        $socialLinks = [
                            'facebook_url' => 'fab fa-facebook-f',
                            'twitter_url' => 'fab fa-twitter',
                            'instagram_url' => 'fab fa-instagram',
                            'youtube_url' => 'fab fa-youtube'
                        ];
                        $hasSocialLinks = collect($socialLinks)->keys()->some(fn($key) => \App\Models\FooterSetting::get($key));
                    @endphp

                    @if($hasSocialLinks)
                        <div class="mt-4">
                            <h5 class="font-bold mb-2 text-divine-gold">Follow Our Journey</h5>
                            <div class="flex space-x-3">
                                @foreach($socialLinks as $key => $icon)
                                    @if(\App\Models\FooterSetting::get($key))
                                        <a href="{{ \App\Models\FooterSetting::get($key) }}" target="_blank" class="text-orange-100 hover:text-divine-gold transition-colors duration-300 bg-white/10 p-2 rounded-full hover:bg-divine-gold/20">
                                            <i class="{{ $icon }} text-lg"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="border-t border-divine-gold/30 mt-8 pt-8 text-center">
                <div class="flex items-center justify-center mb-4">
                    <span class="text-divine-gold text-2xl mr-2">ॐ</span>
                    <span class="text-orange-100">May the stars guide your path to prosperity</span>
                    <span class="text-divine-gold text-2xl ml-2">ॐ</span>
                </div>
                <p class="text-orange-100">{{ \App\Models\FooterSetting::get('copyright_text', '© 2024 AstroServices. ' . __('messages.all_rights_reserved')) }}</p>
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

// Mobile menu functions
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('mobile-menu-icon');
    const closeIcon = document.getElementById('mobile-close-icon');
    
    mobileMenu.classList.toggle('hidden');
    menuIcon.classList.toggle('hidden');
    closeIcon.classList.toggle('hidden');
}

function toggleMobileServices() {
    const servicesMenu = document.getElementById('mobile-services-menu');
    const servicesIcon = document.getElementById('mobile-services-icon');
    
    servicesMenu.classList.toggle('hidden');
    servicesIcon.classList.toggle('rotate-180');
}

function toggleMobileLanguage() {
    const dropdown = document.getElementById('mobile-language-menu');
    dropdown.classList.toggle('hidden');
}

function toggleMobileCurrency() {
    const dropdown = document.getElementById('mobile-currency-menu');
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
    const mobileLanguageDropdown = document.getElementById('mobile-language-dropdown');
    const mobileLanguageMenu = document.getElementById('mobile-language-menu');
    const mobileCurrencyDropdown = document.getElementById('mobile-currency-dropdown');
    const mobileCurrencyMenu = document.getElementById('mobile-currency-menu');

    if (servicesDropdown && !servicesDropdown.contains(event.target)) {
        servicesMenu.classList.add('hidden');
    }

    if (languageDropdown && !languageDropdown.contains(event.target)) {
        languageMenu.classList.add('hidden');
    }

    if (currencyDropdown && !currencyDropdown.contains(event.target)) {
        currencyMenu.classList.add('hidden');
    }

    if (mobileLanguageDropdown && !mobileLanguageDropdown.contains(event.target)) {
        mobileLanguageMenu.classList.add('hidden');
    }

    if (mobileCurrencyDropdown && !mobileCurrencyDropdown.contains(event.target)) {
        mobileCurrencyMenu.classList.add('hidden');
    }
});

// Close mobile menu when window is resized to desktop
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('mobile-menu-icon');
        const closeIcon = document.getElementById('mobile-close-icon');
        
        mobileMenu.classList.add('hidden');
        menuIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    }
});
</script>
</body>
</html>
