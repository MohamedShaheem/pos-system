@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Gold Balance Entry</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gold_balance.index') }}">Gold Balance</a></li>
                        <li class="breadcrumb-item active">Edit Entry</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Edit Gold Balance Entry</h3>
                            <div class="card-tools">
                                <div class="alert alert-info mb-0 py-2">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Current Balance:</strong> {{ number_format($currentBalance, 3) }} grams
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('gold_balance.update', $goldBalance) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('description') is-invalid @enderror" 
                                           id="description" 
                                           name="description" 
                                           value="{{ old('description', $goldBalance->description) }}" 
                                           required 
                                           placeholder="Enter transaction description">
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Transaction Type <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" 
                                                       class="custom-control-input @error('transaction_type') is-invalid @enderror" 
                                                       id="type_in" 
                                                       name="transaction_type" 
                                                       value="in" 
                                                       {{ old('transaction_type', $transactionType) == 'in' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="type_in">
                                                    <i class="fas fa-arrow-down text-success"></i>
                                                    <strong>Gold In</strong>
                                                    <small class="text-muted d-block">Add gold to inventory</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" 
                                                       class="custom-control-input @error('transaction_type') is-invalid @enderror" 
                                                       id="type_out" 
                                                       name="transaction_type" 
                                                       value="out" 
                                                       {{ old('transaction_type', $transactionType) == 'out' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="type_out">
                                                    <i class="fas fa-arrow-up text-danger"></i>
                                                    <strong>Gold Out</strong>
                                                    <small class="text-muted d-block">Remove gold from inventory</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('transaction_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="amount">Amount (Grams) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" 
                                               name="amount" 
                                               value="{{ old('amount', $amount) }}" 
                                               step="0.001" 
                                               min="0.001" 
                                               placeholder="0.000"
                                               required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">grams</span>
                                        </div>
                                    </div>
                                    @error('amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Current Entry Info -->
                                <div class="alert alert-warning">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Original Transaction:</strong> 
                                            @if($transactionType == "in")
                                                <span class="text-success">Gold In: {{ number_format($goldBalance->gold_in, 3) }} grams</span>
                                            @else
                                                <span class="text-danger">Gold Out: {{ number_format($goldBalance->gold_out, 3) }} grams</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Current Balance:</strong> {{ number_format($goldBalance->gold_balance, 3) }} grams
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> Editing this entry will recalculate all subsequent gold balances. Make sure your changes are correct.
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Entry
                                </button>
                                <a href="{{ route('gold_balance.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
       document.addEventListener('DOMContentLoaded', function () {
    const amountInput = document.getElementById('amount');
    const typeInRadio = document.getElementById('type_in');
    const typeOutRadio = document.getElementById('type_out');
    const currentBalance = parseFloat({{ $currentBalance }});
    const balancePreview = document.createElement('div');

    // Insert balance preview just below the amount input
    amountInput.closest('.form-group').appendChild(balancePreview);
    balancePreview.className = "mt-2 font-weight-bold";

    function updateBalancePreview() {
        const amount = parseFloat(amountInput.value) || 0;
        let newBalance = currentBalance;

        if (typeInRadio.checked) {
            newBalance += amount;
        } else if (typeOutRadio.checked) {
            newBalance -= amount;
        } else {
            balancePreview.innerHTML = `<span class="text-muted">Select transaction type to preview new balance</span>`;
            return;
        }

        const balanceColor = newBalance >= 0 ? 'text-success' : 'text-danger';
        balancePreview.innerHTML = `<span class="${balanceColor}">New Balance: ${newBalance.toFixed(3)} grams</span>`;
    }

    // Trigger calculation on change
    amountInput.addEventListener('input', updateBalancePreview);
    typeInRadio.addEventListener('change', updateBalancePreview);
    typeOutRadio.addEventListener('change', updateBalancePreview);

            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const amount = parseFloat(amountInput.value) || 0;
                const isGoldOut = typeOutRadio.checked;

                if (amount === 0) {
                    e.preventDefault();
                    alert('Please enter a valid amount.');
                    return false;
                }

                if (!typeInRadio.checked && !typeOutRadio.checked) {
                    e.preventDefault();
                    alert('Please select transaction type (Gold In or Gold Out).');
                    return false;
                }

                // Show confirmation for changes
                if (amount !== originalAmount || 
                    (isGoldOut && !{{ $transactionType == 'out' ? 'true' : 'false' }}) ||
                    (!isGoldOut && !{{ $transactionType == 'in' ? 'true' : 'false' }})) {
                    
                    const confirmMessage = 'This will recalculate all subsequent balances. Are you sure you want to proceed?';
                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        });
    </script>
@endsection