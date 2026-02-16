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
                <h1>Scanning Products</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('stock-audits.index') }}">Stock Audits</a></li>
                    <li class="breadcrumb-item active">Scan</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content pb-3">
    <div class="container">
        <!-- Progress Card -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="bi bi-clipboard-check"></i> 
                    Audit: {{ $audit->audit_reference }} 
                    @if($audit->audit_type === 'all')
                        - <span class="badge badge-light">Complete Inventory Audit</span>
                    @else
                        - {{ $audit->category->name }}
                    @endif
                </h3>
            </div>
            <div class="card-body">
                @if($audit->audit_type === 'all')
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle"></i>
                        <strong>Complete Inventory Audit:</strong> Scanning all products across all categories
                    </div>
                @endif

                <div class="row text-center">
                    <div class="col-md-4">
                        <h5>Expected Products</h5>
                        <h2 class="text-info">{{ $audit->expected_count }}</h2>
                    </div>
                    <div class="col-md-4">
                        <h5>Scanned</h5>
                        <h2 class="text-success" id="scanned-count">{{ $audit->scanned_count }}</h2>
                    </div>
                    <div class="col-md-4">
                        <h5>Remaining</h5>
                        <h2 class="text-warning" id="remaining-count">{{ $audit->expected_count - $audit->scanned_count }}</h2>
                    </div>
                </div>
                <div class="progress mt-3" style="height: 30px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         id="progress-bar"
                         style="width: {{ $audit->expected_count > 0 ? ($audit->scanned_count / $audit->expected_count * 100) : 0 }}%"
                         aria-valuenow="{{ $audit->scanned_count }}" 
                         aria-valuemin="0" 
                         aria-valuemax="{{ $audit->expected_count }}">
                        <strong id="progress-text">{{ $audit->scanned_count }} / {{ $audit->expected_count }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanning Card -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h3 class="card-title">
                    <i class="bi bi-upc-scan"></i> Scan Barcode
                </h3>
            </div>
            <div class="card-body">
                <form id="scan-form">
                    <div class="form-group">
                        <label for="product_no">Scan or Enter Product Number</label>
                        <div class="input-group input-group-lg">
                            <input type="text" 
                                   class="form-control" 
                                   id="product_no" 
                                   name="product_no" 
                                   placeholder="Scan barcode here..." 
                                   autocomplete="off"
                                   autofocus>
                            <div class="input-group-append">
                                <button class="btn btn-success" type="submit" id="scan-btn">
                                    <i class="bi bi-check-circle"></i> Scan
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle"></i> Focus on this field and scan the barcode. It will automatically submit.
                        </small>
                    </div>
                </form>

                <!-- Alert Messages -->
                <div id="alert-container"></div>

                <!-- Last Scanned Product Info -->
                <div id="last-scanned" class="card mt-3" style="display: none;">
                    <div class="card-header bg-light">
                        <strong><i class="bi bi-check-circle text-success"></i> Last Scanned Product</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Product No:</strong> <span id="last-product-no"></span><br>
                                <strong>Name:</strong> <span id="last-product-name"></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Weight:</strong> <span id="last-product-weight"></span><br>
                                <strong>Category:</strong> <span id="last-product-category"></span>
                            </div>
                            <div class="col-md-4">
                                <strong>Supplier:</strong> <span id="last-product-supplier"></span><br>
                                <strong>Gold Rate:</strong> <span id="last-product-gold-rate"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanned Items List -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-list-check"></i> Scanned Items ({{ $audit->items->count() }})
                </h3>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-bordered" id="scanned-items-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Product No</th>
                            <th>Product Name</th>
                            @if($audit->audit_type === 'all')
                                <th>Category</th>
                            @endif
                            <th>Scanned At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="scanned-items-body">
                        @foreach($audit->items->sortByDesc('scanned_at') as $index => $item)
                        @php
                            $product = \App\Models\Product::where('product_no', $item->product_no)->first();
                        @endphp
                        <tr id="item-row-{{ $item->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item->product_no }}</strong></td>
                            <td>{{ $product?->name ?? 'N/A' }}</td>
                            @if($audit->audit_type === 'all')
                                <td>
                                    <span class="badge badge-secondary">{{ $product?->category?->name ?? 'N/A' }}</span>
                                </td>
                            @endif
                            <td>{{ $item->scanned_at->format('h:i A') }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="deleteItem({{ $item->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center">
            <form action="{{ route('stock-audits.complete', $audit->id) }}" method="POST" style="display: inline;" 
                  onsubmit="return confirm('Are you sure you want to complete this audit? You will not be able to scan more items after this.');">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle"></i> Complete Audit
                </button>
            </form>
            <a href="{{ route('stock-audits.index') }}" class="btn btn-secondary btn-lg">
                <i class="bi bi-pause-circle"></i> Pause & Return Later
            </a>
        </div>
    </div>
</div>

<!-- Audio for feedback -->
{{-- <audio id="success-sound" src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYHGGm98OScTgwOUKng77hjHAU7k9n1y34tBSh+zPLaizsIHGS57OihUBELTKXh8bllHgU3ldz1xn8tBSh+zPLaizsIG2e37+idUhIMTKnj8bhlHQU" preload="auto"></audio>
<audio id="error-sound" src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYHGGm98OScTgwOUKng77hjHAU7k9n1y34tBSh+zPLaizsIHGS57OihUBELTKXh8bllHgU3ldz1xn8tBSh+zPLaizsIG2e37+idUhIMTKnj8bhlHQU" preload="auto"></audio> --}}

<style>
    #product_no {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
    }
    
    .pulse-animation {
        animation: pulse 0.5s;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>

<script>
    let expectedCount = {{ $audit->expected_count }};
    let scannedCount = {{ $audit->scanned_count }};
    let auditType = '{{ $audit->audit_type }}';
    
    $(document).ready(function() {
        $('#product_no').focus();
        
        $('#scan-form').on('submit', function(e) {
            e.preventDefault();
            scanProduct();
        });
    });
    
    function scanProduct() {
        let productNo = $('#product_no').val().trim();
        
        if (!productNo) {
            showAlert('Please enter or scan a product number', 'warning');
            return;
        }
        
        $('#scan-btn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Scanning...');
        
        $.ajax({
            url: '{{ route("stock-audits.scan-product", $audit->id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_no: productNo
            },
            success: function(response) {
                if (response.success) {
                    // $('#success-sound')[0].play();
                    
                    scannedCount = response.scanned_count;
                    updateProgress();
                    
                    if (response.warning) {
                        showAlert('Product scanned but: ' + response.warning, 'warning');
                    } else {
                        showAlert('Product scanned successfully!', 'success');
                    }
                    
                    if (response.product) {
                        $('#last-product-no').text(response.product.product_no);
                        $('#last-product-name').text(response.product.name);
                        $('#last-product-weight').text(response.product.weight);
                        $('#last-product-category').text(response.product.category?.name || 'N/A');
                        $('#last-product-supplier').text(response.product.supplier?.name || 'N/A');
                        $('#last-product-gold-rate').text(response.product.gold_rate?.name || 'N/A');
                        $('#last-scanned').slideDown().addClass('pulse-animation');
                        
                        setTimeout(function() {
                            $('#last-scanned').removeClass('pulse-animation');
                        }, 500);
                    }
                    
                    addScannedItemToTable(response.product);
                    $('#product_no').val('').focus();
                }
            },
            error: function(xhr) {
                // $('#error-sound')[0].play();
                
                let message = 'Error scanning product';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                showAlert(message, 'danger');
                $('#product_no').val('').focus();
            },
            complete: function() {
                $('#scan-btn').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Scan');
            }
        });
    }
    
    function updateProgress() {
        let remaining = expectedCount - scannedCount;
        let percentage = expectedCount > 0 ? (scannedCount / expectedCount * 100) : 0;
        
        $('#scanned-count').text(scannedCount);
        $('#remaining-count').text(remaining);
        $('#progress-bar').css('width', percentage + '%');
        $('#progress-text').text(scannedCount + ' / ' + expectedCount);
        
        if (percentage >= 100) {
            $('#progress-bar').removeClass('bg-warning').addClass('bg-success');
        } else if (percentage >= 50) {
            $('#progress-bar').removeClass('bg-warning').addClass('bg-success');
        } else {
            $('#progress-bar').removeClass('bg-success').addClass('bg-warning');
        }
    }
    
    function addScannedItemToTable(product) {
        if (!product) return;
        
        let currentTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        let rowCount = $('#scanned-items-body tr').length + 1;
        
        let categoryColumn = '';
        if (auditType === 'all') {
            categoryColumn = `<td><span class="badge badge-secondary">${product.category?.name || 'N/A'}</span></td>`;
        }
        
        let newRow = `
            <tr class="table-success">
                <td>${rowCount}</td>
                <td><strong>${product.product_no}</strong></td>
                <td>${product.name || 'N/A'}</td>
                ${categoryColumn}
                <td>${currentTime}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="alert('Item just scanned - refresh to delete')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#scanned-items-body').prepend(newRow);
        
        setTimeout(function() {
            $('#scanned-items-body tr:first').removeClass('table-success');
        }, 2000);
    }
    
    function deleteItem(itemId) {
        if (!confirm('Are you sure you want to remove this item from the audit?')) {
            return;
        }
        
        $.ajax({
            url: '{{ route("stock-audits.index") }}/' + {{ $audit->id }} + '/items/' + itemId,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#item-row-' + itemId).fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    scannedCount--;
                    updateProgress();
                    
                    showAlert('Item removed successfully', 'info');
                }
            },
            error: function() {
                showAlert('Error removing item', 'danger');
            }
        });
    }
    
    function showAlert(message, type) {
        let alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        `;
        
        $('#alert-container').html(alertHtml);
        
        setTimeout(function() {
            $('#alert-container .alert').fadeOut();
        }, 5000);
    }
</script>
@endsection