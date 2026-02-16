@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customer Reservations</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Reservations</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content pb-3">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer Reservations</h3>
                </div>
                <div class="card-body">
                    <table id="reservations-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Reserved Product</th>
                                <th>Product No</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
    @forelse($reservations as $index => $reservation)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $reservation->customer->name ?? 'N/A' }}</td>
            <td>
                @foreach($reservation->reservationDetails as $detail)
                    <div>{{ $detail->product->name ?? 'N/A' }}</div>
                @endforeach
            </td>
            <td>
                @foreach($reservation->reservationDetails as $detail)
                    <div>{{ $detail->product_id ?? 'N/A' }}</div>
                @endforeach
            </td>
            <td>Rs {{ number_format($reservation->total_amount, 2) }}</td>
            <td>Rs {{ number_format($reservation->paid_amount, 2) }}</td>
            <td>Rs {{ number_format($reservation->total_amount - $reservation->paid_amount, 2) }}</td>
            <td>
                <span class="badge badge-{{ $reservation->status === 'completed' ? 'success' : ($reservation->status === 'cancelled' ? 'danger' : 'warning') }}">
                    {{ ucfirst($reservation->status) }}
                </span>
            </td>
            <td>
                <div class="btn-group d-flex" role="group">
                    @if($reservation->status != 'completed')
                        <button class="btn btn-primary btn-sm add-payment" 
                            data-id="{{ $reservation->id }}"
                            data-balance="{{ $reservation->total_amount - $reservation->paid_amount }}"
                            data-customer="{{ $reservation->customer->name }}"
                            data-product="{{ $reservation->reservationDetails->first()->product->name ?? 'N/A' }}">
                            Add Payment
                        </button>
                    @endif 
                    
                    @if($reservation->status == 'completed' && !$reservation->pos_order_id)
                        <button class="btn btn-success btn-sm convert-to-pos" 
                            data-id="{{ $reservation->id }}"
                            data-customer="{{ $reservation->customer->name }}"
                            data-product="{{ $reservation->reservationDetails->first()->product->name ?? 'N/A' }}">
                            Convert to Sale
                        </button>
                    @endif
                    
                    <button class="btn btn-info btn-sm view-history ml-1" 
                        data-id="{{ $reservation->id }}">
                        View History
                    </button>

                    @if($reservation->status == 'pending' && !$reservation->pos_order_id)
                    <button class="btn btn-danger btn-sm cancel-reservation ml-1" 
                        data-id="{{ $reservation->id }}"
                        data-customer="{{ $reservation->customer->name }}"
                        data-product="{{ $reservation->reservationDetails->first()->product->name ?? 'N/A' }}">
                        Cancel
                    </button>
                     @endif

                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="text-center text-muted">No reservations found.</td>
        </tr>
    @endforelse
</tbody>

                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Reservation Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="paymentForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Customer:</strong> <span id="paymentCustomer"></span><br>
                            <strong>Product:</strong> <span id="paymentProduct"></span><br>
                            <strong>Remaining Balance:</strong> Rs <span id="maxAmount">0.00</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Payment Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required>
                            <small class="form-text text-muted">Maximum amount: Rs <span id="maxAmountText">0.00</span></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Payment Method <span class="text-danger">*</span></label>
                            <select class="form-control" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Optional payment notes"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="reservation_id" id="reservationId">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="historyContent">
                        <!-- Payment history will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Convert to POS Order Modal -->
    <div class="modal fade" id="convertModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Convert to POS Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Confirmation Required</strong>
                    </div>
                    <p>Are you sure you want to convert this reservation to a completed POS order?</p>
                    <div class="alert alert-info">
                        <strong>Customer:</strong> <span id="convertCustomer"></span><br>
                        <strong>Product:</strong> <span id="convertProduct"></span><br>
                        <strong>This action cannot be undone.</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="convertReservationId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmConvert">
                        <i class="fas fa-check"></i> Yes, Convert to Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- cancel model --}}
    <div class="modal fade" id="cancelReservationModal" tabindex="-1" role="dialog" aria-labelledby="cancelReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cancel Reservation</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel the reservation for <strong id="cancelCustomerName"></strong>'s product <strong id="cancelProductName"></strong>?
                <br><br>
                {{-- This will:
                <ul>
                    <li>Reset product status to <code>active</code></li>
                    <li>Set quantity back to <code>1</code></li>
                    <li>Refund the paid amount</li>
                </ul> --}}
                <ul>
                    
                     <li>Refund amount</li>
                </ul>
                <input type="hidden" id="cancelReservationId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Keep Reservation</button>
                <button type="button" class="btn btn-danger" id="confirmCancelReservation"><i class="fas fa-times"></i> Yes, Cancel Reservation</button>
            </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#reservations-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            // Add Payment Modal
            $('.add-payment').on('click', function() {
                const reservationId = $(this).data('id');
                const balance = $(this).data('balance');
                const customer = $(this).data('customer');
                const product = $(this).data('product');
                
                $('#reservationId').val(reservationId);
                $('#maxAmount').text(balance.toFixed(2));
                $('#maxAmountText').text(balance.toFixed(2));
                $('#paymentCustomer').text(customer);
                $('#paymentProduct').text(product);
                $('input[name="amount"]').attr('max', balance);
                $('input[name="amount"]').val('');
                $('select[name="payment_method"]').val('');
                $('textarea[name="notes"]').val('');
                $('#paymentModal').modal('show');
            });
            
            // Submit Payment Form
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();
                const reservationId = $('#reservationId').val();
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                
                // Disable submit button
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
                
                $.post(`/reservations/${reservationId}/payments`, formData, function(response) {
                    if (response.success) {
                        $('#paymentModal').modal('hide');
                        toastr.success('Payment added successfully');
                        
                        if (response.is_completed) {
                            toastr.success('Reservation completed! You can now convert it to a POS order.');
                        }
                        
                        // Refresh page to show updated data
                        window.location.reload();
                    }
                }).fail(function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Error adding payment';
                    toastr.error(errorMessage);
                }).always(function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Payment');
                });
            });

            // Open Cancel Modal - ADD THIS EVENT HANDLER
    $(document).on('click', '.cancel-reservation', function() {
        const reservationId = $(this).data('id');
        const customer = $(this).data('customer');
        const product = $(this).data('product');

        $('#cancelReservationId').val(reservationId);
        $('#cancelCustomerName').text(customer);
        $('#cancelProductName').text(product);
        $('#cancelReservationModal').modal('show');
    });

    // Confirm Cancellation - MOVE THIS INSIDE $(document).ready()
    $('#confirmCancelReservation').on('click', function() {
        const reservationId = $('#cancelReservationId').val();
        const button = $(this);

        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cancelling...');

        $.post(`/reservations/${reservationId}/cancel`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(response) {
            if (response.success) {
                $('#cancelReservationModal').modal('hide');
                toastr.success(response.message);
                window.location.reload();
            }
        }).fail(function(xhr) {
            const errorMessage = xhr.responseJSON?.message || 'Error cancelling reservation';
            toastr.error(errorMessage);
        }).always(function() {
            button.prop('disabled', false).html('<i class="fas fa-times"></i> Yes, Cancel Reservation');
        });
    });
            
            // View Payment History
           // View Payment History
// View Payment History
$('.view-history').on('click', function() {
    const reservationId = $(this).data('id');
    
    $('#historyContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#historyModal').modal('show');
    
    $.get(`/reservations/${reservationId}/payment-history`, function(response) {
        if (response.success) {
            // Get first product name from reservationDetails
            const firstProduct = response.reservation.reservation_details && response.reservation.reservation_details.length > 0 
                ? response.reservation.reservation_details[0].product?.name || 'N/A'
                : 'N/A';
            
            let historyHtml = `
                <div class="alert alert-info">
                    <strong>Customer:</strong> ${response.reservation.customer.name}<br>
                    <strong>Product:</strong> ${firstProduct}<br>
                    <strong>Total Amount:</strong> Rs ${parseFloat(response.reservation.total_amount).toFixed(2)}<br>
                    <strong>Paid Amount:</strong> Rs ${parseFloat(response.reservation.paid_amount).toFixed(2)}<br>
                    <strong>Balance:</strong> Rs ${(parseFloat(response.reservation.total_amount) - parseFloat(response.reservation.paid_amount)).toFixed(2)}
                </div>
            `;
            
            if (response.payments.length > 0) {
                historyHtml += `
                    <h6>Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Notes</th>
                                  
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                response.payments.forEach(payment => {
                    const paymentDate = new Date(payment.created_at).toLocaleDateString();
                    historyHtml += `
                        <tr>
                            <td>${paymentDate}</td>
                            <td>Rs ${parseFloat(payment.amount).toFixed(2)}</td>
                            <td>${payment.payment_method}</td>
                            <td>${payment.notes || '-'}</td>
                          
                        </tr>
                    `;
                });
                
                historyHtml += `
                            </tbody>
                        </table>
                    </div>
                `;
            } else {
                historyHtml += '<p class="text-muted">No payments recorded yet.</p>';
            }
            
            $('#historyContent').html(historyHtml);
        }
    }).fail(function(xhr) {
        let errorMessage = 'Error loading payment history';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }
        $('#historyContent').html(`<div class="alert alert-danger">${errorMessage}</div>`);
        console.error('Error loading payment history:', xhr);
    });
});
            
            // Delete Payment
            $(document).on('click', '.delete-payment', function() {
                if (!confirm('Are you sure you want to delete this payment?')) {
                    return;
                }
                
                const reservationId = $(this).data('reservation-id');
                const paymentId = $(this).data('payment-id');
                const button = $(this);
                
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: `/reservations/${reservationId}/payments/${paymentId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Payment deleted successfully');
                            $('#historyModal').modal('hide');
                            window.location.reload();
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || 'Error deleting payment';
                        toastr.error(errorMessage);
                        button.prop('disabled', false).html('Delete');
                    }
                });
            });
            
            // Convert to POS Order
            $('.convert-to-pos').on('click', function() {
                const reservationId = $(this).data('id');
                const customer = $(this).data('customer');
                const product = $(this).data('product');
                
                $('#convertReservationId').val(reservationId);
                $('#convertCustomer').text(customer);
                $('#convertProduct').text(product);
                $('#convertModal').modal('show');
            });
            
            // Confirm Convert to POS Order
            $('#confirmConvert').on('click', function() {
                const reservationId = $('#convertReservationId').val();
                const button = $(this);
                
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Converting...');
                
                $.post(`/reservations/${reservationId}/convert-to-pos`, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function(response) {
                    if (response.success) {
                        $('#convertModal').modal('hide');
                        toastr.success('Reservation successfully converted to POS order');
                        toastr.info(`Invoice Number: ${response.invoice_no}`);
                        window.location.reload();
                    }
                }).fail(function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Error converting reservation';
                    toastr.error(errorMessage);
                }).always(function() {
                    button.prop('disabled', false).html('<i class="fas fa-check"></i> Yes, Convert to Sale');
                });
            });
        });
    </script>
@endsection