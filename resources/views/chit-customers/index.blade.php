@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Chit Customers</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Chit Customers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chit Customer List</h3>
                    <div class="card-tools">
                        <a href="{{ route('chit-customers.createOrEdit') }}" class="btn btn-success">Add Chit Customer</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="chit-customer-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Customer No</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>Tel</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chitCustomers as $customer)
                                <tr>
                                    <td>{{ $customer->customer_no }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>{{ $customer->city }}</td>
                                    <td>{{ $customer->tel }}</td>
                                    <td>
                                        <a href="{{ route('chit-customers.createOrEdit', $customer) }}" class="btn btn-warning">Edit</a>
                                        <form action="{{ route('chit-customers.destroy', $customer) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this chit customer?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        $(document).ready(function() {
            $('#chit-customer-table').DataTable({
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