@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)

@section('content')

<style>
    .card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .search-clear-btn {
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        color: #6c757d;
        z-index: 10;
        padding: 2px 4px;
        border-radius: 2px;
        transition: color 0.2s ease;
    }
    
    .search-clear-btn:hover {
        color: #495057;
        background-color: rgba(0,0,0,0.05);
    }
    
</style>
<section class="pb-3">
<div class="container">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Purchase Old Gold</h2>            
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-semibold">Purchase Old Gold List</h5>
                <a href="{{ route('purchase-old-gold.create') }}" class="btn btn-success">
                    Create New Purchase
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Search and Filter Section -->
            <div class="px-4 py-3 bg-light border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <label class="form-label mb-0 mr-2">Show</label>
                            <select class="form-select form-select-sm" style="width: auto;" id="entriesPerPage">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="ms-2 text-muted">entries</span>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="d-flex justify-content-end align-items-center">
                            <label class="form-label mb-0 mr-2">Search:</label>
                            <div class="position-relative d-flex align-items-center">
                                <input type="text" class="form-control form-control-sm" 
                                       id="searchInput" 
                                       placeholder="Search" 
                                       style="min-width: 200px; padding-right: 40px;">
                                <button type="button" id="clearSearch" class="position-absolute search-clear-btn" 
                                        style="display: none;">
                                    <i class="fas fa-times" style="font-size: 12px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            @if($purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        Invoice No
                                    </div>
                                </th>
                                <th class="py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        Customer
                                    </div>
                                </th>
                                <th class="py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        NIC
                                    </div>
                                </th>
                                <th class="py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        Phone
                                    </div>
                                </th>
                                <th class="py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        Total Items
                                    </div>
                                </th>
                                <th class="py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        Total Amount
                                    </div>
                                </th>
                                <th class="py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        Status
                                    </div>
                                </th>
                                <th class="py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        Date
                                    </div>
                                </th>
                                <th class="py-3 border-0 text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        Actions
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <span class="fw-semibold text-primary">#{{ $purchase->invoice_no }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-medium text-dark">{{ $purchase->customer->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <span >{{ $purchase->customer->nic ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span>{{ $purchase->customer->tel ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-primary rounded-pill">{{ $purchase->details->count() }} items</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-success">Rs. {{ number_format($purchase->details->sum('gold_purchased_amount'), 2) }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge {{ $purchase->status == 'completed' ? 'bg-success' : 'bg-warning' }} rounded-pill">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span >{{ $purchase->created_at->format('Y-m-d H:i') }}</span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('purchase-old-gold.show', $purchase->id) }}" 
                                               class="btn btn-outline-info btn-sm rounded mr-2" title="Edit">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('purchase-old-gold.printInvoice', $purchase->id) }}" 
                                               class="btn btn-outline-success btn-sm rounded" title="Print">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            {{-- <button class="btn btn-outline-danger btn-sm rounded delete-btn" 
                                                    data-id="{{ $purchase->id }}" 
                                                    data-invoice="{{ $purchase->invoice_no }}" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button> --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

               <!-- Pagination -->
                <div class="px-4 py-3 bg-light border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $purchases->firstItem() ?? 0 }}
                            to {{ $purchases->lastItem() ?? 0 }}
                            of {{ $purchases->total() ?? 0 }} results
                        </div>
                        <div>
                            {{ $purchases->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>

            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-coins fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted mb-2">No purchase records found</h5>
                        <p class="text-muted">Start by creating your first purchase old gold record.</p>
                    </div>
                    <a href="{{ route('purchase-old-gold.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> Create New Purchase
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Delete Purchase Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                </div>
                <p class="text-center">Are you sure you want to delete purchase record <strong id="deleteInvoiceNo"></strong>?</p>
                <p class="text-danger text-center"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>
</section>
<script>
$(document).ready(function() {
    let deleteId = null;

    // Delete functionality
    $('.delete-btn').on('click', function() {
        deleteId = $(this).data('id');
        const invoiceNo = $(this).data('invoice');
        $('#deleteInvoiceNo').text('#' + invoiceNo);
        $('#deleteModal').modal('show');
    });

    $('#confirmDelete').on('click', function() {
        if (deleteId) {
            $.ajax({
                url: '{{ route("purchase-old-gold.index") }}/' + deleteId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error deleting record');
                }
            });
        }
    });

    // Real-time search functionality (client-side)
    $('#searchInput').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        let visibleRows = 0;
        
        $('tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            if (searchTerm === '' || rowText.includes(searchTerm)) {
                $(this).show();
                visibleRows++;
            } else {
                $(this).hide();
            }
        });
        
        // Update the results count display
        updateResultsCount(visibleRows);
        
        // Show/hide empty state
        if (visibleRows === 0 && searchTerm !== '') {
            showNoResultsMessage();
        } else {
            hideNoResultsMessage();
        }
    });

    // Entries per page functionality
    $('#entriesPerPage').on('change', function() {
        const perPage = $(this).val();
        const url = new URL(window.location);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to first page
        window.location = url;
    });

    // Function to update results count
    function updateResultsCount(visibleRows) {
        const totalRows = $('tbody tr').length;
        const searchTerm = $('#searchInput').val().trim();
        
        if (searchTerm === '') {
            // Show original pagination info when no search
            $('.results-info').html('Showing {{ $purchases->firstItem() ?? 0 }} to {{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() ?? 0 }} results');
        } else {
            // Show filtered results count
            $('.results-info').html(`Showing ${visibleRows} of ${totalRows} results (filtered)`);
        }
    }

    // Function to show no results message
    function showNoResultsMessage() {
        if ($('#noResultsMessage').length === 0) {
            const noResultsHtml = `
                <tr id="noResultsMessage">
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-search fa-2x text-muted mb-3"></i>
                        <h6 class="text-muted">No results found</h6>
                        <p class="text-muted mb-0">Try adjusting your search terms</p>
                    </td>
                </tr>
            `;
            $('tbody').append(noResultsHtml);
        }
    }

    // Function to hide no results message
    function hideNoResultsMessage() {
        $('#noResultsMessage').remove();
    }

    // Clear search function
    function clearSearch() {
        $('#searchInput').val('');
        $('tbody tr').show();
        hideNoResultsMessage();
        updateResultsCount($('tbody tr').length);
        $('#clearSearch').hide();
    }

    // Add clear button functionality
    $('#clearSearch').on('click', function() {
        clearSearch();
    });

    // Show/hide clear button based on input
    $('#searchInput').on('input', function() {
        if ($(this).val().trim() !== '') {
            $('#clearSearch').show();
        } else {
            $('#clearSearch').hide();
        }
    });

    // Add clear button functionality if needed
    $(document).on('keyup', '#searchInput', function(e) {
        if (e.key === 'Escape') {
            clearSearch();
        }
    });
});
</script>
@endsection