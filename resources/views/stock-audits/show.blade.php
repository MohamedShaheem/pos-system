@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)

@section('content')
<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Stock Audit Results</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-audits.index') }}">Stock Audits</a></li>
                    <li class="breadcrumb-item active">Results</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content pb-3">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- Audit Summary Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="bi bi-clipboard-check"></i> 
                    Audit Summary - {{ $audit->audit_reference }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Audit Type:</strong><br>
                        @if($audit->audit_type === 'all')
                            <span class="badge badge-primary" style="font-size: 14px;">
                                <i class="bi bi-collection"></i> Complete Inventory
                            </span>
                        @else
                            <span class="badge badge-info" style="font-size: 14px;">
                                <i class="bi bi-tag"></i> Category Audit
                            </span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Category:</strong><br>
                        @if($audit->category)
                            <span class="badge badge-secondary" style="font-size: 14px;">{{ $audit->category->name }}</span>
                        @else
                            <span class="text-muted">All Categories</span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong><br>
                        <span class="badge badge-{{ $audit->status == 'completed' ? 'success' : 'warning' }}" style="font-size: 14px;">
                            {{ ucfirst($audit->status) }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Created By:</strong><br>
                        {{ $audit->creator->name }}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Started:</strong><br>
                        {{ $audit->started_at->format('d M Y, h:i A') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Completed:</strong><br>
                        {{ $audit->completed_at ? $audit->completed_at->format('d M Y, h:i A') : 'In Progress' }}
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <div class="info-box-content">
                                <span class="info-box-text">Expected in System</span>
                                <span class="info-box-number">{{ $audit->expected_count }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <div class="info-box-content">
                                <span class="info-box-text">Actually Scanned</span>
                                <span class="info-box-number">{{ $audit->scanned_count }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <div class="info-box-content">
                                <span class="info-box-text">Missing Products</span>
                                <span class="info-box-number">{{ $missingProducts->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <div class="info-box-content">
                                <span class="info-box-text">Extra/Wrong Category</span>
                                <span class="info-box-number">{{ $extraProducts->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Missing Products - THIS IS THE KEY SECTION -->
        @if($missingProducts->count() > 0)
        <div class="card mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                <h3 class="card-title">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Missing Products ({{ $missingProducts->count() }})
                </h3>
                <div class="card-tools">
                    <button class="btn btn-light btn-sm" onclick="printMissingProducts()">
                        <i class="bi bi-printer"></i> Print List
                    </button>
                    {{-- <button class="btn btn-light btn-sm" onclick="exportMissingProducts()">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <i class="bi bi-info-circle"></i>
                    <strong>These products are in your system but were NOT found during physical audit:</strong>
                </div>
                <table class="table table-bordered table-hover" id="missing-products-table">
                    <thead class="thead-dark">
                        <tr>
                            <th>Product No</th>
                            <th>Name</th>
                            @if($audit->audit_type === 'all')
                                <th>Category</th>
                            @endif
                            <th>Supplier</th>
                            <th>Net Weight</th>
                            <th>Wastage</th>
                            <th>Stone</th>
                            <th>Gold Rate</th>
                            <th>Making Charges</th>
                            <th>Total Value</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalValue = 0;
                        @endphp
                        @foreach($missingProducts as $product)
                        @php
                            $goldRate = $product->goldRate?->rate ?? 0;
                            $netWeight = $product->weight ?? 0;
                            $wastage = $product->wastage_weight ?? 0;
                            $stone = $product->stone_weight ?? 0;
                            $making = $product->making_charges ?? 0;
                            
                            $goldValue = ($netWeight + $wastage) * $goldRate;
                            $totalProductValue = $goldValue + $making;
                            $totalValue += $totalProductValue;
                        @endphp
                        <tr class="table-danger">
                            <td><strong>{{ $product->product_no }}</strong></td>
                            <td>{{ $product->name }}</td>
                            @if($audit->audit_type === 'all')
                                <td>
                                    <span class="badge badge-secondary">{{ $product->category?->name ?? 'N/A' }}</span>
                                </td>
                            @endif
                            <td>
                                @if($product->supplier)
                                    <span class="badge badge-secondary">{{ $product->supplier->short_code }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ number_format($netWeight, 3) }}</td>
                            <td>{{ number_format($wastage, 3) }}</td>
                            <td>{{ number_format($stone, 3) }}</td>
                            <td>{{ $product->goldRate->name ?? 'N/A' }}</td>
                            <td>{{ number_format($making, 2) }}</td>
                            <td><strong>Rs. {{ number_format($totalProductValue, 2) }}</strong></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="printLabel({{ $product->product_no }})">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <th colspan="{{ $audit->audit_type === 'all' ? '9' : '8' }}" class="text-right">Total Value of Missing Products:</th>
                            <th colspan="2"><strong>Rs. {{ number_format($totalValue, 2) }}</strong></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @else
        <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white">
                <h3 class="card-title">
                    <i class="bi bi-check-circle"></i> 
                    No Missing Products
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    <strong>Great!</strong> All products in the system were found during the audit.
                </div>
            </div>
        </div>
        @endif

        <!-- Extra Products (Wrong Category or Not in System) -->
        @if($extraProducts->count() > 0)
        <div class="card mb-4 border-warning">
            <div class="card-header bg-warning">
                <h3 class="card-title">
                    <i class="bi bi-question-circle"></i> 
                    Extra/Misplaced Products ({{ $extraProducts->count() }})
                </h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i>
                    <strong>These products were scanned but:</strong> 
                    @if($audit->audit_type === 'category')
                        Either don't exist in system OR belong to a different category
                    @else
                        Don't exist in the system
                    @endif
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product No</th>
                            <th>Scanned At</th>
                            <th>Scanned By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($extraProducts as $item)
                        @php
                            $product = \App\Models\Product::where('product_no', $item->product_no)->first();
                        @endphp
                        <tr>
                            <td><strong>{{ $item->product_no }}</strong></td>
                            <td>{{ $item->scanned_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $item->scanner->name }}</td>
                            <td>
                                @if(!$product)
                                    <span class="badge badge-danger">Not in System</span>
                                @elseif($audit->audit_type === 'category' && $product->product_category_id != $audit->product_category_id)
                                    <span class="badge badge-warning">Wrong Category: {{ $product->category->name }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Successfully Scanned Products -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h3 class="card-title">
                    <i class="bi bi-check-all"></i> 
                    Successfully Scanned Products ({{ $scannedProducts->count() }})
                </h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm" id="scanned-products-table">
                    <thead>
                        <tr>
                            <th>Product No</th>
                            <th>Product Name</th>
                            @if($audit->audit_type === 'all')
                                <th>Category</th>
                            @endif
                            <th>Scanned At</th>
                            <th>Scanned By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scannedProducts as $item)
                        @php
                            $product = \App\Models\Product::where('product_no', $item->product_no)->first();
                        @endphp
                        <tr>
                            <td>{{ $item->product_no }}</td>
                            <td>{{ $product?->name ?? 'N/A' }}</td>
                            @if($audit->audit_type === 'all')
                                <td>
                                    <span class="badge badge-secondary">{{ $product?->category?->name ?? 'N/A' }}</span>
                                </td>
                            @endif
                            <td>{{ $item->scanned_at->format('d M, h:i A') }}</td>
                            <td>{{ $item->scanner->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mb-4">
            <a href="{{ route('stock-audits.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Audits
            </a>
            @if($audit->status == 'in_progress')
            <a href="{{ route('stock-audits.scan', $audit->id) }}" class="btn btn-warning">
                <i class="bi bi-upc-scan"></i> Continue Scanning
            </a>
            @endif
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#missing-products-table').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": false,
        });

        $('#scanned-products-table').DataTable({
            "paging": true,
            "pageLength": 25,
            "searching": true,
            "ordering": true,
        });
    });

    function printMissingProducts() {
        var printContent = document.getElementById('missing-products-table').outerHTML;
        var printWindow = window.open('', '', 'height=600,width=800');
        
        printWindow.document.write('<html><head><title>Missing Products Report</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
        printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }');
        printWindow.document.write('th { background-color: #dc3545; color: white; }');
        printWindow.document.write('h2 { text-align: center; color: #dc3545; }');
        printWindow.document.write('.info { margin-bottom: 20px; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h2>Missing Products Report</h2>');
        printWindow.document.write('<div class="info">');
        printWindow.document.write('<p><strong>Audit Reference:</strong> {{ $audit->audit_reference }}</p>');
        @if($audit->audit_type === 'category')
        printWindow.document.write('<p><strong>Category:</strong> {{ $audit->category->name }}</p>');
        @else
        printWindow.document.write('<p><strong>Audit Type:</strong> Complete Inventory Audit</p>');
        @endif
        printWindow.document.write('<p><strong>Date:</strong> {{ now()->format("d M Y, h:i A") }}</p>');
        printWindow.document.write('<p><strong>Total Missing:</strong> {{ $missingProducts->count() }} products</p>');
        printWindow.document.write('</div>');
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }

    function exportMissingProducts() {
        alert('Excel export functionality coming soon!');
        // You can implement CSV/Excel export here
    }

    function printLabel(productNo) {
        alert('Print label for product: ' + productNo);
        // Implement your label printing logic here
    }
</script>
@endsection