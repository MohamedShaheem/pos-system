@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($chit) ? 'Edit Chit' : 'Create Chit' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('chits.index') }}">Chits</a></li>
                        <li class="breadcrumb-item active">{{ isset($chit) ? 'Edit' : 'Create' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ isset($chit) ? route('chits.update', $chit->id) : route('chits.store') }}" method="POST">
                                @csrf
                                @if (isset($chit))
                                    @method('PUT')
                                @endif

                                <div class="form-group">
                                    <label for="name">Chit Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $chit->name ?? '') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="month_from">Month From</label>
                                    <select name="month_from" class="form-control">
                                        <option value="">Select Month</option>
                                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                            <option value="{{ $month }}">{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="month_to">Month To</label>
                                    <select name="month_to" class="form-control">
                                        <option value="">Select Month</option>
                                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                            <option value="{{ $month }}">{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="amount_per_month">Amount Per Month</label>
                                    <input type="number" name="amount_per_month" class="form-control" value="{{ old('amount_per_month', $chit->amount_per_month ?? '') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="total_amount">Total Amount</label>
                                    <input type="number" name="total_amount" class="form-control" value="{{ old('total_amount', $chit->total_amount ?? '') }}" >
                                </div>
                                <button type="submit" class="btn btn-success">{{ isset($chit) ? 'Update Chit' : 'Create Chit' }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if ($errors->any())
            toastr.error("Please fix the errors and try again.");
        @endif
    </script>
@endsection