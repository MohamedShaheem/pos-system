@extends(Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user')

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Daily Gold Balance Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gold_balance.index') }}">Gold Balance</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gold_balance_form.daily_report_form') }}">Daily Report</a></li>
                        <li class="breadcrumb-item active">{{ $reportDate->format('Y-m-d') }}</li>
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
                            <h3 class="card-title">Gold Balance Report - {{ $reportDate->format('F d, Y') }}</h3>
                            <div class="card-tools">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm mr-2" onclick="window.print()">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                    <a href="{{ route('gold_balance_form.daily_report_form') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="reportContent">
                            <!-- Report Header -->
                            <div class="text-center mb-4">
                                <h2>Jewel Plaza</h2>
                                <h4>Daily Gold Balance Report</h4>
                                <p>Date: {{ $reportDate->format('F d, Y (l)') }}</p>
                                <hr>
                            </div>

                            <!-- Summary Lines -->
                            <div class="mb-4">
                                    <p><strong>Total Transactions:</strong> {{ $reportData['transaction_count'] }}</p>
                                    <p><strong>Total Gold In:</strong> <span class="text-success" style="font-weight: 600;">{{ number_format($reportData['total_gold_in'], 3) }}g</span></p>
                                    <p><strong>Total Gold Out:</strong> <span class="text-danger" style="font-weight: 600;">{{ number_format($reportData['total_gold_out'], 3) }}g</span></p>
                                    <p><strong>Closing Balance:</strong>
                                        <span class="{{ $reportData['net_change'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: 600;">
                                            {{ $reportData['net_change'] >= 0 ? '+' : '' }}{{ number_format($reportData['net_change'], 3) }}g
                                        </span>
                                    </p>                                
                            </div>

                            <!-- Transaction Details -->
                            @if($reportData['transactions']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Time</th>
                                            <th>Description</th>
                                            <th class="text-right">Gold In (g)</th>
                                            <th class="text-right">Gold Out (g)</th>
                                            <th class="text-right">Net Change (g)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportData['transactions'] as $index => $transaction)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $transaction->created_at->format('h:i A') }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td class="text-right">
                                                @if($transaction->gold_in > 0)
                                                    <span class="text-success fw-bold">{{ number_format($transaction->gold_in, 3) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                @if($transaction->gold_out > 0)
                                                    <span class="text-danger fw-bold">{{ number_format($transaction->gold_out, 3) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <span class="{{ $transaction->net_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->net_amount >= 0 ? '+' : '' }}{{ number_format($transaction->net_amount, 3) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="thead-light">
                                        <tr>
                                            <th colspan="3" class="text-right">TOTALS:</th>
                                            <th class="text-right text-success fw-bold">{{ number_format($reportData['total_gold_in'], 3) }}</th>
                                            <th class="text-right text-danger fw-bold">{{ number_format($reportData['total_gold_out'], 3) }}</th>
                                            <th class="text-right fw-bold {{ $reportData['net_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $reportData['net_change'] >= 0 ? '+' : '' }}{{ number_format($reportData['net_change'], 3) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info text-center">
                                <h4>No Transactions Found</h4>
                                <p>There were no gold balance transactions recorded on {{ $reportDate->format('F d, Y') }}.</p>
                                <p><strong>Balance remained constant at:</strong> {{ number_format($reportData['opening_balance'], 3) }}g</p>
                            </div>
                            @endif

                            <!-- Report Footer -->
                            <div class="mt-4">
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><small>Generated on: {{ now()->format('F d, Y h:i A') }}</small></p>
                                        <p><small>Generated by: {{ Auth::user()->name }}</small></p>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <p><small>Report Type: Daily Gold Balance</small></p>
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
        .thead-dark th {
            background-color: #343a40 !important;
            color: white !important;
        }

        .table tfoot th {
            background-color: #e9ecef;
            border-top: 2px solid #dee2e6;
        }

        @media print {
            .content-header,
            .breadcrumb,
            .card-tools,
            .btn,
            .no-print {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .table {
                font-size: 11px;
            }

            .table th,
            .table td {
                border: 1px solid #000 !important;
                padding: 4px !important;
            }

            .thead-dark th {
                background-color: #f0f0f0 !important;
                color: #000 !important;
                border: 1px solid #000 !important;
            }

            .table tfoot th {
                background-color: #f8f9fa !important;
                border: 1px solid #000 !important;
            }

            @page {
                margin: 0.5in;
                size: A4;
            }
        }
    </style>

    <script>
        // Print optimization
        window.addEventListener('beforeprint', () => {
            document.querySelectorAll('.no-print, .btn, .card-tools').forEach(el => el.style.display = 'none');
        });

        window.addEventListener('afterprint', () => {
            document.querySelectorAll('.no-print, .btn, .card-tools').forEach(el => el.style.display = '');
        });
    </script>
@endsection
