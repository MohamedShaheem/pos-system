@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Categories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Category List</h3>
                    <div class="card-tools">
                        <a href="{{ route('categories.createOrEdit') }}" class="btn btn-success">Create New Category</a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="category-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Short Code</th>
                                <th>Name</th>
                                <th>Order No</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->short_code }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        <a href="{{ route('categories.createOrEdit', $category->id) }}" class="btn btn-warning">Edit</a>
                                        {{-- <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this category?')">
                                                Delete
                                            </button>
                                        </form> --}}
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
            $('#category-table').DataTable({
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
