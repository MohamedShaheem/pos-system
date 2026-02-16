@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
<section class="pb-3">
<div class="content-header">
    <div class="container">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Order Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right m-0">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pos_orders.index') }}">POS Orders</a></li>
                    <li class="breadcrumb-item active">Order Details</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container">
        <div class="card shadow-sm">
            <!-- Improved Card Header -->
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-primary fw-bold">Order #{{ $posOrder->invoice_no }}</h3>
                    <div class="d-flex gap-2">
                       @php
                        $routeName = 'customer-transaction';
                       @endphp
                       <a href="{{ route('print.invoice', [$posOrder, 'routeName' => $routeName]) }}" class="btn btn-success" title="Print">
                            <i class="fas fa-print me-1"></i> Print Invoice
                        </a>
                        <a href="{{ route('customer.transactions',$posOrder->customer->id ) }}" class="btn btn-secondary btn-sm">
                            Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Customer and Order Information Section -->
                <div class="row mb-4">
                    <!-- Customer Information -->
                    <div class="col-lg-6 col-md-12 mb-3">
                        <div class="info-section">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-user me-2"></i> Customer Information
                            </h5>
                            <div class="info-content">
                                <div class="row mb-2">
                                    <div class="col-4 col-sm-3">
                                        <strong class="text-muted">Name:</strong>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        {{ $posOrder->customer->name }}
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 col-sm-3">
                                        <strong class="text-muted">Address:</strong>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        {{ $posOrder->customer->address }}
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 col-sm-3">
                                        <strong class="text-muted">Contact:</strong>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        {{ $posOrder->customer->tel ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-6 col-md-12 mb-3">
                        <div class="info-section">
                            <h5 class="section-title mb-3">
                                <i class="fas fa-file-invoice me-2"></i> Order Summary
                            </h5>
                            <div class="info-content">
                                <div class="row mb-2">
                                    <div class="col-4 col-sm-3">
                                        <strong class="text-muted">Date:</strong>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        {{ $formattedDate }}
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 col-sm-3">
                                        <strong class="text-muted">Status:</strong>
                                    </div>
                                    <div class="col-8 col-sm-9">
                                        @if($posOrder->status == 'complete')
                                            <span class="badge bg-success">Complete</span>
                                        @elseif($posOrder->status == 'pending')
                                            <span class="badge bg-info">Pending</span>
                                        @elseif($posOrder->status == 'hold')
                                            <span class="badge bg-warning text-dark">On Hold</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details Table Section -->
                <div class="order-details-section">
                    <h5 class="section-title mb-3">
                        <i class="fas fa-list-ul me-2"></i>Order Details
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="fw-semibold">Description</th>
                                    <th class="fw-semibold">Ct.</th>
                                    <th class="fw-semibold">Net Wt</th>
                                    <th class="fw-semibold">Wastage</th>
                                    <th class="text-end fw-semibold">Amount (Rs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Product Rows -->
                                @foreach($posOrder->orderDetails as $detail)
                                    <tr>
                                        <td class="fw-medium">{{ $detail->product->name }} ({{ $detail->product->product_no }})</td>
                                        <td>{{ $detail->product->goldRate->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($detail->weight, 3) }}</td>
                                        <td>{{ number_format($detail->wastage_weight, 3) }}</td>
                                        <td class="text-end fw-medium">{{ number_format($detail->amount, 2) }}</td>
                                    </tr>
                                @endforeach

                                <!-- Exchange Gold Rows -->
                                @if($posOrder->customerGoldExchanges->isNotEmpty())
                                    @foreach($posOrder->customerGoldExchanges as $exchange)
                                        <tr class="advance-row">
                                            <td class="fw-bold text-primary">Exchange Gold</td>
                                            <td>{{ $exchange->goldRate->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($exchange->gold_weight, 3) }}</td>
                                            <td>0.000</td>
                                            <td class="text-end fw-bold text-primary">- {{ number_format($exchange->gold_purchased_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif

                                <!-- Gold Advance Used Row -->
                                @if($goldAdvanceUsed > 0)
                                    <tr class="advance-row">
                                        <td class="fw-bold text-primary">
                                            A/D Gold
                                            @if($goldAdvanceOrderNo)
                                                <br><small class="text-muted fst-italic">(Order No: {{ $goldAdvanceOrderNo }})</small>
                                            @endif
                                        </td>
                                        <td>{{ $goldAdvanceUsedGoldRateName ?? 'N/A' }}</td>
                                        <td class="text-danger">- {{ number_format($goldAdvanceUsed, 3) }}</td>
                                        <td></td>
                                        <td class="text-end amount-hidden">{{ number_format($goldAdvanceAmount, 2) }}</td>
                                    </tr>
                                @endif

                                @php
                                    // Calculate total exchange gold amount and weight
                                    $totalExchangeGoldWeight = $posOrder->customerGoldExchanges->sum('gold_weight');
                                    $totalExchangeGoldAmount = $posOrder->customerGoldExchanges->sum('gold_purchased_amount');

                                    // Adjusted net weight after gold advance and exchange gold
                                    $adjustedNetWeight = $totalNetWeight - $goldAdvanceUsed - $totalExchangeGoldWeight;

                                    // Subtotal before cash advance deduction
                                    $subtotalAmount = $totalProductAmount - $goldAdvanceAmount - $totalExchangeGoldAmount;

                                    // Amount after cash advance deduction
                                    $afterAdvancesAmount = $subtotalAmount - $cashAdvanceUsed;
                                @endphp

                                @if($goldAdvanceUsed > 0) 
                                    <!-- Subtotal Row after Gold Advance -->
                                    <tr class="subtotal-row table-light border-top border-2">
                                        <td class="fw-semibold"></td>
                                        <td></td>
                                        <td class="fw-semibold">{{ number_format($adjustedNetWeight, 3) }}</td>
                                        <td class="fw-semibold">{{ number_format($totalWastageWeight, 3) }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($subtotalAmount, 2) }}</td>
                                    </tr>
                                @endif

                                <!-- Cash Advance Used Row -->
                                @if($cashAdvanceUsed > 0)
                                    <tr class="advance-row">
                                        <td class="fw-bold text-primary">
                                            A/D Cash
                                            @if($cashAdvanceOrderNo)
                                                <br><small class="text-muted fst-italic">(Order No: {{ $cashAdvanceOrderNo }})</small>
                                            @endif
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-end fw-bold text-primary">-{{ number_format($cashAdvanceUsed, 2) }}</td>
                                    </tr>
                                @endif

                                <!-- Final Subtotal after all advances -->
                                <tr class="subtotal-row table-light border-top border-2">
                                    <td class="fw-semibold"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="fw-bold">Sub total</td>
                                    <td class="text-end fw-bold">{{ number_format($afterAdvancesAmount, 2) }}</td>
                                </tr>

                                <!-- Cash Payment Row -->
                                <tr>
                                    <td class="fw-semibold"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="fw-bold">
                                        @if($paymentMethod)
                                            Cash payment ({{$paymentMethod}})
                                        @else
                                            Cash payment
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">- {{ number_format($cashPaymentAtPurchase, 2) }}</td>
                                </tr>

                                <!-- Discount Row -->
                                <tr class="empty-row">
                                    <td class="fw-semibold"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="fw-bold">Discount</td>
                                    <td class="text-end fw-bold">- {{ number_format($discount, 2) }}</td>
                                </tr>

                                <!-- Amount Due Row -->
                                <tr class="balance-row table-warning border-top border-3">
                                    <td class="fw-semibold"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="fw-bold fs-5">Amount Due</td>
                                    <td class="text-end fw-bold fs-5 text-dark">{{ number_format($finalBalance, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<style>
    /* Enhanced Styling */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1rem 1.5rem;
    }
    
    .info-section {
        background: #f8f9fa;
        border-radius: 0.375rem;
        padding: 1.25rem;
        height: 100%;
        border-left: 4px solid #0d6efd;
    }
    
    .section-title {
        color: #495057;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .info-content .row {
        margin-bottom: 0.75rem;
    }
    
    .info-content .row:last-child {
        margin-bottom: 0;
    }
    
    .order-details-section {
        margin-top: 2rem;
    }
    
    .table {
        border-radius: 0.375rem;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .table th {
        background: #343a40 !important;
        color: white !important;
        font-weight: 600;
        border: none;
        padding: 0.875rem 0.75rem;
        font-size: 0.9rem;
    }
    
    .table td {
        vertical-align: middle;
        padding: 0.875rem 0.75rem;
        border-color: #e9ecef;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .advance-row {
        color: blue;
    }
    
    .subtotal-row {
        background-color: #f0f0f0;
        font-weight: bold;
    }
    
    .balance-row {
        background-color: #e6f3ff;
        font-weight: bold;
    }
    
    .amount-hidden {
        visibility: hidden;
    }
    
    .empty-row {
        height: 40px;
    }
    
    .table-light {
        background-color: #f8f9fa !important;
    }
    
    .table-warning {
        background-color: #fff3cd !important;
    }
    
    .badge {
        font-size: 0.8rem;
        padding: 0.4em 0.8em;
        font-weight: 500;
    }
    
    .text-end {
        text-align: right !important;
    }
    
    /* Button Enhancements */
    .btn {
        border-radius: 0.375rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    /* Enhanced border and spacing */
    .border-top.border-2 {
        border-top: 2px solid #dee2e6 !important;
    }
    
    .border-top.border-3 {
        border-top: 3px solid #ffc107 !important;
    }
    
    /* Icon styling */
    .fas {
        opacity: 0.8;
    }
    
    /* Responsive Improvements */
    @media (max-width: 768px) {
        .card-header .d-flex {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch !important;
        }
        
        .section-title {
            font-size: 1.1rem;
        }
        
        .info-content .col-4 {
            min-width: 100px;
        }
        
        .table-responsive {
            border-radius: 0.375rem;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
        
        .d-flex.gap-2.justify-content-end {
            justify-content: stretch !important;
            flex-direction: column;
        }
    }
</style>
@endsection