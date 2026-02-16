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
                <h1>Stock Audits</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Stock Audits</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content pb-3">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Audit History</h3>
                <div class="card-tools">
                    <a href="{{ route('stock-audits.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Start New Audit
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <table id="audits-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Audit Ref</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Expected</th>
                            <th>Scanned</th>
                            <th>Missing</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audits as $audit)
                        @php
                            $missingCount = $audit->expected_count - $audit->scanned_count;
                        @endphp
                        <tr>
                            <td><strong>{{ $audit->audit_reference }}</strong></td>
                            <td>
                                @if($audit->audit_type === 'all')
                                    <span class="badge badge-primary">
                                        <i class="bi bi-collection"></i> Complete Inventory
                                    </span>
                                @else
                                    <span class="badge badge-info">
                                        <i class="bi bi-tag"></i> Category
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($audit->category)
                                    <span class="badge badge-secondary">{{ $audit->category->name }}</span>
                                @else
                                    <span class="text-muted">All Categories</span>
                                @endif
                            </td>
                            <td>{{ $audit->expected_count }}</td>
                            <td>{{ $audit->scanned_count }}</td>
                            <td>
                                @if($missingCount > 0)
                                    <span class="badge badge-danger" style="font-size: 13px;">{{ $missingCount }}</span>
                                @else
                                    <span class="badge badge-success">0</span>
                                @endif
                            </td>
                            <td>
                                @if($audit->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($audit->status == 'in_progress')
                                    <span class="badge badge-warning">In Progress</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($audit->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $audit->started_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $audit->creator->name }}</td>
                            <td>
                                <a href="{{ route('stock-audits.show', $audit->id) }}" class="btn btn-info btn-sm" title="View Results">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($audit->status == 'in_progress')
                                    <a href="{{ route('stock-audits.scan', $audit->id) }}" class="btn btn-warning btn-sm" title="Continue Scanning">
                                        <i class="bi bi-upc-scan"></i>
                                    </a>
                                @endif
                                <form action="{{ route('delete-audit', $audit->id) }}" method="POST" 
                                    onsubmit="return confirm('Are you sure you want to delete this audit?');" style="display: inline;">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-danger btn-sm" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#audits-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[7, 'desc']], // Sort by started date
        });
    });
</script>
@endsection