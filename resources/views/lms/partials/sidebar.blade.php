{{-- LMS Sidebar Navigation Partial --}}
{{-- Can be included via @include('lms.partials.sidebar') --}}

<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 lg:static lg:inset-auto transition-transform duration-200 ease-in-out flex flex-col" id="lms-sidebar-partial">
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
                $sidebarUnreadCount = \App\Models\LmsNotification::where('user_id', auth()->id())->whereNull('read_at')->count();
            @endphp
            @if($sidebarUnreadCount > 0)
                <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                    {{ $sidebarUnreadCount > 99 ? '99+' : $sidebarUnreadCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('lms.export') }}"
           class="sidebar-link flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('lms.export') ? 'active' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
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
