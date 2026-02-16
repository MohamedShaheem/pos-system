@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>POS Order Details Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">POS Order Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <h3 class="card-title">Order Details</h3>
                    </div>
                    <div class="card-tools">
                        <div class="col-md-2 text-left">
                            <button class="btn no-print btn-warning btn-sm" onclick="window.print()">Print</button>
                        </div>
                    </div>
                    
                    <div class="row col-12">
                        <form id="filterForm" class="row" method="GET" action="{{ route('pos_orders.pos_order_details') }}">
                            <div class="col-md-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" name="date_range" class="form-control" id="dateRange" value="{{ request('start_date') && request('end_date') ? request('start_date') . ' - ' . request('end_date') : now()->subWeek()->format('d/m/Y') . ' - ' . now()->format('d/m/Y') }}">
                                </div>
                                <input type="hidden" name="start_date" id="startDate" value="{{ request('start_date', now()->subWeek()->format('d/m/Y')) }}">
                                <input type="hidden" name="end_date" id="endDate" value="{{ request('end_date', now()->format('d/m/Y')) }}">
                            </div>
                            <div class="col-md-2 text-left">
                                <button type="submit" class="btn no-print btn-success">Filter</button>
                            </div>
                        </form>
                        
                    </div>
                </div>
                <div class="card-body">
                    <table id="order-details-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Gram</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalAmount = 0;
                                $totalGram = 0;
                                $no = 1;
                            @endphp
                            @foreach ($posOrders as $index => $posOrder)
                                @foreach ($posOrder->orderDetails as $detail)
                                    <tr>
                                        <td>{{ $no++  }}</td>
                                        <td>{{ $detail->product ? $detail->product->name : 'N/A' }}</td>
                                        <td>{{ $posOrder->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $detail->weight }}</td>
                                        <td>{{ number_format($detail->amount, 2) }}</td>
                                    </tr>
                                    @php
                                        $totalGram += $detail->weight;
                                        $totalAmount += $detail->amount;
                                    @endphp
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total</th>
                                <th>{{ $totalGram }}</th>
                                <th>{{ number_format($totalAmount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- /moment js --}}
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize date range picker
            $('#dateRange').daterangepicker({
                startDate: moment('{{ request('start_date', now()->subWeek()->format('d/m/Y')) }}', 'DD/MM/YYYY'),
                endDate: moment('{{ request('end_date', now()->format('d/m/Y')) }}', 'DD/MM/YYYY'),
                locale: {
                    format: 'DD/MM/YYYY'
                }
            }, function(start, end) {
                $('#startDate').val(start.format('DD/MM/YYYY'));
                $('#endDate').val(end.format('DD/MM/YYYY'));
            });
        });
    </script>
@endsection