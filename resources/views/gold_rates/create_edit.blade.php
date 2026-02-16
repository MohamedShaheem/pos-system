@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
<section class="content-header">
    <div class="container">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ isset($goldRate) ? 'Edit Rate' : 'Create Rate' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('gold_rates.index') }}">Gold Rates</a></li>
                    <li class="breadcrumb-item active">{{ isset($goldRate) ? 'Edit' : 'Create' }}</li>
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

                        <form action="{{ isset($goldRate) ? route('gold_rates.storeOrUpdate', $goldRate) : route('gold_rates.storeOrUpdate') }}" method="POST">
                            @csrf

                            {{-- Type --}}
                            {{-- <div class="form-group">
                                <label for="type">Rate Type</label>
                                <select name="type" id="type" class="form-control" {{ isset($goldRate) ? 'readonly' : '' }} required>
                                    <option value="">Select Type</option>
                                    <option value="gold" {{ old('type', $goldRate->type ?? '') === 'gold' ? 'selected' : '' }}>Gold</option>
                                    <option value="silver" {{ old('type', $goldRate->type ?? '') === 'silver' ? 'selected' : '' }}>Silver</option>
                                    <option value="goldpcs" {{ old('type', $goldRate->type ?? '') === 'goldpcs' ? 'selected' : '' }}>Gold pcs</option>
                                    <option value="silverpcs" {{ old('type', $goldRate->type ?? '') === 'silverpcs' ? 'selected' : '' }}>Silver pcs</option>
                                </select>
                            </div> --}}

                            <div class="form-group">
                                <label for="type">Rate Type</label>

                                <select name="type" id="type" class="form-control" 
                                    {{ isset($goldRate) ? 'disabled' : '' }} required>
                                    <option value="">Select Type</option>
                                    <option value="gold" {{ old('type', $goldRate->type ?? '') === 'gold' ? 'selected' : '' }}>Gold</option>
                                    <option value="silver" {{ old('type', $goldRate->type ?? '') === 'silver' ? 'selected' : '' }}>Silver</option>
                                    <option value="goldpcs" {{ old('type', $goldRate->type ?? '') === 'goldpcs' ? 'selected' : '' }}>Gold pcs</option>
                                    <option value="silverpcs" {{ old('type', $goldRate->type ?? '') === 'silverpcs' ? 'selected' : '' }}>Silver pcs</option>
                                </select>

                                @if(isset($goldRate))
                                    <!-- Preserve value since disabled fields aren't submitted -->
                                    <input type="hidden" name="type" value="{{ $goldRate->type }}">
                                @endif
                            </div>


                            {{-- Name --}}
                            <div class="form-group">
                                <label for="name">Carat / Type Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $goldRate->name ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                    <label for="percentage">Percentage / Remarks</label>
                                    <input type="text" id="percentage" name="percentage" class="form-control" value="{{ old('percentage', $goldRate->percentage ?? '') }}">
                            </div>

                            {{-- Gold-specific fields --}}
                            <div id="gold-fields" style="display: none;">
                                <div class="form-group">
                                    <label for="rate_per_pawn">Gold Rate (per pawn)</label>
                                    <input type="number" step="0.01" id="rate_per_pawn" name="rate_per_pawn" class="form-control" value="{{ old('rate_per_pawn', $goldRate->rate_per_pawn ?? '') }}">
                                </div>

                                
                            </div>

                            

                            {{-- Rate (common) --}}
                            <div class="form-group">
                                <label for="rate">Rate (per gram)</label>
                                <input type="number" step="0.01" id="rate" name="rate" class="form-control" 
                                    value="{{ old('rate', $goldRate->rate ?? '') }}" 
                                    {{ old('type', $goldRate->type ?? '') === 'gold' ? 'readonly' : '' }} required>
                            </div>


                            <button type="submit" class="btn btn-success">{{ isset($goldRate) ? 'Update' : 'Create' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('rate_per_pawn').addEventListener('input', function() {
        let perPawnRate = parseFloat(this.value) || 0;
        let perGramRate = perPawnRate / 8;
        document.getElementById('rate').value = perGramRate.toFixed(2);
    });

    function toggleFields() {
        let type = document.getElementById('type').value;
        let goldFields = document.getElementById('gold-fields');
        let rateInput = document.getElementById('rate');

        if (type === 'gold') {
            goldFields.style.display = 'block';
            rateInput.readOnly = true; // disable typing
        } else {
            goldFields.style.display = 'none';
            rateInput.readOnly = false; // allow typing
        }
    }

    document.getElementById('type').addEventListener('change', toggleFields);
    toggleFields(); // Initial load

    // Auto-calc gold rate from pawn
    let pawnInput = document.getElementById('rate_per_pawn');
    if (pawnInput) {
        pawnInput.addEventListener('input', function() {
            let perPawnRate = parseFloat(this.value) || 0;
            document.getElementById('rate').value = (perPawnRate / 8).toFixed(2);
        });
    }

    @if (session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if ($errors->any())
        toastr.error("Please fix the errors and try again.");
    @endif
</script>
@endsection
