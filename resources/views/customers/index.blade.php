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
                    <h1>Customers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Customers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container pb-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer List</h3>
                    <div class="card-tools">
                        <a href="{{ route('customers.create') }}" class="btn btn-success">Add Customer</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                   <form method="GET" action="{{ route('customers.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Filter by Religion:</label>
                                <select name="religion" class="form-control">
                                    <option value="">All Religions</option>
                                    @foreach($religions as $value => $label)
                                        <option value="{{ $value }}" {{ request('religion') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Search:</label>
                                <input type="text" name="search" class="form-control"
                                    value="{{ request('search') }}" placeholder="Search customers...">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Info -->
                    @if(request('religion'))
                        <div class="alert alert-info">
                            Showing customers with religion: <strong>{{ $religions[request('religion')] ?? request('religion') }}</strong>
                            <span class="badge badge-primary">{{ $customers->count() }} result(s)</span>
                        </div>
                    @endif

                    <table id="customer-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Religion</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>Tel</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    @php
                                        $religionMap = [
                                            'tamil' => 'Hindu',
                                            'muslim' => 'Islam',
                                            'christian' => 'Christian',
                                            'buddhist' => 'Buddhist',
                                        ];

                                        $displayReligion = $religionMap[$customer->religion] ?? ucfirst($customer->religion);
                                    @endphp

                                    <td>
                                        <span class="badge 
                                            @if($customer->religion == 'muslim') badge-success
                                            @elseif($customer->religion == 'christian') badge-info  
                                            @elseif($customer->religion == 'buddhist') badge-orange
                                            @elseif($customer->religion == 'tamil') badge-warning
                                            @else badge-secondary
                                            @endif">
                                            {{ $displayReligion }}
                                        </span>
                                    </td>

                                    <td>{{ $customer->address }}</td>
                                    <td>{{ $customer->city }}</td>
                                    <td>{{ $customer->tel }}</td>
                                    <td>{{ $customer->email }}</td>                                
                                    <td>
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">Edit</a>

                                       
                                        {{-- <form action="{{ route('customers.destroy', $customer) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button style="display:none;" type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                      <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted">
                                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                            </p>
                        </div>
                        <div>
                            {{ $customers->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                    @if($customers->isEmpty())
                        <div class="alert alert-warning text-center">
                            <h5>No customers found</h5>
                            @if(request('religion'))
                                <p>No customers found with the selected religion filter.</p>
                                <a href="{{ route('customers.index') }}" class="btn btn-primary">View All Customers</a>
                            @else
                                <p>No customers have been added yet.</p>
                                <a href="{{ route('customers.create') }}" class="btn btn-success">Add First Customer</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Advance History Modal -->
    <div class="modal fade" id="advanceHistoryModal" tabindex="-1" role="dialog" aria-labelledby="advanceHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="flex-shrink: 0;">
                    <h5 class="modal-title" id="advanceHistoryModalLabel">Advance History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="flex: 1; overflow-y: auto; padding: 20px;">
                    <div id="customerInfo" class="mb-3" style="flex-shrink: 0;">
                        <!-- Customer info will be populated here -->
                    </div>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                        <table class="table table-striped mb-0" id="advanceHistoryTable" style="position: relative;">
                            <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th style="border-top: none; background-color: #f8f9fa;">Date</th>
                                    <th style="border-top: none; background-color: #f8f9fa;">Type</th>
                                    <th style="border-top: none; background-color: #f8f9fa;">Amount</th>
                                    <th style="border-top: none; background-color: #f8f9fa;">Notes</th>
                                    <th style="border-top: none; background-color: #f8f9fa;">Action</th>
                                    {{-- <th>Order ID</th> --}}
                                </tr>
                            </thead>
                            <tbody id="advanceHistoryTableBody">
                                <!-- History data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noHistoryMessage" class="text-center text-muted" style="display: none; padding: 20px;">
                        <p>No advance history found for this customer.</p>
                    </div>
                </div>
                <div class="modal-footer" style="flex-shrink: 0;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<style>
    .badge-orange {
    background-color: rgb(255, 136, 0);
    color: white;
}
</style>
    <script>
        $(document).ready(function() {
            $('#customer-table').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false, // disable DT search
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });


        });

        function showAdvanceHistory(customerId) {
            // Show loading state
            $('#customerInfo').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
            $('#advanceHistoryTableBody').html('');
            $('#noHistoryMessage').hide();
            
            // Show modal
            $('#advanceHistoryModal').modal('show');
            
            // Fetch advance history
            $.ajax({
                url: `/customers/${customerId}/advances`,
                type: 'GET',
                success: function(response) {
                    // Update customer info
                    $('#customerInfo').html(`
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-2">Customer: ${response.customer.name}</h6>
                                <p class="card-text mb-1"><strong>Address:</strong> ${response.customer.address || 'N/A'}</p>
                                <p class="card-text mb-1"><strong>City:</strong> ${response.customer.city || 'N/A'}</p>
                                <p class="card-text mb-1"><strong>Tel:</strong> ${response.customer.tel || 'N/A'}</p>
                                <p class="card-text mb-0"><strong>Current Balance:</strong> Rs. ${parseFloat(response.total_balance).toFixed(2)}</p>
                            </div>
                        </div>
                    `);
                    
                    // Update history table
                    if (response.advances.length > 0) {
                        let tableBody = '';
                        response.advances.forEach(function(advance) {
                            const date = new Date(advance.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            
                            let typeClass, typeText;

                            if (advance.type === 'deposit') {
                                typeClass = 'badge-success';
                                typeText = 'Deposit';
                            } else if (advance.type === 'usage') {
                                typeClass = 'badge-warning';
                                typeText = 'Usage';
                            } else if (advance.type === 'cancelled') {
                                typeClass = 'badge-danger';
                                typeText = 'Cancelled';
                            } else {
                                typeClass = 'badge-secondary';
                                typeText = 'Unknown';
                            }

                            const amount = advance.type === 'deposit' ? '+' + parseFloat(advance.amount).toFixed(2) : '-' + parseFloat(advance.amount).toFixed(2);
                            
                            tableBody += `
                                <tr>
                                    <td>${date}</td>
                                    <td><span class="badge ${typeClass}">${typeText}</span></td>
                                    <td>Rs. ${amount}</td>
                                    <td>${advance.notes || '-'}</td>
                                     <td>
    <a href="/receipt/print/${advance.id}" target="_blank" class="btn btn-sm btn-primary">Print</a>
    ${advance.type === 'deposit' ? `<a href="#" onclick="cancelAdvance(${advance.id})" class="btn btn-sm btn-danger">Cancel</a>` : ''}
</td>
     
                                </tr>
                            `;
                        });
                        $('#advanceHistoryTableBody').html(tableBody);
                        // <td>${advance.pos_order_id || '-'}</td>
                    } else {
                        $('#advanceHistoryTableBody').html('');
                        $('#noHistoryMessage').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#customerInfo').html('<div class="alert alert-danger">Error loading customer information.</div>');
                    $('#advanceHistoryTableBody').html('');
                    console.error('Error fetching advance history:', error);
                }
            });
        }

                    function cancelAdvance(id) {
                if (!confirm("Are you sure you want to cancel this advance?")) return;

                $.ajax({
                    url: `/advance/${id}/cancel`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            // Refresh the modal content
                            $('#advanceHistoryModal').modal('hide');
                            setTimeout(() => {
                                showAdvanceHistory({{ $customer->id ?? 'null' }});
                            }, 500);
                        } else {
                            alert(response.message || 'Failed to cancel advance.');
                        }
                    },
                    error: function(xhr) {
                        alert('An error occurred while cancelling the advance.');
                    }
                });
            }
    </script>
@endsection