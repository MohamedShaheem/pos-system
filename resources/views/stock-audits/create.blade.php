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
                <h1>Start New Stock Audit</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-audits.index') }}">Stock Audits</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content pb-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="bi bi-clipboard-check"></i> Audit Configuration
                        </h3>
                    </div>
                    <form action="{{ route('stock-audits.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Audit Type Selection -->
                            <div class="form-group">
                                <label>
                                    Audit Type <span class="text-danger">*</span>
                                </label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" 
                                                   id="audit_type_category" 
                                                   name="audit_type" 
                                                   value="category" 
                                                   class="custom-control-input" 
                                                   checked>
                                            <label class="custom-control-label" for="audit_type_category">
                                                <strong>Category Audit</strong>
                                                <br>
                                                <small class="text-muted">Audit products from a specific category</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" 
                                                   id="audit_type_all" 
                                                   name="audit_type" 
                                                   value="all" 
                                                   class="custom-control-input">
                                            <label class="custom-control-label" for="audit_type_all">
                                                <strong>Complete Inventory Audit</strong>
                                                <br>
                                                <small class="text-muted">Audit ALL products in stock</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Category Selection (shown only for category audit) -->
                            <div class="form-group" id="category-selection">
                                <label for="product_category_id">
                                    Select Category to Audit <span class="text-danger">*</span>
                                </label>
                                <select name="product_category_id" id="product_category_id" class="form-control">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        @php
                                            $productCount = \App\Models\Product::where('product_category_id', $category->id)
                                                ->where('qty', '>', 0)
                                                ->where('is_approved', 1)
                                                ->where('status', 'active')
                                                ->whereIn('product_type', ['gold', 'silver'])
                                                ->count();
                                        @endphp
                                        <option value="{{ $category->id }}" data-count="{{ $productCount }}">
                                            {{ $category->name }} ({{ $productCount }} products)
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Only products currently in stock will be included in the audit.
                                </small>
                            </div>

                            <div id="category-info" class="alert alert-info" style="display: none;">
                                <i class="bi bi-info-circle"></i>
                                <strong>Expected Products:</strong> <span id="expected-count">0</span>
                                <br>
                                <small>You will need to scan all these products during the audit.</small>
                            </div>

                            <!-- All Products Info (shown only for all audit) -->
                            <div id="all-products-info" class="alert alert-primary" style="display: none;">
                                <i class="bi bi-info-circle"></i>
                                <strong>Complete Inventory Audit</strong>
                                <br>
                                Expected Products: <strong>{{ \App\Models\Product::where('qty', '=', 1)->where('is_approved', 1)->where('status', 'active')->count() }}</strong>
                                <br>
                                <small>You will audit ALL products across all categories currently in stock.</small>
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes (Optional)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" 
                                    placeholder="Add any notes about this audit (e.g., reason for audit, special instructions)"></textarea>
                            </div>

                            <div class="alert alert-warning">
                                <h5><i class="bi bi-exclamation-triangle"></i> Before You Start:</h5>
                                <ul class="mb-0">
                                    <li>Make sure you have a barcode scanner ready</li>
                                    <li id="gather-text">Gather all physical products from the selected category</li>
                                    <li>The audit will track which products are scanned</li>
                                    <li>You can pause and resume the audit anytime</li>
                                    <li>Missing products will be identified when you complete the audit</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-play-circle"></i> Start Audit
                            </button>
                            <a href="{{ route('stock-audits.index') }}" class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handle audit type change
        $('input[name="audit_type"]').change(function() {
            var auditType = $(this).val();
            
            if (auditType === 'all') {
                $('#category-selection').slideUp();
                $('#category-info').slideUp();
                $('#all-products-info').slideDown();
                $('#product_category_id').prop('required', false);
                $('#gather-text').text('Gather all physical products from your inventory');
            } else {
                $('#category-selection').slideDown();
                $('#all-products-info').slideUp();
                $('#product_category_id').prop('required', true);
                $('#gather-text').text('Gather all physical products from the selected category');
                
                // Trigger change to show count if already selected
                $('#product_category_id').trigger('change');
            }
        });

        // Handle category selection change
        $('#product_category_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var count = selectedOption.data('count');
            
            if (count > 0) {
                $('#expected-count').text(count);
                $('#category-info').slideDown();
            } else {
                $('#category-info').slideUp();
            }
        });
    });
</script>
@endsection