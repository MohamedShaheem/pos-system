@extends('layouts.admin')

@section('content')
<div class="container pb-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Stock Ledger Summary Report</h3>
        <div class="d-flex align-items-center">
            {{-- Filter Buttons --}}
            <div class="btn-group me-3 mr-3" role="group">
                <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="gold">
                    <i class="fas fa-coins text-warning"></i> Gold
                </button>
                <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="silver">
                    <i class="fas fa-coins text-info"></i> Silver
                </button>
                <button type="button" class="btn btn-secondary filter-btn active" data-filter="both">
                    <i class="fas fa-layer-group"></i> Both
                </button>
            </div>
            
            <button onclick="printReport()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>

    {{-- Combined Summary (only visible when both sections are shown) --}}
    <div class="section-wrapper combined-summary" id="combined-summary">
        <h5 class="section-title">Overall Summary</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="summary-card">
                    <h6 class="text-warning">
                        <i class="fas fa-coins"></i> Gold Total
                    </h6>
                    <p class="mb-1"><strong>Items:</strong> {{ $goldData->sum('total_items') }}</p>
                    <p class="mb-0"><strong>Net Weight:</strong> {{ number_format($goldData->sum(function($row) { return (float)str_replace(',', '', $row['net_weight']); }), 2) }} g</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="summary-card">
                    <h6 class="text-info">
                        <i class="fas fa-coins"></i> Silver Total
                    </h6>
                    <p class="mb-1"><strong>Items:</strong> {{ $silverData->sum('total_items') }}</p>
                    <p class="mb-0"><strong>Net Weight:</strong> {{ number_format($silverData->sum(function($row) { return (float)str_replace(',', '', $row['net_weight']); }), 2) }} g</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Gold Section --}}
    <div class="section-wrapper gold-section" id="gold-section">
        <h5 class="section-title">Gold Products</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm stock-table">
                <thead class="thead-dark">
                    <tr>
                        <th class="col-no">#</th>
                        <th class="col-code">Code</th>
                        <th class="col-category">Category</th>
                        <th class="col-items">Items</th>
                        <th class="col-approval">On Ap.</th>
                        <th class="col-weight">Weight</th>
                        <th class="col-stone print-hidden">Stone Wt.</th>
                        <th class="col-net print-hidden">Net Wt.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($goldData as $row)
                    <tr>
                        <td class="text-center"> @if(!$row['is_sub']) {{ $row['sort_order'] }} @endif</td>

                       
                        <td class="text-center">
                            @if(!$row['is_sub'])
                                {{ $row['short_code'] ?? 'N/A' }}
                            @endif
                        </td>
                        
                        <td @if($row['is_sub']) class="text-primary font-italic" @endif>
                            @if($row['is_sub'])
                                &nbsp;&nbsp;&nbsp;↳ {{ $row['name'] }}
                                <small class="text-muted">
                                    (Wt: {{ $row['weight'] }} g, S.Wt: {{ $row['stone_weight'] }} g, Net: {{ $row['net_weight'] }} g)
                                </small>
                            @else
                                {{ $row['name'] }}
                            @endif
                        </td>

                        <td class="text-right">{{ $row['total_items'] }}</td>
                        <td class="text-right">-</td>
                        <td class="text-right">
                            @if(!$row['is_sub']) {{ $row['weight'] }} @endif
                        </td>
                        <td class="text-right print-hidden">
                            @if(!$row['is_sub']) {{ $row['stone_weight'] }} @endif
                        </td>
                        <td class="text-right font-weight-bold print-hidden">
                            @if(!$row['is_sub']) {{ $row['net_weight'] }} @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No categories found</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" class="text-center font-weight-bold">Total Gold</td>
                        <td class="text-right font-weight-bold">
                            {{ $goldData->where('is_sub', false)->sum('total_items') }}
                        </td>
                        <td class="text-right font-weight-bold">-</td>
                        <td class="text-right font-weight-bold">
                            {{ number_format($goldData->where('is_sub', false)->sum(function($row) {
                                return (float) str_replace(',', '', $row['weight']);
                            }), 2) }}
                        </td>
                        <td class="text-right font-weight-bold print-hidden">
                            {{ number_format($goldData->where('is_sub', false)->sum(function($row) {
                                return (float) str_replace(',', '', $row['stone_weight']);
                            }), 2) }}
                        </td>
                        <td class="text-right font-weight-bold total-highlight print-hidden">
                            {{ number_format($goldData->where('is_sub', false)->sum(function($row) {
                                return (float) str_replace(',', '', $row['net_weight']);
                            }), 2) }}
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>

    {{-- Silver Section --}}
    <div class="section-wrapper silver-section" id="silver-section">
        <h5 class="section-title">Silver Products</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm stock-table">
                <thead class="thead-dark">
                    <tr>
                        <th class="col-no">#</th>
                        <th class="col-code">Code</th>
                        <th class="col-category">Category</th>
                        <th class="col-items">Items</th>
                        <th class="col-approval">On Ap.</th>
                        <th class="col-weight">Weight</th>
                        <th class="col-stone">Stone Wt.</th>
                        <th class="col-net">Net Wt.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($silverData as $row)
                    <tr>
                        <td class="text-center"> @if(!$row['is_sub']) {{ $row['sort_order'] }} @endif</td>
                        <td class="text-center">
                            @if(!$row['is_sub'])
                                {{ $row['short_code'] ?? 'N/A' }}
                            @endif
                        </td>

                        <td @if($row['is_sub']) class="text-primary font-italic" @endif>
                            @if($row['is_sub'])
                                &nbsp;&nbsp;&nbsp;↳ {{ $row['name'] }}
                                <small class="text-muted">
                                    (Wt: {{ $row['weight'] }} g, S.Wt: {{ $row['stone_weight'] }} g, Net: {{ $row['net_weight'] }} g)
                                </small>
                            @else
                                {{ $row['name'] }}
                            @endif
                        </td>
                        <td class="text-right">{{ $row['total_items'] }}</td>
                        <td class="text-right">-</td>
                        <td class="text-right">
                            @if(!$row['is_sub']) {{ $row['weight'] }} @endif
                        </td>
                        <td class="text-right">
                            @if(!$row['is_sub']) {{ $row['stone_weight'] }} @endif
                        </td>
                        <td class="text-right font-weight-bold">
                            @if(!$row['is_sub']) {{ $row['net_weight'] }} @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No categories found</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" class="text-center font-weight-bold">Total Silver</td>
                        <td class="text-right font-weight-bold">
                            {{ $silverData->where('is_sub', false)->sum('total_items') }}
                        </td>
                        <td class="text-right font-weight-bold">-</td>
                        <td class="text-right font-weight-bold">
                            {{ number_format($silverData->where('is_sub', false)->sum(function($row) {
                                return (float) str_replace(',', '', $row['weight']);
                            }), 2) }}
                        </td>
                        <td class="text-right font-weight-bold">
                            {{ number_format($silverData->where('is_sub', false)->sum(function($row) {
                                return (float) str_replace(',', '', $row['stone_weight']);
                            }), 2) }}
                        </td>
                        <td class="text-right font-weight-bold total-highlight">
                            {{ number_format($silverData->where('is_sub', false)->sum(function($row) {
                                return (float) str_replace(',', '', $row['net_weight']);
                            }), 2) }}
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>

    
</div>

<style>
/* Screen Styles */
.section-wrapper {
    margin-bottom: 2rem;
}

.section-title {
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 8px;
    margin-bottom: 15px;
}

.stock-table {
    font-size: 14px;
    margin-bottom: 0;
}

.stock-table th {
    background-color: #343a40 !important;
    color: white;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 10px 8px;
}

.stock-table td {
    padding: 8px;
    vertical-align: middle;
}

.total-row {
    background-color: #f8f9fa !important;
}

.total-highlight {
    background-color: #e9ecef !important;
}

/* Filter button styles */
.filter-btn {
    border-radius: 6px;
    padding: 8px 16px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.filter-btn.active {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.filter-btn:not(.active):hover {
    background-color: #f8f9fa;
    border-color: #6c757d;
}

/* Summary card styles */
.summary-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    margin-bottom: 15px;
}

.summary-card h6 {
    margin-bottom: 10px;
    font-weight: 600;
}

/* Column width optimization */
.col-no { width: 5%; }
.col-code { width: 5%; }
.col-category { width: 20%; }
.col-items { width: 5%; }
.col-approval { width: 5%; }
.col-weight { width: 7%; }
.col-stone { width: 12%; }
.col-net { width: 7%; }

/* Hidden sections */
.section-hidden {
    display: none !important;
}

@media print {
     @page {
        size: A4 portrait;
        margin: 10mm 8mm 12mm 8mm;
    }

    .btn, .navbar, .sidebar, .breadcrumb, .btn-group {
        display: none !important;
    }

    .table-responsive {
        overflow: visible !important;
        display: block !important;
    }

    .d-flex:has(.btn), .d-flex:has(.btn-group) {
        display: none !important;
    }

    .container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100%;
    }

     .print-hidden {
        display: none !important;
        visibility: hidden !important;
    }


     body {
        font-family: Arial, sans-serif;
        font-size: 15px !important;
        line-height: 1.4 !important;
        color: #000;
        background: white;
    }

    .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 15px;
    }

    .print-header h2 {
        font-size: 18px;
        font-weight: bold;
        margin: 0 0 5px 0;
    }

    .print-date {
        font-size: 9px;
        color: #333;
        margin: 0;
    }

    .section-title {
        font-size: 12px;
        font-weight: bold;
        margin: 10px 0 8px 0;
        padding: 2px 0;
        border-bottom: 1px solid #333;
    }

    .stock-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
        font-size: 15px;
        table-layout: fixed;
    }

    .stock-table th {
        background-color: #e9ecef !important;
        color: #000 !important;
        font-weight: bold;
        font-size: 15px;
        padding: 4px 3px;
        text-align: center;
        border: 0.5px solid #000 !important;
    }

    .stock-table td {
        padding: 3px 3px;
        border: 0.5px solid #333 !important;
        font-size: 15px;
    }

    .text-center { text-align: center !important; }
    .text-right { text-align: right !important; }
    .text-left { text-align: left !important; }

    .total-row td {
        font-weight: bold !important;
        background-color: #f0f0f0 !important;
    }

    .total-highlight {
        background-color: #d4edda !important;
        font-weight: bold !important;
    }

    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    tbody { display: table-row-group; }
    tr { page-break-inside: avoid; }

    .silver-section {
        page-break-before: auto;
        margin-top: 25px;
    }

    .section-wrapper {
        margin-bottom: 10px;
    }

    /* Hide sections that are not visible on screen during print */
    .section-hidden {
        display: none !important;
    }

    .combined-summary {
        display: none !important;
    }

    .main-footer{
        display: none !important;
    }
}

/* Screen improvements */
@media screen {
    .stock-table {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 6px;
        overflow: hidden;
    }
    
    .stock-table th {
        background: linear-gradient(135deg, #343a40 0%, #495057 100%);
    }
    
    .stock-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .total-row {
        background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%) !important;
    }
    
    .section-wrapper {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }
}
</style>

<script>
// Global variable to track current filter
let currentFilter = 'both';

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            setFilter(filter);
            
            // Update button states
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Set default filter to 'both'
    setFilter('both');
    adjustTableLayout();
});

function setFilter(filter) {
    currentFilter = filter;
    const goldSection = document.getElementById('gold-section');
    const silverSection = document.getElementById('silver-section');
    const combinedSummary = document.getElementById('combined-summary');
    
    // Remove hidden class from all sections first
    goldSection.classList.remove('section-hidden');
    silverSection.classList.remove('section-hidden');
    combinedSummary.classList.remove('section-hidden');
    
    switch(filter) {
        case 'gold':
            silverSection.classList.add('section-hidden');
            combinedSummary.classList.add('section-hidden');
            break;
        case 'silver':
            goldSection.classList.add('section-hidden');
            combinedSummary.classList.add('section-hidden');
            break;
        case 'both':
            // Show all sections
            break;
    }
}

function printReport() {
    // Remove any existing print header first
    const existingHeader = document.querySelector('.print-header');
    if (existingHeader) existingHeader.remove();

    // Determine report title based on current filter
    let reportTitle = 'Stock Ledger Summary Report';
    switch(currentFilter) {
        case 'gold':
            reportTitle = 'Stock Ledger Summary Report - Gold Products';
            break;
        case 'silver':
            reportTitle = 'Stock Ledger Summary Report - Silver Products';
            break;
        case 'both':
            reportTitle = 'Stock Ledger Summary Report - All Products';
            break;
    }

    // Add print header
    const header = document.createElement('div');
    header.className = 'print-header';
    header.innerHTML = `
        <h2>Jewel Plaza<br>${reportTitle}</h2>
        <p class="print-date">
            Stocks as at: ${(() => {
                const d = new Date();
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = String(d.getFullYear()).slice(-2);
                const date = `${day}/${month}/${year}`;
                return `${date}`;
            })()}
        </p>
    `;

    // Insert at top
    document.querySelector('.container').insertBefore(header, document.querySelector('.container').firstChild);

    window.onafterprint = function() {
        location.reload();
    };

    // Trigger print
    window.print();
    
}

// Print using Ctrl+P
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        printReport();
    }
});

// Auto-adjust table on window resize
window.addEventListener('resize', function() {
    adjustTableLayout();
});

function adjustTableLayout() {
    const tables = document.querySelectorAll('.stock-table');
    tables.forEach(table => {
        // Ensure tables fit within container
        if (table.scrollWidth > table.parentElement.clientWidth) {
            table.style.fontSize = '0.85em';
        }
    });
}
</script>

@endsection