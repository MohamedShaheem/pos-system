@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($chitCustomer) ? 'Edit Chit Customer' : 'Add Chit Customer' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('chit-customers.index') }}">Chit Customers</a></li>
                        <li class="breadcrumb-item active">{{ isset($chitCustomer) ? 'Edit' : 'Add' }}</li>
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

                            <form action="{{ isset($chitCustomer) ? route('chit-customers.storeOrUpdate', $chitCustomer) : route('chit-customers.storeOrUpdate') }}" method="POST">
                                @csrf
                                
                                <div class="form-group">
                                    <label>Customer No</label>
                                    <input type="text" name="customer_no" class="form-control" value="{{ old('customer_no', $chitCustomer->customer_no ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $chitCustomer->name ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $chitCustomer->address ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $chitCustomer->city ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label>Tel</label>
                                    <input type="text" name="tel" class="form-control" value="{{ old('tel', $chitCustomer->tel ?? '') }}">
                                </div>

                                <button type="submit" class="btn btn-success">{{ isset($chitCustomer) ? 'Update' : 'Save' }}</button>
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