@extends(Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user')

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sub Categories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Sub Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sub Category List</h3>
                    <div class="card-tools">
                        <a href="{{ route('subcategories.createOrEdit') }}" class="btn btn-success">Add Sub Category</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="subcategory-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Main Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subcategories as $sub)
                                <tr>
                                    <td>{{ $sub->name }}</td>
                                    <td>{{ $sub->category->name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('subcategories.createOrEdit', $sub->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('subcategories.destroy', $sub->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button style="display: none;" type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this sub category?')">Delete</button>
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
            $('#subcategory-table').DataTable({
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
