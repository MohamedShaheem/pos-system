@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)

@section('content')
<div class="content-header">
    <div class="container">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0">Customer Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Customer Management</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container pb-4">
        <!-- Customer Search Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title m-0">Select Customer</h3>
            </div>
            <div class="card-body">
                <form id="customerSelectForm">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-8">
                            <label for="customer">Customer</label>
                            <select name="customer_id" class="form-control" id="customer" required>
                                <option value="">Select a customer...</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->name }} - {{ $customer->phone ?? $customer->tel }}
                                        @if($customer->email) - {{ $customer->email }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 text-right">
                            <button id="loadCustomerBtn" class="btn btn-success btn-block" disabled>Load Customer Details</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 10px;
    }
</style>

<!-- jQuery (if not already included globally) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('#customer').select2({
            placeholder: "Search customer by name or phone...",
            allowClear: true,
            width: '100%'
        });

        const loadCustomerBtn = document.getElementById('loadCustomerBtn');

        // Enable button only when a customer is selected
        $('#customer').on('change', function () {
            loadCustomerBtn.disabled = !$(this).val();
        });

        // Redirect on button click
        loadCustomerBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const customerId = $('#customer').val();
            if (customerId) {
                window.location.href = `/customer-transactions/${customerId}`;
            }
        });
    });
</script>
@endsection
