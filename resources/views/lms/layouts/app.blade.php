<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - LMS | {{ \App\Models\SiteSetting::get('site_name', 'AstroServices') }}</title>
    @if(\App\Models\SiteSetting::get('site_icon'))
        <link rel="icon" type="image/x-icon" href="{{ \App\Models\SiteSetting::get('site_icon') }}">
    @endif

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }

        /* Sidebar active state */
        .sidebar-link.active {
            background-color: rgba(99, 102, 241, 0.1);
            border-right: 3px solid #6366f1;
            color: #6366f1;
        }

        /* Notification dropdown */
        .notification-dropdown {
            max-height: 320px;
            overflow-y: auto;
        }

        /* Responsive sidebar */
        @media (max-width: 1023px) {
            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 40;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="lms-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 lg:static lg:inset-auto transition-transform duration-200 ease-in-out flex flex-col">
            <!-- Brand -->
            <div class="flex items-center h-16 px-6 border-b border-gray-200 shrink-0">
                <a href="{{ route('lms.dashboard') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                    <span class="text-lg font-semibold text-gray-800">LMS</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto" aria-label="LMS Navigation">
                <a href="{{ route('lms.dashboard') }}"
                   class="sidebar-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('lms.dashboard') ? 'active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center mr-3"></i>
                    Dashboard
                </a>

                <a href="{{ route('lms.leads.index') }}"
                   class="sidebar-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('lms.leads.index') || request()->routeIs('lms.leads.show') || request()->routeIs('lms.leads.edit') ? 'active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <i class="fas fa-users w-5 text-center mr-3"></i>
                    Leads
                </a>

                <a href="{{ route('lms.leads.create') }}"
                   class="sidebar-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('lms.leads.create') ? 'active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <i class="fas fa-user-plus w-5 text-center mr-3"></i>
                    Create Lead
                </a>

                <a href="{{ route('lms.notifications.index') }}"
                   class="sidebar-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('lms.notifications.*') ? 'active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <i class="fas fa-bell w-5 text-center mr-3"></i>
                    Notifications
                    @php
                        $unreadNotificationCount = \App\Models\LmsNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
                    @endphp
                    @if($unreadNotificationCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('lms.export') }}"
                   class="sidebar-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                    <i class="fas fa-file-export w-5 text-center mr-3"></i>
                    Export
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="px-4 py-4 border-t border-gray-200 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-indigo-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Sidebar Overlay (mobile) -->
        <div id="sidebar-overlay" class="sidebar-overlay hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200 h-16 flex items-center px-4 sm:px-6 shrink-0">
                <!-- Mobile menu button -->
                <button type="button" onclick="toggleSidebar()" class="lg:hidden mr-4 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md p-1" aria-label="Toggle sidebar navigation">
                    <i class="fas fa-bars text-lg"></i>
                </button>

                <!-- Page Title -->
                <h1 class="text-lg font-semibold text-gray-800 truncate">
                    @yield('page-title', 'Dashboard')
                </h1>

                <!-- Right side actions -->
                <div class="ml-auto flex items-center space-x-4">
                    <!-- Notification Bell -->
                    <div class="relative" id="notification-bell-container">
                        <button type="button" onclick="toggleNotificationDropdown()" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full" aria-label="View notifications" aria-expanded="false" aria-haspopup="true">
                            <i class="fas fa-bell text-lg"></i>
                            @if($unreadNotificationCount > 0)
                                <span id="notification-badge" class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full transform translate-x-1 -translate-y-1">
                                    {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
                                </span>
                            @else
                                <span id="notification-badge" class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full transform translate-x-1 -translate-y-1 hidden">
                                    0
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50" role="menu">
                            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                                @if($unreadNotificationCount > 0)
                                    <form action="{{ route('lms.notifications.mark-all-read') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark all read</button>
                                    </form>
                                @endif
                            </div>
                            <div class="notification-dropdown divide-y divide-gray-100">
                                @php
                                    $recentNotifications = \App\Models\LmsNotification::where('user_id', auth()->id())
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @forelse($recentNotifications as $notification)
                                    <a href="{{ $notification->lead_id ? route('lms.leads.show', $notification->lead_id) : '#' }}"
                                       class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ $notification->isRead() ? 'opacity-60' : '' }}">
                                        <div class="flex items-start space-x-3">
                                            <div class="shrink-0 mt-0.5">
                                                @if($notification->type === 'new_lead')
                                                    <i class="fas fa-user-plus text-green-500"></i>
                                                @elseif($notification->type === 'status_changed')
                                                    <i class="fas fa-exchange-alt text-blue-500"></i>
                                                @elseif($notification->type === 'follow_up_overdue')
                                                    <i class="fas fa-clock text-red-500"></i>
                                                @else
                                                    <i class="fas fa-info-circle text-gray-400"></i>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 truncate">{{ $notification->title }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ $notification->message }}</p>
                                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                            @if(!$notification->isRead())
                                                <span class="w-2 h-2 bg-indigo-500 rounded-full shrink-0 mt-2"></span>
                                            @endif
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-4 py-6 text-center text-sm text-gray-500">
                                        <i class="fas fa-bell-slash text-gray-300 text-2xl mb-2"></i>
                                        <p>No notifications yet</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="px-4 py-3 border-t border-gray-200">
                                <a href="{{ route('lms.notifications.index') }}" class="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                    View All Notifications
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="relative" id="user-menu-container">
                        <button type="button" onclick="toggleUserMenu()" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-md p-1" aria-label="User menu" aria-expanded="false" aria-haspopup="true">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600 text-xs"></i>
                            </div>
                            <span class="hidden sm:inline font-medium">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50" role="menu">
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-t-lg" role="menuitem">
                                <i class="fas fa-home mr-2"></i> View Site
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-b-lg" role="menuitem">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center" role="alert">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span class="text-sm text-green-700">{{ session('success') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700" aria-label="Dismiss">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center" role="alert">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span class="text-sm text-red-700">{{ session('error') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700" aria-label="Dismiss">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center" role="alert">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                        <span class="text-sm text-yellow-700">{{ session('warning') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="ml-auto text-yellow-500 hover:text-yellow-700" aria-label="Dismiss">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="px-4 sm:px-6 lg:px-8 py-4 border-t border-gray-200 bg-white">
                <p class="text-sm text-gray-500 text-center">
                    &copy; {{ date('Y') }} {{ \App\Models\SiteSetting::get('site_name', 'AstroServices') }}. Lead Management System.
                </p>
            </footer>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Sidebar toggle (mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('lms-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Notification dropdown toggle
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            const userDropdown = document.getElementById('user-dropdown');

            userDropdown.classList.add('hidden');
            dropdown.classList.toggle('hidden');
        }

        // User menu toggle
        function toggleUserMenu() {
            const dropdown = document.getElementById('user-dropdown');
            const notifDropdown = document.getElementById('notification-dropdown');

            notifDropdown.classList.add('hidden');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const notifContainer = document.getElementById('notification-bell-container');
            const userContainer = document.getElementById('user-menu-container');
            const notifDropdown = document.getElementById('notification-dropdown');
            const userDropdown = document.getElementById('user-dropdown');

            if (!notifContainer.contains(event.target)) {
                notifDropdown.classList.add('hidden');
            }
            if (!userContainer.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        });

        // Update notification bell dynamically
        function updateNotificationBell(event) {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.classList.remove('hidden');
                let count = parseInt(badge.textContent) || 0;
                count++;
                badge.textContent = count > 9 ? '9+' : count;
            }

            // Optionally show a toast notification
            showToast(event.title, event.message);
        }

        // Simple toast notification
        function showToast(title, message) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm z-50 transform transition-all duration-300 translate-y-0 opacity-100';
            toast.innerHTML = `
                <div class="flex items-start space-x-3">
                    <i class="fas fa-bell text-indigo-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-800">${title}</p>
                        <p class="text-xs text-gray-500 mt-1">${message}</p>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600" aria-label="Dismiss notification">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    </script>

    <!-- Laravel Echo for Real-Time Notifications -->
    @auth
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        const userId = {{ auth()->id() }};

        // Configure Laravel Echo with Pusher
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config("broadcasting.connections.pusher.key", "") }}',
            cluster: '{{ config("broadcasting.connections.pusher.options.cluster", "mt1") }}',
            forceTLS: true,
            encrypted: true,
        });

        // Listen on the private channel for real-time notifications
        window.Echo.private(`lms.notifications.${userId}`)
            .listen('NewLeadCreated', (e) => { updateNotificationBell(e); })
            .listen('LeadStatusChanged', (e) => { updateNotificationBell(e); })
            .listen('FollowUpOverdue', (e) => { updateNotificationBell(e); });
    </script>
    @endauth

    @stack('scripts')
</body>
</html>
