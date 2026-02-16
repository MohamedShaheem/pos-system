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
                <h1>Pending Product Merge Approvals</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Merge Approvals</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Merge Approval Requests</h3>
            </div>
            <div class="card-body">
                <table id="merge-approval-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Requested By</th>
                            <th>Products</th>
                            <th>New Product</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingMerges as $merge)
                        <tr>
                            <td>{{ $merge->id }}</td>
                            <td>{{ $merge->creator->name }}</td>
                            <td>
                                @foreach($merge->source_products_data as $product)
                                    {{ $product['name'] }} ({{ $product['product_no'] }})<br>
                                @endforeach
                            </td>
                            <td>{{ $merge->merged_product_data['name'] }}</td>
                            <td>{{ $merge->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('products.merge.approval.show', $merge->id) }}" class="btn btn-sm btn-primary">
                                    Review
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

{{-- DataTables script --}}
@push('scripts')
<script>
    $(document).ready(function () {
        $('#merge-approval-table').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "order": [[0, 'desc']],
        });
    });
</script>
@endpush

@endsection
