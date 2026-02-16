@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)
@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($customer) ? 'Edit Customer' : 'Add Customer' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active">{{ isset($customer) ? 'Edit' : 'Add' }}</li>
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
                            <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <script>
                                // Auto-hide after 5 seconds
                                setTimeout(function () {
                                    $('#error-alert').alert('close');
                                }, 2000);
                            </script>
                        @endif

                            <form action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}" method="POST">
                                @csrf
                                @if (isset($customer))
                                    @method('PUT')
                                @endif
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name ?? '') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="tel" class="form-control" value="{{ old('tel', $customer->tel ?? '') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}">
                                </div>
                                <div class="form-group">
                                    <label>Religion <span class="text-danger">*</span></label>
                               <select name="religion" id="religion" class="form-control" required>
                                    <option value="">Select a religion</option>
                                   <option value="tamil" 
                                        {{ old('religion', $customer->religion ?? '') == 'tamil' ? 'selected' : '' }}>
                                        Hindu
                                    </option>

                                    <option value="muslim" 
                                        {{ old('religion', $customer->religion ?? '') == 'muslim' ? 'selected' : '' }}>
                                        Islam
                                    </option>

                                    <option value="christian" 
                                        {{ old('religion', $customer->religion ?? '') == 'christian' ? 'selected' : '' }}>
                                        Christian
                                    </option>

                                    <option value="buddhist" 
                                        {{ old('religion', $customer->religion ?? '') == 'buddhist' ? 'selected' : '' }}>
                                        Buddhist
                                    </option>

                                </select>
                                </div>
                                <button type="submit" class="btn btn-success">{{ isset($customer) ? 'Update' : 'Save' }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection