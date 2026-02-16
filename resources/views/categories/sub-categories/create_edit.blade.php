@extends(Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user')

@section('content')
<section class="content-header">
    <div class="container">
        <h1>{{ isset($subcategory) ? 'Edit Sub Category' : 'Create Sub Category' }}</h1>
    </div>
</section>

<section class="content">
    <div class="container">
        <form action="{{ isset($subcategory) ? route('subcategories.storeOrUpdate', $subcategory) : route('subcategories.storeOrUpdate') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="product_category_id">Parent Category</label>
                <select name="product_category_id" class="form-control" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('product_category_id', $subcategory->product_category_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name">Sub Category Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $subcategory->name ?? '') }}" required>
            </div>

            <button type="submit" class="btn btn-success">{{ isset($subcategory) ? 'Update' : 'Create' }}</button>
        </form>
    </div>
</section>
@endsection
