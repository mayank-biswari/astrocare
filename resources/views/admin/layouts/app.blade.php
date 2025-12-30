<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin Panel') - AstroServices</title>
  @if(\App\Models\SiteSetting::get('site_icon'))
    <link rel="icon" type="image/x-icon" href="{{ \App\Models\SiteSetting::get('site_icon') }}">
  @endif

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications -->
      <li class="nav-item dropdown">
        @php $unreadCount = \App\Models\Notification::getUnreadCount(); @endphp
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          @if($unreadCount > 0)
            <span class="badge badge-warning navbar-badge">{{ $unreadCount }}</span>
          @endif
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">{{ $unreadCount }} Notifications</span>
          @if($unreadCount > 0)
            @php $recentNotifications = \App\Models\Notification::getRecent(); @endphp
            @foreach($recentNotifications as $notification)
              <div class="dropdown-divider"></div>
              <a href="{{ route('admin.notifications.read', $notification->id) }}" class="dropdown-item">
                <i class="fas fa-{{ $notification->type === 'contact' ? 'envelope' : ($notification->type === 'consultation' ? 'comments' : 'star') }} mr-2"></i>
                {{ Str::limit($notification->title, 25) }}
                <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
              </a>
            @endforeach
            <div class="dropdown-divider"></div>
            <a href="{{ route('admin.notifications') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
          @else
            <div class="dropdown-divider"></div>
            <span class="dropdown-item text-center text-muted">No new notifications</span>
          @endif
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i> {{ auth()->user()->name }}
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="{{ route('home') }}" class="dropdown-item">
            <i class="fas fa-home mr-2"></i> View Site
          </a>
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}" class="dropdown-item">
            @csrf
            <button type="submit" class="btn btn-link p-0 text-left w-100">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
          </form>
        </div>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
      @if(\App\Models\SiteSetting::get('site_logo'))
        <img src="{{ \App\Models\SiteSetting::get('site_logo') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8; max-height: 33px;">
      @endif
      <span class="brand-text font-weight-light">AstroServices Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-box"></i>
              <p>Products</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>Orders</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.consultations') }}" class="nav-link {{ request()->routeIs('admin.consultations*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-comments"></i>
              <p>Consultations</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.admins') }}" class="nav-link {{ request()->routeIs('admin.admins*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>Admin Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tags"></i>
              <p>Categories</p>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.cms*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('admin.cms*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>
                CMS
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.cms.pages') }}" class="nav-link {{ request()->routeIs('admin.cms.pages*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Pages</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.cms.categories') }}" class="nav-link {{ request()->routeIs('admin.cms.categories*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Categories</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.cms.page-types') }}" class="nav-link {{ request()->routeIs('admin.cms.page-types*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Page Types</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.cms.comments') }}" class="nav-link {{ request()->routeIs('admin.cms.comments*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Comments</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.languages') }}" class="nav-link {{ request()->routeIs('admin.languages*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-language"></i>
              <p>Languages</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.currencies') }}" class="nav-link {{ request()->routeIs('admin.currencies*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-dollar-sign"></i>
              <p>Currencies</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.payment-gateways') }}" class="nav-link {{ request()->routeIs('admin.payment-gateways*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-credit-card"></i>
              <p>Payment Gateways</p>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.contact*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('admin.contact*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-envelope"></i>
              <p>
                Contact
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.contact.submissions') }}" class="nav-link {{ request()->routeIs('admin.contact.submissions*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Submissions</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.contact.settings') }}" class="nav-link {{ request()->routeIs('admin.contact.settings*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Settings</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.footer*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.footer*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Settings
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>General</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.footer.settings') }}" class="nav-link {{ request()->routeIs('admin.footer*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Footer</p>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              @yield('breadcrumb')
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ session('success') }}
          </div>
        @endif
        
        @yield('content')
      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2024 AstroServices.</strong> All rights reserved.
  </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Custom Pagination Styles */
.pagination {
    margin: 0;
}
.pagination .page-link {
    color: #007bff;
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 0.375rem 0.75rem;
    margin-left: -1px;
    line-height: 1.25;
    text-decoration: none;
}
.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}
.pagination .page-item.active .page-link {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}
.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}
</style>

<script>
// SweetAlert delete confirmation
$('.delete-btn').on('click', function(e) {
    e.preventDefault();
    const form = $(this).closest('form');
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>
</body>
</html>