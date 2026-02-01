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
<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
      @include('admin.partials.menu')
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
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@stack('scripts')

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
