@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tax Rates</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Tax Rates</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tax Rate List</h3>
                    <div class="card-tools">
                        <a href="{{ route('tax_rates.createOrEdit') }}" class="btn btn-success">Create New Tax Rate</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tax-rate-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Rate (%)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($taxRates as $taxRate)
                                <tr>
                                    <td>{{ $taxRate->name }}</td>
                                    <td>{{ $taxRate->rate }}</td>
                                    <td>
                                        <a href="{{ route('tax_rates.createOrEdit', $taxRate->id) }}" class="btn btn-warning">Edit</a>
                                        <form action="{{ route('tax_rates.destroy', $taxRate->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this tax rate?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('#tax-rate-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,   
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@endsection
