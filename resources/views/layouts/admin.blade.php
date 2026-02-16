<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Jewel Plaza - LOCAL</title>
  <link rel="shortcut icon" href="{{asset('media/logo.png')}}" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
<style>

  .main-sidebar .nav-link {
    font-weight: 400 !important;
  }
  
  .content-wrapper {
    padding-top: 35px;
  }

  .main-header {
    z-index: 1030;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  /* Notification bell icon styling */
.main-header .navbar-nav > .nav-item > .nav-link > .fas.fa-bell {
    font-size: 20px !important;
    color: #272727 !important;
}

.main-header .navbar-badge {
    background-color: #ff0000 !important;
    color: #fff !important;
    font-size: 12px !important;
    font-weight: bold !important;
    padding: 3px 6px !important;
    border-radius: 50% !important;
    top: 5px !important;
    right: 5px !important;
}

/* Notification dropdown styles */
.dropdown-menu-lg {
    min-width: 300px;
    max-height: 400px;
    overflow-y: auto;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
}

.dropdown-header {
    font-size: 16px;
    font-weight: 600;
    background-color: #f1f1f1;
    padding: 10px 15px;
    border-bottom: 1px solid #ddd;
}

.dropdown-item {
    padding: 10px 15px;
    font-size: 14px;
    white-space: normal;
}

.dropdown-item:hover {
    background-color: #f0f8ff;
    color: #0056b3;
}

.dropdown-item .text-muted {
    font-size: 12px;
}


</style>


</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed sidebar-collapse">


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

  {{-- <!-- Gold Rates Ticker in Navbar -->
<ul class="navbar-nav ml-auto align-items-center">
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

      <!-- Notification Dropdown -->
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" title="Pending Approvals">
            <i class="fas fa-bell"></i>
            @if(isset($totalPending) && $totalPending > 0)
                <span class="badge badge-warning navbar-badge">{{ $totalPending }}</span>
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <div class="dropdown-divider"></div>

            @forelse($pendingProducts as $product)
                <a href="{{ route('products.approval.index') }}" class="dropdown-item">
                    <i class="fas fa-box mr-2"></i> Product: {{ $product->name }}
                    <br> <small>{{ $product->created_at }}</small>
                    <span class="float-right text-muted text-sm">Pending</span>
                </a>
            @empty
            @endforelse

            @forelse($pendingMerges as $merge)
                <a href="{{ route('products.merge.approval.show', $merge->id) }}" class="dropdown-item">
                    <i class="fas fa-compress-arrows-alt mr-2"></i> Merge Request #{{ $merge->id }}
                    <br> <small>{{ $merge->created_at }}</small>
                    <span class="float-right text-muted text-sm">Pending</span>
                </a>
            @empty
            @endforelse

            @if($totalPending === 0)
                <span class="dropdown-item text-muted text-center">No pending approvals</span>
            @endif
        </div>
    </li>




      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
          <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <div class="dropdown-divider"></div>
          {{-- @if (Auth::user()->role->name == 'superadmin')
          <a href="{{route('profile.edit')}}" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> Profile
          </a>
          @endif --}}
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

         {{-- approvals --}}
          <li class="nav-item {{ request()->routeIs('products.merge.approval.*') || request()->routeIs('products.approval.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('products.merge.approval.*') || request()->routeIs('products.approval.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-check-circle"></i>
              <p>
                Approvals
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('products.merge.approval.index') }}"
                  class="nav-link {{ request()->routeIs('products.merge.approval.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Merge Approval</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('products.approval.index') }}"
                  class="nav-link {{ request()->routeIs('products.approval.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Product Approval</p>
                </a>
              </li>
            </ul>
          </li>

         {{-- Products --}}
          <li class="nav-item {{ (request()->routeIs('products.*') && !request()->routeIs('products.merge.approval.*') && !request()->routeIs('products.approval.index')) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ (request()->routeIs('products.*') && !request()->routeIs('products.merge.approval.*') && !request()->routeIs('products.approval.index')) ? 'active' : '' }}">
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
                <a href="{{ route('products.createOrEdit') }}"
                  class="nav-link {{ request()->routeIs('products.createOrEdit') && !request()->product ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Product</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('products.merge.index') }}"
                  class="nav-link {{ request()->routeIs('products.merge.index') ? 'active' : '' }}">
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

         <!-- Category Menu -->
          <li class="nav-item {{ request()->routeIs('categories.*') || request()->routeIs('subcategories.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('categories.*') || request()->routeIs('subcategories.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-th-large"></i> 
              <p>
                Categories
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              {{-- Category Items --}}
              <li class="nav-item">
                <a href="{{ route('categories.createOrEdit') }}" class="nav-link {{ request()->routeIs('categories.createOrEdit') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Category</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Category List</p>
                </a>
              </li>

              {{-- Sub Category Items --}}
              <li class="nav-item">
                <a href="{{ route('subcategories.createOrEdit') }}" class="nav-link {{ request()->routeIs('subcategories.createOrEdit') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Sub Category</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('subcategories.index') }}" class="nav-link {{ request()->routeIs('subcategories.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sub Category List</p>
                </a>
              </li>
            </ul>
          </li>


         
           <!-- Supplier Menu -->
          <li class="nav-item {{ request()->routeIs('suppliers.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-truck"></i>
              <p>
                Suppliers
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
               <li class="nav-item">
                <a href="{{ route('suppliers.createOrEdit') }}" class="nav-link {{ request()->routeIs('suppliers.createOrEdit') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Supplier</p>
                </a>
              </li>
             <li class="nav-item">
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Supplier List</p>
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


          <!-- Gold Rates Menu -->
          <li class="nav-item {{ request()->routeIs('gold_rates.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('gold_rates.*') ? 'active' : '' }}">
             <i class="nav-icon fas fa-gem"></i>
              <p>
                Gold / Silver Rates
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <li class="nav-item">
                <a href="{{ route('gold_rates.index') }}" class="nav-link {{ request()->routeIs('gold_rates.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Rates</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('gold_rates.createOrEdit') }}" class="nav-link {{ request()->routeIs('gold_rates.createOrEdit') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Gold / Silver Rate</p>
                </a>
              </li>
              
            </ul>
          </li>



           <!-- Gold Balance Menu -->
          <li class="nav-item {{ request()->routeIs('gold_balance.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->routeIs('gold_balance.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-coins"></i>
              <p>
                Gold balances
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('gold_balance.create') }}" class="nav-link {{ request()->routeIs('gold_balance.create') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Gold Balace</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('gold_balance.index') }}" class="nav-link {{ request()->routeIs('gold_balance.index') ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Gold balances</p>
                </a>
              </li>
              <li class="nav-item">
                  <a href="{{ route('gold_balance_form.daily_report_form') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Daily Report</p>
                  </a>
              </li>
            </ul>
          </li>

          
          <!-- customer management -->
          <li class="nav-item">
            <a href="{{ route('customer.management.index') }}" class="nav-link {{ request()->routeIs('customer.management.index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-cog"></i>
              <p>Customer Management</p>
            </a>
          </li>

          <!-- Invoice -->
          <li class="nav-item">
            <a href="{{ route('pos_orders.index') }}" class="nav-link {{ request()->routeIs('pos_orders.index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-receipt"></i>
              <p>Invoice History</p>
            </a>
          </li>

          <!-- Invoice -->
          <li class="nav-item">
            <a href="{{ route('reservation.index') }}" class="nav-link {{ request()->routeIs('reservation.index') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tags"></i>
              <p>Reservation</p>
            </a>
          </li>


          <!-- Reports Menu -->
          <li class="nav-item {{ request()->routeIs('reports.*') || request()->routeIs('pos_orders.details') || request()->routeIs('gold_balance_form.daily_report_form') ? 'menu-open' : '' }}">
              <a href="#" class="nav-link {{ request()->routeIs('reports.*') || request()->routeIs('pos_orders.details') || request()->routeIs('gold_balance_form.daily_report_form') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-chart-bar"></i>
                  <p>
                      Reports
                      <i class="fas fa-angle-left right"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview">
                  <li class="nav-item">
                      <a href="{{ route('pos_orders.details') }}" 
                        class="nav-link {{ request()->routeIs('pos_orders.details') ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Sales Report</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ route('reports.stock_ledger_summary') }}" 
                        class="nav-link {{ request()->routeIs('reports.stock_ledger_summary') ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Stock Ledger Summary</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="{{ route('gold_balance_form.daily_report_form') }}" 
                        class="nav-link {{ request()->routeIs('gold_balance_form.daily_report_form') ? 'active' : '' }}">
                          <i class="far fa-circle nav-icon"></i>
                          <p>Gold Balance Report</p>
                      </a>
                  </li>
              </ul>
          </li>


          {{-- Stock Audit --}}
          <li class="nav-item">
              <a href="{{ route('stock-audits.index') }}" class="nav-link {{ request()->routeIs('stock-audits.index') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-clipboard-check"></i>
                  <p>Stock Audits</p>
              </a>
          </li>

          {{-- users --}}

          <li class="nav-item">
              <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                  <i class="nav-icon fas fa-users"></i>
                  <p>Manage Users</p>
              </a>
          </li>


        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
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
