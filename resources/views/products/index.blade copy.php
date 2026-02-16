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
                    <h1>Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content pb-3">
        <div class="container" >
            <div class="card" >
                <div class="card-header">
                    <h3 class="card-title">Product List</h3>
                    <div class="card-tools">
                        <a href="{{ route('product.disable.show') }}" class="btn btn-primary">Disabled Product List</a>
                        <a href="{{ route('products.createOrEdit') }}" class="btn btn-success">Create New Product</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-end">
                        <form method="GET" action="{{ route('products.index') }}" class="mb-3 d-flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search products...">
                            
                            <button type="submit" class="btn btn-primary ml-2">Search</button>
    
                            <!-- Reset button -->
                            <a href="{{ route('products.index') }}" class="btn btn-secondary ml-2">Reset</a>
                        </form>
                    </div>
                    <table id="product-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product No</th>
                                <th>Name</th>
                                <th>Net Weight</th>
                                <th>Wastage Weight</th>
                                <th>Stone Weight</th>
                                <th>Gold Rate</th>
                                <th>Making Charges</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->product_no }}</td>
                                    <td>{{ $product->name }} <br>
                                    @if (!empty($product->supplier_id))
                                        <span class="badge badge-success" style="font-size: 15px">{{ $product->supplier->short_code }}</span>
                                    @endif
                                    {!! $product->qty > 0 ? "" : '<span class="badge badge-warning">Sold out</span>' !!}
                                    </td>
                                    <td>{{ number_format($product->weight, 3) }}</td>
                                    <td>{{ number_format($product->wastage_weight, 3) }}</td>
                                    <td>{{ number_format($product->stone_weight, 3) }}</td>
                                    <td>{{ $product->goldRate->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($product->making_charges, 2) }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->subCategory ? $product->subCategory->name : '' }}</td>
                                    <td class="d-flex">
                                        <a href="{{ route('products.createOrEdit', $product->id) }}" class="btn btn-warning btn-sm" style="margin-right: 5px;">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <button class="btn btn-info btn-sm"  style="margin-right: 5px;"  onclick="printLabel({{ $product->product_no }})">
                                            <i class="bi bi-upc "></i>
                                        </button>

                                        {{-- <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form> --}}

                                        <a href="{{ route('product.disable', $product->id) }}" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to disable this product?')">
                                            <i class="fas fa-ban"></i>
                                        </a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted">
                                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                            </p>
                        </div>
                        <div>
                            {{ $products->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>

    <!-- Approval Notification Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="approvalModalLabel">
                        <i class="bi bi-clock-history me-2"></i>
                        <span id="modalTitle">Product Pending Approval</span>
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-hourglass-split" style="font-size: 3rem; color: #072cff;"></i>
                    </div>
                    <h6 id="modalMessage">Your product has been submitted and is awaiting admini approval.</h6>
                    <div class="mt-3">
                        <p class="text-muted mb-1">Product: <strong id="modalProductName"></strong></p>
                        <p class="text-muted mb-0">Product No: <strong id="modalProductNo"></strong></p>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <small><i class="bi bi-info-circle"></i> You will be notified once the admin reviews your submission.</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">
                        Ok
                    </button>
                    @if(Auth::user()->role->name === 'superadmin')
                    <button type="button" class="btn btn-success" onclick="printApprovalLabel()" id="printLabelBtn" style="display: none;">
                        <i class="bi bi-printer me-1"></i> Print Label
                    </button>
                    @else
                    @endif
                </div>
            </div>
        </div>
    </div>


    <style>
        .barcode-container {
            display: none;
        }
    
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            
            .barcode-container {
                display: block !important;
                width: 2.2in !important;
                height: 1in !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: always;
            }
            
            img {
                max-width: 100% !important;
                height: auto !important;
            }
        }
    </style>

    <script>
        // Store product info for modal
        let currentProductNo = null;
        let currentProductName = null;

        function printLabel(product_no) {
            const url = `/products/label/${product_no}`;
            const printWindow = window.open(url, '_blank', 'width=600,height=400');
            if (!printWindow) {
                alert('Please allow popups to print the label.');
            }
        }


        function printApprovalLabel() {
            if (currentProductNo) {
                printLabel(currentProductNo);
                $('#approvalModal').modal('hide');
            }
        }

        $(document).ready(function() {
            $('#product-table').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
                "order": [[0, 'desc']],
            });

            // Check if we should show the approval popup
            @if(session('show_approval_popup') && session('product_data'))
                const productData = @json(session('product_data'));
                const popupType = "{{ session('popup_type') }}";
                const popupMessage = "{{ session('popup_message') }}";
                
                // Store product info
                currentProductNo = productData.product_no;
                currentProductName = productData.name;
                
                // Update modal content
                $('#modalProductName').text(currentProductName);
                $('#modalProductNo').text(currentProductNo);
                $('#modalMessage').text(popupMessage);
                
                // Update title based on action
                if (popupType === 'create') {
                    $('#modalTitle').text('Product Create - Pending Approval');
                    $('#printLabelBtn').show(); // Show print option for new products
                } else {
                    $('#modalTitle').text('Product Update - Pending Approval');
                    $('#printLabelBtn').hide(); // Hide print option for updates
                }
                
                // Show modal after a short delay
                setTimeout(function() {
                    $('#approvalModal').modal('show');
                }, 100);
            @endif
        });
    </script>
@endsection