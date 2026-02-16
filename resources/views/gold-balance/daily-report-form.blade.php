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
                        <li class="breadcrumb-item active">Daily Report</li>
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
                            <h3 class="card-title">Generate Daily Gold Balance Report</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('gold_balance.daily_report') }}" id="reportForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="report_date">Select Date:</label>
                                            <input 
                                                type="date" 
                                                class="form-control @error('report_date') is-invalid @enderror" 
                                                id="report_date" 
                                                name="report_date" 
                                                value="{{ old('report_date', now()->format('Y-m-d')) }}"
                                                max="{{ now()->format('Y-m-d') }}"
                                                required
                                            >
                                            @error('report_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="btn-group" role="group">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-eye"></i> View Report
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .info-box {
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            margin-bottom: 20px;
        }

        .info-box-icon {
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            font-size: 24px;
            float: left;
        }

        .info-box-content {
            padding: 5px 10px;
            margin-left: 70px;
        }

        .info-box-text {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info-box-number {
            display: block;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
@endsection