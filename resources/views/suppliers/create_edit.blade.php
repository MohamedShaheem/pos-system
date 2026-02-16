@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($supplier) ? 'Edit Supplier' : 'Create Supplier' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                        <li class="breadcrumb-item active">{{ isset($supplier) ? 'Edit' : 'Create' }}</li>
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

                            <form action="{{ isset($supplier) ? route('suppliers.storeOrUpdate', $supplier) : route('suppliers.storeOrUpdate') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="supplier_name">Supplier Name</label>
                                    <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name', $supplier->supplier_name ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="short_code">Supplier Short Code</label>
                                    <input type="text" name="short_code" class="form-control" maxlength="4" value="{{ old('short_code', $supplier->short_code ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $supplier->address ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $supplier->city ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="contact_no">Contact No</label>
                                    <input type="text" name="contact_no" class="form-control" value="{{ old('contact_no', $supplier->contact_no ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ old('status', $supplier->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $supplier->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success">{{ isset($supplier) ? 'Update' : 'Create' }}</button>
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
