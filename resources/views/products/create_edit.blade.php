@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)
@section('content')
<style>
/* === Gold Rate Select2 Refined Styling === */
.select2-container--bootstrap4 .select2-selection--single {
    height: 42px !important;
    display: flex;
    align-items: center;
    border: 1.8px solid #ccc;
    border-radius: 8px;
    background: #fff;
    transition: all 0.25s ease;
    padding-left: 8px;
}

/* Hover + Focus */
.select2-container--bootstrap4 .select2-selection--single:hover {
    border-color: #0d6efd;
    box-shadow: 0 0 4px rgba(13, 110, 253, 0.3);
}
.select2-container--bootstrap4.select2-container--open .select2-selection--single {
    border-color: #0d6efd;
    box-shadow: 0 0 6px rgba(13, 110, 253, 0.25);
}

/* Selected Rate Highlight */
.select2-container--bootstrap4 .select2-selection--single.selected-rate {
    background: linear-gradient(90deg, #f0f8ff, #ffffff);
    border-color: #0d6efd;
    box-shadow: 0 0 5px rgba(13, 110, 253, 0.25);
}

/* Correct text rendering (prevent weird symbols like %% or x overlap) */
.select2-container--bootstrap4 .select2-selection__rendered {
    color: #004085 !important;
    font-weight: 600;
    font-size: 1rem;
    padding-right: 24px !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Placeholder style */
.select2-container--bootstrap4 .select2-selection__placeholder {
    color: #999 !important;
    font-weight: 400;
    font-style: italic;
}

/* Remove weird × icon alignment */
.select2-container--bootstrap4 .select2-selection__clear {
    font-size: 1.1rem;
    font-weight: bold;
    color: #ff4d4d !important;
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}

/* Dropdown box */
.select2-container--bootstrap4 .select2-dropdown {
    border-radius: 8px;
    border: 1px solid #ccc;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    z-index: 9999;
}

/* Limit height */
.select2-container--bootstrap4 .select2-results__options {
    max-height: 250px !important;
    overflow-y: auto !important;
}

/* Options styling */
.select2-results__option {
    /* padding: 8px 12px; */
    font-size: 0.95rem;
    color: #333;
}

/* Highlight hovered option */
.select2-results__option--highlighted {
    background: #0d6efd !important;
    color: #fff !important;
}
</style>

    <div class="content-header">
        <div class="container">
            <div class="row mb-2 justify-content-center">
                <div class="col-sm-5">
                    <h1>{{ isset($product) ? 'Edit Product' : 'Create Product' }}</h1>
                </div>
                <div class="col-sm-5">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                        <li class="breadcrumb-item active">{{ isset($product) ? 'Edit' : 'Create' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

   <div class="content">
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        {{-- <form action="{{ isset($product) ? route('products.storeOrUpdate', $product) : route('products.storeOrUpdate') }}" method="POST"> --}}
                        <form action="{{ Auth::user()->role->name === 'superadmin' 
                            ? (isset($product) ? route('admin.products.storeOrUpdate', $product) : route('admin.products.storeOrUpdate')) 
                            : (isset($product) ? route('products.storeOrUpdate', $product) : route('products.storeOrUpdate')) }}" 
                            method="POST" id="mergeFrm">

                            @csrf

                            @if (!isset($product))
                                {{-- Creating product → editable input --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="product_no" class="form-label">Product No / Scan Barcode</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-barcode text-muted"></i>
                                                    </span>
                                                </div>
                                                <input type="number" id="product_no" name="product_no" 
                                                    class="form-control" 
                                                    value="{{ old('product_no') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Editing product → readonly input --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="product_no" class="form-label">Product No</label>
                                            <input type="number" name="product_no" 
                                                class="form-control" 
                                                value="{{ old('product_no', $product->product_no) }}" 
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <!-- Product Type -->
                            <div class="row">
                                <!-- Type -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Type</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="0" {{ old('type', $product->type ?? '') == '0' ? 'selected' : '' }}>Single</option>
                                            <option value="1" {{ old('type', $product->type ?? '') == '1' ? 'selected' : '' }}>Weight</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Product Type -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_type">Product Type</label>
                                        <select name="product_type" id="product_type" class="form-control" required>
                                            <option value="gold" {{ old('product_type', $product->product_type ?? '') == 'gold' ? 'selected' : '' }}>Gold</option>
                                            <option value="silver" {{ old('product_type', $product->product_type ?? '') == 'silver' ? 'selected' : '' }}>Silver</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Product Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier</label>
                                        <select name="supplier_id" class="form-control">
                                            <option value="">Select supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" 
                                                    {{ old('supplier_id', $product->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->short_code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Category and Sub Category row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_category_id">Category</label>
                                        <select name="product_category_id" id="product_category_id" class="form-control" required>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('product_category_id', $product->product_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sub_category_id">Sub Category</label>
                                        <select name="sub_category_id" id="sub_category_id" class="form-control">
                                            <option value="">Select Sub Category</option>
                                            {{-- Options will be filled dynamically --}}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Gold/Silver Rate -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" id="rate_group">
                                        <label for="gold_rate_id" id="rate_label">Gold Rate</label>
                                        <select name="gold_rate_id" class="form-control">
                                            <option value="">Select Rate</option>
                                            {{-- Example rate options --}}
                                            {{-- @foreach ($goldRates as $rate)
                                                <option value="{{ $rate->id }}" {{ old('gold_rate_id', $product->gold_rate_id ?? '') == $rate->id ? 'selected' : '' }}>
                                                    {{ $rate->name }} ({{ rtrim(rtrim(number_format($rate->percentage, 2), '0'), '.') }}%) - {{ $rate->rate_per_pawn }}
                                                </option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr style="border: solid rgb(83, 83, 83) 1px;">
                            <!-- Weight Information -->
                            <div class="row">
                                @if(Auth::check() && Auth::user()->role->name === 'staff')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="weight">
                                            Net Weight (g.mg) 
                                            <span id="scale-status" 
                                                style="display:inline-block; width:10px; height:10px; border-radius:50%; background:red;">
                                            </span>
                                        </label>
                                        <input type="text" id="weight" name="weight" class="form-control" style="border:solid 1px rgb(0, 38, 255);font-weight: bold; font-size: 1.2rem; background: #f0f8ff; color: #12af04;" value="{{ old('weight', $product->weight ?? '') }}" required readonly>
                                    </div>
                                </div>
                                @else
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="weight">
                                            Net Weight (g.mg) 
                                            <span id="scale-status" 
                                                style="display:inline-block; width:10px; height:10px; border-radius:50%; background:red;">
                                            </span>
                                        </label>
                                        <input type="text" id="weight" name="weight" class="form-control" style="border:solid 1px rgb(0, 38, 255);font-weight: bold; font-size: 1.2rem; background: #f0f8ff; color: #12af04;" value="{{ old('weight', $product->weight ?? '') }}" required >
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="wastage_weight">Wastage Weight (g.mg)</label>
                                        <input type="text" name="wastage_weight" class="form-control" value="{{ old('wastage_weight', $product->wastage_weight ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="stone_weight">Stone Weight (g.mg)</label>
                                        <input type="text" name="stone_weight" class="form-control" value="{{ old('stone_weight', $product->stone_weight ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="making_charges">Making Charges</label>
                                        <input type="text" name="making_charges" class="form-control" value="{{ old('making_charges', $product->making_charges ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Fields -->
                            <div class="form-group" style="display: none">
                                <label for="qty">Quantity</label>
                                <input type="number" name="qty" class="form-control" value="1" required>
                            </div>

                            <!-- Action Buttons -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Create' }}</button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary ml-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>

<script>
    // const socket = io('http://localhost:3001'); // Node.js scale server
    const socket = io('http://127.0.0.1:3001');

    socket.on('connect', () => {
        document.getElementById('scale-status').style.background = 'green';
    });

    socket.on('disconnect', () => {
        document.getElementById('scale-status').style.background = 'red';
        document.getElementById('weight').value = 0;
    });

    socket.on('weight', (data) => {
        console.log("Weight:", data);
        document.getElementById('weight').value = data;
    });
</script>

<script>
/* ---------------- Scale Connection (OUTSIDE DOMContentLoaded) ---------------- */
// const socket = io('http://127.0.0.1:3001');

// socket.on('connect', () => {
//     console.log('Scale connected!');
//     const statusEl = document.getElementById('scale-status');
//     if (statusEl) statusEl.style.background = 'green';
// });

// socket.on('disconnect', () => {
//     // console.log('Scale disconnected!');
//     const statusEl = document.getElementById('scale-status');
//     const weightEl = document.getElementById('weight');
//     if (statusEl) statusEl.style.background = 'red';
//     if (weightEl) weightEl.value = 0;
// });

// socket.on('weight', (data) => {
//     // console.log("Weight received:", data);
//     const weightEl = document.getElementById('weight');
//     if (weightEl) weightEl.value = data;
// });


/* ---------------- DOM Ready (Everything else INSIDE) ---------------- */
document.addEventListener('DOMContentLoaded', function () {
    const $productNoInput = $('#product_no');
    $productNoInput.focus();

    /* ---------- Subcategory Handling ---------- */
    const subcategories = @json($subcategories);
    const categorySelect = document.getElementById('product_category_id');
    const subCategorySelect = document.getElementById('sub_category_id');

    function populateSubCategories(categoryId, selectedSubCatId = null) {
        subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';
        if (!categoryId) return;

        const filteredSubs = subcategories.filter(sub => sub.product_category_id == categoryId);
        filteredSubs.forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.name;
            if (selectedSubCatId && selectedSubCatId == sub.id) option.selected = true;
            subCategorySelect.appendChild(option);
        });
    }

    const initialCategoryId = categorySelect.value;
    const initialSubCatId = "{{ old('sub_category_id', $product->sub_category_id ?? '') }}";
    if (initialCategoryId) populateSubCategories(initialCategoryId, initialSubCatId);
    categorySelect.addEventListener('change', e => populateSubCategories(e.target.value));


    /* ---------- Rate Label Update ---------- */
    const productTypeSelect = document.getElementById('product_type');
    const rateLabel = document.getElementById('rate_label');

    function updateRateLabel() {
        const type = productTypeSelect.value.toLowerCase();
        rateLabel.textContent = type === 'gold' ? 'Gold Rate' :
                                type === 'silver' ? 'Silver Rate' : 'Rate';
    }

    updateRateLabel();
    productTypeSelect.addEventListener('change', updateRateLabel);


    /* ---------- Gold/Silver Rate Fetch ---------- */
    const goldRateSelect = $('select[name="gold_rate_id"]');

    function refreshSelect2() {
        const $select = $('select[name="gold_rate_id"]');
        if ($select.data('select2')) {
            $select.select2('destroy');
        }
        $select.select2({
            theme: 'bootstrap4',
            placeholder: 'Search by rate name or price...',
            allowClear: true,
            width: '100%'
        });
    }

    function fetchGoldRates(productType) {
        const selectedRateId = "{{ old('gold_rate_id', $product->gold_rate_id ?? '') }}";

        fetch(`/gold-rates/filter/${productType}`)
            .then(res => res.json())
            .then(data => {
                goldRateSelect.html(
                    `<option value="">Select ${productType === 'silver' ? 'Silver' : 'Gold'} Rate</option>`
                );

                data.forEach(rate => {
                    const rateText = productType === 'gold'
                        ? `${rate.name} (${rate.percentage ?? 0}) - Rs. ${Number(rate.rate_per_pawn).toLocaleString()}`
                        : `${rate.name} (${rate.percentage ?? 0}) - Rs. ${Number(rate.rate).toLocaleString()}`;

                    const selected = rate.id == selectedRateId ? 'selected' : '';
                    goldRateSelect.append(`<option value="${rate.id}" ${selected}>${rateText}</option>`);
                });

                refreshSelect2();
            });
    }

    fetchGoldRates(productTypeSelect.value);
    productTypeSelect.addEventListener('change', e => fetchGoldRates(e.target.value));


    /* ---------- Highlight Selected Rate ---------- */
    function applyHighlight() {
        const selection = goldRateSelect.next('.select2-container').find('.select2-selection');
        selection.toggleClass('selected-rate', !!goldRateSelect.val());
    }

    goldRateSelect.on('change', applyHighlight);
    applyHighlight();


    /* ---------- Form Submission Guard ---------- */
    const form = document.getElementById('mergeFrm');
    if (form) {
        const submitButton = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function () {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Please wait...';
            form.classList.add('submitted');
        });

        form.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault();
        });
    }

    /* ---------- Error Message ---------- */
    @if ($errors->any())
        toastr.error("Please fix the errors and try again.");
    @endif
});
</script>


@endsection