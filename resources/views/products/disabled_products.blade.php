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
                        Disabled Product List
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
        <div class="container">
            <div class="card" >
                <div class="card-header">
                    <h3 class="card-title">Disabled Product List</h3>
                     <div class="card-tools">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back Product List</a>
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
                                <th>Last Update Date</th>
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
                                    <td>{{ $product->updated_at }}</td>
                                    <td style="white-space: nowrap;">
                                        <a href="javascript:void(0);" 
                                        class="btn btn-primary btn-sm" 
                                        onclick="activateProduct({{ $product->id }}, '{{ $product->product_no }}')">
                                            <i class="fas fa-check"></i>
                                        </a>


                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-danger font-weight-bold">
                                        No disabled products found.
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function activateProduct(productId, productNo) {
    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to activate product <strong>${productNo}</strong>!`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, activate it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `/products/active/${productId}`;
        }
    });
}
</script>




@endsection