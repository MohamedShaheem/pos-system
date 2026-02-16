@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Chit List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Chits</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chit List</h3>
                    <div class="card-tools">
                        <a href="{{ route('chits.create') }}" class="btn btn-success">Add Chit</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="chit-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Serial No</th>
                                <th>Name</th>
                                <th>Month From</th>
                                <th>Month To</th>
                                <th>Total Amount</th>
                                <th>Amount Per Month</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chits as $chit)
                                <tr>
                                    <td>{{ $chit->serial_no }}</td>
                                    <td>{{ $chit->name }}</td>
                                    <td>{{ $chit->month_from }}</td>
                                    <td>{{ $chit->month_to }}</td>
                                    <td>{{ $chit->total_amount }}</td>
                                    <td>{{ $chit->amount_per_month }}</td>
                                    <td>
                                        <a href="{{ route('chits.show', $chit) }}" class="btn btn-info">View</a>
                                        <a href="{{ route('chits.edit', $chit) }}" class="btn btn-warning">Edit</a>
                                        <form action="{{ route('chits.destroy', $chit) }}" method="POST" style="display:inline;">
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
    </div>

    <script>
        $(document).ready(function() {
            $('#chit-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });
    </script>
@endsection