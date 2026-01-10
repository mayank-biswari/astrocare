@php
use App\Services\AdminMenuService;
$menuItems = AdminMenuService::getMenuItems();
@endphp

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        @foreach($menuItems as $item)
            @if(isset($item['children']))
                <li class="nav-item {{ collect($item['children'])->pluck('active')->contains(function($pattern) { return request()->routeIs($pattern); }) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ collect($item['children'])->pluck('active')->contains(function($pattern) { return request()->routeIs($pattern); }) ? 'active' : '' }}">
                        <i class="nav-icon {{ $item['icon'] }}"></i>
                        <p>
                            {{ $item['title'] }}
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @foreach($item['children'] as $child)
                            <li class="nav-item">
                                <a href="{{ route($child['route']) }}" class="nav-link {{ request()->routeIs($child['active']) ? 'active' : '' }}">
                                    <i class="{{ $child['icon'] }} nav-icon"></i>
                                    <p>{{ $child['title'] }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                <li class="nav-item">
                    <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                        <i class="nav-icon {{ $item['icon'] }}"></i>
                        <p>{{ $item['title'] }}</p>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</nav>