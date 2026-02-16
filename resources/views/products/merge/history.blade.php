@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)
@section('content')

<style>
.badge {
    font-size: 0.95rem;
    padding: 4px 8px;
    margin: 2px;
    font-weight: 500;
}

.product-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 8px;
    font-size: 0.85rem;
}

.product-card .product-name {
    font-weight: 600;
    font-size: 0.9rem;
    color: #2c3e50;
    margin-bottom: 4px;
}

.product-card .product-no {
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 6px;
}

.property-row {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 6px;
}

.property-badge {
    font-size: 0.7rem;
    padding: 3px 6px;
    white-space: nowrap;
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    font-weight: 600;
    font-size: 0.75rem;
    margin-right: 6px;
}

.step-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.merge-flow {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.merge-arrow {
    color: #28a745;
    font-size: 1.2rem;
}

.portions-list {
    font-size: 0.75rem;
    line-height: 1.6;
    color: #495057;
}

.portions-list li {
    margin-bottom: 3px;
}

.table-compact {
    font-size: 0.85rem;
}

.table-compact th {
    padding: 10px 8px;
    font-weight: 600;
    background: #f8f9fa;
    font-size: 0.8rem;
    vertical-align: middle;
}

.table-compact td {
    padding: 10px 8px;
    vertical-align: top;
}

.date-badge {
    font-size: 0.75rem;
    white-space: nowrap;
}

.user-badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

.leftover-empty {
    font-size: 0.75rem;
    color: #6c757d;
    font-style: italic;
}

/* Compact pagination */
.pagination {
    margin: 0;
}

.pagination .page-link {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
}

@media (max-width: 768px) {
    .table-compact {
        font-size: 0.75rem;
    }
    
    .product-card {
        padding: 8px;
    }
    
    .step-number {
        width: 20px;
        height: 20px;
        font-size: 0.7rem;
    }
}
</style>

<div class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-size: 1.5rem;">Merge History</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('products.merge.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Merge
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-compact table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 120px;">Date & User</th>
                                        <th style="width: 25%;">
                                            <span class="step-number">1</span> Source Products
                                        </th>
                                        <th style="width: 20%;">
                                            <span class="step-number">2</span> Portions Taken
                                        </th>
                                        <th style="width: 20%;">
                                            <span class="step-number">3</span> Merged Product
                                        </th>
                                        <th style="width: 20%;">
                                            <span class="step-number">4</span> Leftover
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mergeHistory as $history)
                                        @php
                                            $sourceA = $history->details->where('type', 'source_a')->first();
                                            $sourceB = $history->details->where('type', 'source_b')->first();
                                            $sourceAData = $sourceA ? json_decode($sourceA->product_data, true) : [];
                                            $sourceBData = $sourceB ? json_decode($sourceB->product_data, true) : [];
                                            $mergedDetail = $history->details->where('type', 'merged')->first();
                                            $mergedData = $mergedDetail ? json_decode($mergedDetail->product_data, true) : [];
                                            $leftoverDetail = $history->details->where('type', 'leftover')->first();
                                            $leftoverData = $leftoverDetail ? json_decode($leftoverDetail->product_data, true) : [];
                                        @endphp
                                        <tr>
                                            <!-- Date & User Column -->
                                            <td>
                                                <div class="date-badge badge badge-light mb-2">
                                                    <i class="far fa-calendar"></i>
                                                    {{ $history->merged_at->format('M d, Y') }}
                                                </div>
                                                <div class="date-badge badge badge-light mb-2">
                                                    <i class="far fa-clock"></i>
                                                    {{ $history->merged_at->format('h:i A') }}
                                                </div>
                                                <div class="user-badge badge badge-primary">
                                                    <i class="far fa-user"></i>
                                                    {{ $history->mergedBy->name ?? 'N/A' }}
                                                </div>
                                            </td>

                                            <!-- Source Products Column -->
                                            <td>
                                                @if(!empty($sourceAData))
                                                <div class="product-card">
                                                    <div class="product-name">Product A: {{ $sourceAData['name'] ?? 'N/A' }}</div>
                                                    <div class="product-no">#{{ $sourceAData['product_no'] ?? 'N/A' }}</div>
                                                    <div class="property-row">
                                                        <span class="badge badge-info property-badge">
                                                            W: {{ number_format($sourceAData['original_weight'] ?? 0, 3) }}g
                                                        </span>
                                                        @if(($sourceAData['original_wastage'] ?? 0) > 0)
                                                        <span class="badge badge-secondary property-badge">
                                                            Wst: {{ number_format($sourceAData['original_wastage'], 3) }}g
                                                        </span>
                                                        @endif
                                                        @if(($sourceAData['original_stone'] ?? 0) > 0)
                                                        <span class="badge badge-secondary property-badge">
                                                            St: {{ number_format($sourceAData['original_stone'], 3) }}g
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <div class="merge-arrow text-center">
                                                    <i class="fas fa-plus"></i>
                                                </div>
                                                
                                                @if(!empty($sourceBData))
                                                <div class="product-card">
                                                    <div class="product-name">Product B: {{ $sourceBData['name'] ?? 'N/A' }}</div>
                                                    <div class="product-no">#{{ $sourceBData['product_no'] ?? 'N/A' }}</div>
                                                    <div class="property-row">
                                                        <span class="badge badge-info property-badge">
                                                            W: {{ number_format($sourceBData['original_weight'] ?? 0, 3) }}g
                                                        </span>
                                                        @if(($sourceBData['original_wastage'] ?? 0) > 0)
                                                        <span class="badge badge-secondary property-badge">
                                                            Wst: {{ number_format($sourceBData['original_wastage'], 3) }}g
                                                        </span>
                                                        @endif
                                                        @if(($sourceBData['original_stone'] ?? 0) > 0)
                                                        <span class="badge badge-secondary property-badge">
                                                            St: {{ number_format($sourceBData['original_stone'], 3) }}g
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                            </td>

                                            <!-- Portions Taken Column -->
                                            <td>
                                                @if(!empty($sourceAData['portion_weight']))
                                                <div class="mb-2">
                                                    <strong style="font-size: 0.8rem; color: #495057;">From Product A:</strong>
                                                    <ul class="portions-list pl-3 mb-0 mt-1">
                                                        <li>Weight: <strong>{{ number_format($sourceAData['portion_weight'], 3) }}g</strong></li>
                                                        @if(($sourceAData['portion_wastage'] ?? 0) > 0)
                                                        <li>Wastage: {{ number_format($sourceAData['portion_wastage'], 3) }}g</li>
                                                        @endif
                                                        @if(($sourceAData['portion_stone'] ?? 0) > 0)
                                                        <li>Stone: {{ number_format($sourceAData['portion_stone'], 3) }}g</li>
                                                        @endif
                                                        @if(($sourceAData['portion_charges'] ?? 0) > 0)
                                                        <li>Charges: {{ number_format($sourceAData['portion_charges'], 2) }}</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                @endif
                                                
                                                @if(!empty($sourceBData['portion_weight']))
                                                <div>
                                                    <strong style="font-size: 0.8rem; color: #495057;">From Product B:</strong>
                                                    <ul class="portions-list pl-3 mb-0 mt-1">
                                                        <li>Weight: <strong>{{ number_format($sourceBData['portion_weight'], 3) }}g</strong></li>
                                                        @if(($sourceBData['portion_wastage'] ?? 0) > 0)
                                                        <li>Wastage: {{ number_format($sourceBData['portion_wastage'], 3) }}g</li>
                                                        @endif
                                                        @if(($sourceBData['portion_stone'] ?? 0) > 0)
                                                        <li>Stone: {{ number_format($sourceBData['portion_stone'], 3) }}g</li>
                                                        @endif
                                                        @if(($sourceBData['portion_charges'] ?? 0) > 0)
                                                        <li>Charges: {{ number_format($sourceBData['portion_charges'], 2) }}</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                @endif
                                            </td>

                                            <!-- Merged Product Column -->
                                            <td>
                                                @if(!empty($mergedData))
                                                <div class="product-card" style="background: #d4edda; border-color: #28a745;">
                                                    <div class="product-name" style="color: #155724;">
                                                        {{ $mergedData['name'] ?? 'N/A' }}
                                                    </div>
                                                    <div class="product-no">#{{ $mergedData['product_no'] ?? 'N/A' }}</div>
                                                    <div class="property-row">
                                                        <span class="badge badge-success property-badge">
                                                            W: {{ number_format($mergedData['weight'] ?? 0, 3) }}g
                                                        </span>
                                                        @if(($mergedData['wastage_weight'] ?? 0) > 0)
                                                        <span class="badge badge-success property-badge">
                                                            Wst: {{ number_format($mergedData['wastage_weight'], 3) }}g
                                                        </span>
                                                        @endif
                                                        @if(($mergedData['stone_weight'] ?? 0) > 0)
                                                        <span class="badge badge-success property-badge">
                                                            St: {{ number_format($mergedData['stone_weight'], 3) }}g
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                            </td>

                                            <!-- Leftover Column -->
                                            <td>
                                                @if(!empty($leftoverData))
                                                <div class="product-card" style="background: #fff3cd; border-color: #ffc107;">
                                                    <div class="product-name" style="color: #856404;">
                                                        {{ $leftoverData['name'] ?? 'N/A' }}
                                                    </div>
                                                    <div class="product-no">#{{ $leftoverData['product_no'] ?? 'N/A' }}</div>
                                                    <div class="property-row">
                                                        <span class="badge badge-warning property-badge">
                                                            W: {{ number_format($leftoverData['weight'] ?? 0, 3) }}g
                                                        </span>
                                                        @if(($leftoverData['wastage_weight'] ?? 0) > 0)
                                                        <span class="badge badge-warning property-badge">
                                                            Wst: {{ number_format($leftoverData['wastage_weight'], 3) }}g
                                                        </span>
                                                        @endif
                                                        @if(($leftoverData['stone_weight'] ?? 0) > 0)
                                                        <span class="badge badge-warning property-badge">
                                                            St: {{ number_format($leftoverData['stone_weight'], 3) }}g
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @else
                                                <div class="text-center">
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-balance-scale"></i> Added to Gold Balance
                                                    </span>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p class="mb-0">No merge history found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <div class="text-muted" style="font-size: 0.8rem;">
                                Showing {{ $mergeHistory->firstItem() ?? 0 }} to {{ $mergeHistory->lastItem() ?? 0 }} 
                                of {{ $mergeHistory->total() }} entries
                            </div>
                            <div>
                                {{ $mergeHistory->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection