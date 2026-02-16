@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>POS Orders</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">POS Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content pb-3">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">POS Order List</h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary"> POS Order</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="order-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Total</th>
                                {{-- <th>Advance</th> --}}
                                {{-- <th>Balance</th> --}}
                                {{-- <th>Status</th> --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posOrders as $posOrder)
                                <tr>
                                    <td>{{ $posOrder->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $posOrder->invoice_no }}
                                        @if($posOrder->status == 'draft')
                                            <span class="badge badge-warning">Draft</span>
                                        @elseif($posOrder->status == 'hold')
                                            <span class="badge badge-warning">On Hold</span>
                                        @endif    
                                    </td>
                                    <td>
                                        {{ $posOrder->customer->name }}
                                        <a href="{{ route('customer.transactions', $posOrder->customer_id) }}" class="btn btn-link btn-xs">View Transactions</a>
                                    </td>
                                    <td>{{ $posOrder->total }}</td>
                                    {{-- <td>{{ $posOrder->advance }}</td> --}}
                                    {{-- <td>{{ $posOrder->balance }}</td> --}}
                                    {{-- <td>{{ ucfirst($posOrder->status) }}</td> --}}
                                    <td>
                                        @php
                                            $routeName = 'customer-transaction';
                                        @endphp
                                        {{-- <a href="{{ route('pos_orders.edit', $posOrder) }}" class="btn btn-warning">Edit</a> --}}
                                        <a href="{{ route('print.invoice', [$posOrder, 'routeName' => $routeName]) }}" class="btn btn-success btn-xs">Print</a>
                                        @if($posOrder->status != 'hold')
                                            <form action="{{ route('invoice.hold', $posOrder->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-xs">Hold</button>
                                            </form>
                                        @else
                                            <form action="{{ route('invoice.release', $posOrder->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-xs">Release</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('pos_orders.destroy', $posOrder) }}" method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted">
                                Showing {{ $posOrders->firstItem() ?? 0 }} to {{ $posOrders->lastItem() ?? 0 }} of {{ $posOrders->total() }} Invoices
                            </p>
                        </div>
                        <div>
                            {{ $posOrders->links('pagination::bootstrap-4') }}
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    {{-- <script>
        $(document).ready(function() {
            $('#order-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });
    </script> --}}
@endsection
