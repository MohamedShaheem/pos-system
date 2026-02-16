@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')

<style>
.container{
    max-width: 90%;
}
.summary-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: none;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #17a2b8, #138496);
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 24px;
    color: white;
}

.icon-orders {
    background: linear-gradient(135deg, #0075fa, #006eff);
}

.icon-advance {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

.icon-reservations {
    background: linear-gradient(135deg, #ffc107, #e0a800);
}

.icon-status {
    background: linear-gradient(135deg, #28a745, #1e7e34);
}

.card-title-custom {
    font-size: 14px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.card-value {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
    line-height: 1.2;
}

.payment-breakdown {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
    margin-top: 15px;
}

.payment-breakdown small {
    font-size: 12px;
    line-height: 1.4;
}


/* Complete the missing CSS styles */
.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.badge-paid {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.badge-partial {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
}

.badge-unpaid {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    color: white;
}

.btn-gradient {
    background: linear-gradient(135deg, #17a2b8, #138496);
    border: none;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.btn-gradient:hover {
    background: linear-gradient(135deg, #006eff, #006eff);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4);
    color: white;
}

.btn-gradient.btn-info {
    background: linear-gradient(135deg, #17a2b8, #138496);
}

.btn-gradient.btn-info:hover {
    background: linear-gradient(135deg, #138496, #17a2b8);
    box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
}

.btn-gradient.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.btn-gradient.btn-success:hover {
    background: linear-gradient(135deg, #20c997, #28a745);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}

.nav-tabs .nav-link {
    border: none;
    color: #666;
    font-weight: 600;
    padding: 15px 20px;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    background: #f8f9fa;
}

.nav-tabs .nav-link.active {
    background: linear-gradient(135deg,  #003af8, #0011f8);
    color: white;
    border-color: transparent;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.table thead th {
    background: linear-gradient(135deg, #006cf8, #006cf8);
    color: white;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 12px;
}


.customer-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid #667eea;
}

.payment-summary {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.modal-header {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
    border-bottom: none;
}

.modal-header .close {
    color: white;
    opacity: 1;
}

.modal-header .close:hover {
    color: white;
    opacity: 0.8;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    border: none;
    border-left: 4px solid #17a2b8;
}

@media (max-width: 768px) {
    .summary-card {
        margin-bottom: 20px;
    }
    
    .card-value {
        font-size: 24px;
    }
    
    .btn-group .btn {
        padding: 5px 8px;
        font-size: 12px;
    }
    
    .table-responsive {
        font-size: 14px;
    }
}

/* Loading spinner */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Badge styles */
.badge {
    font-size: 11px;
    font-weight: 600;
}

.bg-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.bg-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
}

.bg-danger {
    background: linear-gradient(135deg, #dc3545, #e83e8c) !important;
}

.bg-info {
    background: linear-gradient(135deg, #17a2b8, #138496) !important;
}

.bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

.bg-secondary {
    background: linear-gradient(135deg, #6c757d, #545b62) !important;
}

/* Add these CSS styles to your existing styles */

.nav-pills .nav-link {
    border-radius: 20px;
    margin-right: 10px;
    color: #666;
    font-weight: 600;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
    color: #333;
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
}

.gold-text {
    color: #FFD700 !important;
    font-weight: 600;
}

.gold-badge {
    background: linear-gradient(135deg, #FFD700, #FFA500) !important;
    color: white !important;
}


/* Responsive adjustments for advance tables */
@media (max-width: 768px) {
    .table-responsive table {
        font-size: 12px;
    }
    
    .btn-group-sm .btn {
        padding: 2px 6px;
        font-size: 10px;
    }
    
    .nav-pills .nav-link {
        padding: 8px 12px;
        font-size: 12px;
        margin-bottom: 5px;
    }
}
</style>
<section class="content">
    
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title mb-0">Complete Transaction History for <b>{{ $customer->name }}</b></h3>
                    <a href="{{ route('pos_orders.index') }}" class="btn btn-secondary btn-sm ml-auto">Back to Orders</a>
                    <a href="{{ route('customer.management.index') }}" class="btn btn-secondary btn-sm ml-3">Back to CustomerManagement</a>
                </div>

                <div class="card-body">
                    <!-- Customer Information -->
                    <div class="customer-info mb-4">
                        <p class="mb-1"><strong>Customer Details:</strong></p>
                        @if ($customer->name == "Walk-in Customer")
                            <p class="mb-1">{{ $customer->name }}</p>
                        @else
                            <p class="mb-1">Address: {{ $customer->address }}</p>
                            <p class="mb-1">City: {{ $customer->city }}</p>
                            <p class="mb-1">Phone: {{ $customer->tel }}</p>
                        @endif
                    </div>

                    @if($customer->id !== 1)
                    <!-- Enhanced Summary Cards -->
                    <div class="payment-summary mb-4">
                        <div class="row">
                            <!-- Update the Cash Advance Summary Card -->
                            <div class="col-md-3 mb-4">
                                <div class="card summary-card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="card-icon icon-advance">
                                            <i class="fas fa-piggy-bank"></i>
                                        </div>
                                        <h5 class="card-title-custom">A/D CASH</h5>
                                        <h3 class="card-value text-info">Rs {{ number_format($cashAdvanceBalance, 2) }}</h3>
                                        <div class="payment-breakdown">
                                            <small class="text-dark" style="font-size: 13px; font-weight: 600;">
                                                <i class="fas fa-plus text-success"></i> Total Deposits: Rs {{ number_format($cashAdvanceDeposits, 2) }}<br>
                                                <i class="fas fa-minus text-danger"></i> Total Used: Rs {{ number_format($cashAdvanceUsage, 2) }}<br>
                                                <i class="fas fa-undo text-warning"></i> Total Refunded: Rs {{ number_format($cashAdvanceRefunds, 2) }}
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-danger btn-sm mt-2" data-toggle="modal" data-target="#RefundAdvanceModal">
                                            <i class="fas fa-undo"></i> Refund A/D
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add new Gold Advance Summary Card after the Cash Advance card -->
                            <div class="col-md-3 mb-4">
                                <div class="card summary-card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="card-icon" style="background: linear-gradient(135deg, #FFD700, #FFA500);">
                                            <i class="fas fa-coins"></i>
                                        </div>
                                        <h5 class="card-title-custom">A/D GOLD</h5>
                                        <h3 class="card-value" style="color: #FFD700;">{{ number_format($goldAdvanceBalance, 3) }}g</h3>
                                        <div class="payment-breakdown">
                                            <small class="text-dark" style="font-size: 15px; font-weight: 600;">
                                                <i class="fas fa-plus text-success"></i> Total Deposits: {{ number_format($goldAdvanceDeposits, 3) }}g<br>
                                                <i class="fas fa-minus text-danger"></i> Total Used: {{ number_format($goldAdvanceUsage, 3) }}g
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reservations Summary -->
                            <div class="col-md-3 mb-4">
                                <div class="card summary-card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="card-icon icon-reservations">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <h5 class="card-title-custom">Reservations</h5>
                                        <h3 class="card-value text-warning">{{ $totalReservations }}</h3>
                                        <div class="payment-breakdown" >
                                            <small class="text-dark" style="font-size: 15px; font-weight: 600;">
                                                <i class="fas fa-clock text-warning"></i> Total Pending: {{ $pendingReservations }}<br>
                                                <i class="fas fa-check text-success"></i> Total Completed: {{ $completedReservations }}<br>
                                                <i class="fas fa-check text-danger"></i> Total Cancelled: {{ $cancelledReservations }}<br>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Actions -->
                            <div class="col-md-3 mb-4">
                                <div class="card summary-card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="card-icon icon-status">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <h5 class="card-title-custom">Payment Status</h5>
                                        <div class="mb-3">
                                            @if($customer->payment_status == 'paid')
                                                <span class="status-badge badge-paid">
                                                    <i class="fas fa-check"></i> Paid
                                                </span>
                                            @elseif($customer->payment_status == 'partial')
                                                <span class="status-badge badge-partial">
                                                    <i class="fas fa-clock"></i> Partial
                                                </span>
                                            @else
                                                <span class="status-badge badge-unpaid">
                                                    <i class="fas fa-times"></i> Unpaid
                                                </span>
                                            @endif
                                        </div>
                                          @if($customer->payment_status !== 'paid')
                                            <div class="payment-breakdown mb-2" >
                                                <small class="text-dark" style="font-size: 15px; font-weight: 600;">
                                                    <i class="fas fa-clock text-danger"></i> Total Unpaid Amount<br>
                                                    Rs<span class="text-danger" style="font-size: 20px;"> {{ number_format($customer->total_balance, 2) }}</span>
                                                </small>
                                            </div>

                                            <div>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPaymentModal">
                                                    <i class="fas fa-plus-circle"></i> Add Payment
                                                </button>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @endif

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs mb-4" id="transactionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="pos-orders-tab" data-toggle="tab" href="#pos-orders" role="tab">
                                <i class="fas fa-shopping-cart"></i> POS Orders ({{ $posOrders->total() }})
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="reservations-tab" data-toggle="tab" href="#reservations" role="tab">
                                <i class="fas fa-calendar-check"></i> Reservations ({{ $totalReservations }})
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="advances-tab" data-toggle="tab" href="#advances" role="tab">
                                <i class="fas fa-piggy-bank"></i> Advance History
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments" role="tab">
                                <i class="fas fa-money-bill-wave"></i> Payment Logs
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="transactionTabsContent">
                        <!-- POS Orders Tab -->
                        <div class="tab-pane fade show active" id="pos-orders" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Invoice No</th>
                                            <th>Total</th>
                                            <th>A/D Cash</th>
                                            <th>A/D Gold</th>
                                            <th>Total Ex Gold</th>
                                            <th>Discount</th>
                                            <th>Sub Total</th>
                                            <th>Cash Payment</th>
                                            <th>Amount Due</th>
                                            <th>Status</th>
                                            <th>Processed By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($posOrders as $order)
                                            @php
                                                // Cash Advance
                                                $cashAdvanceUsed = $order->advanceUses->sum('amount');
                                                $cashAdvanceOrderNo = optional($order->advanceUses->first()?->customerAdvance)->order_no;

                                                // Gold Advance
                                                $goldAdvanceUsed = $order->goldAdvanceUses->sum('gold_amount');
                                                $goldAdvanceAmount = $order->goldAdvanceUses->sum(function ($use) {
                                                    return ($use->gold_amount ?? 0) * ($use->gold_rate ?? 0);
                                                });
                                                $goldRateUsed = optional($order->goldAdvanceUses->first()?->customerGoldAdvance?->goldRate)->name;

                                                // Exchange Gold
                                                $exchangeGoldWeight = $order->customerGoldExchanges->sum('gold_weight');
                                                $exchangeGoldAmount = $order->customerGoldExchanges->sum('gold_purchased_amount');

                                                // Payments
                                                $cashPayments = $order->payments->sum('amount');

                                                // Totals
                                                $total = $order->total;
                                                $discount = $order->discount;
                                                $balance = $order->balance;

                                                // Subtotal calculation adjusted for cash, gold, and exchange gold advances
                                                $subtotal = $total - $discount - $cashAdvanceUsed - $goldAdvanceAmount - $exchangeGoldAmount;
                                            @endphp

                                            <tr>
                                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                <td>{{ $order->invoice_no }}</td>
                                                <td>Rs {{ number_format($total, 2) }}</td>

                                                {{-- Cash Advance --}}
                                                <td>
                                                    @if($cashAdvanceUsed > 0)
                                                        <span class="text-success">Rs {{ number_format($cashAdvanceUsed, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                {{-- Gold Advance --}}
                                                <td>
                                                    @if($goldAdvanceUsed > 0)
                                                        <span class="text-success">{{ $goldAdvanceUsed }}g</span><br>
                                                        <small>Rs {{ number_format($goldAdvanceAmount, 2) }} 
                                                        @if($goldRateUsed) ({{ $goldRateUsed }}) @endif</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                {{-- Exchange Gold --}}
                                                <td>
                                                    @if($exchangeGoldWeight > 0)
                                                        <span class="text-success">{{ number_format($exchangeGoldWeight, 3) }}g</span><br>
                                                        <small>Rs {{ number_format($exchangeGoldAmount, 2) }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                
                                                {{-- Discount --}}
                                                <td>Rs {{ number_format($discount, 2) }}</td>

                                                {{-- Subtotal --}}
                                                <td>Rs {{ number_format($subtotal, 2) }}</td>

                                                {{-- Cash Payment --}}
                                                <td>Rs {{ number_format($cashPayments, 2) }}</td>

                                                {{-- Balance --}}
                                                <td>Rs {{ number_format($balance, 2) }}</td>

                                                {{-- Status --}}
                                                <td>
                                                    @if($order->status == 'hold')
                                                        <span class="badge bg-warning">On Hold</span>
                                                    @elseif($order->status == 'complete')
                                                        <span class="badge bg-success">Complete</span>
                                                    @elseif($order->status == 'pending')
                                                        <span class="badge bg-info">Pending</span>
                                                    @else
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ $order->processedByUser->name ?? '' }}
                                                </td>
                                                {{-- Actions --}}
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        {{-- <a href="{{ route('pos_orders.show', $order) }}" class="btn btn-info" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a> --}}
                                                        @php
                                                            $routeName = 'customer-transaction';
                                                        @endphp
                                                        <a href="{{ route('print.invoice', [$order, 'routeName' => $routeName]) }}" class="btn btn-success" title="Print">
                                                            <i class="fas fa-print"></i>
                                                        </a>
                                                        {{-- @if($order->remaining_balance > 0 && $cashAdvanceBalance > 0)
                                                            <button type="button" class="btn btn-warning" title="Use Advance" onclick="useAdvance({{ $order->id }}, {{ $order->remaining_balance }})">
                                                                <i class="fas fa-piggy-bank"></i>
                                                            </button>
                                                        @endif
                                                        @if($order->status != 'hold')
                                                            <form action="{{ route('invoice.hold', $order->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-warning" title="Hold">
                                                                    <i class="fas fa-pause"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('invoice.release', $order->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-primary" title="Release">
                                                                    <i class="fas fa-play"></i>
                                                                </button>
                                                            </form>
                                                        @endif --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>                          
                                 <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted">
                                            Showing {{ $posOrders->firstItem() ?? 0 }} to {{ $posOrders->lastItem() ?? 0 }} of {{ $posOrders->total() }} POS Orders
                                        </p>
                                    </div>
                                    <div>
                                        {{ $posOrders->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reservations Tab -->
                        <div class="tab-pane fade" id="reservations" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Products</th>
                                            <th>Total Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                            <th>Delivery Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reservations as $reservation)
                                            <tr>
                                                <td>{{ $reservation->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $reservation->reservationDetails->count() }} items</span>
                                                </td>
                                                <td>Rs {{ number_format($reservation->total_amount, 2) }}</td>
                                                <td>Rs {{ number_format($reservation->paid_amount, 2) }}</td>
                                                <td>Rs {{ number_format($reservation->total_amount - $reservation->paid_amount, 2) }}</td>
                                                <td>
                                                    @if($reservation->status == 'completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($reservation->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($reservation->status == 'cancelled')
                                                        <span class="badge bg-danger">Cancelled</span>
                                                    @endif
                                                </td>
                                                <td>{{ $reservation->delivery_date ? \Carbon\Carbon::parse($reservation->delivery_date)->format('d/m/Y') : '-' }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-info" onclick="viewReservation({{ $reservation->id }})" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if($reservation->status == 'pending')
                                                            <button type="button" class="btn btn-success" onclick="addReservationPayment({{ $reservation->id }})" title="Add Payment">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        @endif
                                                        @if($reservation->status == 'completed' && !$reservation->pos_order_id)
                                                            <button type="button" class="btn btn-primary" onclick="convertToPOS({{ $reservation->id }})" title="Convert to POS">
                                                                <i class="fas fa-exchange-alt"></i>
                                                            </button>
                                                        @endif
                                                        @if($reservation->status == 'pending')
                                                            <button type="button" class="btn btn-danger" onclick="cancelReservation({{ $reservation->id }})" title="Cancel">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- <div class="mt-3">
                                    {{ $reservations->links() }}
                                </div> --}}
                                 <!-- Pagination -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted">
                                            Showing {{ $reservations->firstItem() ?? 0 }} to {{ $reservations->lastItem() ?? 0 }} of {{ $reservations->total() }} Reservations
                                        </p>
                                    </div>
                                    <div>
                                        {{ $reservations->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advances Tab -->                                      
                        <div class="tab-pane fade" id="advances" role="tabpanel">
                            <!-- Sub-navigation pills for different advance types -->
                            <ul class="nav nav-pills mb-3" id="advanceSubTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="cash-only-advances-tab" data-toggle="pill" href="#cash-only-advances" role="tab">
                                        <i class="fas fa-money-bill"></i> Cash Advances Only ({{ $cashOnlyAdvances->count() }})
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="gold-only-advances-tab" data-toggle="pill" href="#gold-only-advances" role="tab">
                                        <i class="fas fa-coins"></i> Gold Advances Only ({{ $goldOnlyAdvances->count() }})
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="cash-gold-advances-tab" data-toggle="pill" href="#cash-gold-advances" role="tab">
                                        <i class="fas fa-handshake"></i> Cash & Gold Advances ({{ $combinedAdvances->count() }})
                                    </a>
                                </li>
                            </ul>

                            <!-- Sub-tab content -->
                            <div class="tab-content" id="advanceSubTabsContent">
                                <!-- Cash Only Advances Sub-tab -->                                                    
                                <div class="tab-pane fade show active" id="cash-only-advances" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Order No</th>
                                                    <th>Type</th>
                                                    <th>Amount (Rs)</th>
                                                    <th>Balance (Rs)</th>
                                                    <th>POS Order</th>
                                                    <th>Notes</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $cashOnlyTransactions = collect();
                                                    
                                                    // Collect cash-only advance transactions
                                                    foreach($cashOnlyAdvances as $advance) {
                                                        $currentBalance = $advance->details->sum('amount') - 
                                                                        $advance->advanceUse->sum('amount') - 
                                                                        $advance->refunds->sum('amount');
                                                        
                                                        // Deposits
                                                        foreach($advance->details as $detail) {
                                                            $cashOnlyTransactions->push([
                                                                'id' => $detail->id,
                                                                'advance_id' => $advance->id,
                                                                'detail_id' => $detail->id,
                                                                'date' => $detail->created_at,
                                                                'note' => $detail->note,
                                                                'order_no' => $advance->order_no,
                                                                'type' => 'deposit',
                                                                'amount' => $detail->amount,
                                                                'balance' => $currentBalance,
                                                                'pos_order' => null,
                                                                // 'notes' => $advance->note,
                                                                'can_print' => true
                                                            ]);
                                                        }
                                                        
                                                        // Uses
                                                        foreach($advance->advanceUse as $use) {
                                                            $cashOnlyTransactions->push([
                                                                'id' => $use->id,
                                                                'advance_id' => $advance->id,
                                                                'detail_id' => null,
                                                                'date' => $use->created_at,
                                                                'order_no' => $advance->order_no,
                                                                'type' => 'usage',
                                                                'amount' => $use->amount,
                                                                'balance' => null,
                                                                'pos_order' => $use->posOrder,
                                                                'notes' => 'Used for order',
                                                                'can_print' => false
                                                            ]);
                                                        }
                                                        
                                                        // Refunds
                                                        foreach($advance->refunds as $refund) {
                                                            $cashOnlyTransactions->push([
                                                                'id' => $refund->id,
                                                                'advance_id' => $advance->id,
                                                                'detail_id' => null,
                                                                'date' => $refund->created_at,
                                                                'order_no' => $advance->order_no,
                                                                'type' => 'refund',
                                                                'amount' => $refund->amount,
                                                                'balance' => null,
                                                                'pos_order' => null,
                                                                'notes' => $refund->notes ?? 'Advance refund',
                                                                'can_print' => false
                                                            ]);
                                                        }
                                                    }
                                                    
                                                    $cashOnlyTransactions = $cashOnlyTransactions->sortByDesc('date');
                                                @endphp
                                                
                                                @forelse($cashOnlyTransactions as $transaction)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @if($transaction['order_no'])
                                                                <a href="javascript:void(0);" 
                                                                    class="btn btn-sm btn-outline-primary order-info-link"
                                                                    data-order-no="{{ $transaction['order_no'] }}">
                                                                        {{ $transaction['order_no'] }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">General</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['type'] == 'deposit')
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-plus"></i> Deposit
                                                                </span>
                                                            @elseif($transaction['type'] == 'refund')
                                                                <span class="badge bg-warning">
                                                                    <i class="fas fa-undo"></i> Refund
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-minus"></i> Usage
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['type'] == 'deposit')
                                                                <span class="text-success">+Rs {{ number_format($transaction['amount'], 2) }}</span>
                                                            @else
                                                                <span class="text-danger">-Rs {{ number_format($transaction['amount'], 2) }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['balance'] !== null)
                                                                <strong>Rs {{ number_format($transaction['balance'], 2) }}</strong>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['pos_order'])
                                                                <a href="{{ route('pos_orders.show', $transaction['pos_order']) }}" class="btn btn-sm btn-outline-primary">
                                                                    {{ $transaction['pos_order']->invoice_no }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $transaction['note'] ?? '-'}}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                @php
                                                                    $routeName = 'customer-transaction';
                                                                @endphp
                                                                
                                                                @if($transaction['can_print'] && $transaction['detail_id'])
                                                                    <a href="{{ route('print.receipt', [
                                                                        'advance' => $transaction['advance_id'], 
                                                                        'detail' => $transaction['detail_id'], 
                                                                        'routeName' => $routeName
                                                                    ]) }}" 
                                                                    class="btn btn-success" 
                                                                    title="Print Receipt"
                                                                    >
                                                                        <i class="fas fa-print"></i>
                                                                    </a>
                                                                @else
                                                                    <button class="btn btn-secondary disabled" 
                                                                            title="Receipt not available for this transaction type"
                                                                            disabled>
                                                                        <i class="fas fa-print"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted">No cash-only advances found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Gold Only Advances Sub-tab -->
                                <div class="tab-pane fade" id="gold-only-advances" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Order No</th>
                                                    <th>Type</th>
                                                    <th>Gold Amount (g)</th>
                                                    <th>Gold Rate</th>
                                                    <th>Balance (g)</th>
                                                    <th>POS Order</th>
                                                    <th>Notes</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $goldOnlyTransactions = collect();
                                                    
                                                    // Collect gold-only advance transactions
                                                    foreach($goldOnlyAdvances as $goldAdvance) {
                                                        $currentBalance = $goldAdvance->details->sum('gold_amount') - 
                                                                        $goldAdvance->goldAdvanceUse->sum('gold_amount');
                                                        
                                                        // Deposits
                                                        foreach($goldAdvance->details as $detail) {
                                                            $goldOnlyTransactions->push([
                                                                'id' => $detail->id,
                                                                'advance_id' => $goldAdvance->id,
                                                                'detail_id' => $detail->id,
                                                                'date' => $detail->created_at,
                                                                'order_no' => $goldAdvance->order_no,
                                                                'type' => 'deposit',
                                                                'gold_amount' => $detail->gold_amount,
                                                                'gold_rate' => $goldAdvance->goldRate,
                                                                'balance' => $currentBalance,
                                                                'pos_order' => null,
                                                                'notes' => $detail->note,
                                                                'can_print' => true
                                                            ]);
                                                        }
                                                        
                                                        // Uses
                                                        foreach($goldAdvance->goldAdvanceUse as $use) {
                                                            $goldOnlyTransactions->push([
                                                                'id' => $use->id,
                                                                'advance_id' => $goldAdvance->id,
                                                                'detail_id' => null,
                                                                'date' => $use->created_at,
                                                                'order_no' => $goldAdvance->order_no,
                                                                'type' => 'usage',
                                                                'gold_amount' => $use->gold_amount,
                                                                'gold_rate' => $goldAdvance->goldRate,
                                                                'balance' => null,
                                                                'pos_order' => $use->posOrder,
                                                                'notes' => 'Used for order',
                                                                'can_print' => false
                                                            ]);
                                                        }
                                                    }
                                                    
                                                    $goldOnlyTransactions = $goldOnlyTransactions->sortByDesc('date');
                                                @endphp
                                                
                                                @forelse($goldOnlyTransactions as $transaction)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @if($transaction['order_no'])
                                                                <a href="javascript:void(0);" 
                                                                    class="btn btn-sm btn-outline-primary order-info-link"
                                                                    data-order-no="{{ $transaction['order_no'] }}">
                                                                        {{ $transaction['order_no'] }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">General</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['type'] == 'deposit')
                                                                <span class="badge bg-success">
                                                                    <i class="fas fa-plus"></i> Deposit
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-minus"></i> Usage
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['type'] == 'deposit')
                                                                <span class="text-success">+{{ number_format($transaction['gold_amount'], 3) }}g</span>
                                                            @else
                                                                <span class="text-danger">-{{ number_format($transaction['gold_amount'], 3) }}g</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['gold_rate'])
                                                                {{ $transaction['gold_rate']->name }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['balance'] !== null)
                                                                <strong>{{ number_format($transaction['balance'], 3) }}g</strong>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['pos_order'])
                                                                <a href="{{ route('pos_orders.show', $transaction['pos_order']) }}" class="btn btn-sm btn-outline-primary">
                                                                    {{ $transaction['pos_order']->invoice_no }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $transaction['notes'] ?? '-' }}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                @php
                                                                    $routeName = 'customer-transaction';
                                                                @endphp
                                                                
                                                                @if($transaction['can_print'] && $transaction['detail_id'])
                                                                    <a href="{{ route('print.receipt.gold', [
                                                                        'advance' => $transaction['advance_id'], 
                                                                        'detail' => $transaction['detail_id'], 
                                                                        'routeName' => $routeName
                                                                    ]) }}" 
                                                                    class="btn btn-success" 
                                                                    title="Print Gold Receipt"
                                                                    >
                                                                        <i class="fas fa-print"></i>
                                                                    </a>
                                                                @else
                                                                    <button class="btn btn-secondary disabled" 
                                                                            title="Receipt not available for this transaction type"
                                                                            disabled>
                                                                        <i class="fas fa-print"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">No gold-only advances found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Cash & Gold Combined Advances Sub-tab -->
                                <div class="tab-pane fade" id="cash-gold-advances" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Order No</th>
                                                    <th>Type</th>
                                                    <th>Cash Amount (Rs)</th>
                                                    <th>Gold Amount (g)</th>
                                                    <th>Gold Rate</th>
                                                    <th>Cash Balance</th>
                                                    <th>Gold Balance</th>
                                                    <th>POS Order</th>
                                                    <th>Notes</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php 
                                                    $combinedTransactions = collect();
                                                    
                                                    // Collect combined advance transactions
                                                    foreach($combinedAdvances as $orderNo => $advances) {
                                                        $cashAdvance = $advances['cash'] ?? null;
                                                        $goldAdvance = $advances['gold'] ?? null;
                                                        
                                                        $cashBalance = $cashAdvance ? 
                                                            ($cashAdvance->details->sum('amount') - 
                                                            $cashAdvance->advanceUse->sum('amount') - 
                                                            $cashAdvance->refunds->sum('amount')) : 0;
                                                            
                                                        $goldBalance = $goldAdvance ? 
                                                            ($goldAdvance->details->sum('gold_amount') - 
                                                            $goldAdvance->goldAdvanceUse->sum('gold_amount')) : 0;
                                                        
                                                        // For combined receipts, we need to collect cash and gold details from the same transaction
                                                        $combinedDeposits = [];
                                                        
                                                        // Group cash deposits by timestamp to match with gold deposits
                                                        if ($cashAdvance) {
                                                            foreach($cashAdvance->details as $cashDetail) {
                                                                $timestamp = $cashDetail->created_at->format('Y-m-d H:i:s');
                                                                if (!isset($combinedDeposits[$timestamp])) {
                                                                    $combinedDeposits[$timestamp] = [
                                                                        'cash_detail' => null,
                                                                        'gold_details' => []
                                                                    ];
                                                                }
                                                                $combinedDeposits[$timestamp]['cash_detail'] = $cashDetail;
                                                            }
                                                        }
                                                        
                                                        // Group gold deposits by timestamp
                                                        if ($goldAdvance) {
                                                            foreach($goldAdvance->details as $goldDetail) {
                                                                $timestamp = $goldDetail->created_at->format('Y-m-d H:i:s');
                                                                if (!isset($combinedDeposits[$timestamp])) {
                                                                    $combinedDeposits[$timestamp] = [
                                                                        'cash_detail' => null,
                                                                        'gold_details' => []
                                                                    ];
                                                                }
                                                                $combinedDeposits[$timestamp]['gold_details'][] = $goldDetail;
                                                            }
                                                        }
                                                        
                                                        // Create combined deposit transactions
                                                        foreach($combinedDeposits as $timestamp => $combined) {
                                                            $cashDetail = $combined['cash_detail'];
                                                            $goldDetails = $combined['gold_details'];
                                                            
                                                            if ($cashDetail || !empty($goldDetails)) {
                                                                $goldDetailIds = collect($goldDetails)->pluck('id')->implode(',');
                                                                
                                                                $combinedTransactions->push([
                                                                    'date' => $cashDetail ? $cashDetail->created_at : $goldDetails[0]->created_at,
                                                                    'order_no' => $orderNo,
                                                                    'type' => 'combined_deposit',
                                                                    'cash_amount' => $cashDetail ? $cashDetail->amount : null,
                                                                    'gold_amount' => collect($goldDetails)->sum('gold_amount') ?: null,
                                                                    'gold_rate' => $goldAdvance ? $goldAdvance->goldRate : null,
                                                                    'cash_balance' => $cashBalance,
                                                                    'gold_balance' => $goldBalance,
                                                                    'pos_order' => null,
                                                                    'notes' => ($cashAdvance ? $cashAdvance->note : ''),
                                                                    'cash_detail_id' => $cashDetail ? $cashDetail->id : null,
                                                                    'gold_detail_ids' => $goldDetailIds,
                                                                    'can_print' => ($cashDetail && !empty($goldDetails))
                                                                ]);
                                                            }
                                                        }
                                                        
                                                        // Cash uses (separate from deposits)
                                                        if ($cashAdvance) {
                                                            foreach($cashAdvance->advanceUse as $use) {
                                                                $combinedTransactions->push([
                                                                    'date' => $use->created_at,
                                                                    'order_no' => $orderNo,
                                                                    'type' => 'cash_usage',
                                                                    'cash_amount' => $use->amount,
                                                                    'gold_amount' => null,
                                                                    'gold_rate' => null,
                                                                    'cash_balance' => null,
                                                                    'gold_balance' => null,
                                                                    'pos_order' => $use->posOrder,
                                                                    'notes' => 'Used for order',
                                                                    'cash_detail_id' => null,
                                                                    'gold_detail_ids' => null,
                                                                    'can_print' => false
                                                                ]);
                                                            }
                                                            
                                                            // Cash refunds
                                                            foreach($cashAdvance->refunds as $refund) {
                                                                $combinedTransactions->push([
                                                                    'date' => $refund->created_at,
                                                                    'order_no' => $orderNo,
                                                                    'type' => 'cash_refund',
                                                                    'cash_amount' => $refund->amount,
                                                                    'gold_amount' => null,
                                                                    'gold_rate' => null,
                                                                    'cash_balance' => null,
                                                                    'gold_balance' => null,
                                                                    'pos_order' => null,
                                                                    'notes' => $refund->notes ?? 'Cash refund',
                                                                    'cash_detail_id' => null,
                                                                    'gold_detail_ids' => null,
                                                                    'can_print' => false
                                                                ]);
                                                            }
                                                        }
                                                        
                                                        // Gold uses (separate from deposits)
                                                        if ($goldAdvance) {
                                                            foreach($goldAdvance->goldAdvanceUse as $use) {
                                                                $combinedTransactions->push([
                                                                    'date' => $use->created_at,
                                                                    'order_no' => $orderNo,
                                                                    'type' => 'gold_usage',
                                                                    'cash_amount' => null,
                                                                    'gold_amount' => $use->gold_amount,
                                                                    'gold_rate' => $goldAdvance->goldRate,
                                                                    'cash_balance' => null,
                                                                    'gold_balance' => null,
                                                                    'pos_order' => $use->posOrder,
                                                                    'notes' => 'Used for order',
                                                                    'cash_detail_id' => null,
                                                                    'gold_detail_ids' => null,
                                                                    'can_print' => false
                                                                ]);
                                                            }
                                                        }
                                                    }
                                                    
                                                    $combinedTransactions = $combinedTransactions->sortByDesc('date');
                                                @endphp
                                                
                                                @forelse($combinedTransactions as $transaction)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            <a href="javascript:void(0);" 
                                                                class="btn btn-sm btn-outline-primary order-info-link"
                                                                data-order-no="{{ $transaction['order_no'] }}">
                                                                    {{ $transaction['order_no'] }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $typeConfig = [
                                                                    'combined_deposit' => ['class' => 'bg-primary', 'icon' => 'fas fa-plus', 'text' => 'Cash And Gold Deposit'],
                                                                    'cash_usage' => ['class' => 'bg-danger', 'icon' => 'fas fa-minus', 'text' => 'Cash Usage'],
                                                                    'cash_refund' => ['class' => 'bg-warning', 'icon' => 'fas fa-undo', 'text' => 'Cash Refund'],
                                                                    'gold_usage' => ['class' => 'bg-danger', 'icon' => 'fas fa-minus', 'text' => 'Gold Usage']
                                                                ];
                                                                $config = $typeConfig[$transaction['type']] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question', 'text' => 'Unknown'];
                                                            @endphp
                                                            <span class="badge {{ $config['class'] }}">
                                                                <i class="{{ $config['icon'] }}"></i> {{ $config['text'] }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($transaction['cash_amount'])
                                                                @if(str_contains($transaction['type'], 'deposit'))
                                                                    <span class="text-success">+Rs {{ number_format($transaction['cash_amount'], 2) }}</span>
                                                                @else
                                                                    <span class="text-danger">-Rs {{ number_format($transaction['cash_amount'], 2) }}</span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['gold_amount'])
                                                                @if(str_contains($transaction['type'], 'deposit'))
                                                                    <span class="text-success">+{{ number_format($transaction['gold_amount'], 3) }}g</span>
                                                                @else
                                                                    <span class="text-danger">-{{ number_format($transaction['gold_amount'], 3) }}g</span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['gold_rate'])
                                                                {{ $transaction['gold_rate']->name }}
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['cash_balance'] !== null && str_contains($transaction['type'], 'deposit'))
                                                                <strong>Rs {{ number_format($transaction['cash_balance'], 2) }}</strong>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['gold_balance'] !== null && str_contains($transaction['type'], 'deposit'))
                                                                <strong>{{ number_format($transaction['gold_balance'], 3) }}g</strong>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($transaction['pos_order'])
                                                                <a href="{{ route('pos_orders.show', $transaction['pos_order']) }}" class="btn btn-sm btn-outline-primary">
                                                                    {{ $transaction['pos_order']->invoice_no }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $transaction['notes'] ?? '-' }}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                @php
                                                                    $routeName = 'customer-transaction';
                                                                @endphp
                                                                
                                                                @if($transaction['can_print'] && $transaction['cash_detail_id'] && $transaction['gold_detail_ids'])
                                                                    <a href="{{ route('print.receipt.gold.cash', [
                                                                        'cashDetailId' => $transaction['cash_detail_id'], 
                                                                        'goldDetailIds' => $transaction['gold_detail_ids'], 
                                                                        'routeName' => $routeName
                                                                    ]) }}" 
                                                                    class="btn btn-success" 
                                                                    title="Print Combined Receipt"
                                                                    >
                                                                        <i class="fas fa-print"></i>
                                                                    </a>
                                                                @else
                                                                    <button class="btn btn-secondary disabled" 
                                                                            title="Receipt not available for this transaction type"
                                                                            disabled>
                                                                        <i class="fas fa-print"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center text-muted">No combined cash & gold advances found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Logs Tab -->
<!-- Payment Logs Tab -->
<div class="tab-pane fade" id="payments" role="tabpanel">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Invoice/Reference</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paymentLogs as $payment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment['date'])->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($payment['type'] == 'POS Payment')
                                <span class="badge bg-primary">POS Payment</span>
                            @elseif($payment['type'] == 'Advance Payment')
                                <span class="badge bg-success">A/D Cash</span>
                            @else
                                <span class="badge bg-warning">Reservation</span>
                            @endif
                        </td>
                        <td>{{ $payment['reference'] }}</td>
                        <td>Rs {{ number_format($payment['amount'], 2) }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ ucfirst(str_replace('_', ' ', $payment['method'])) }}
                            </span>
                        </td>
                        <td>{{ $payment['reference_no'] ?? '-' }}</td>
                        <td>{{ $payment['notes'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No payment logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <p class="text-muted">
                Showing {{ $paymentLogs->firstItem() ?? 0 }} to {{ $paymentLogs->lastItem() ?? 0 }} of {{ $paymentLogs->total() }} Payment Logs
            </p>
        </div>
        <div>
            {{ $paymentLogs->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<!-- Replace the existing modal sections with these cleaned up versions -->


<!-- REFUND Cash Advance Modal -->
<div class="modal fade" id="RefundAdvanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Refund Cash Advance for {{ $customer->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="RefundAdvanceForm">
                <div class="modal-body">
                    <div class="alert alert-primary">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Select a specific advance or leave blank to refund from general advance balance.
                        </small>
                    </div>

                    <!-- Advance Selection Dropdown -->
                    <div class="form-group">
                        <label class="form-label">Select Advance to Refund From</label>
                        <select class="form-control" id="advance_id" name="advance_id">
                            <option value="">General Advance (No specific order)</option>
                            <!-- Options will be populated via JavaScript -->
                        </select>
                        <small class="text-muted">Choose which advance balance to refund from</small>
                    </div>

                    <!-- Balance Display -->
                    <div class="form-group">
                        <label class="form-label">Available Balance</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs</span>
                            </div>
                            <input type="text" class="form-control" id="available_balance" readonly placeholder="Select advance to see balance" />
                        </div>
                        <small class="text-muted">This is the maximum amount you can refund</small>
                    </div>
                    
                    <!-- Refund Amount -->
                    <div class="form-group">
                        <label class="form-label">Refund Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs</span>
                            </div>
                            <input type="number" class="form-control" id="refund_amount" name="amount" step="0.01" min="0.01" placeholder="0.00" required />
                        </div>
                        <div class="invalid-feedback" id="amount_error"></div>
                    </div>

                    <!-- Notes -->
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Optional notes about this refund..."></textarea>
                    </div>

                    <!-- Loading State -->
                    <div id="loading_state" style="display: none;">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Loading advance balances...
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="refund_submit_btn">
                        <i class="fas fa-undo"></i> Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>






<!-- Cash Advance Modal -->
<div class="modal fade" id="addAdvanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Cash Advance for {{ $customer->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addAdvanceForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs </span>
                            </div>
                            <input type="number" class="form-control" name="amount" step="0.01" min="0.01" placeholder="0.00" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Order No</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">No: </span>
                            </div>
                            <input type="text" class="form-control" name="order_no" placeholder="Optional order number" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Optional notes about this advance payment..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i> Current cash advance balance: Rs {{ number_format($cashAdvanceBalance, 2) }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Advance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Gold Advance Modal -->
<div class="modal fade" id="addGoldAdvanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Gold Advance for {{ $customer->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addGoldAdvanceForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="gold_rate" class="form-label">Gold Rate</label>
                        <select name="gold_rate" id="gold_rate" class="form-control" required>
                            <option value="">Select Gold Rate</option>
                            @foreach ($goldRates->take(4) as $rate)
                                <option value="{{ $rate->id }}">{{ $rate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gold Amount (grams)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">g </span>
                            </div>
                            <input type="number" class="form-control" name="gold_amount" step="0.001" min="0.001" placeholder="0.000" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Order No</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">No: </span>
                            </div>
                            <input type="text" class="form-control" name="gold_order_no" placeholder="Optional order number" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Optional notes about this gold advance..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i> Current gold advance balance: {{ number_format($goldAdvanceBalance, 3) }}g
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Gold Advance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cash + Gold Advance Modal -->
<div class="modal fade" id="addCashGoldAdvanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Cash + Gold Advance for {{ $customer->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCashGoldAdvanceForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Cash Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs.</span>
                            </div>
                            <input type="number" class="form-control" name="cash_amount" step="0.01" min="0.01" placeholder="0.00" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Gold (Carat-wise) <span class="text-danger">*</span></label>
                        <div id="goldCaratGroupList">
                            <div class="gold-carat-row input-group mb-2">
                                <select name="gold_rates[]" class="form-control mr-2 gold-carat-select" required>
                                    <option value="">Select Carat</option>
                                    @foreach ($goldRates->take(4) as $rate)
                                        <option value="{{ $rate->id }}">{{ $rate->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="gold_grams[]" class="form-control mr-2 gold-gram-input" step="0.001" min="0.001" placeholder="Grams" required />
                                <button type="button" class="btn btn-sm btn-success add-carat-row">+</button>
                            </div>
                        </div>
                        <small class="text-muted">Add multiple gold entries with different carats if needed</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Order No <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">No:</span>
                            </div>
                            <input type="text" class="form-control" name="cash_gold_order_no" placeholder="Enter order number" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="cash_gold_note" rows="3" placeholder="Optional notes about this advance..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Advance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Order Info Modal -->
<div class="modal fade" id="orderInfoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Advance Summary for Order No: <span id="modalOrderNo"></span></h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Cash Advance Balance:</strong> Rs <span id="cashAdvanceAmount">0.00</span></p>
                <p><strong>Gold Advance Balance:</strong> <span id="goldAdvanceAmount">0.000</span>g</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal (keep existing one) -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment for {{ $customer->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('payments.store', $customer->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount">Payment Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rs </span>
                            </div>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                required min="0.01" max="{{ $customer->total_balance }}"
                                placeholder="Enter payment amount">
                        </div>
                        <small class="text-muted">Maximum payment amount: Rs {{ number_format($customer->total_balance, 2) }}</small>
                    </div>

                    <div class="form-group">
                        <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="chq">Chq</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reference_no">Reference Number</label>
                        <input type="text" class="form-control" id="reference_no" name="reference_no"
                            placeholder="Enter reference number (optional)">
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"
                            placeholder="Enter any additional notes (optional)"></textarea>
                    </div>

                    <div class="alert alert-info text-dark">
                        <small>
                            <i class="fas fa-info-circle"></i> This payment will be automatically distributed across all unpaid orders, starting with the oldest orders first.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    // Check URL for pagination parameters and switch to appropriate tab
    var urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('payments_page')) {
        $('#transactionTabs a[href="#payments"]').tab('show');
        // Scroll to tabs after tab switch
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $('#transactionTabs').offset().top - 100
            }, 300);
        }, 100);
    } else if (urlParams.has('page')) {
        $('#transactionTabs a[href="#pos-orders"]').tab('show');
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $('#transactionTabs').offset().top - 100
            }, 300);
        }, 100);
    } else if (urlParams.has('reservations_page')) {
        $('#transactionTabs a[href="#reservations"]').tab('show');
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $('#transactionTabs').offset().top - 100
            }, 300);
        }, 100);
    }

    // Get customer ID from the current page - this should be available in your blade template
    const customerId = {{ $customer->id }};
    
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Order info modal functionality
    $(document).on('click', '.order-info-link', function() {
        const orderNo = $(this).data('order-no');
        $('#modalOrderNo').text(orderNo);

        $.ajax({
            url: `/customer/advance-summary/${orderNo}`,
            method: 'GET',
            success: function(response) {
                $('#cashAdvanceAmount').text(response.cash || '0.00');
                $('#goldAdvanceAmount').text(response.gold || '0.000');
                $('#orderInfoModal').modal('show');
            },
            error: function() {
                alert('Failed to fetch advance summary.');
            }
        });
    });

    // Cash + Gold Advance - Add/Remove carat rows
    $(document).on('click', '.add-carat-row', function() {
        const $row = $(this).closest('.gold-carat-row');
        const $newRow = $row.clone();

        $newRow.find('input').val('');
        $newRow.find('select').prop('selectedIndex', 0);
        $newRow.find('.add-carat-row')
            .removeClass('btn-success add-carat-row')
            .addClass('btn-danger remove-carat-row')
            .text('–');

        $('#goldCaratGroupList').append($newRow);
    });

    $(document).on('click', '.remove-carat-row', function() {
        $(this).closest('.gold-carat-row').remove();
    });

    // Cash + Gold Advance Form Submission
    $('#addCashGoldAdvanceForm').on('submit', function(e) {
        e.preventDefault();

        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Processing...');

        // Collect carat-wise gold data with proper validation
        const goldEntries = [];
        let hasValidGoldEntries = false;
        
        $('#goldCaratGroupList .gold-carat-row').each(function() {
            const caratId = $(this).find('.gold-carat-select').val();
            const gram = $(this).find('.gold-gram-input').val();
            
            if (caratId && gram && parseFloat(gram) > 0) {
                goldEntries.push({ 
                    carat_id: parseInt(caratId), 
                    gram: parseFloat(gram) 
                });
                hasValidGoldEntries = true;
            }
        });

        // Validate that we have at least one valid gold entry
        if (!hasValidGoldEntries) {
            alert('Please add at least one gold entry with valid carat and gram values');
            $submitBtn.prop('disabled', false).text(originalText);
            return;
        }

        // Get cash amount and validate
        const cash_amount = parseFloat($('input[name="cash_amount"]').val());
        if (!cash_amount || cash_amount <= 0) {
            alert('Please enter a valid cash amount');
            $submitBtn.prop('disabled', false).text(originalText);
            return;
        }

        const routeName = 'customer-transaction';

        const formData = {
            cash_amount: cash_amount,
            cash_gold_order_no: $('input[name="cash_gold_order_no"]').val(),
            cash_gold_note: $('textarea[name="cash_gold_note"]').val(),
            routeName: routeName,
            gold_entries: goldEntries,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: `/customer/cash-gold/${customerId}/advance`,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'Advance added successfully');
                    $('#addCashGoldAdvanceModal').modal('hide');
                    
                    // Reset form properly
                    $('#addCashGoldAdvanceForm')[0].reset();
                    $('#goldCaratGroupList .gold-carat-row:not(:first)').remove();
                    $('#goldCaratGroupList .gold-carat-row:first input').val('');
                    $('#goldCaratGroupList .gold-carat-row:first select').prop('selectedIndex', 0);

                    if (response.print_url) {
                    // Fixed: Open in same tab instead of new tab
                    window.location.href = response.print_url;
                    // Remove location.reload() since we're navigating away
                    } else {
                        // Only reload if not printing
                        location.reload();
                    }
                } else {
                    alert(response.message || 'Failed to add advance');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response?.errors) {
                    let errorMessage = 'Validation errors:\n';
                    Object.values(response.errors).forEach(err => {
                        errorMessage += '- ' + err[0] + '\n';
                    });
                    alert(errorMessage);
                } else {
                    alert(response?.message || 'Failed to add advance');
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Cash Advance Form Submission
    $('#addAdvanceForm').on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        const routeName = 'customer-transaction';
        $submitBtn.prop('disabled', true).text('Processing...');
        
        const formData = {
            amount: parseFloat($('input[name="amount"]').val()),
            order_no: $('input[name="order_no"]').val(),
            notes: $('textarea[name="notes"]').val(),
            routeName: routeName,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: `/customer/${customerId}/advance`,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'Advance added successfully');
                    $('#addAdvanceModal').modal('hide');
                    $('#addAdvanceForm')[0].reset();
                    
                    if (response.print_url) {
                    // Fixed: Open in same tab instead of new tab
                    window.location.href = response.print_url;
                    // Remove location.reload() since we're navigating away
                    } else {
                        // Only reload if not printing
                        location.reload();
                    }
                } else {
                    alert(response.message || 'Failed to add advance');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessage = 'Validation errors:\n';
                    Object.keys(response.errors).forEach(key => {
                        errorMessage += '- ' + response.errors[key][0] + '\n';
                    });
                    alert(errorMessage);
                } else {
                    alert(response?.message || 'Failed to add advance');
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
    
    // Gold Advance Form Submission
    $('#addGoldAdvanceForm').on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        const routeName = 'customer-transaction';

        $submitBtn.prop('disabled', true).text('Processing...');
        
        const formData = {
            gold_amount: parseFloat($('input[name="gold_amount"]').val()),
            gold_rate: $('#gold_rate').val(),
            order_no: $('input[name="gold_order_no"]').val(),
            routeName: routeName,
            note: $('textarea[name="note"]').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: `/customer/gold/${customerId}/advance`,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'Gold Advance added successfully');
                    $('#addGoldAdvanceModal').modal('hide');
                    $('#addGoldAdvanceForm')[0].reset();

                     if (response.print_url) {
                    // Fixed: Open in same tab instead of new tab
                    window.location.href = response.print_url;
                    // Remove location.reload() since we're navigating away
                    } else {
                        // Only reload if not printing
                        location.reload();
                    }
                } else {
                    alert(response.message || 'Failed to add Gold advance');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessage = 'Validation errors:\n';
                    Object.keys(response.errors).forEach(key => {
                        errorMessage += '- ' + response.errors[key][0] + '\n';
                    });
                    alert(errorMessage);
                } else {
                    alert(response?.message || 'Failed to add gold advance');
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });



// REFUND FUNCTIONS

$('#RefundAdvanceModal').on('show.bs.modal', function() {
    loadAdvanceBalances();
});

function loadAdvanceBalances() {
    $('#loading_state').show();
    const $advanceSelect = $('#advance_id');
    
    $.ajax({
        url: `/customer/${customerId}/advance-balances`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                advanceBalances = response.advances;
                
                $advanceSelect.find('option:not(:first)').remove();
                
                response.advances.forEach(function(advance) {
                    $advanceSelect.append(
                        `<option value="${advance.id}" data-balance="${advance.current_balance}">
                            ${advance.display_name} (Rs ${advance.current_balance.toFixed(2)} available)
                        </option>`
                    );
                });

                if (response.advances.length === 0) {
                    $('#available_balance').val('No advance balance available');
                    $('#refund_submit_btn').prop('disabled', true);
                }
            } else {
                alert('Failed to load advance balances');
            }
        },
        error: function() {
            alert('Error loading advance balances');
        },
        complete: function() {
            $('#loading_state').hide();
        }
    });
}

$('#advance_id').on('change', function() {
    const selectedAdvanceId = $(this).val();
    const $balanceInput = $('#available_balance');
    const $amountInput = $('#refund_amount');

    if (selectedAdvanceId) {
        const selectedAdvance = advanceBalances.find(a => a.id == selectedAdvanceId);
        if (selectedAdvance) {
            $balanceInput.val(selectedAdvance.current_balance.toFixed(2));
            $amountInput.attr('max', selectedAdvance.current_balance);
            $('#refund_submit_btn').prop('disabled', false);
        }
    } else {
        const generalBalance = {{ $cashAdvanceBalance }};
        $balanceInput.val(generalBalance.toFixed(2));
        $amountInput.attr('max', generalBalance);
        $('#refund_submit_btn').prop('disabled', generalBalance <= 0);
    }
    
    $amountInput.val('');
    clearValidationErrors();
});

$('#refund_amount').on('input', function() {
    validateRefundAmount();
});

function validateRefundAmount() {
    const amount = parseFloat($('#refund_amount').val()) || 0;
    const availableBalance = parseFloat($('#available_balance').val()) || 0;
    const $amountInput = $('#refund_amount');
    const $errorDiv = $('#amount_error');
    
    if (amount > availableBalance) {
        $amountInput.addClass('is-invalid');
        $errorDiv.text(`Amount cannot exceed available balance of Rs ${availableBalance.toFixed(2)}`);
        $('#refund_submit_btn').prop('disabled', true);
        return false;
    } else if (amount <= 0) {
        $amountInput.addClass('is-invalid');
        $errorDiv.text('Amount must be greater than 0');
        $('#refund_submit_btn').prop('disabled', true);
        return false;
    } else {
        $amountInput.removeClass('is-invalid');
        $errorDiv.text('');
        $('#refund_submit_btn').prop('disabled', false);
        return true;
    }
}

function clearValidationErrors() {
    $('#refund_amount').removeClass('is-invalid');
    $('#amount_error').text('');
}

$('#RefundAdvanceForm').on('submit', function(e) {
    e.preventDefault();

    if (!validateRefundAmount()) {
        return;
    }

    const $submitBtn = $('#refund_submit_btn');
    const originalText = $submitBtn.html();
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

    const formData = {
        advance_id: $('#advance_id').val() || null,
        amount: parseFloat($('#refund_amount').val()),
        notes: $('textarea[name="notes"]').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    $.ajax({
        url: `/customer/${customerId}/advance-refund`,
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                alert(response.message || 'Refund processed successfully');
                $('#RefundAdvanceModal').modal('hide');
                $('#RefundAdvanceForm')[0].reset();
                $('#available_balance').val('');
                clearValidationErrors();
                location.reload();
            } else {
                alert(response.message || 'Failed to process refund');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            if (response && response.errors) {
                let errorMessage = 'Validation errors:\n';
                Object.keys(response.errors).forEach(key => {
                    errorMessage += '- ' + response.errors[key][0] + '\n';
                });
                alert(errorMessage);
            } else {
                alert(response?.message || 'Failed to process refund');
            }
        },
        complete: function() {
            $submitBtn.prop('disabled', false).html(originalText);
        }
    });
});

$('#RefundAdvanceModal').on('hidden.bs.modal', function() {
    $('#RefundAdvanceForm')[0].reset();
    $('#available_balance').val('');
    $('#advance_id').find('option:not(:first)').remove();
    clearValidationErrors();
    $('#refund_submit_btn').prop('disabled', false);
});


   // View Reservation Function with Payment Details
window.viewReservation = function(reservationId) {
    // Remove existing modal if any
    $('#reservationModal').remove();
    
    const modal = $(`
        <div class="modal fade" id="reservationModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reservation Details</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);
    
    $('body').append(modal);
    $('#reservationModal').modal('show');
    
    // Fetch reservation details
    $.ajax({
        url: `/reservations/${reservationId}`,
        method: 'GET',
        success: function(data) {
            if (data.success) {
                const reservation = data.reservation;
                let productsHtml = '';
                
                if (data.product_details && data.product_details.length > 0) {
                    data.product_details.forEach(product => {
                        productsHtml += `
                            <tr>
                                <td>${product.product_name}</td>
                                <td>${product.quantity}</td>
                                <td>Rs ${parseFloat(product.unit_price).toFixed(2)}</td>
                                <td>Rs ${parseFloat(product.line_total).toFixed(2)}</td>
                                <td>${parseFloat(product.weight || 0).toFixed(3)}g</td>
                                <td>${product.current_product_status}</td>
                            </tr>
                        `;
                    });
                }
                
                // Get payment history
                $.ajax({
                    url: `/reservations/${reservationId}/payment-history`,
                    method: 'GET',
                    success: function(paymentData) {
                        let paymentsHtml = '';
                        
                        if (paymentData.success && paymentData.payments && paymentData.payments.length > 0) {
                            paymentData.payments.forEach(payment => {
                                const paymentClass = payment.amount >= 0 ? 'text-success' : 'text-danger';
                                const paymentIcon = payment.amount >= 0 ? 'fa-plus' : 'fa-minus';
                                const paymentType = payment.amount >= 0 ? 'Payment' : 'Refund';
                                
                                paymentsHtml += `
                                    <tr>
                                        <td>${new Date(payment.created_at).toLocaleDateString('en-GB')} ${new Date(payment.created_at).toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'})}</td>
                                        <td>
                                            <span class="badge ${payment.amount >= 0 ? 'bg-success' : 'bg-danger'}">
                                                <i class="fas ${paymentIcon}"></i> ${paymentType}
                                            </span>
                                        </td>
                                        <td class="${paymentClass}">Rs ${Math.abs(parseFloat(payment.amount)).toFixed(2)}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                ${payment.payment_method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                            </span>
                                        </td>
                                        <td>${payment.notes || '-'}</td>
                                        <td>
                                            ${payment.created_at && new Date(payment.created_at) > new Date(Date.now() - 24*60*60*1000) && payment.amount > 0 ? 
                                                `<button type="button" class="btn btn-danger btn-sm" onclick="deleteReservationPayment(${reservationId}, ${payment.id})">
                                                    <i class="fas fa-trash"></i>
                                                </button>` : 
                                                '-'
                                            }
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            paymentsHtml = '<tr><td colspan="6" class="text-center text-muted">No payment history found</td></tr>';
                        }
                        
                        // Update modal content with both reservation and payment details
                        modal.find('.modal-body').html(`
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Reservation Info</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Date:</strong> ${new Date(reservation.created_at).toLocaleDateString('en-GB')}</p>
                                            <p><strong>Status:</strong> 
                                                <span class="badge badge-${reservation.status === 'completed' ? 'success' : reservation.status === 'cancelled' ? 'danger' : 'warning'}">
                                                    ${reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1)}
                                                </span>
                                            </p>
                                            <p><strong>Delivery Date:</strong> ${reservation.delivery_date ? new Date(reservation.delivery_date).toLocaleDateString('en-GB') : 'Not set'}</p>
                                            ${reservation.pos_order_id ? `<p><strong>POS Order:</strong> <span class="badge bg-primary">#${reservation.pos_order_id}</span></p>` : ''}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Amount Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Total Amount:</strong> <span class="text-primary">Rs ${parseFloat(reservation.total_amount).toFixed(2)}</span></p>
                                            <p><strong>Paid Amount:</strong> <span class="text-success">Rs ${parseFloat(reservation.paid_amount).toFixed(2)}</span></p>
                                            <p><strong>Balance:</strong> <span class="text-${reservation.total_amount - reservation.paid_amount > 0 ? 'danger' : 'success'}">Rs ${(reservation.total_amount - reservation.paid_amount).toFixed(2)}</span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Customer Info</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Name:</strong> ${reservation.customer.name}</p>
                                            <p><strong>Phone:</strong> ${reservation.customer.tel || 'Not provided'}</p>
                                            <p><strong>City:</strong> ${reservation.customer.city || 'Not provided'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Products Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Reserved Products</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Quantity</th>
                                                            <th>Unit Price</th>
                                                            <th>Total</th>
                                                            <th>Weight</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${productsHtml}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment History Section -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Payment History</h6>
                                            ${reservation.status === 'pending' && (reservation.total_amount - reservation.paid_amount) > 0 ? 
                                                `<button type="button" class="btn btn-success btn-sm" onclick="addReservationPaymentModal(${reservationId})">
                                                    <i class="fas fa-plus"></i> Add Payment
                                                </button>` : 
                                                ''
                                            }
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Date & Time</th>
                                                            <th>Type</th>
                                                            <th>Amount</th>
                                                            <th>Method</th>
                                                            <th>Notes</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${paymentsHtml}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    },
                    error: function() {
                        modal.find('.modal-body').html(`
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Reservation Info</h6>
                                    <p><strong>Date:</strong> ${new Date(reservation.created_at).toLocaleDateString('en-GB')}</p>
                                    <p><strong>Status:</strong> <span class="badge badge-${reservation.status === 'completed' ? 'success' : 'warning'}">${reservation.status}</span></p>
                                    <p><strong>Delivery Date:</strong> ${reservation.delivery_date ? new Date(reservation.delivery_date).toLocaleDateString('en-GB') : 'Not set'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Amount Details</h6>
                                    <p><strong>Total Amount:</strong> Rs ${parseFloat(reservation.total_amount).toFixed(2)}</p>
                                    <p><strong>Paid Amount:</strong> Rs ${parseFloat(reservation.paid_amount).toFixed(2)}</p>
                                    <p><strong>Balance:</strong> Rs ${(reservation.total_amount - reservation.paid_amount).toFixed(2)}</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Products</h6>
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${productsHtml}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i> Could not load payment history
                            </div>
                        `);
                    }
                });
                
            } else {
                modal.find('.modal-body').html('<div class="alert alert-danger">Error loading reservation details</div>');
            }
        },
        error: function(error) {
            console.error('Error:', error);
            modal.find('.modal-body').html('<div class="alert alert-danger">Error loading reservation details</div>');
        }
    });
    
    // Remove modal after hiding
    $('#reservationModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
};

    // Use Advance function
    window.useAdvance = function(orderId, remainingBalance) {
        const available = {{ $cashAdvanceBalance }};
        if (available <= 0) {
            alert('No advance balance available');
            return;
        }

        const maxAmount = Math.min(available, remainingBalance);
        const amount = prompt(`Use advance amount (Max: Rs ${maxAmount.toFixed(2)}):`);
        
        if (amount && parseFloat(amount) > 0 && parseFloat(amount) <= maxAmount) {
            $.ajax({
                url: `/orders/${orderId}/use-advance`,
                method: 'POST',
                data: {
                    amount: parseFloat(amount),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Advance used successfully');
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to use advance');
                    }
                },
                error: function() {
                    alert('Error using advance');
                }
            });
        }
    };

// Add Payment Modal for Reservation
window.addReservationPaymentModal = function(reservationId) {
    // Remove existing payment modal if any
    $('#addReservationPaymentModal').remove();
    
    const paymentModal = $(`
        <div class="modal fade" id="addReservationPaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Payment to Reservation</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="addReservationPaymentForm">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="payment_amount">Payment Amount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rs</span>
                                    </div>
                                    <input type="number" step="0.01" class="form-control" id="payment_amount" name="amount" required min="0.01" placeholder="Enter payment amount">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment_method_res">Payment Method</label>
                                <select class="form-control" id="payment_method_res" name="payment_method" required>
                                    <option value="">Select payment method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="payment_notes">Notes</label>
                                <textarea class="form-control" id="payment_notes" name="notes" rows="2" placeholder="Enter any notes (optional)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `);
    
    $('body').append(paymentModal);
    $('#addReservationPaymentModal').modal('show');
    
    // Handle form submission
    $('#addReservationPaymentForm').on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Processing...');
        
        const formData = {
            amount: parseFloat($('#payment_amount').val()),
            payment_method: $('#payment_method_res').val(),
            notes: $('#payment_notes').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: `/reservations/${reservationId}/payments`,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Payment added successfully');
                    $('#addReservationPaymentModal').modal('hide');
                    
                    // Refresh the reservation modal if it's open
                    if ($('#reservationModal').is(':visible')) {
                        viewReservation(reservationId);
                    }
                    
                    // Refresh the main page
                    location.reload();
                } else {
                    alert(response.message || 'Failed to add payment');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    let errorMessage = 'Validation errors:\n';
                    Object.keys(response.errors).forEach(key => {
                        errorMessage += '- ' + response.errors[key][0] + '\n';
                    });
                    alert(errorMessage);
                } else {
                    alert(response?.message || 'Failed to add payment');
                }
            },
            complete: function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
    
    // Remove modal after hiding
    $('#addReservationPaymentModal').on('hidden.bs.modal', function() {
        $(this).remove();
    });
};

// Delete Reservation Payment
window.deleteReservationPayment = function(reservationId, paymentId) {
    if (confirm('Are you sure you want to delete this payment?')) {
        $.ajax({
            url: `/reservations/${reservationId}/payments/${paymentId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Payment deleted successfully');
                    
                    // Refresh the reservation modal if it's open
                    if ($('#reservationModal').is(':visible')) {
                        viewReservation(reservationId);
                    }
                    
                    // Refresh the main page
                    location.reload();
                } else {
                    alert(response.message || 'Failed to delete payment');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Failed to delete payment');
            }
        });
    }
};

// Update existing functions
window.addReservationPayment = function(reservationId) {
    addReservationPaymentModal(reservationId);
};

window.convertToPOS = function(reservationId) {
    if (confirm('Are you sure you want to convert this reservation to a POS order? This action cannot be undone.')) {
        $.ajax({
            url: `/reservations/${reservationId}/convert-to-pos`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Reservation converted to POS order successfully');
                    location.reload();
                } else {
                    alert(response.message || 'Failed to convert reservation');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Failed to convert reservation');
            }
        });
    }
};

window.cancelReservation = function(reservationId) {
    if (confirm('Are you sure you want to cancel this reservation? This will restore product quantities and refund any payments.')) {
        $.ajax({
            url: `/reservations/${reservationId}/cancel`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'Reservation cancelled successfully');
                    location.reload();
                } else {
                    alert(response.message || 'Failed to cancel reservation');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Failed to cancel reservation');
            }
        });
    }
};
});
</script>
@endsection