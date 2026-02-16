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
                    <h1>
                        Weight Products 
                        <span id="scale-status" 
                            style="
                                display:inline-block;
                                width:12px;
                                height:12px;
                                border-radius:50%;
                                background:red;
                                margin-left:8px;
                            ">
                        </span>
                    </h1>

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
                    <h3 class="card-title">Weight Product List</h3>
                    <div class="card-tools">
                        <a href="{{ route('products.createOrEdit') }}" class="btn btn-success">Create New Product</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-end">
                        <form method="GET" action="{{ route('products.weight.adjust') }}" class="mb-3 d-flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search products...">
                            
                            <button type="submit" class="btn btn-primary ml-2">Search</button>
    
                            <!-- Reset button -->
                            <a href="{{ route('products.weight.adjust') }}" class="btn btn-secondary ml-2">Reset</a>
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
                            @forelse ($products as $product)
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
                                    @if(!$product->is_approved == 0)
                                    <td style="white-space: nowrap;">
                                      <!-- ADJUST WEIGHT BUTTONS -->
                                        <button class="btn btn-success btn-sm mr-1" 
                                            onclick="openAdjustPopup({{ $product->id }}, 'add', '{{ $product->name }}', '{{ $product->product_no }}')">
                                            <i class="bi bi-plus-circle"></i>
                                        </button>

                                        <button class="btn btn-danger btn-sm" 
                                            onclick="openAdjustPopup({{ $product->id }}, 'minus', '{{ $product->name }}', '{{ $product->product_no }}')">
                                            <i class="bi bi-dash-circle"></i>
                                        </button>

                                        <a href="{{ route('products.weight.adjust.details', $product->id) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                    @else
                                    <td>
                                        <p class="badge badge-danger">Waiting for admin approval</p>
                                    </td>
                                    @endif
                                </tr>
                             @empty
                                <tr>
                                    <td colspan="10" class="text-center text-danger font-weight-bold">
                                        No Weight Based Products found.
                                    </td>
                                </tr>
                            @endforelse
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

    <!-- ADJUST WEIGHT MODAL -->
<div class="modal fade" id="adjustWeightModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Adjust Weight</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="adjust_product_id">
                <input type="hidden" id="adjust_type">

                <div class="form-group">
                    <label>Product</label>
                    <input type="text" id="adjust_product_name" class="form-control" style="color: black;" readonly>
                </div>
                <div class="form-group">
                    <label>Product No</label>
                    <input type="text" id="adjust_product_no" class="form-control" style="color: black;" readonly>
                </div>


                <div class="form-group">
                    <label>Note</label>
                    <input type="text" id="note" name="note" class="form-control">
                </div>

                <div class="form-group">
                    <label>Weight</label>
                    <input type="text" id="scale_live_value" class="form-control text-success font-weight-bold" readonly>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" onclick="submitWeightAdjustment()">Save</button>
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>

<script>

const socket = io('http://127.0.0.1:3001'); 
let lastScaleValue = 0;

socket.on('connect', () => {
    const status = document.getElementById("scale-status");
    if (status) status.style.background = "green";
});

socket.on('disconnect', () => {
    const status = document.getElementById("scale-status");
    if (status) status.style.background = "red";
});

socket.on('weight', (data) => {
    lastScaleValue = data;
    const el = document.getElementById("scale_live_value");
    if (el) el.value = data;
});

</script>

<script>

function openAdjustPopup(productId, type, productName, productNo) {
    document.getElementById("adjust_product_id").value = productId;
    document.getElementById("adjust_type").value = type;

    document.getElementById("adjust_product_name").value = productName;
    document.getElementById("adjust_product_no").value = productNo;

    const scaleValue = lastScaleValue || 0;
    document.getElementById("scale_live_value").value = scaleValue;

    $("#adjustWeightModal").modal("show");
}



function submitWeightAdjustment() {
    const productId = document.getElementById("adjust_product_id").value;
    const type = document.getElementById("adjust_type").value;

    // TAKE ONLY LIVE SCALE VALUE
    let value = document.getElementById("scale_live_value").value;
    let note = document.getElementById("note").value;

    if (!value || isNaN(value) || value == 0) {
        toastr.info("Invalid scale value");
        return;
    }

    fetch(`/products/weight-adjust/${productId}`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            value: value,
            type: type,
            note: note
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            $("#adjustWeightModal").modal("hide");
            location.reload();
        }
    });
}

</script>

<script>
$('#adjustWeightModal').on('hidden.bs.modal', function () {
    // Clear all fields
    document.getElementById("adjust_product_id").value = "";
    document.getElementById("adjust_type").value = "";
    document.getElementById("adjust_product_name").value = "";
    document.getElementById("adjust_product_no").value = "";
    document.getElementById("note").value = "";
    document.getElementById("scale_live_value").value = "";
});
</script>


@endsection