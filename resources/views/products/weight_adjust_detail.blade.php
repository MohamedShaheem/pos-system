@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)

@section('content')
<div class="container py-4">
       <div class="mb-4 d-flex justify-content-between">
         <h3 class="font-weight-bold">Weight Adjustment Details</h3>
            <a href="{{ route('products.weight.adjust') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> Back to Product List
            </a>
        </div>
   

    {{-- Product Info --}}
    <div class="card mb-4 border-info shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Product Information</h5>
        </div>
        <div class="card-body">
            <p><strong>Product Name:</strong> <span class="text-success">{{ $product->name }}</span></p>
            <p><strong>Product No:</strong> <span class="badge badge-primary p-2">{{ $product->product_no }}</span></p>
        </div>
    </div>

    {{-- Adjustments Table --}}
    <div class="card border-warning shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">Weight Adjustments</h5>
        </div>
        <div class="card-body p-0">
            @if($product->productWeightAdjusts && $product->productWeightAdjusts->count() > 0)
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Note</th>
                        <th>Type</th>
                        <th>Weight</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->productWeightAdjusts as $index => $adjust)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $adjust->note ?? '-' }}</td>
                        <td>
                            @if($adjust->adjust_type === 'add')
                                <span class="text-success" style="font-weight:700; font-size:22px;">+</span>
                            @else
                                <span class="text-danger" style="font-weight:700; font-size:22px;">−</span>
                            @endif
                        </td>

                        <td>{{ number_format($adjust->weight, 3) }}</td>
                        <td>{{ $adjust->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-center m-3 text-muted font-italic">No adjustments found for this product.</p>
            @endif
        </div>
    </div>

 
</div>
@endsection
