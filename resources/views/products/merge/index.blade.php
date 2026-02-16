@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)

@section('content')
<!-- Add Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px) !important;
}

input.border-danger,
select.border-danger {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.1rem rgba(220, 53, 69, 0.25);
}

/* Invalid feedback styling */
.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Focus state for required fields */
input[required]:focus,
select[required]:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* SweetAlert custom styling */
.swal2-html-container ul {
    text-align: left;
    padding-left: 20px;
}

.swal2-html-container ul li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.product-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.product-properties {
    margin-top: 10px;
}

.product-properties span {
    display: inline-block;
    margin-right: 15px;
    font-size: 0.9rem;
    color: #6c757d;
}

.portion-input {
    margin-bottom: 10px;
}

.portion-input label {
    font-weight: 500;
    color: #495057;
}

.merge-arrow {
    text-align: center;
    font-size: 2rem;
    color: #28a745;
    margin: 20px 0;
}

.leftover-section {
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
    background: #fff3cd;
}

.leftover-section h6 {
    color: #856404;
    margin-bottom: 15px;
}

.merged-product-section {
    border: 1px solid #28a745;
    border-radius: 8px;
    padding: 15px;
    background: #d4edda;
}

.merged-product-section h6 {
    color: #155724;
    margin-bottom: 15px;
}

.hidden {
    display: none;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/*  */
.step-indicator {
    margin: 40px 0;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    padding: 0 20px;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 15px;
    left: 50px;
    right: 50px;
    height: 3px;
    background: #e9ecef;
    z-index: 0;
}

.step-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
    flex: 1;
    min-width: 0;
    padding: 0 10px;
}

.step {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-bottom: 10px;
    border: 3px solid #e9ecef;
    transition: all 0.3s ease;
    font-size: 14px;
    box-sizing: border-box;
}

.step.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
    box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.2);
}

.step.completed {
    background: #28a745;
    color: white;
    border-color: #28a745;
}

.step-label {
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
    text-align: center;
    max-width: 100px;
    line-height: 1.4;
    word-break: break-word;
    white-space: normal;
}

.step-label {
    font-weight: 600;
}

.step-label.completed {
    color: #28a745;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .step-indicator {
        padding: 0 10px;
    }
    
    .step {
        width: 28px;
        height: 28px;
        font-size: 13px;
    }
    
    .step-label {
        font-size: 12px;
        max-width: 70px;
    }
    
    .step-indicator::before {
        top: 14px;
        left: 40px;
        right: 40px;
    }
}

@media (max-width: 480px) {
    .step-label {
        font-size: 11px;
        max-width: 60px;
    }
    
    .step-indicator::before {
        left: 30px;
        right: 30px;
    }
}
/*  */


.preview-section {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.preview-section h6 {
    color: #495057;
    margin-bottom: 15px;
}

.leftover-preview {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 10px;
    margin-top: 10px;
}

.nav-buttons {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
}
</style>

<div class="content-header">
    <div class="container">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Merge Products</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('products.merge.history')}}" class="btn btn-primary">Merge History</a>
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
                        <!-- Step Indicator -->
                      <div class="step-indicator">
    <div class="step-container">
        <span class="step active" id="step1">1</span>
        <span class="step-label active">Select Products</span>
    </div>
    <div class="step-container">
        <span class="step" id="step2">2</span>
        <span class="step-label">Define Portions</span>
    </div>
    <div class="step-container">
        <span class="step" id="step3">3</span>
        <span class="step-label">Merged Product Details</span>
    </div>
    <div class="step-container">
        <span class="step" id="step4">4</span>
        <span class="step-label">Leftover Products</span>
    </div>
</div>

                        <form action="{{ route('products.merge.store') }}" method="POST" id="mergeFrm">
                            {{-- <form action="{{ Auth::user()->role->name === 'superadmin' ? route('products.super.merge.store') : route('products.merge.store') }}" method="POST" id="mergeFrm"> --}}
                            @csrf
                            <input type="hidden" name="merge_type" value="2-1">

                            <!-- Step 1: Select Products -->
                            <div class="step-content" id="step1Content">
                                <h4>Step 1: Select Products to Merge</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>First Product</label>
                                            <select name="source_products[]" class="form-control select2-products" id="productA" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                        data-weight="{{ $product->weight }}"
                                                        data-wastage="{{ $product->wastage_weight }}"
                                                        data-stone="{{ $product->stone_weight }}"
                                                        data-charges="{{ $product->making_charges }}"
                                                        data-name="{{ $product->name }}"
                                                        data-product-no="{{ $product->product_no }}"
                                                        data-qty="{{ $product->qty }}"
                                                        data-category="{{ $product->product_category_id }}"
                                                        data-gold-rate="{{ $product->gold_rate_id }}">
                                                        ({{ $product->product_no }}) {{ $product->name }}  - {{ $product->weight }}g ({{ optional($product->goldRate)->name ?? 'No Rate' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="productADetails" class="product-card hidden">
                                            <div class="product-name"></div>
                                            <div class="product-properties">
                                                <span>Weight: <strong class="weight">0</strong>g</span>
                                                <span>Wastage: <strong class="wastage">0</strong>g</span>
                                                <span>Stone: <strong class="stone">0</strong>g</span>
                                                <span>Charges: <strong class="charges">0</strong></span>
                                                <span>Qty: <strong class="qty">0</strong></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Second Product</label>
                                            <select name="source_products[]" class="form-control select2-products" id="productB" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                        data-weight="{{ $product->weight }}"
                                                        data-wastage="{{ $product->wastage_weight }}"
                                                        data-stone="{{ $product->stone_weight }}"
                                                        data-charges="{{ $product->making_charges }}"
                                                        data-name="{{ $product->name }}"
                                                        data-product-no="{{ $product->product_no }}"
                                                        data-qty="{{ $product->qty }}"
                                                        data-category="{{ $product->product_category_id }}"
                                                        data-gold-rate="{{ $product->gold_rate_id }}">
                                                       ({{ $product->product_no }}) {{ $product->name }}  - {{ $product->weight }}g ({{ optional($product->goldRate)->name ?? 'No Rate' }})

                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="productBDetails" class="product-card hidden">
                                            <div class="product-name"></div>
                                            <div class="product-properties">
                                                <span>Weight: <strong class="weight">0</strong>g</span>
                                                <span>Wastage: <strong class="wastage">0</strong>g</span>
                                                <span>Stone: <strong class="stone">0</strong>g</span>
                                                <span>Charges: <strong class="charges">0</strong></span>
                                                <span>Qty: <strong class="qty">0</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nav-buttons">
                                    <button type="button" class="btn btn-primary" onclick="nextStep(2)" id="step1Next" disabled>Next: Define Portions</button>
                                </div>
                            </div>

                            <!-- Step 2: Define Portions -->
                            <div class="step-content hidden" id="step2Content">
                                <h4>Step 2: Define Portions to Take from Each Product</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Portions from Product A</h5>
                                        <div class="portion-input">
                                            <label>Weight Portion (g) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="product_a_weight_portion" step="0.001" min="0.001" required>
                                            <small class="text-muted">Available: <span id="productAMaxWeight">0</span>g</small>
                                        </div>
                                        <div class="portion-input">
                                            <label>Wastage Weight Portion (g)</label>
                                            <input type="number" class="form-control" name="product_a_wastage_portion" step="0.001" min="0">
                                            <small class="text-muted">Available: <span id="productAMaxWastage">0</span>g</small>
                                        </div>
                                        <div class="portion-input">
                                            <label>Stone Weight Portion (g)</label>
                                            <input type="number" class="form-control" name="product_a_stone_portion" step="0.001" min="0">
                                            <small class="text-muted">Available: <span id="productAMaxStone">0</span>g</small>
                                        </div>
                                        <div class="portion-input">
                                            <label>Making Charges Portion</label>
                                            <input type="number" class="form-control" name="product_a_charges_portion" step="0.01" min="0">
                                            <small class="text-muted">Available: <span id="productAMaxCharges">0</span></small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h5>Portions from Product B</h5>
                                        <div class="portion-input">
                                            <label>Weight Portion (g) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="product_b_weight_portion" step="0.001" min="0.001" required>
                                            <small class="text-muted">Available: <span id="productBMaxWeight">0</span>g</small>
                                        </div>
                                        <div class="portion-input">
                                            <label>Wastage Weight Portion (g)</label>
                                            <input type="number" class="form-control" name="product_b_wastage_portion" step="0.001" min="0">
                                            <small class="text-muted">Available: <span id="productBMaxWastage">0</span>g</small>
                                        </div>
                                        <div class="portion-input">
                                            <label>Stone Weight Portion (g)</label>
                                            <input type="number" class="form-control" name="product_b_stone_portion" step="0.001" min="0">
                                            <small class="text-muted">Available: <span id="productBMaxStone">0</span>g</small>
                                        </div>
                                        <div class="portion-input">
                                            <label>Making Charges Portion</label>
                                            <input type="number" class="form-control" name="product_b_charges_portion" step="0.01" min="0">
                                            <small class="text-muted">Available: <span id="productBMaxCharges">0</span></small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Live Preview -->
                                <div class="preview-section">
                                    <h6>Live Preview</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="leftover-preview">
                                                <strong>Product A Leftover:</strong>
                                                <div id="leftoverAPreview">
                                                    <small>Weight: <span id="leftoverAWeight">0</span>g | Wastage: <span id="leftoverAWastage">0</span>g | Stone: <span id="leftoverAStone">0</span>g | Charges: <span id="leftoverACharges">0</span></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="leftover-preview">
                                                <strong>Product B Leftover:</strong>
                                                <div id="leftoverBPreview">
                                                    <small>Weight: <span id="leftoverBWeight">0</span>g | Wastage: <span id="leftoverBWastage">0</span>g | Stone: <span id="leftoverBStone">0</span>g | Charges: <span id="leftoverBCharges">0</span></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="nav-buttons">
                                    <button type="button" class="btn btn-secondary" onclick="prevStep(1)">Previous</button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(3)" id="step2Next">Next: Merged Product Details</button>
                                </div>
                            </div>

                            <!-- Step 3: Merged Product Details -->
                            <div class="step-content hidden" id="step3Content">
                                <h4>Step 3: Merged Product Details</h4>
                                <div class="merged-product-section">
                                    <h6>New Merged Product</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Product Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="merged_product_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 hidden">
                                            <div class="form-group">
                                                <label>Quantity <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="merged_product_qty" min="1" value="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Supplier <span class="text-danger">*</span></label>
                                                <select name="merged_product_supplier_id" class="form-control select2" required>
                                                    <option value="">Select Supplier</option>
                                                    @foreach($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">{{ $supplier->short_code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                   <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Category <span class="text-danger">*</span></label>
                                                <select name="merged_product_category_id" id="product_category_id" class="form-control select2" required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Sub Category <span class="text-danger">*</span></label>
                                                <select name="merged_product_sub_category_id" id="sub_category_id" class="form-control select2" required>
                                                    <option value="">Select Sub Category</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        
                                        <div class="col-md-6" style="display: none;">
                                            <div class="form-group">
                                                <label>Gold Rate <span class="text-danger">*</span></label>
                                                <select name="merged_product_gold_rate_id" class="form-control select2" required>
                                                    <option value="">Select Gold Rate</option>
                                                    @foreach($goldRates as $rate)
                                                        <option value="{{ $rate->id }}">{{ $rate->name }} - {{ $rate->rate }}/g</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="merged_product_desc" rows="3"></textarea>
                                    </div>
                                    
                                    <!-- Merged Product Properties Preview -->
                                    <div class="preview-section">
                                        <h6>Merged Product Properties</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Total Weight:</strong> <span id="mergedWeight">0</span>g
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Total Wastage:</strong> <span id="mergedWastage">0</span>g
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Total Stone:</strong> <span id="mergedStone">0</span>g
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Total Charges:</strong> <span id="mergedCharges">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="nav-buttons">
                                    <button type="button" class="btn btn-secondary" onclick="prevStep(2)">Previous</button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(4)" id="step3Next">Next: Leftover Products</button>
                                </div>
                            </div>

                           <!-- Step 4: Combined Leftover Product -->
                            <div class="step-content hidden" id="step4Content">
                                <h4>Step 4: Combined Leftover Product (Optional)</h4>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="leftover-section">
                                            <h6>Combined Leftover Product</h6>
                                           <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="create_option" id="createLeftover" value="leftover">
                                                <label class="form-check-label" for="createLeftover">
                                                    Create combined leftover product from both source products
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="create_option" id="createDamage" value="damage">
                                                <label class="form-check-label" for="createDamage">
                                                    Add to Gold Balance Tbl (Damage)
                                                </label>
                                            </div>

                                            
                                            <!-- Combined Leftover Preview -->
                                            <div class="preview-section mb-3">
                                                <h6>Combined Leftover Properties</h6>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Total Weight:</strong> <span id="combinedLeftoverWeight">0</span>g
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Total Wastage:</strong> <span id="combinedLeftoverWastage">0</span>g
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Total Stone:</strong> <span id="combinedLeftoverStone">0</span>g
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Total Charges:</strong> <span id="combinedLeftoverCharges">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div id="leftoverFields" class="hidden">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Leftover Product Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="leftover_name">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 hidden">
                                                        <div class="form-group">
                                                            <label>Quantity <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="leftover_qty" min="1" value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Supplier <span class="text-danger">*</span></label>
                                                            <select name="leftover_supplier_id" class="form-control select2">
                                                                <option value="">Select Supplier</option>
                                                                @foreach($suppliers as $supplier)
                                                                    <option value="{{ $supplier->id }}">{{ $supplier->short_code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Category <span class="text-danger">*</span></label>
                                                            <select name="leftover_category_id" id="leftover_category_id" class="form-control select2">
                                                                <option value="">Select Category</option>
                                                                @foreach($categories as $category)
                                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Sub Category <span class="text-danger">*</span></label>
                                                            <select name="leftover_sub_category_id" id="leftover_sub_category_id" class="form-control select2">
                                                                <option value="">Select Sub Category</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row" style="display: none;">                                    
                                                    <div class="col-md-6" style="display: none;">
                                                        <div class="form-group">
                                                            <label>Gold Rate <span class="text-danger">*</span></label>
                                                            <select name="leftover_gold_rate_id" class="form-control select2">
                                                                <option value="">Select Gold Rate</option>
                                                                @foreach($goldRates as $rate)
                                                                    <option value="{{ $rate->id }}">{{ $rate->name }} - {{ $rate->rate }}/g</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="leftover_desc" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="nav-buttons">
                                    <button type="button" class="btn btn-secondary" onclick="prevStep(3)">Previous</button>
                                    <button type="submit" class="btn btn-success" id="submitBtn">Complete Merge</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });
    
    $('.select2-products').select2({
        theme: 'bootstrap4'
    });

    // Product selection change handlers
    $('#productA').on('change', function() {
        updateProductDetails('A');
        checkStep1Complete();
    });

    $('#productB').on('change', function() {
        updateProductDetails('B');
        checkStep1Complete();
    });

    // Portion input change handlers
    $('input[name^="product_a_"]').on('input', function() {
        validatePortionInput($(this));
        updateLeftoverPreview();
        updateMergedPreview();
        updateCombinedLeftovers();
    });

    $('input[name^="product_b_"]').on('input', function() {
        validatePortionInput($(this));
        updateLeftoverPreview();
        updateMergedPreview();
        updateCombinedLeftovers();
    });

    // Pre-fill merged product name when products are selected
    $('#productA, #productB').on('change', function() {
        const productAName = $('#productA option:selected').data('name');
        const productBName = $('#productB option:selected').data('name');
        
        if (productAName && productBName) {
            const suggestedName = productAName + ' + ' + productBName;
            if (!$('input[name="merged_product_name"]').val()) {
                $('input[name="merged_product_name"]').val(suggestedName);
            }
        }
    });



// $('input[name="create_option"]').on('change', function() {
//     const leftoverFields = $('#leftoverFields');
//     if ($(this).val() === 'leftover') {
//         leftoverFields.removeClass('hidden');
//         // Pre-fill leftover name
//         const productAName = $('#productA option:selected').data('name');
//         const productBName = $('#productB option:selected').data('name');
//         if (productAName && productBName) {
//             $('input[name="leftover_name"]').val(productAName + ' + ' + productBName + ' - Combined Leftover');
//         }
//         // **Add this line:**
//         setLeftoverGoldRateFromSelectedProduct('A'); // or 'B' depending on your logic
//     } else {
//         leftoverFields.addClass('hidden');
//         // Clear leftover gold rate if not leftover
//         $('select[name="leftover_gold_rate_id"]').val(null).trigger('change');
//     }
// });


$('input[name="create_option"]').on('change', function() {
    const leftoverFields = $('#leftoverFields');
    if ($(this).val() === 'leftover') {
        leftoverFields.removeClass('hidden');
        // Pre-fill leftover name
        const productAName = $('#productA option:selected').data('name');
        const productBName = $('#productB option:selected').data('name');
        if (productAName && productBName) {
            $('input[name="leftover_name"]').val(productAName + ' + ' + productBName + ' - Combined Leftover');
        }
        // Set gold rate and supplier from selected product
        setLeftoverGoldRateFromSelectedProduct('A');
        setLeftoverSupplierFromSelectedProduct('A');
    } else {
        leftoverFields.addClass('hidden');
        // Clear leftover fields if not leftover
        $('select[name="leftover_gold_rate_id"]').val(null).trigger('change');
        $('select[name="leftover_supplier_id"]').val(null).trigger('change');
    }
});


    // Form submission
    $('#mergeFrm').on('submit', function(e) {
        e.preventDefault();
        

    if (!validateStep4()) {
        return false;
    }

        Swal.fire({
            title: 'Confirm Product Merge',
            text: 'Are you sure you want to merge these products? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Yes, merge products!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Merging products, please wait.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                
                // Submit the form
                this.submit();
            }
        });
    });
////////////

// real-time validation feedback for required fields
$(document).ready(function() {
    // Highlight empty required fields on blur
    $('input[required], select[required]').on('blur', function() {
        if (!$(this).val()) {
            $(this).addClass('border-danger');
        } else {
            $(this).removeClass('border-danger');
        }
    });
    
    // Remove highlight when field is filled
    $('input[required], select[required]').on('change input', function() {
        if ($(this).val()) {
            $(this).removeClass('border-danger');
        }
    });
});

////////


function checkSubCategoryRequired(categorySelectId, subCategorySelectId) {
    const categoryId = $(categorySelectId).val();
    const $subCategoryField = $(subCategorySelectId);
    
    if (categoryId) {
        $.ajax({
            url: '{{ route("get.subcategories") }}',
            type: 'GET',
            data: { category_id: categoryId },
            success: function(data) {
                if (data.length > 0) {
                    // Category has subcategories - make it required
                    $subCategoryField.prop('required', true);
                    $subCategoryField.closest('.form-group').find('label').html('Sub Category <span class="text-danger">*</span>');
                } else {
                    // Category has no subcategories - make it optional
                    $subCategoryField.prop('required', false);
                    $subCategoryField.closest('.form-group').find('label').text('Sub Category');
                }
            }
        });
    }
}

// Call when category changes
$('#product_category_id').on('change', function() {
    checkSubCategoryRequired('#product_category_id', '#sub_category_id');
});

$('#leftover_category_id').on('change', function() {
    checkSubCategoryRequired('#leftover_category_id', '#leftover_sub_category_id');
});

     $('#product_category_id').on('change', function () {
            let categoryId = $(this).val();
            let $subCategory = $('#sub_category_id');
            $subCategory.empty().append('<option value="">Loading...</option>');

            if (categoryId) {
                $.ajax({
                    url: '{{ route("get.subcategories") }}',
                    type: 'GET',
                    data: { category_id: categoryId },
                    success: function (data) {
                        $subCategory.empty().append('<option value="">Select Sub Category</option>');
                        $.each(data, function (i, subcategory) {
                            $subCategory.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                        });
                    },
                    error: function () {
                        $subCategory.empty().append('<option value="">Error loading</option>');
                    }
                });
            } else {
                $subCategory.empty().append('<option value="">Select Sub Category</option>');
            }
        });


        // Leftover product category change handler
$('#leftover_category_id').on('change', function() {
    let categoryId = $(this).val();
    let $subCategory = $('#leftover_sub_category_id');
    $subCategory.empty().append('<option value="">Loading...</option>');

    if (categoryId) {
        $.ajax({
            url: '{{ route("get.subcategories") }}',
            type: 'GET',
            data: { category_id: categoryId },
            success: function(data) {
                $subCategory.empty().append('<option value="">Select Sub Category</option>');
                $.each(data, function(i, subcategory) {
                    $subCategory.append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                });
            },
            error: function() {
                $subCategory.empty().append('<option value="">Error loading</option>');
            }
        });
    } else {
        $subCategory.empty().append('<option value="">Select Sub Category</option>');
    }
});
});


function setLeftoverSupplierFromSelectedProduct(productId) {
    const selected = $('#product' + productId + ' option:selected');
    const supplierId = selected.data('supplier-id'); // Make sure your product options have this data attribute
    
    if (supplierId) {
        $('select[name="leftover_supplier_id"]').val(supplierId).trigger('change');
    }
}


function setMergedProductGoldRateFromSelectedProduct(productId) {
    var selectedOption = $('#product' + productId + ' option:selected');
    var goldRateId = selectedOption.data('gold-rate');
    
    if (goldRateId) {
        $('select[name="merged_product_gold_rate_id"]').val(goldRateId).trigger('change');
    }
}

function setLeftoverGoldRateFromSelectedProduct(productId) {
    const selected = $('#product' + productId + ' option:selected');
    const goldRateId = selected.data('gold-rate');

    if (goldRateId) {
        $('select[name="leftover_gold_rate_id"]').val(goldRateId).trigger('change');
    }
}



function updateProductDetails(productKey) {
    const select = $('#product' + productKey);
    const details = $('#product' + productKey + 'Details');
    const option = select.find('option:selected');
    
    if (option.val()) {
        const name = option.data('name');
        const productNo = option.data('product-no');
        const weight = option.data('weight');
        const wastage = option.data('wastage');
        const stone = option.data('stone');
        const charges = option.data('charges');
        const qty = option.data('qty');
        
        details.find('.product-name').text(name + ' (' + productNo + ')');
        details.find('.weight').text(weight);
        details.find('.wastage').text(wastage);
        details.find('.stone').text(stone);
        details.find('.charges').text(charges);
        details.find('.qty').text(qty);
        
        // Update max values for portions
        $('#product' + productKey + 'MaxWeight').text(weight);
        $('#product' + productKey + 'MaxWastage').text(wastage);
        $('#product' + productKey + 'MaxStone').text(stone);
        $('#product' + productKey + 'MaxCharges').text(charges);
        
        details.removeClass('hidden');
    } else {
        details.addClass('hidden');
    }
}

function checkStep1Complete() {
    const productA = $('#productA').val();
    const productB = $('#productB').val();
    
    if (productA && productB && productA !== productB) {
        $('#step1Next').prop('disabled', false);
    } else {
        $('#step1Next').prop('disabled', true);
    }
}

function updateLeftoverPreview() {
    // Update Product A leftover
    const productAOption = $('#productA option:selected');
    const productAWeight = parseFloat(productAOption.data('weight')) || 0;
    const productAWastage = parseFloat(productAOption.data('wastage')) || 0;
    const productAStone = parseFloat(productAOption.data('stone')) || 0;
    const productACharges = parseFloat(productAOption.data('charges')) || 0;
    
    const productAWeightPortion = parseFloat($('input[name="product_a_weight_portion"]').val()) || 0;
    const productAWastagePortion = parseFloat($('input[name="product_a_wastage_portion"]').val()) || 0;
    const productAStonePortion = parseFloat($('input[name="product_a_stone_portion"]').val()) || 0;
    const productAChargesPortion = parseFloat($('input[name="product_a_charges_portion"]').val()) || 0;
    
    const leftoverAWeight = productAWeight - productAWeightPortion;
    const leftoverAWastage = productAWastage - productAWastagePortion;
    const leftoverAStone = productAStone - productAStonePortion;
    const leftoverACharges = productACharges - productAChargesPortion;
    
    $('#leftoverAWeight').text(leftoverAWeight.toFixed(3));
    $('#leftoverAWastage').text(leftoverAWastage.toFixed(3));
    $('#leftoverAStone').text(leftoverAStone.toFixed(3));
    $('#leftoverACharges').text(leftoverACharges.toFixed(2));
    
    // Update Product B leftover
    const productBOption = $('#productB option:selected');
    const productBWeight = parseFloat(productBOption.data('weight')) || 0;
    const productBWastage = parseFloat(productBOption.data('wastage')) || 0;
    const productBStone = parseFloat(productBOption.data('stone')) || 0;
    const productBCharges = parseFloat(productBOption.data('charges')) || 0;
    
    const productBWeightPortion = parseFloat($('input[name="product_b_weight_portion"]').val()) || 0;
    const productBWastagePortion = parseFloat($('input[name="product_b_wastage_portion"]').val()) || 0;
    const productBStonePortion = parseFloat($('input[name="product_b_stone_portion"]').val()) || 0;
    const productBChargesPortion = parseFloat($('input[name="product_b_charges_portion"]').val()) || 0;
    
    const leftoverBWeight = productBWeight - productBWeightPortion;
    const leftoverBWastage = productBWastage - productBWastagePortion;
    const leftoverBStone = productBStone - productBStonePortion;
    const leftoverBCharges = productBCharges - productBChargesPortion;
    
    $('#leftoverBWeight').text(leftoverBWeight.toFixed(3));
    $('#leftoverBWastage').text(leftoverBWastage.toFixed(3));
    $('#leftoverBStone').text(leftoverBStone.toFixed(3));
    $('#leftoverBCharges').text(leftoverBCharges.toFixed(2));
}

function updateMergedPreview() {
    const productAWeightPortion = parseFloat($('input[name="product_a_weight_portion"]').val()) || 0;
    const productAWastagePortion = parseFloat($('input[name="product_a_wastage_portion"]').val()) || 0;
    const productAStonePortion = parseFloat($('input[name="product_a_stone_portion"]').val()) || 0;
    const productAChargesPortion = parseFloat($('input[name="product_a_charges_portion"]').val()) || 0;
    
    const productBWeightPortion = parseFloat($('input[name="product_b_weight_portion"]').val()) || 0;
    const productBWastagePortion = parseFloat($('input[name="product_b_wastage_portion"]').val()) || 0;
    const productBStonePortion = parseFloat($('input[name="product_b_stone_portion"]').val()) || 0;
    const productBChargesPortion = parseFloat($('input[name="product_b_charges_portion"]').val()) || 0;
    
    const mergedWeight = productAWeightPortion + productBWeightPortion;
    const mergedWastage = productAWastagePortion + productBWastagePortion;
    const mergedStone = productAStonePortion + productBStonePortion;
    const mergedCharges = productAChargesPortion + productBChargesPortion;
    
    $('#mergedWeight').text(mergedWeight.toFixed(3));
    $('#mergedWastage').text(mergedWastage.toFixed(3));
    $('#mergedStone').text(mergedStone.toFixed(3));
    $('#mergedCharges').text(mergedCharges.toFixed(2));
}

function updateCombinedLeftovers() {
    const productAOption = $('#productA option:selected');
    const productBOption = $('#productB option:selected');
    
    if (!productAOption.val() || !productBOption.val()) return;
    
    // Get product data
    const productAWeight = parseFloat(productAOption.data('weight')) || 0;
    const productAWastage = parseFloat(productAOption.data('wastage')) || 0;
    const productAStone = parseFloat(productAOption.data('stone')) || 0;
    const productACharges = parseFloat(productAOption.data('charges')) || 0;
    
    const productBWeight = parseFloat(productBOption.data('weight')) || 0;
    const productBWastage = parseFloat(productBOption.data('wastage')) || 0;
    const productBStone = parseFloat(productBOption.data('stone')) || 0;
    const productBCharges = parseFloat(productBOption.data('charges')) || 0;
    
    // Get portions from form
    const portionAWeight = parseFloat($('input[name="product_a_weight_portion"]').val()) || 0;
    const portionAWastage = parseFloat($('input[name="product_a_wastage_portion"]').val()) || 0;
    const portionAStone = parseFloat($('input[name="product_a_stone_portion"]').val()) || 0;
    const portionACharges = parseFloat($('input[name="product_a_charges_portion"]').val()) || 0;
    
    const portionBWeight = parseFloat($('input[name="product_b_weight_portion"]').val()) || 0;
    const portionBWastage = parseFloat($('input[name="product_b_wastage_portion"]').val()) || 0;
    const portionBStone = parseFloat($('input[name="product_b_stone_portion"]').val()) || 0;
    const portionBCharges = parseFloat($('input[name="product_b_charges_portion"]').val()) || 0;
    
    // Calculate combined leftovers
    const leftoverWeight = (productAWeight - portionAWeight) + (productBWeight - portionBWeight);
    const leftoverWastage = (productAWastage - portionAWastage) + (productBWastage - portionBWastage);
    const leftoverStone = (productAStone - portionAStone) + (productBStone - portionBStone);
    const leftoverCharges = (productACharges - portionACharges) + (productBCharges - portionBCharges);
    
    // Update display
    $('#combinedLeftoverWeight').text(leftoverWeight.toFixed(3));
    $('#combinedLeftoverWastage').text(leftoverWastage.toFixed(3));
    $('#combinedLeftoverStone').text(leftoverStone.toFixed(3));
    $('#combinedLeftoverCharges').text(leftoverCharges.toFixed(2));
}

function nextStep(step) {
    // Validate current step
    if (step === 2) {
        if (!validateStep1()) return;
    } else if (step === 3) {
        if (!validateStep2()) return;
    } else if (step === 4) {
        if (!validateStep3()) return;
    }

    // Hide all steps
    $('.step-content').addClass('hidden');

    // Show target step
    $('#step' + step + 'Content').removeClass('hidden');

    // Update step indicators
    $('.step').removeClass('active completed');
    for (let i = 1; i < step; i++) {
        $('#step' + i).addClass('completed');
    }
    $('#step' + step).addClass('active');

    // Step 3 logic
    if (step === 3) {
        const productAName = $('#productA option:selected').data('name');
        const productBName = $('#productB option:selected').data('name');

        if (productAName && productBName && !$('input[name="merged_product_name"]').val()) {
            $('input[name="merged_product_name"]').val(productAName + ' + ' + productBName);
        }

        // Auto-select gold rates from Product A
        setMergedProductGoldRateFromSelectedProduct('A');
        setLeftoverGoldRateFromSelectedProduct('A'); // You can change 'A' to 'B' if preferred
    }
}



function prevStep(step) {
    // Hide all steps
    $('.step-content').addClass('hidden');
    
    // Show target step
    $('#step' + step + 'Content').removeClass('hidden');
    
    // Update step indicators
    $('.step').removeClass('active completed');
    for (let i = 1; i < step; i++) {
        $('#step' + i).addClass('completed');
    }
    $('#step' + step).addClass('active');
}

function validateStep1() {
    const productA = $('#productA').val();
    const productB = $('#productB').val();
    
    if (!productA || !productB) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please select both products to merge.'
        });
        return false;
    }
    
    if (productA === productB) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Cannot merge a product with itself. Please select different products.'
        });
        return false;
    }
    
    return true;
}

function validateStep2() {
    const productAWeightPortion = parseFloat($('input[name="product_a_weight_portion"]').val()) || 0;
    const productBWeightPortion = parseFloat($('input[name="product_b_weight_portion"]').val()) || 0;
    
    if (productAWeightPortion <= 0 || productBWeightPortion <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Weight portions must be greater than 0 for both products.'
        });
        return false;
    }
    
    // Validate portions don't exceed available amounts
    const productAOption = $('#productA option:selected');
    const productBOption = $('#productB option:selected');
    
    const productAWeight = parseFloat(productAOption.data('weight')) || 0;
    const productBWeight = parseFloat(productBOption.data('weight')) || 0;
    
    if (productAWeightPortion > productAWeight) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: `Weight portion for Product A (${productAWeightPortion}g) exceeds available weight (${productAWeight}g).`
        });
        return false;
    }
    
    if (productBWeightPortion > productBWeight) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: `Weight portion for Product B (${productBWeightPortion}g) exceeds available weight (${productBWeight}g).`
        });
        return false;
    }
    
    return true;
}

function validateStep3() {
    const name = $('input[name="merged_product_name"]').val().trim();
    const supplier = $('select[name="merged_product_supplier_id"]').val();
    const category = $('select[name="merged_product_category_id"]').val();
    const subCategory = $('select[name="merged_product_sub_category_id"]').val();
    const goldRate = $('select[name="merged_product_gold_rate_id"]').val();
    const qty = parseInt($('input[name="merged_product_qty"]').val()) || 0;
    
    const errors = [];
    
    if (!name) {
        errors.push('Enter Product Name');
    }
    
    if (!supplier) {
        errors.push('Select Supplier');
    }
    
    if (!category) {
        errors.push('Select Category');
    }
    
    // Check if sub-category is required
    const subCategoryField = $('#sub_category_id');
    if (subCategoryField.prop('required') && !subCategory) {
        errors.push('Select Sub Category (required for this category)');
    }
    
    if (!goldRate) {
        errors.push('Select Gold Rate');
    }
    
    if (qty <= 0) {
        errors.push('Enter valid Quantity (must be greater than 0)');
    }
    
    if (errors.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Required Fields',
            html: '<div style="text-align: left;"><strong>Please complete the following:</strong><ul style="margin-top: 10px;">' +
                errors.map(err => `<li>${err}</li>`).join('') +
                '</ul></div>',
            width: '500px'
        });
        return false;
    }
    
    return true;
}

function validateStep4() {
    const createOption = $('input[name="create_option"]:checked').val();
    
    // Check if any option is selected
    if (!createOption) {
        Swal.fire({
            icon: 'error',
            title: 'Selection Required',
            text: 'Please select either "Create combined leftover product" or "Add to Gold Balance Tbl (Damage)" option.',
            confirmButtonText: 'OK'
        });
        
        // Highlight the radio buttons to draw attention
        $('.form-check-input').closest('.form-check').css('border', '2px solid #dc3545').css('padding', '8px').css('border-radius', '5px');
        
        return false;
    } else {
        // Remove highlighting if option is selected
        $('.form-check-input').closest('.form-check').css('border', 'none').css('padding', '');
    }
    
    // If leftover option is selected, validate leftover fields
    if (createOption === 'leftover') {
        const leftoverName = $('input[name="leftover_name"]').val().trim();
        const leftoverSupplier = $('select[name="leftover_supplier_id"]').val();
        const leftoverCategory = $('select[name="leftover_category_id"]').val();
        const leftoverSubCategory = $('select[name="leftover_sub_category_id"]').val();
        const leftoverGoldRate = $('select[name="leftover_gold_rate_id"]').val();
        const leftoverQty = parseInt($('input[name="leftover_qty"]').val()) || 0;
        
        const errors = [];
        
        if (!leftoverName) {
            errors.push('Enter Leftover Product Name');
        }
        
        if (!leftoverSupplier) {
            errors.push('Select Leftover Supplier');
        }
        
        if (!leftoverCategory) {
            errors.push('Select Leftover Category');
        }
        
        // Check if leftover sub-category is required
        const leftoverSubCategoryField = $('#leftover_sub_category_id');
        if (leftoverSubCategoryField.prop('required') && !leftoverSubCategory) {
            errors.push('Select Leftover Sub Category (required for this category)');
        }
        
        if (!leftoverGoldRate) {
            errors.push('Select Leftover Gold Rate');
        }
        
        if (leftoverQty <= 0) {
            errors.push('Enter valid Leftover Quantity (must be greater than 0)');
        }
        
        if (errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Leftover Product Fields',
                html: '<div style="text-align: left;"><strong>Please complete the following for leftover product:</strong><ul style="margin-top: 10px;">' +
                    errors.map(err => `<li>${err}</li>`).join('') +
                    '</ul></div>',
                width: '500px'
            });
            return false;
        }
    }
    
    return true;
}

function validatePortionInput(input) {
    const value = parseFloat(input.val()) || 0;
    const fieldName = input.attr('name');
    let maxValue = 0;
    let fieldType = '';
    
    // Determine which product and field type
    if (fieldName.includes('product_a_')) {
        const productOption = $('#productA option:selected');
        if (fieldName.includes('weight_portion')) {
            maxValue = parseFloat(productOption.data('weight')) || 0;
            fieldType = 'weight';
        } else if (fieldName.includes('wastage_portion')) {
            maxValue = parseFloat(productOption.data('wastage')) || 0;
            fieldType = 'wastage';
        } else if (fieldName.includes('stone_portion')) {
            maxValue = parseFloat(productOption.data('stone')) || 0;
            fieldType = 'stone';
        } else if (fieldName.includes('charges_portion')) {
            maxValue = parseFloat(productOption.data('charges')) || 0;
            fieldType = 'charges';
        }
    } else if (fieldName.includes('product_b_')) {
        const productOption = $('#productB option:selected');
        if (fieldName.includes('weight_portion')) {
            maxValue = parseFloat(productOption.data('weight')) || 0;
            fieldType = 'weight';
        } else if (fieldName.includes('wastage_portion')) {
            maxValue = parseFloat(productOption.data('wastage')) || 0;
            fieldType = 'wastage';
        } else if (fieldName.includes('stone_portion')) {
            maxValue = parseFloat(productOption.data('stone')) || 0;
            fieldType = 'stone';
        } else if (fieldName.includes('charges_portion')) {
            maxValue = parseFloat(productOption.data('charges')) || 0;
            fieldType = 'charges';
        }
    }
    
    // Validate against max value
    if (value > maxValue) {
        input.addClass('is-invalid');
        input.siblings('.invalid-feedback').remove();
        input.after(`<div class="invalid-feedback">${fieldType} portion (${value}) exceeds available ${fieldType} (${maxValue})</div>`);
    } else {
        input.removeClass('is-invalid');
        input.siblings('.invalid-feedback').remove();
    }
}

// Session message handling
@if(session('success'))
    $(document).ready(function() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            html: '{!! session('success') !!}',
            showConfirmButton: false,
            timer: 5000
        });
    });
@endif

@if(session('error'))
    $(document).ready(function() {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}'
        });
    });
@endif

@if($errors->any())
    $(document).ready(function() {
        let errorMessages = '';
        @foreach($errors->all() as $error)
            errorMessages += '{{ $error }}<br>';
        @endforeach
        
        Swal.fire({
            icon: 'error',
            title: 'Validation Errors',
            html: errorMessages
        });
    });
@endif
</script>

@endsection