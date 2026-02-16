{{-- Blade View File - Updated --}}
@extends(
    Auth::user()->role->name === 'admin' ? 'layouts.user' :
    (Auth::user()->role->name === 'staff' ? 'layouts.staff' :
    (Auth::user()->role->name === 'superadmin' ? 'layouts.admin' : 'layouts.staff'))
)
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Create Purchase Old Gold</h4>
                    <a href="{{ route('purchase-old-gold.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form id="purchaseForm">
                        @csrf
                        <!-- Customer Selection -->
                        <div class="row mb-4">
    <div class="col-md-6">
        <label for="customer_search" class="form-label">Customer <span class="text-danger">*</span></label>
        <div class="position-relative">
            <input type="text" class="form-control" id="customer_search" 
                   placeholder="Search or select customer by name, phone, or NIC...">
            <div class="position-absolute" style="top: 0; right: 0; height: 100%; display: flex; align-items: center;">
                <button class="btn btn-outline-secondary btn-sm me-1" type="button" id="toggleDropdown" 
                        style="border: none; background: none; height: 30px;">
                    <i class="fas fa-chevron-down"></i>
                </button>
                <button class="btn btn-outline-secondary btn-sm" type="button" id="clearCustomer" 
                        style="border: none; background: none; height: 30px; margin-right: 8px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- Combined dropdown for both search results and all customers -->
        <div id="customerDropdown" class="dropdown-menu w-100" style="display: none; max-height: 250px; overflow-y: auto; position: absolute; z-index: 1050;">
            <div id="searchResults"></div>
            <div id="allCustomers">
                <div class="dropdown-header">All Customers</div>
                @foreach($customers as $customer)
                    <div class="dropdown-item customer-option" data-id="{{ $customer->id }}" 
                         data-name="{{ $customer->name }}" data-phone="{{ $customer->phone ?? '' }}" 
                         data-nic="{{ $customer->nic ?? '' }}" style="cursor: pointer;">
                        <strong>{{ $customer->name }}</strong><br>
                        <small class="text-muted">
                            {{ $customer->tel ?: 'No phone' }} | {{ $customer->nic ?: 'No NIC' }}
                        </small>
                    </div>
                @endforeach
            </div>
        </div>
        
        <input type="hidden" id="customer_id" name="customer_id">
        <div class="invalid-feedback" id="customer_error"></div>
    </div>
    
    <div class="col-md-6">
        <label for="customer_nic" class="form-label">Customer NIC</label>
        <input type="text" class="form-control" id="customer_nic" name="customer_nic" 
               placeholder="NIC will be auto-filled if available">
    </div>
</div>

                        {{-- <div class="row mb-3">
                            <div class="col-12">
                                <div id="selectedCustomerInfo" class="alert alert-info" style="display: none;">
                                    <strong>Selected Customer:</strong> <span id="customerName"></span> 
                                    (<span id="customerPhone"></span>)
                                </div>
                            </div>
                        </div> --}}

                        <!-- Gold Items Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Gold Items</h5>
                                    <button type="button" class="btn btn-success btn-sm" id="addGoldItem">
                                        <i class="fas fa-plus"></i> Add Gold Item
                                    </button>
                                </div>
                                
                                <div id="goldItemsContainer">
                                    <!-- Gold items will be dynamically added here -->
                                </div>
                                <div class="invalid-feedback" id="gold_items_error"></div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Total Items: <span id="totalItems" class="text-primary">0</span></strong>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Total Grams: <span id="totalGrams" class="text-info">0.00</span> g</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Total Amount: Rs. <span id="totalAmount" class="text-success">0.00</span></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-save"></i> Save Purchase
                                </button>
                                <a href="{{ route('purchase-old-gold.index') }}" class="btn btn-secondary btn-lg ml-2">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gold Item Template -->
<template id="goldItemTemplate">
    <div class="card mb-3 gold-item">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Gold Rate <span class="text-danger">*</span></label>
                    <select class="form-control gold-rate-select" name="gold_items[][gold_rate_id]" required>
                        <option value="">Select Gold Rate</option>
                        @foreach($goldRates as $rate)
                            <option value="{{ $rate->id }}" data-rate="{{ $rate->rate }}">
                                {{ $rate->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gold Grams <span class="text-danger">*</span></label>
                    <input type="number" class="form-control gold-gram-input" 
                           name="gold_items[][gold_gram]" step="0.01" min="0.01" 
                           placeholder="0.00" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Purchase Amount <span class="text-danger">*</span></label>
                    <input type="number" class="form-control purchase-amount-input" 
                           name="gold_items[][gold_purchased_amount]" step="0.01" min="0.01" 
                           placeholder="0.00" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Actions</label>
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-gold-item">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
$(document).ready(function() {
    let goldItemIndex = 0;
    let searchTimeout;
    let selectedCustomerId = null;
    
    // Customer data from server
    const customers = @json($customers);
    console.log('Loaded customers:', customers.length);

    // Add first gold item on page load
    addGoldItem();

    // Customer search functionality
    $('#customer_search').on('input', function() {
        const query = $(this).val().toLowerCase();
        
        clearTimeout(searchTimeout);
        
        if (query.length >= 2) {
            const localResults = customers.filter(customer => 
                customer.name.toLowerCase().includes(query) ||
                (customer.tel && customer.tel.includes(query)) ||
                (customer.nic && customer.nic.toLowerCase().includes(query))
            );
            
            if (localResults.length > 0) {
                displaySearchResults(localResults);
                showDropdown();
            } else {
                searchTimeout = setTimeout(() => {
                    searchCustomersFromServer(query);
                }, 300);
            }
        } else if (query.length === 0) {
            showAllCustomers();
        } else {
            $('#customerDropdown').hide();
        }
    });

    // Toggle dropdown visibility
    $('#toggleDropdown').on('click', function() {
        if ($('#customerDropdown').is(':visible')) {
            $('#customerDropdown').hide();
        } else {
            if ($('#customer_search').val().length >= 2) {
                // Show search results if there's a search query
                const query = $('#customer_search').val().toLowerCase();
                const localResults = customers.filter(customer => 
                    customer.name.toLowerCase().includes(query) ||
                    (customer.tel && customer.tel.includes(query)) ||
                    (customer.nic && customer.nic.toLowerCase().includes(query))
                );
                displaySearchResults(localResults);
            } else {
                showAllCustomers();
            }
            showDropdown();
        }
    });

    // Clear customer selection
    $('#clearCustomer').on('click', function() {
        clearCustomerSelection();
        showAllCustomers();
    });

    // Add gold item
    $('#addGoldItem').on('click', function() {
        addGoldItem();
    });

    // Remove gold item
    $(document).on('click', '.remove-gold-item', function() {
        $(this).closest('.gold-item').remove();
        updateSummary();
    });

    // Calculate amounts when inputs change
    $(document).on('input', '.gold-gram-input, .purchase-amount-input', function() {
        updateSummary();
    });

    // Form submission with enhanced error handling
    $('#purchaseForm').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Form submission started');
        console.log('Selected customer ID:', selectedCustomerId);
        console.log('Gold items count:', $('.gold-item').length);
        
        if (!validateForm()) {
            console.log('Client-side validation failed');
            return;
        }

        // Build form data manually to ensure proper structure
        const formData = {
            _token: $('[name="_token"]').val(),
            customer_id: selectedCustomerId,
            customer_nic: $('#customer_nic').val(),
            gold_items: []
        };

        // Collect gold items data
        $('.gold-item').each(function(index) {
            const item = {
                gold_rate_id: $(this).find('.gold-rate-select').val(),
                gold_gram: $(this).find('.gold-gram-input').val(),
                gold_purchased_amount: $(this).find('.purchase-amount-input').val()
            };
            formData.gold_items.push(item);
        });

        console.log('Form data to submit:', formData);
        
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '{{ route("purchase-old-gold.store") }}',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Success response:', response);
                if (response.print_url) {
                    window.location.href = response.print_url;
                } else {
                    alert('Error: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error Details:');
                console.log('Status:', xhr.status);
                console.log('Status Text:', xhr.statusText);
                console.log('Response Text:', xhr.responseText);
                console.log('Error:', error);
                
                let errorMessage = 'Please check the form for errors';
                
                if (xhr.responseJSON) {
                    console.log('Response JSON:', xhr.responseJSON);
                    
                    if (xhr.responseJSON.errors) {
                        showValidationErrors(xhr.responseJSON.errors);
                    }
                    
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred. Please try again.';
                } else if (xhr.status === 422) {
                    errorMessage = 'Validation errors found. Please check your inputs.';
                } else if (xhr.status === 419) {
                    errorMessage = 'Session expired. Please refresh the page.';
                }
                
                alert(errorMessage);
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Purchase');
            }
        });
    });

    // Select customer from dropdown
    $(document).on('click', '.customer-option', function() {
        const customerId = $(this).data('id');
        const customerName = $(this).data('name');
        const customerPhone = $(this).data('phone');
        const customerNIC = $(this).data('nic');
        
        selectCustomer(customerId, customerName, customerPhone, customerNIC);
    });

    // Hide dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#customer_search, #customerDropdown, #toggleDropdown, #clearCustomer').length) {
            $('#customerDropdown').hide();
        }
    });

    // FUNCTIONS

    function searchCustomersFromServer(query) {
        $.ajax({
            url: '{{ route("purchase-old-gold.search-customers") }}',
            data: { search: query },
            success: function(customers) {
                displaySearchResults(customers);
                showDropdown();
            },
            error: function() {
                const searchResults = $('#searchResults');
                searchResults.empty();
                searchResults.append('<div class="dropdown-item-text">Search temporarily unavailable</div>');
                $('#allCustomers').hide();
                showDropdown();
            }
        });
    }

    // Show search results only
    function displaySearchResults(customers) {
        const searchResults = $('#searchResults');
        const allCustomers = $('#allCustomers');
        
        searchResults.empty();
        allCustomers.hide();

        if (customers.length === 0) {
            searchResults.append('<div class="dropdown-item-text">No customers found</div>');
        } else {
            searchResults.append('<div class="dropdown-header">Search Results</div>');
            customers.forEach(customer => {
                searchResults.append(`
                    <div class="dropdown-item customer-option" data-id="${customer.id}" 
                         data-name="${customer.name}" data-phone="${customer.tel || ''}" 
                         data-nic="${customer.nic || ''}" style="cursor: pointer;">
                        <strong>${customer.name}</strong><br>
                        <small class="text-muted">
                            ${customer.tel || 'No phone'} | ${customer.nic || 'No NIC'}
                        </small>
                    </div>
                `);
            });
        }
    }

    // Show all customers
    function showAllCustomers() {
        $('#searchResults').empty();
        $('#allCustomers').show();
    }

    // Show dropdown
    function showDropdown() {
        $('#customerDropdown').show();
    }

    function selectCustomer(customerId, customerName, customerPhone, customerNIC) {
        selectedCustomerId = customerId;

        $('#customer_search').val(customerName);
        $('#customer_id').val(customerId);
        $('#customer_nic').val(customerNIC || '');
        
        $('#customerName').text(customerName);
        $('#customerPhone').text(customerPhone || 'No phone');
        $('#selectedCustomerInfo').show();
        $('#customerDropdown').hide();
        
        // Clear any previous errors
        $('#customer_error').text('');
        $('#customer_search').removeClass('is-invalid');
        
        console.log('Customer selected:', { customerId, customerName, customerPhone, customerNIC });
    }

    function clearCustomerSelection() {
        selectedCustomerId = null;
        $('#customer_search').val('');
        $('#customer_id').val('');
        $('#customer_nic').val('');
        $('#selectedCustomerInfo').hide();
        $('#customerDropdown').hide();
        console.log('Customer selection cleared');
    }

    function addGoldItem() {
        const template = $('#goldItemTemplate').html();
        if (!template) {
            console.error('Gold item template not found!');
            return;
        }
        
        const newItem = $(template);
        
        // Update name attributes to include index
        newItem.find('select[name*="gold_items"]').attr('name', `gold_items[${goldItemIndex}][gold_rate_id]`);
        newItem.find('input[name*="gold_gram"]').attr('name', `gold_items[${goldItemIndex}][gold_gram]`);
        newItem.find('input[name*="gold_purchased_amount"]').attr('name', `gold_items[${goldItemIndex}][gold_purchased_amount]`);
        
        $('#goldItemsContainer').append(newItem);
        goldItemIndex++;
        updateSummary();
        
        console.log('Gold item added, current index:', goldItemIndex);
    }

    function updateSummary() {
        let totalItems = $('.gold-item').length;
        let totalGrams = 0;
        let totalAmount = 0;

        $('.gold-item').each(function() {
            const grams = parseFloat($(this).find('.gold-gram-input').val()) || 0;
            const amount = parseFloat($(this).find('.purchase-amount-input').val()) || 0;
            
            totalGrams += grams;
            totalAmount += amount;
        });

        $('#totalItems').text(totalItems);
        $('#totalGrams').text(totalGrams.toFixed(2));
        $('#totalAmount').text(totalAmount.toFixed(2));
    }

    function validateForm() {
        let isValid = true;
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Validate customer selection
        if (!selectedCustomerId) {
            $('#customer_search').addClass('is-invalid');
            $('#customer_error').text('Please select a customer');
            isValid = false;
            console.log('Customer validation failed');
        }

        // Validate gold items
        const goldItemsCount = $('.gold-item').length;
        if (goldItemsCount === 0) {
            $('#gold_items_error').text('Please add at least one gold item');
            isValid = false;
            console.log('No gold items found');
        }

        // Validate individual gold items
        $('.gold-item').each(function(index) {
            const goldRateSelect = $(this).find('.gold-rate-select');
            const goldGramInput = $(this).find('.gold-gram-input');
            const purchaseAmountInput = $(this).find('.purchase-amount-input');

            if (!goldRateSelect.val()) {
                goldRateSelect.addClass('is-invalid');
                isValid = false;
                console.log(`Gold rate missing for item ${index}`);
            }

            const gramValue = parseFloat(goldGramInput.val());
            if (!goldGramInput.val() || isNaN(gramValue) || gramValue <= 0) {
                goldGramInput.addClass('is-invalid');
                isValid = false;
                console.log(`Invalid gold gram for item ${index}: ${goldGramInput.val()}`);
            }

            const amountValue = parseFloat(purchaseAmountInput.val());
            if (!purchaseAmountInput.val() || isNaN(amountValue) || amountValue <= 0) {
                purchaseAmountInput.addClass('is-invalid');
                isValid = false;
                console.log(`Invalid purchase amount for item ${index}: ${purchaseAmountInput.val()}`);
            }
        });

        console.log('Form validation result:', isValid);
        return isValid;
    }

    function showValidationErrors(errors) {
        console.log('Showing validation errors:', errors);
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Show customer errors
        if (errors.customer_id) {
            $('#customer_search').addClass('is-invalid');
            $('#customer_error').text(errors.customer_id[0]);
        }

        // Show gold items errors
        if (errors.gold_items) {
            $('#gold_items_error').text(errors.gold_items[0]);
        }

        // Show individual item errors
        Object.keys(errors).forEach(key => {
            const match = key.match(/gold_items\.(\d+)\.(.+)/);
            if (match) {
                const index = match[1];
                const field = match[2];
                const goldItem = $('.gold-item').eq(index);
                
                if (field === 'gold_rate_id') {
                    goldItem.find('.gold-rate-select').addClass('is-invalid');
                } else if (field === 'gold_gram') {
                    goldItem.find('.gold-gram-input').addClass('is-invalid');
                } else if (field === 'gold_purchased_amount') {
                    goldItem.find('.purchase-amount-input').addClass('is-invalid');
                }
            }
        });
    }
});
</script>
@endsection