@extends('layouts.admin')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($customer) ? 'Edit Customer' : 'Add Customer' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('Store.index') }}">Store</a></li>
                        <li class="breadcrumb-item active">{{ isset($customer) ? 'Edit' : 'Add' }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
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

                            <form action="{{ isset($store) ? route('stores.update', $store) : route('stores.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($store))
                                    @method('PUT')
                                @endif
                    
                                <div class="form-group">
                                    <label for="name">Store Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $store->name ?? '') }}" required>
                                </div>
                    
                                <div class="form-group">
                                    <label for="sub_name">Sub Name</label>
                                    <input type="text" name="sub_name" class="form-control" value="{{ old('sub_name', $store->sub_name ?? '') }}">
                                </div>
                    
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ old('address', $store->address ?? '') }}" required>
                                </div>
                    
                                <div class="form-group">
                                    <label for="phone_no_1">Phone No 1</label>
                                    <input type="text" name="phone_no_1" class="form-control" value="{{ old('phone_no_1', $store->phone_no_1 ?? '') }}" required>
                                </div>
                    
                                <div class="form-group">
                                    <label for="phone_no_2">Phone No 2</label>
                                    <input type="text" name="phone_no_2" class="form-control" value="{{ old('phone_no_2', $store->phone_no_2 ?? '') }}">
                                </div>
                    
                                <div class="form-group">
                                    <label for="logo">Store Logo</label>
                                    @if (isset($store) && $store->logo)
                                        <p>Current Logo: <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo" style="width: 100px;"></p>
                                    @endif
                                    <input type="file" name="logo" class="form-control">
                                </div>
                    
                                <button type="submit" class="btn btn-success">{{ isset($store) ? 'Update' : 'Create' }}</button>
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
