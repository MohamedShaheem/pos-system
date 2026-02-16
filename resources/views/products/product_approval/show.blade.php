@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0"><i class="fas fa-code-branch mr-2"></i>Review Merge Request #{{ $pendingMerge->id }}</h1>
            <span class="badge badge-warning badge-lg">Pending Review</span>
        </div>
    </div>
</div>

<div class="content">
    <div class="container pb-3">
        <!-- Source Products Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-boxes mr-2"></i>Source Products</h4>
            </div>
            <div class="card-body">
                @foreach($pendingMerge->source_products_data as $key => $product)
                <div class="row align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="col-md-3">
                        <h6 class="text-primary mb-1">{{ $product['name'] }}</h6>
                        <small class="text-muted">{{ $product['product_no'] }}</small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong>{{ $product['weight'] }}g</strong>
                            <small class="text-muted">Weight</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong>{{ $product['wastage_weight'] }}g</strong>
                            <small class="text-muted">Wastage</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong>{{ $product['stone_weight'] }}g</strong>
                            <small class="text-muted">Stone</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="d-flex flex-column">
                            <strong>{{ number_format($product['making_charges'], 2) }}</strong>
                            <small class="text-muted">Making Charges</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Merged Product Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-plus-circle mr-2"></i>Merged Product Details</h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center py-3">
                    <div class="col-md-4">
                        <h6 class="text-success mb-1">{{ $pendingMerge->merged_product_data['name'] }}</h6>
                        <div class="small text-muted">
                            <div><strong>Category:</strong> {{ optional(\App\Models\ProductCategory::find($pendingMerge->merged_product_data['product_category_id']))->name }}</div>
                            <div><strong>Sub Category:</strong> {{ optional(\App\Models\SubCategory::find($pendingMerge->merged_product_data['sub_category_id']))->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong class="text-success">{{ $pendingMerge->merge_details['product_a_weight_portion'] + $pendingMerge->merge_details['product_b_weight_portion'] }}g</strong>
                            <small class="text-muted">Total Weight</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong class="text-success">{{ ($pendingMerge->merge_details['product_a_wastage_portion'] ?? 0) + ($pendingMerge->merge_details['product_b_wastage_portion'] ?? 0) }}g</strong>
                            <small class="text-muted">Total Wastage</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong class="text-success">{{ ($pendingMerge->merge_details['product_a_stone_portion'] ?? 0) + ($pendingMerge->merge_details['product_b_stone_portion'] ?? 0) }}g</strong>
                            <small class="text-muted">Total Stone</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong class="text-success">{{ number_format(($pendingMerge->merge_details['product_a_charges_portion'] ?? 0) + ($pendingMerge->merge_details['product_b_charges_portion'] ?? 0), 2) }}</strong>
                            <small class="text-muted">Total Charges</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leftover Product Section (if exists) -->
        @if($pendingMerge->leftover_product_data)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="fas fa-archive mr-2"></i>Leftover Product</h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center py-3">
                    <div class="col-md-4">
                        <h6 class="text-info mb-1">{{ $pendingMerge->leftover_product_data['name'] }}</h6>
                        <div class="small text-muted">
                            <div><strong>Category:</strong> {{ optional(\App\Models\ProductCategory::find($pendingMerge->leftover_product_data['product_category_id']))->name }}</div>
                            <div><strong>Sub Category:</strong> {{ optional(\App\Models\SubCategory::find($pendingMerge->leftover_product_data['sub_category_id']))->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong class="text-info">{{ $pendingMerge->leftover_product_data['weight'] }}g</strong>
                            <small class="text-muted">Weight</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong class="text-info">{{ $pendingMerge->leftover_product_data['wastage_weight'] }}g</strong>
                            <small class="text-muted">Wastage</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong class="text-info">{{ $pendingMerge->leftover_product_data['stone_weight'] }}g</strong>
                            <small class="text-muted">Stone</small>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="d-flex flex-column">
                            <strong class="text-info">{{ number_format($pendingMerge->leftover_product_data['making_charges'], 2) }}</strong>
                            <small class="text-muted">Charges</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Summary Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="fas fa-calculator mr-2"></i>Merge Summary</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-2">
                            <span>Total Source Products:</span>
                            <strong>{{ count($pendingMerge->source_products_data) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span>Products After Merge:</span>
                            <strong>{{ $pendingMerge->leftover_product_data ? '2' : '1' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-2">
                            <span>Merge Status:</span>
                            <span class="badge badge-warning">Pending Review</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span>Requested On:</span>
                            <strong>{{ $pendingMerge->created_at->format('M d, Y H:i') }}</strong>
                        </div>
                    </div>
                </div>
                
                @if(isset($pendingMerge->merge_details['create_option']) && $pendingMerge->merge_details['create_option'] === 'damage')
                @php
                    // Calculate combined leftover weight safely
                    $combinedLeftoverWeight = 0;
                    
                    try {
                        $sourceProducts = $pendingMerge->source_products_data ?? [];
                        $mergeDetails = $pendingMerge->merge_details ?? [];
                        
                        if (!empty($sourceProducts) && !empty($mergeDetails)) {
                            // Get source products - handle both array formats
                            $productA = isset($sourceProducts['product_a']) ? $sourceProducts['product_a'] : ($sourceProducts[0] ?? []);
                            $productB = isset($sourceProducts['product_b']) ? $sourceProducts['product_b'] : ($sourceProducts[1] ?? []);
                            
                            // Calculate leftover weight from Product A
                            $productAWeight = $productA['weight'] ?? 0;
                            $productAWeightPortion = $mergeDetails['product_a_weight_portion'] ?? 0;
                            $leftoverWeightA = max(0, $productAWeight - $productAWeightPortion);
                            
                            // Calculate leftover weight from Product B
                            $productBWeight = $productB['weight'] ?? 0;
                            $productBWeightPortion = $mergeDetails['product_b_weight_portion'] ?? 0;
                            $leftoverWeightB = max(0, $productBWeight - $productBWeightPortion);
                            
                            // Total leftover weight (this is what goes to gold balance)
                            $combinedLeftoverWeight = $leftoverWeightA + $leftoverWeightB;
                        }
                    } catch (Exception $e) {
                        // Handle any errors gracefully
                        $combinedLeftoverWeight = 0;
                    }
                @endphp
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-2">
                            <span><i class="fas fa-balance-scale mr-2 text-primary" style="font-weight: 600;"></i>Leftover Gold Balance:</span>
                            <strong class="badge badge-primary" style="font-size: 15px;">{{ number_format($combinedLeftoverWeight, 3) }}g</strong>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow-sm">
            <div class="card-body text-right">
                <form action="{{ route('products.merge.approval.approve', $pendingMerge->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success mr-3">
                        <i class="fas fa-check mr-2"></i>Approve Merge
                    </button>
                </form>
                
                <button type="button" class="btn btn-danger mr-3" data-toggle="modal" data-target="#rejectModal">
                    <i class="fas fa-times mr-2"></i>Reject Merge
                </button>
                
                <a href="{{ route('products.merge.approval.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-m" role="document">
        <div class="modal-content">
            <form action="{{ route('products.merge.approval.reject', $pendingMerge->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Reject Merge Request</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason" class="font-weight-bold">Are you sure you want to reject this product?<span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban mr-2"></i>Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.badge-lg {
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

.card {
    border: none;
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.card-header {
    border-bottom: 3px solid rgba(255,255,255,0.2);
}

.btn-lg {
    padding: 12px 30px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.modal-content {
    border-radius: 10px;
    overflow: hidden;
}

.alert {
    border-left: 4px solid #f39c12;
}

@media (max-width: 768px) {
    .row.align-items-center > [class*="col-"] {
        text-align: center !important;
        margin-bottom: 15px;
    }
    
    .btn-lg {
        width: 100%;
        margin-bottom: 10px;
        margin-right: 0 !important;
    }
}
</style>
@endsection