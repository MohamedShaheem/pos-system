@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Chit Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('chits.index') }}">Chits</a></li>
                        <li class="breadcrumb-item active">Chit Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"> {{ $chit->name }} - #{{ $chit->serial_no }}</h3>
                </div>
                <div class="card-body">
                    <p><strong>Total Amount:</strong> {{ number_format($chit->total_amount, 2) }}</p>
                    <p><strong>Amount Per Month:</strong> {{ number_format($chit->amount_per_month, 2) }}</p>

                    <h4>Chit Details</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Monthly Amounts</th>
                                <th>Total Paid</th>
                                <th>Chit Amount Paid</th>
                                <th>Balance to Pay</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chitDetails as $detail)
                                <tr class="customer-row">
                                    <td class="align-middle">{{ $detail->chitCustomer->customer_no }} - {{ $detail->chitCustomer->name }}</td>
                                    <td>
                                        <div class="row">
                                            @foreach(range(1, 12) as $month)
                                                @php
                                                    $monthNames = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                                                    $monthField = 'month_' . $month;
                                                    $monthNoteField = 'month_' . $month . '_note';
                                                @endphp
                                                <div class="col-2">
                                                    <div class="form-group mb-0">
                                                        <label class="small text-muted">
                                                            {{ $monthNames[$month - 1] }}
                                                            <i class="fas fa-info-circle {{ $detail->$monthNoteField ? 'text-success' : 'text-primary' }} ml-1" 
                                                               style="cursor: pointer;"
                                                               data-toggle="modal" 
                                                               data-target="#noteModal-{{ $detail->id }}-{{ $month }}"
                                                               title="{{ $detail->$monthNoteField ? 'Has Note: ' . $detail->$monthNoteField : 'Add Note' }}"></i>
                                                        </label>
                                                        <input type="number" 
                                                            id="amount-{{ $detail->id }}-{{ $month }}"
                                                            step="0.01" 
                                                            class="form-control form-control-sm month-amount" 
                                                            data-month="{{ $month }}"
                                                            value="{{ number_format($detail->$monthField, 2, '.', '') }}"
                                                            onchange="updateMonthAmount(this, {{ $detail->chitCustomer->id }}, {{ $month }})">
                                                    </div>
                                                </div>

                                                <!-- Note Modal -->
                                                <div class="modal fade" id="noteModal-{{ $detail->id }}-{{ $month }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Note for {{ $monthNames[$month - 1] }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Add Note</label>
                                                                    <textarea 
                                                                        class="form-control month-note" 
                                                                        rows="3" 
                                                                        placeholder="Enter note for {{ $monthNames[$month - 1] }}"
                                                                        data-month="{{ $month }}"
                                                                        data-customer-id="{{ $detail->chitCustomer->id }}"
                                                                        data-amount-input="#amount-{{ $detail->id }}-{{ $month }}"
                                                                    >{{ $detail->$monthNoteField }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary save-note">Save Note</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($month % 6 == 0)
                                                    </div><div class="row">
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="align-middle total_paid_display">
                                        {{ number_format($detail->total_paid, 2) }}
                                    </td>
                                    <td class="align-middle">
                                        <input type="number" 
                                            step="0.01" 
                                            class="form-control form-control-sm chit-amount" 
                                            value="{{ number_format($detail->paid_amount ?? 0, 2, '.', '') }}"
                                            onchange="updateChitPaidAmount(this, {{ $detail->chitCustomer->id }})">
                                    </td>
                                    <td class="align-middle">
                                        {{ number_format($detail->total_paid - $detail->paid_amount, 2) }}
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('chits.removeCustomer', [$chit, $detail->chitCustomer->id]) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <h4>Enroll Customer to This Chit</h4>
                    <form action="{{ route('chits.updateCustomers', $chit) }}" method="POST">
                        @csrf
                        <div class="form-group col-6">
                            <label for="chit_customer_id" style="text-align: left;">Select Customer</label>
                            <select name="chit_customer_id" id="chit_customer_id" class="form-control form-control-sm" required>
                                <option value="">Select a customer</option>
                                @foreach($chitCustomers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_no }} - {{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Enroll Customer</button>
                    </form>
                </div>
                <div class="card-footer">
                    <a href="{{ route('chits.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Select2 CSS -->
    <link href="{{ asset('plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <!-- Include Select2 Bootstrap 4 Theme CSS -->
    <link href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}" rel="stylesheet" />

    <!-- Include Select2 JS -->
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const table = $('table').DataTable({
                pageLength: 25,
                searching: true,
                ordering: false,
                columnDefs: [
                    {
                        targets: [0], // Customer Name column
                        searchable: true
                    },
                    {
                        targets: '_all', // all other columns
                        searchable: false
                    }
                ],
            });
            
            $('#chit_customer_id').select2({
                theme: 'bootstrap4',
                allowClear: true
            });

            // Handle save note button click
            $('.save-note').click(function() {
                const modal = $(this).closest('.modal');
                const textarea = modal.find('.month-note');
                const month = textarea.data('month');
                const customerId = textarea.data('customer-id');
                const amountInput = $(textarea.data('amount-input'));
                const note = textarea.val();

                updateMonthAmount(amountInput[0], customerId, month, note);
                modal.modal('hide');

                // Update info icon title and color
                const infoIcon = amountInput.closest('.form-group').find('.fa-info-circle');
                infoIcon.attr('title', note ? 'Has Note: ' + note : 'Add Note');
                if (note) {
                    infoIcon.addClass('text-success').removeClass('text-primary');
                } else {
                    infoIcon.addClass('text-primary').removeClass('text-success');
                }
            });
        });

        function updateMonthAmount(input, customerId, monthNumber, note = '') {
            const monthAmount = parseFloat(input.value) || 0;
            const row = input.closest('tr');
            
            fetch('{{ route("chits.updateChitDetail") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    chit_customer_id: customerId,
                    month_amount: monthAmount,
                    month_number: monthNumber,
                    month_note: note,
                    total_paid: calculateRowTotal(row),
                    chit_id: {{ $chit->id }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateTotalPaidDisplay(row);
                    
                    toastr.success('Month details updated successfully');
                } else {
                    console.error('Error updating chit details:', data.message);
                    toastr.error('Error updating month details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Error updating month details');
            });
        }

        function calculateRowTotal(row) {
            let total = 0;
            row.querySelectorAll('.month-amount').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            return total;
        }

        function updateTotalPaidDisplay(row) {
            const total = calculateRowTotal(row);
            row.querySelector('.total_paid_display').textContent = total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // Update balance to pay
            const chitAmountPaid = parseFloat(row.querySelector('.chit-amount').value) || 0;
            const balanceCell = row.querySelector('td:nth-last-child(2)');
            const balance = total - chitAmountPaid;
            balanceCell.textContent = balance.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        
        

        function updateChitPaidAmount(input, customerId) {
            const paidAmount = parseFloat(input.value) || 0;
            const row = input.closest('tr');
            const totalPaidText = row.querySelector('.total_paid_display').textContent.trim();
            const totalAmount = parseFloat(totalPaidText.replace(/,/g, '')) || 0;
            const isChitPaid = paidAmount >= totalAmount;

            fetch('{{ route("chits.updateChitPaidStatus") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    chit_customer_id: customerId,
                    chit_id: {{ $chit->id }},
                    paid_amount: paidAmount,
                    is_chit_paid: isChitPaid
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Format the input value
                    input.value = parseFloat(paidAmount).toFixed(2);
                    
                    // Update the balance cell
                    const balanceCell = row.querySelector('td:nth-last-child(2)');
                    const balance = totalAmount - paidAmount;
                    balanceCell.textContent = balance.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    toastr.success('Amount updated successfully');
                } else {
                    toastr.error('Error updating amount: ' + data.message);
                    // Reset to previous value if there's an error
                    input.value = parseFloat(input.defaultValue).toFixed(2);
                    
                    // Reset balance display
                    const balanceCell = row.querySelector('td:nth-last-child(2)');
                    const originalBalance = totalAmount - parseFloat(input.defaultValue);
                    balanceCell.textContent = originalBalance.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Error updating amount');
                input.value = parseFloat(input.defaultValue).toFixed(2);
            });
        }
    </script>

    <style>
        .customer-row {
            border-top: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
        }

        .form-group label {
            font-size: 0.8rem;
            text-align: center;
            display: block;
            font-weight: bold;
            color: #495057;
        }

        .month-amount {
            text-align: center;
        }

        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .fa-info-circle {
            transition: color 0.2s;
        }

        .fa-info-circle:hover {
            color: #0056b3 !important;
        }

        .fa-info-circle.text-success {
            color: #28a745 !important;
        }

        .fa-info-circle.text-success:hover {
            color: #218838 !important;
        }
    </style>
@endsection