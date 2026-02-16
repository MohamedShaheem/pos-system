@extends(
    Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.user'
)

@section('content')
    <section class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Gold Balance Entry</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('gold_balance.index') }}">Gold Balance</a></li>
                        <li class="breadcrumb-item active">Add Entry</li>
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
                            <h3 class="card-title">New Gold Balance Entry</h3>
                            <div class="card-tools">
                                <div class="alert alert-info mb-0 py-2">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Current Balance:</strong> {{ number_format($currentBalance, 3) }} grams
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('gold_balance.store') }}" method="POST">
                            @csrf
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
                                           value="{{ old('description') }}" 
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
                                                       id="gold_in" 
                                                       name="transaction_type" 
                                                       value="gold_in" 
                                                       {{ old('transaction_type') == 'gold_in' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="gold_in">
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
                                                       id="gold_out" 
                                                       name="transaction_type" 
                                                       value="gold_out" 
                                                       {{ old('transaction_type') == 'out' ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="gold_out">
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
                                               value="{{ old('amount') }}" 
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

                                <!-- Balance Preview -->
                                <div class="alert alert-secondary" id="balance-preview" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Current Balance:</strong> <span id="current-balance">{{ number_format($currentBalance, 3) }}</span> grams
                                        </div>
                                        <div class="col-md-6">
                                            <strong>New Balance:</strong> <span style="font-weight: 800; color:rgb(12, 250, 12);" id="new-balance">{{ number_format($currentBalance, 3) }}</span> grams
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Note:</strong> 
                                    <span id="transaction-note">Select transaction type to see balance calculation.</span>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Entry
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
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const typeInRadio = document.getElementById('gold_in');
            const typeOutRadio = document.getElementById('gold_out');
            const balancePreview = document.getElementById('balance-preview');
            const currentBalanceSpan = document.getElementById('current-balance');
            const newBalanceSpan = document.getElementById('new-balance');
            const transactionNote = document.getElementById('transaction-note');
            
            const currentBalance = {{ $currentBalance }};

            // Format number input to 3 decimal places
            amountInput.addEventListener('blur', function() {
                if (this.value) {
                    this.value = parseFloat(this.value).toFixed(3);
                }
                updateBalancePreview();
            });

            // Update balance preview when transaction type changes
            typeInRadio.addEventListener('change', updateBalancePreview);
            typeOutRadio.addEventListener('change', updateBalancePreview);
            amountInput.addEventListener('input', updateBalancePreview);

            function updateBalancePreview() {
                const amount = parseFloat(amountInput.value) || 0;
                const isGoldIn = typeInRadio.checked;
                const isGoldOut = typeOutRadio.checked;

                if (amount > 0 && (isGoldIn || isGoldOut)) {
                    balancePreview.style.display = 'block';
                    
                    let newBalance;
                    if (isGoldIn) {
                        newBalance = currentBalance + amount;
                        transactionNote.innerHTML = '<strong>Gold In:</strong> Adding ' + amount.toFixed(3) + ' grams to inventory.';
                        // newBalanceSpan.className = 'text-success';
                    } else {
                        newBalance = currentBalance - amount;
                        transactionNote.innerHTML = '<strong>Gold Out:</strong> Removing ' + amount.toFixed(3) + ' grams from inventory.';
                        
                        if (newBalance < 0) {
                            newBalanceSpan.className = 'text-danger';
                            transactionNote.innerHTML += ' <strong class="text-danger">Warning: Insufficient balance!</strong>';
                        } else {
                            newBalanceSpan.className = 'text-warning';
                        }
                    }
                    
                    currentBalanceSpan.textContent = currentBalance.toFixed(3);
                    newBalanceSpan.textContent = newBalance.toFixed(3);
                } else {
                    balancePreview.style.display = 'none';
                    transactionNote.innerHTML = 'Select transaction type and enter amount to see balance calculation.';
                }
            }

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

                if (isGoldOut && amount > currentBalance) {
                    e.preventDefault();
                    alert('Insufficient gold balance. Available: ' + currentBalance.toFixed(3) + ' grams');
                    return false;
                }

                if (!typeInRadio.checked && !typeOutRadio.checked) {
                    e.preventDefault();
                    alert('Please select transaction type (Gold In or Gold Out).');
                    return false;
                }
            });
        });
    </script>
@endsection