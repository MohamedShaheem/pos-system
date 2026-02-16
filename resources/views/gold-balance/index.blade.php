@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Gold Balance</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Gold Balance</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Gold Balance Transactions</h3>
                            <div class="card-tools">
                                <a href="{{ route('gold_balance.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add New Entry
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Search and entries controls -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <label for="entries-select" class="mr-2">Show</label>
                                        <select id="entries-select" class="form-control form-control-sm" style="width: 80px;">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100" selected>100</option>
                                        </select>
                                        <span class="ml-2">entries</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end">
                                        <div class="d-flex align-items-center">
                                            <label for="search-input" class="mr-2">Search:</label>
                                            <input type="text" id="search-input" class="form-control form-control-sm" style="width: 200px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="gold-table">
                                    <thead style="background-color: #5a6c7d; color: white;">
                                        <tr>
                                            <th>Seq</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>In (Gram)<br>
                                                @if($goldBalances->count() > 0)
                                                    <span style="color: rgb(10, 223, 28)">
                                                        {{ number_format($goldBalances->sum('gold_in'), 2) }}g
                                                    </span>
                                                @endif
                                            </th>

                                            <th>Out (Gram)<br>
                                                @if($goldBalances->count() > 0)
                                                    <span style="color: rgb(250, 74, 74)">
                                                        {{ number_format($goldBalances->sum('gold_out'), 2) }}g
                                                    </span>
                                                @endif
                                            </th>

                                            <th>Balance (Gram) <br>
                                                @if($goldBalances->count() > 0)
                                                    <span style="color: rgb(10, 191, 223)">{{ number_format($goldBalances->last()->gold_balance, 2) }}g</span> 
                                                @endif
                                            </th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($goldBalances as $index => $balance)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info">{{ $index + 1 }}</span>
                                                </td>
                                                <td>{{ $balance->created_at->format('Y-m-d') }}</td>
                                                <td>{{ $balance->description }}</td>
                                                <td class="text-right">
                                                    @if($balance->gold_in)
                                                        <span class="text-success">{{ number_format($balance->gold_in, 3) }}</span>
                                                    @else
                                                        <span class="text-muted">0.000</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if($balance->gold_out)
                                                        <span class="text-danger">{{ number_format($balance->gold_out, 3) }}</span>
                                                    @else
                                                        <span class="text-muted">0.000</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    <span class="text-info font-weight-bold">{{ number_format($balance->gold_balance, 3) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group justify-item-center" role="group">
                                                        <a href="{{ route('gold_balance.edit', $balance->id) }}" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('gold_balance.destroy', $balance) }}" method="POST" >
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this entry?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No gold balance entries found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination info -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="dataTables_info">
                                        Showing {{ $goldBalances->count() > 0 ? 1 : 0 }} to {{ $goldBalances->count() }} of {{ $goldBalances->count() }} entries
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .table th {
            background-color: #5a6c7d !important;
            color: white !important;
            border-color: #495057 !important;
        }
        
        .badge {
            padding: 0.5em 0.75em;
            font-size: 0.875rem;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .text-info {
            color: #17a2b8 !important;
        }
        
        .text-muted {
            color: #6c757d !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const entriesSelect = document.getElementById('entries-select');
            const table = document.getElementById('gold-table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Entries per page functionality
            entriesSelect.addEventListener('change', function() {
                const limit = parseInt(this.value);
                
                rows.forEach((row, index) => {
                    if (index < limit) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection