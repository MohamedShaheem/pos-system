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
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">POS Order</a>
                    </div>
                </div>

                <div class="card-body">

                    {{-- FILTER FORM --}}
                  <form method="GET" action="{{ route('pos_orders.index') }}" class="mb-3">
                        <div class="form-row" >

                            <div class="col-md-5 mb-2 d-flex">
                                <input type="text" name="invoice" class="form-control"
                                    placeholder="Search by Invoice No"
                                    value="{{ $invoice ?? '' }}">
                                
                                <button type="submit" class="btn btn-primary mr-2 ml-2">Search</button>
                            </div>

                            <div class="col-md-2 mb-2">
                                <input type="date" name="from" class="form-control"
                                    value="{{ $from ?? '' }}">
                            </div>

                            <div class="col-md-3 mb-2">
                                <input type="date" name="to" class="form-control"
                                    value="{{ $to ?? '' }}">
                            </div>

                            <div class="col-md-2 mb-2 d-flex">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
                                <a href="{{ route('pos_orders.index') }}" class="btn btn-secondary">Reset</a>
                            </div>

                        </div>
                    </form>


                    {{-- TABLE --}}
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                {{-- <th>Total</th> --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($posOrders as $posOrder)
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
                                        <a href="{{ route('customer.transactions', $posOrder->customer_id) }}"
                                           class="btn btn-link btn-xs">View Transactions</a>
                                    </td>
                                    {{-- <td>{{ $posOrder->total }}</td> --}}

                                    <td>
                                        @php $routeName = 'customer-transaction'; @endphp

                                        <a href="{{ route('print.invoice', [$posOrder, 'routeName' => $routeName]) }}"
                                           class="btn btn-success btn-xs">Print</a>

                                        @if($posOrder->status != 'hold')
                                            <form action="{{ route('invoice.hold', $posOrder->id) }}"
                                                  method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-xs">Hold</button>
                                            </form>
                                        @else
                                            <form action="{{ route('invoice.release', $posOrder->id) }}"
                                                  method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-xs">Release</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('pos_orders.destroy', $posOrder) }}"
                                              method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No invoices found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- PAGINATION + INFO --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted">
                                Showing {{ $posOrders->firstItem() ?? 0 }} to
                                {{ $posOrders->lastItem() ?? 0 }} of
                                {{ $posOrders->total() }} Invoices
                            </p>
                        </div>
                        <div>
                            {{ $posOrders->appends([
                                'invoice' => $invoice,
                                'from' => $from,
                                'to' => $to
                            ])->links('pagination::bootstrap-4') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
