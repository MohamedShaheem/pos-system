<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Jewel Plaza</title>
  <link rel="shortcut icon" href="{{asset('media/logo.png')}}" type="image/x-icon">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

  <!-- Gold Rates Ticker in Navbar -->
{{-- <ul class="navbar-nav ml-auto align-items-center">
    <li class="nav-item mx-2 text-light">
        @foreach($goldRates as $rate)
            <span class="mx-3 border rounded px-3 py-2 bg-dark text-light shadow-sm">
                <strong class="text-success">{{ $rate->name }} :</strong>
                <span class="text-warning text-bold">Rs.{{ number_format($rate->rate_per_pawn, 2) }}/pawn | </span> 
                <span class="text-info text-bold">Rs.{{ number_format($rate->rate, 2) }}/g</span>
                <small >({{ $rate->updated_at->format('d M Y') }})</small>
            </span>
        @endforeach
    </li>
</ul> --}}

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
          <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          @if (Auth::user()->role->name == 'superadmin')
          <a href="{{route('profile.edit')}}" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> Profile
          </a>
          @endif
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}" class="dropdown-item">
            @csrf
            <a href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();">
              <i class="fas fa-sign-out-alt mr-2"></i>Sign out
            </a>
          </form>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
      <img src="{{asset('media/logo.png')}}" alt="Jewllery Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">POS - Jewel Plaza</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <!-- Dashboard -->
          <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>POS</p>
            </a>
          </li>

          {{-- purchase old gold --}}
          <li class="nav-item {{ request()->routeIs('purchase-old-gold.*') || request()->routeIs('purchase-old-gold.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('purchase-old-gold.*') || request()->routeIs('purchase-old-gold.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-archive"></i>
              <p>
                Purchase Old Gold
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('purchase-old-gold.index') }}"
                  class="nav-link {{ request()->routeIs('purchase-old-gold.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Purchase Old Gold List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('purchase-old-gold.create') }}"
                  class="nav-link {{ request()->routeIs('purchase-old-gold.create') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Purchase Old Gold</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- Products Menu -->
          <li class="nav-item {{ request()->routeIs('products.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-box"></i>
              <p>
                Products
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List Products</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('products.createOrEdit') }}" class="nav-link {{ request()->routeIs('products.createOrEdit') && !request()->product ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Product</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('products.merge.index') }}" class="nav-link {{ request()->routeIs('products.merge.*') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Merge Products</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('products.weight.adjust') }}"
                  class="nav-link {{ request()->routeIs('products.weight.adjust') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Weight Products</p>
                </a>
              </li>
            </ul>
          </li>

                     <!-- Customers Menu -->
          <li class="nav-item {{ request()->routeIs('customers.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Customers
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('customers.create') }}" class="nav-link {{ request()->routeIs('customers.create') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Customer</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Customer List</p>
                </a>
              </li>
            </ul>
          </li>
          
          <!-- Invoice -->
          {{-- <li class="nav-item">
            <a href="{{ route('reservation.index') }}" class="nav-link {{ request()->routeIs('reservation.index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-receipt"></i>
              <p>Reservation</p>
            </a>
          </li> --}}



        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper p-4">
    <!-- Content Header (Page header) -->
    @yield('content')
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; {{ date('Y')}} Jewel Plaza.</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

</div>
<!-- ./wrapper -->
   <script>
    // Configure toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif


        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif


        @if ($errors->any())
            toastr.error("Please fix the errors and try again.");
        @endif
    </script>
<!-- jQuery -->
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<script></script>

</body>
</html>
