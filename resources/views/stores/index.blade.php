@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Customers</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Customer List</h3>
                    <div class="card-tools">
                        <a href="{{ route('customers.create') }}" class="btn btn-success">Add Customer</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="customer-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Sub Name</th>
                                <th>Address</th>
                                <th>Phone No 1</th>
                                <th>Phone No 2</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stores as $store)
                                <tr>
                                    <td>{{ $store->name }}</td>
                                    <td>{{ $store->sub_name }}</td>
                                    <td>{{ $store->address }}</td>
                                    <td>{{ $store->phone_no_1 }}</td>
                                    <td>{{ $store->phone_no_2 }}</td>
                                    <td>
                                        <a href="{{ route('stores.edit', $store) }}" class="btn btn-warning">Edit</a>
                                        <form action="{{ route('stores.destroy', $store) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
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
            $('#customer-table').DataTable({
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
