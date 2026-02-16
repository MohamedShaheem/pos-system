<!-- resources/views/tax_rates/create_edit.blade.php -->
@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($taxRate) ? 'Edit Tax Rate' : 'Create Tax Rate' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tax_rates.index') }}">Tax Rates</a></li>
                        <li class="breadcrumb-item active">{{ isset($taxRate) ? 'Edit' : 'Create' }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
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

                            <form action="{{ isset($taxRate) ? route('tax_rates.update', $taxRate) : route('tax_rates.store') }}" method="POST">
                                @csrf
                                @if (isset($taxRate))
                                    @method('PUT')
                                @endif

                                <div class="form-group">
                                    <label for="name">Tax Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $taxRate->name ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="rate">Rate (%)</label>
                                    <input type="number" name="rate" class="form-control" value="{{ old('rate', $taxRate->rate ?? '') }}" required>
                                </div>

                                <button type="submit" class="btn btn-success">{{ isset($taxRate) ? 'Update' : 'Create' }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if ($errors->any())
            toastr.error("Please fix the errors and try again.");
        @endif
    </script>
@endsection
