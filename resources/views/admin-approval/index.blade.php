@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)

@section('content')
<style>
    .modern-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .modern-table {
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .modern-table thead {
        background: linear-gradient(135deg, #ffffff, #ffffff);
        color: rgb(0, 0, 0);
    }
    
    .modern-table th {
        padding: 1rem 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border: none;
    }
    
    .modern-table td {
        padding: 1rem 0.75rem;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }
    
    .modern-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:hover {
        background-color: #f8f9ff;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .product-id {
        font-weight: 600;
        color: #000000;
        font-family: monospace;
    }
    
    .creator-badge {
        background: linear-gradient(135deg, #74b9ff, #0984e3);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        display: inline-block;
        white-space: nowrap;
    }
    
    .weight-display {
        font-weight: 500;
        color: #2d3436;
    }
    
    .rate-display {
        background: #ffeaa7;
        color: #2d3436;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 500;
        white-space: nowrap;
    }
    
    .date-display {
        color: #636e72;
        font-size: 0.9rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .btn-modern {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }
    
    .btn-approve {
        background: green;
        color: white;
        box-shadow: 0 2px 8px rgba(0, 184, 148, 0.3);
    }
    
    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 184, 148, 0.4);
        color: white;
    }
    
    .btn-reject {
        background: red;
        color: white;
        box-shadow: 0 2px 8px rgba(225, 112, 85, 0.3);
    }
    
    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(225, 112, 85, 0.4);
        color: white;
    }

    .search-box {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .search-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        border-color: #6c5ce7;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        outline: none;
    }
    
    .no-data {
        text-align: center;
        padding: 3rem;
        color: #74b9ff;
    }
    
    .no-data-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Mobile Card Layout */
    .mobile-card-layout {
        display: none;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
        padding: 1.25rem;
        transition: all 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .product-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .product-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3436;
        margin: 0;
    }

    .product-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin: 1rem 0;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 0.75rem;
        color: #636e72;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-weight: 500;
        color: #2d3436;
    }

    .mobile-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .mobile-actions .btn-modern {
        flex: 1;
        justify-content: center;
        min-width: 120px;
    }

    /* Responsive Breakpoints */
    @media (max-width: 1200px) {
        .modern-table th,
        .modern-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }
        
        .creator-badge,
        .category-badge,
        .subcategory-badge {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
        }
    }

    @media (max-width: 992px) {
        .container {
            padding: 0 15px;
        }
        
        .page-header {
            padding: 1rem 0;
        }
        
        .page-title {
            font-size: 1.75rem;
        }
        
        .search-box {
            padding: 0.75rem;
        }
        
        .modern-table {
            font-size: 0.85rem;
        }
    }
    
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .search-box {
            margin-bottom: 1.5rem;
        }
        
        /* Hide table and show mobile cards */
        .desktop-table-layout {
            display: none;
        }
        
        .mobile-card-layout {
            display: block;
        }
        
        .product-info-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .mobile-actions .btn-modern {
            font-size: 0.8rem;
            padding: 0.6rem 0.8rem;
        }
        
        .no-data {
            padding: 2rem 1rem;
        }
        
        .no-data-icon {
            font-size: 3rem;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 0 10px;
        }
        
        .search-box {
            padding: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .search-input {
            font-size: 0.9rem;
            padding: 0.6rem 0.8rem;
        }
        
        .product-card {
            padding: 1rem;
        }
        
        .product-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .mobile-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .mobile-actions .btn-modern {
            width: 100%;
            min-width: auto;
        }
        
        .creator-badge {
            align-self: flex-start;
        }
    }

    @media (max-width: 400px) {
        .page-title {
            font-size: 1.25rem;
        }
        
        .product-card {
            padding: 0.75rem;
        }
        
        .btn-modern {
            font-size: 0.75rem;
            padding: 0.5rem 0.6rem;
        }
    }

    /* Landscape phone adjustments */
    @media (max-width: 768px) and (orientation: landscape) {
        .product-info-grid {
            grid-template-columns: 1fr 1fr;
        }
        
        .mobile-actions {
            flex-direction: row;
        }
    }

    /* Print styles */
    @media print {
        .search-box,
        .mobile-actions,
        .action-buttons {
            display: none !important;
        }
        
        .product-card {
            box-shadow: none;
            border: 1px solid #ddd;
            break-inside: avoid;
        }
    }
</style>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">
            Product Approvals
        </h1>
    </div>
</div>

<div class="container pb-5">

    <!-- Search Box -->
    <div class="search-box">
        <div class="row">
            <div class="col-lg-4 col-md-7">
                <input type="text" id="searchInput" class="search-input" placeholder="Search products by name, ID, or creator...">
            </div>
            {{-- <div class="col-lg-4 col-md-5 mt-3 mt-md-0">
                <select id="categoryFilter" class="search-input">
                    <option value="">All Categories</option>
                    @foreach($products->pluck('category.name')->unique()->filter() as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div> --}}
        </div>
    </div>
    @if(count($products) > 0)
        <!-- Desktop Table Layout -->
        <div class="modern-card desktop-table-layout">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table modern-table mb-0" id="productsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Requested By</th>
                                <th>Product Name</th>
                                <th>Net Weight</th>
                                <th>Wastage</th>
                                <th>Stone Weight</th>
                                <th>Gold Rate</th>
                                <th>Making Charges</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr class="product-row">
                                <td>
                                    <span class="product-id">#{{ $product->product_no }}</span>
                                </td>
                                <td>
                                    <span class="creator-badge">
                                        {{ optional($product->creator)->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                </td>
                                <td>
                                    <span class="weight-display">{{ number_format($product->weight, 3) }}g</span>
                                </td>
                                <td>
                                    <span class="weight-display">{{ number_format($product->wastage_weight, 3) }}g</span>
                                </td>
                                <td>
                                    <span class="weight-display">{{ number_format($product->stone_weight, 3) }}g</span>
                                </td>
                                <td>
                                    <span class="rate-display">
                                        {{ $product->goldRate->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ number_format($product->making_charges, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="category-badge">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td>
                                    @if($product->subCategory)
                                        <span class="subcategory-badge">
                                            {{ $product->subCategory->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="date-display">
                                        {{ $product->created_at->format('d M Y') }}<br>
                                        <small>{{ $product->created_at->format('H:i') }}</small>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('product.approval', $product->id) }}" 
                                           class="btn-modern btn-approve">
                                            <i class="fas fa-check"></i>
                                            Approve
                                        </a>
                                        <a href="{{ route('product.reject', $product->id) }}" 
                                           class="btn-modern btn-reject"
                                           onclick="return confirm('Are you sure you want to reject this product?')">
                                            <i class="fas fa-times"></i>
                                            Reject
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile Card Layout -->
        <div class="mobile-card-layout">
            @foreach($products as $product)
            <div class="product-card">
                <div class="product-header">
                    <div>
                        <h3 class="product-title">{{ $product->name }}</h3>
                        <span class="product-id">#{{ $product->product_no }}</span>
                    </div>
                    <span class="creator-badge">
                        {{ optional($product->creator)->name ?? 'N/A' }}
                    </span>
                </div>

                <div class="product-info-grid">
                    <div class="info-item">
                        <span class="info-label">Net Weight</span>
                        <span class="info-value weight-display">{{ number_format($product->weight, 3) }}g</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Wastage</span>
                        <span class="info-value weight-display">{{ number_format($product->wastage_weight, 3) }}g</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Stone Weight</span>
                        <span class="info-value weight-display">{{ number_format($product->stone_weight, 3) }}g</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gold Rate</span>
                        <span class="info-value rate-display">{{ $product->goldRate->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Making Charges</span>
                        <span class="info-value"><strong>{{ number_format($product->making_charges, 2) }}</strong></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Created</span>
                        <span class="info-value date-display">
                            {{ $product->created_at->format('d M Y') }}
                            <small>({{ $product->created_at->format('H:i') }})</small>
                        </span>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="category-badge">{{ $product->category->name }}</span>
                        @if($product->subCategory)
                            <span class="subcategory-badge">{{ $product->subCategory->name }}</span>
                        @endif
                    </div>
                </div>

                <div class="mobile-actions">
                    <a href="{{ route('product.approval', $product->id) }}" 
                       class="btn-modern btn-approve">
                        <i class="fas fa-check"></i>
                        Approve
                    </a>
                    <a href="{{ route('product.reject', $product->id) }}" 
                       class="btn-modern btn-reject"
                       onclick="return confirm('Are you sure you want to reject this product?')">
                        <i class="fas fa-times"></i>
                        Reject
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="modern-card">
            <div class="no-data">
                <div class="no-data-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h4>No Pending Approvals</h4>
                <p class="text-muted">All products have been reviewed and processed.</p>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const table = document.getElementById('productsTable');
    const mobileCards = document.getElementById('mobileCards');
    const tableRows = table ? table.querySelectorAll('tbody tr') : [];
    const cardItems = mobileCards ? mobileCards.querySelectorAll('.product-card-item') : [];

    function filterContent() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        
        // Filter table rows (desktop)
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const categoryText = row.querySelector('.category-badge')?.textContent.toLowerCase() || '';
            
            const matchesSearch = searchTerm === '' || text.includes(searchTerm);
            const matchesCategory = selectedCategory === '' || categoryText.includes(selectedCategory);
            
            if (matchesSearch && matchesCategory) {
                row.style.display = '';
                row.style.animation = 'fadeIn 0.3s ease';
            } else {
                row.style.display = 'none';
            }
        });

        // Filter mobile cards
        cardItems.forEach(card => {
            const productName = card.getAttribute('data-product-name') || '';
            const productId = card.getAttribute('data-product-id') || '';
            const creator = card.getAttribute('data-creator') || '';
            const category = card.getAttribute('data-category') || '';
            
            const matchesSearch = searchTerm === '' || 
                                productName.includes(searchTerm) || 
                                productId.includes(searchTerm) || 
                                creator.includes(searchTerm);
            const matchesCategory = selectedCategory === '' || category.includes(selectedCategory);
            
            if (matchesSearch && matchesCategory) {
                card.style.display = '';
                card.style.animation = 'fadeIn 0.3s ease';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterContent);
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterContent);
    }

    // Handle orientation changes on mobile
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            // Force repaint after orientation change
            const elements = document.querySelectorAll('.product-card');
            elements.forEach(el => {
                el.style.transform = 'translateZ(0)';
                setTimeout(() => {
                    el.style.transform = '';
                }, 10);
            });
        }, 100);
    });
});

function showLoading() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.style.display = 'block';
    }
    return true;
}
</script>

@endsection