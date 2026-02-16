@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($category) ? 'Edit Category' : 'Create Category' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
                        <li class="breadcrumb-item active">{{ isset($category) ? 'Edit' : 'Create' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
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

                            <form action="{{ isset($category) ? route('categories.storeOrUpdate', $category) : route('categories.storeOrUpdate') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="short_code">Short Code</label>
                                    <input type="text" name="short_code" class="form-control"
                                        value="{{ old('short_code', $category->short_code ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label for="name">Category Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $category->name ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="sort_order">Row Number</label>
                                    <input type="number" name="sort_order" class="form-control"
                                        value="{{ old('sort_order', $category->sort_order ?? '') }}">
                                </div>

                                <button type="submit" class="btn btn-success">
                                    {{ isset($category) ? 'Update' : 'Create' }}
                                </button>
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
