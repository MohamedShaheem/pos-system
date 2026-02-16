@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Purchase Old Gold Details</h4>
                    <div>
                        {{-- <a href="{{ route('purchase-old-gold.edit', $purchaseOldGold->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a> --}}
                        <a href="{{ route('purchase-old-gold.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Purchase Header Info --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                    <h6 class="card-title text-muted">Purchase Information</h6>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6"><strong>Invoice No:</strong></div>
                                        <div class="col-6">
                                            <span class="badge badge-info badge-lg">#{{ $purchaseOldGold->invoice_no }}</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6"><strong>Status:</strong></div>
                                        <div class="col-6">
                                            <span class="badge badge-{{ $purchaseOldGold->status == 'completed' ? 'success' : 'warning' }} badge-lg">
                                                {{ ucfirst($purchaseOldGold->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6"><strong>Date:</strong></div>
                                        <div class="col-6">{{ $purchaseOldGold->created_at->format('Y-m-d H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                    <h6 class="card-title text-muted">Customer Information</h6>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-4"><strong>Name:</strong></div>
                                        <div class="col-8">{{ $purchaseOldGold->customer->name ?? 'N/A' }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-4"><strong>Phone:</strong></div>
                                        <div class="col-8">{{ $purchaseOldGold->customer->tel ?? 'N/A' }}</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-4"><strong>NIC:</strong></div>
                                        <div class="col-8">{{ $purchaseOldGold->customer->nic ?? 'N/A' }}</div>
                                    </div>                           
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Purchase Details --}}
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">Gold Items</h5>
                            @if($purchaseOldGold->details->count())
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Rate (Per Gram)</th>
                                                <th>Gold Grams</th>
                                                <th>Purchase Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($purchaseOldGold->details as $index => $detail)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $detail->goldRate->name }}</strong></td>
                                                <td><strong class="text-primary">{{ number_format($detail->gold_gram, 3) }} g</strong></td>
                                                <td><strong class="text-success">Rs. {{ number_format($detail->gold_purchased_amount, 2) }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="thead-light">
                                            <tr>
                                                <th colspan="2">Totals:</th>
                                                <th><strong class="text-primary">{{ number_format($totalGrams, 3) }} g</strong></th>
                                                <th><strong class="text-success">Rs. {{ number_format($totalAmount, 2) }}</strong></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> No gold items found for this purchase.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    {{-- <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-outline-primary mr-2 no-print" onclick="window.print()">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <a href="{{ route('purchase-old-gold.edit', $purchaseOldGold->id) }}" class="btn btn-warning mr-2 no-print">
                                <i class="fas fa-edit"></i> Edit Purchase
                            </a>
                            <button class="btn btn-outline-danger no-print" onclick="deletePurchase()">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div> --}}

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deletePurchase() {
    if (!confirm('Are you sure you want to delete this purchase record? This action cannot be undone.')) {
        return;
    }
    fetch('{{ route("purchase-old-gold.destroy", $purchaseOldGold->id) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("purchase-old-gold.index") }}';
        } else {
            alert('Error: ' + (data.message || 'Unable to delete.'));
        }
    })
    .catch(() => alert('Error deleting purchase record'));
}
</script>

@endsection
