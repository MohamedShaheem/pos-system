<?php

use App\Http\Controllers\ChitController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GoldRateController;
use App\Http\Controllers\POSOrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\ChitCustomerController;
use App\Http\Controllers\CustomerAdvanceController;
use App\Http\Controllers\CustomerAdvanceRefundController;
use App\Http\Controllers\CustomerGoldAdvanceController;
use App\Http\Controllers\CustomerGoldCashAdvanceController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\GoldBalanceController;
use App\Http\Controllers\ProductMergeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\POSOrderAdvanceController;
use App\Http\Controllers\PurchaseOldGoldController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StockAuditController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Models\CustomerGoldAdvance;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:superadmin'])->group(function () {
        Route::resource('stores', StoreController::class);
        Route::get('stores/create-or-edit/{store?}', [StoreController::class, 'createOrEdit'])->name('stores.createOrEdit');
        Route::post('stores/store-or-update/{store?}', [StoreController::class, 'storeOrUpdate'])->name('stores.storeOrUpdate');


        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}', [UserController::class, 'update']);
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        
        Route::get('/users/barcode/{userBarcode}', [UserController::class, 'generateBarcode'])->name('users.barcode');

        Route::resource('tax_rates', TaxRateController::class);
        Route::get('tax_rates/create-or-edit/{taxRate?}', [TaxRateController::class, 'createOrEdit'])->name('tax_rates.createOrEdit');
        Route::post('tax_rates/store-or-update/{taxRate?}', [TaxRateController::class, 'storeOrUpdate'])->name('tax_rates.storeOrUpdate');

        Route::prefix('stock-audits')->name('stock-audits.')->group(function () {
                Route::get('/', [StockAuditController::class, 'index'])->name('index');
                Route::get('/create', [StockAuditController::class, 'create'])->name('create');
                Route::post('/', [StockAuditController::class, 'store'])->name('store');
                Route::get('/{id}/scan', [StockAuditController::class, 'scan'])->name('scan');
                Route::post('/{id}/scan-product', [StockAuditController::class, 'scanProduct'])->name('scan-product');
                Route::post('/{id}/complete', [StockAuditController::class, 'complete'])->name('complete');
                Route::get('/{id}', [StockAuditController::class, 'show'])->name('show');
                Route::delete('/{auditId}/items/{itemId}', [StockAuditController::class, 'deleteItem'])->name('delete-item');
            });

        Route::delete('/stock-audits/{id}/delete', [StockAuditController::class, 'deleteAudit'])->name('delete-audit'); 
    });

    Route::middleware(['role:admin|superadmin'])->group(function () {
        
        Route::get('chits', [ChitController::class, 'index'])->name('chits.index');
        Route::get('chits/create', [ChitController::class, 'create'])->name('chits.create');
        Route::post('chits', [ChitController::class, 'store'])->name('chits.store');
        Route::get('chits/{chit}', [ChitController::class, 'show'])->name('chits.show');
        Route::get('chits/{chit}/edit', [ChitController::class, 'edit'])->name('chits.edit');
        Route::put('chits/{chit}', [ChitController::class, 'update'])->name('chits.update');
        Route::delete('chits/{chit}', [ChitController::class, 'destroy'])->name('chits.destroy');
        
        Route::post('/chits/updateChitPaidStatus', [ChitController::class, 'updateChitPaidStatus'])->name('chits.updateChitPaidStatus');
        Route::post('/chits/updateChitDetail', [ChitController::class, 'updateChitDetail'])->name('chits.updateChitDetail');
        Route::post('chits/{chit}/customers', [ChitController::class, 'updateCustomers'])->name('chits.updateCustomers');
        Route::delete('chits/{chit}/customers/{customer}', [ChitController::class, 'removeCustomer'])->name('chits.removeCustomer');
        Route::post('chits/{chit}/mark-months-complete', [ChitController::class, 'markMonthsComplete'])->name('chits.markMonthsComplete');
        Route::delete('chits/{chit}/customers/{customer}', [ChitController::class, 'removeCustomer'])->name('chits.removeCustomer');
        Route::get('chit-customers', [ChitCustomerController::class, 'index'])->name('chit-customers.index');
        Route::get('chit-customers/create-or-edit/{chitCustomer?}', [ChitCustomerController::class, 'createOrEdit'])->name('chit-customers.createOrEdit');
        Route::post('chit-customers/store-or-update/{chitCustomer?}', [ChitCustomerController::class, 'storeOrUpdate'])->name('chit-customers.storeOrUpdate');
        Route::delete('chit-customers/{chitCustomer}', [ChitCustomerController::class, 'destroy'])->name('chit-customers.destroy');
    
        
        Route::get('/dashboard', [POSOrderController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard_v/{id?}', [POSOrderController::class, 'dashboard_v'])->name('dashboard_v');
        Route::get('/product-details/{id}', [POSOrderController::class, 'getProductDetails'])->name('product.details');

        Route::get('/print-invoice/{id}/{routeName}', [POSOrderController::class, 'printInvoice'])->name('print.invoice');
        Route::get('/print-receipt/{advance}/{detail}/{routeName}', [CustomerAdvanceController::class, 'printReceipt'])->name('print.receipt');
        Route::get('/print-receipt-gold/{advance}/{detail}/{routeName}', [CustomerGoldAdvanceController::class, 'printReceipt'])->name('print.receipt.gold');
        Route::get('/print-receipt-gold-cash/{cashDetailId}/{goldDetailIds}/{routeName}', [CustomerGoldCashAdvanceController::class, 'printReceipt'])->name('print.receipt.gold.cash');
        Route::get('/print-reservation/{reservationId}/{routeName}', [ReservationController::class, 'printReservationReceipt'])
        ->name('print.reservation.receipt');
        Route::get('purchase-old-gold/{purchaseOldGold}/invoice', [PurchaseOldGoldController::class, 'printInvoice'])
        ->name('purchase-old-gold.printInvoice');


        
        Route::get('/customer-transactions/{customerId}', [CustomerManagementController::class, 'customerTransactions'])->name('customer.transactions');
        Route::post('/hold-invoice/{id}', [POSOrderController::class, 'holdInvoice'])->name('invoice.hold');
        Route::post('/release-invoice/{id}', [POSOrderController::class, 'releaseInvoice'])->name('invoice.release');
        Route::get('/customer/advance-summary/{orderNo}', [CustomerManagementController::class, 'getAdvanceSummary']);
        // Route::get('/pos-orders/view/{id}', [CustomerManagementController::class, 'view'])->name('pos_orders.view');


        // Add these routes to your routes/web.php file
        Route::prefix('purchase-old-gold')->name('purchase-old-gold.')->group(function () {
        Route::get('/', [PurchaseOldGoldController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOldGoldController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOldGoldController::class, 'store'])->name('store');
        Route::get('/{purchaseOldGold}', [PurchaseOldGoldController::class, 'show'])->name('show');
        Route::get('/{purchaseOldGold}/edit', [PurchaseOldGoldController::class, 'edit'])->name('edit');
        Route::put('/{purchaseOldGold}', [PurchaseOldGoldController::class, 'update'])->name('update');
        Route::delete('/{purchaseOldGold}', [PurchaseOldGoldController::class, 'destroy'])->name('destroy');
        
        // AJAX routes for customer search and management
        Route::get('/search/customers', [PurchaseOldGoldController::class, 'searchCustomers'])->name('search-customers');
        Route::get('/customers/all', [PurchaseOldGoldController::class, 'getAllCustomers'])->name('get-all-customers');
        Route::get('/customers/{customer}', [PurchaseOldGoldController::class, 'getCustomer'])->name('get-customer');
        Route::put('/customers/{customer}/update', [PurchaseOldGoldController::class, 'updateCustomerNIC'])->name('update-customer-nic');
        Route::get('/gold-rates', [PurchaseOldGoldController::class, 'getGoldRates'])->name('get-gold-rates');
        });
       

        Route::post('/verify-staff-barcode', [POSOrderController::class, 'verifyStaffBarcode'])->name('verify.staff.barcode');
        Route::get('/pos-order-details', [POSOrderController::class, 'posOrderDetails'])->name('pos_orders.details');
        Route::get('/pos-orders/details', [POSOrderController::class, 'posOrderDetails'])->name('pos_orders.pos_order_details');

        
        Route::get('/reports/stock-ledger-summary', [ReportController::class, 'stockLedgerSummary'])->name('reports.stock_ledger_summary');

        // Route::get('/dashboard', function () {
        //     return view('dashboard');
        // })->name('dashboard');

        // Customer advance routes
        Route::get('/customers/{customer}/advances', [CustomerAdvanceController::class, 'index'])->name('customer.advances.index');
        Route::get('/customer/{customer}/advance-balance', [CustomerAdvanceController::class, 'getBalance'])->name('customer.advance.balance');
        Route::post('/customer/{customer}/advance', [CustomerAdvanceController::class, 'store'])->name('customer.advance.store');
        Route::post('/pos-order/{order}/use-advance', [CustomerAdvanceController::class, 'useAdvance'])->name('customer.advance.use');
        Route::delete('/advance/{id}/cancel', [CustomerAdvanceController::class, 'cancelAdvance'])->name('customer.advance.cancel');
        Route::get('/customer/advances/{id}/order-no/suggestions', [CustomerAdvanceController::class, 'orderNoSuggestions']);


        // Customer gold advance routes
        Route::get('/customers/gold/{customer}/advances', [CustomerGoldAdvanceController::class, 'index'])->name('customer.gold.advances.index');
        Route::get('/customer/gold/{customer}/advance-balance', [CustomerGoldAdvanceController::class, 'getBalance'])->name('customer.gold.advance.balance');
        Route::post('/customer/gold/{customer}/advance', [CustomerGoldAdvanceController::class, 'store'])->name('customer.gold.advance.store');
        Route::post('/pos-order/gold/{order}/use-advance', [CustomerGoldAdvanceController::class, 'useAdvance'])->name('customer.gold.advance.use');
        Route::delete('/advance/gold/{id}/cancel', [CustomerGoldAdvanceController::class, 'cancelAdvance'])->name('customer.gold.advance.cancel');
        Route::get('/customer/gold/advances/{id}/order-no/suggestions', [CustomerGoldAdvanceController::class, 'orderNoSuggestions']);


        Route::get('/get-subcategories', [ProductMergeController::class, 'getSubcategories'])->name('get.subcategories');
                // refund routes
        // Add these routes to your web.php file
        Route::get('/customer/{customerId}/advance-balances', [CustomerManagementController::class, 'getCustomerAdvanceBalances'])
            ->name('customer.advance.balances');

        Route::post('/customer/{customerId}/advance-refund', [CustomerManagementController::class, 'processRefund'])
            ->name('customer.advance.refund');

        // Customer Cash gold advance routes
        Route::get('/customers/cash-gold/{customer}/advances', [CustomerGoldCashAdvanceController::class, 'index'])->name('customer.cash-gold.advances.index');
        Route::post('/customer/cash-gold/{customer}/advance', [CustomerGoldCashAdvanceController::class, 'store'])->name('customer.cash-gold.advance.store');
        Route::post('/pos-order/cash-gold/{order}/use-advance', [CustomerGoldCashAdvanceController::class, 'useAdvance'])->name('customer.cash-gold.advance.use');
        Route::delete('/advance/cash-gold/{id}/cancel', [CustomerGoldCashAdvanceController::class, 'cancelAdvance'])->name('customer.cash-gold.advance.cancel');
        Route::get('/customer/cash-gold/advances/{id}/order-no/suggestions', [CustomerGoldCashAdvanceController::class, 'orderNoSuggestions']);

        Route::get('/customer/cash-gold/{customerId}/advance-balance', [CustomerGoldCashAdvanceController::class, 'getCombinedBalance']);

        // Get customer advance data (general + order numbers)
        Route::get('/pos/advance/{customerId}', [POSOrderAdvanceController::class, 'getAdvanceData']);

        // Get order-specific advance details
        Route::get('/pos/advance/{customerId}/order/{orderNo}', [POSOrderAdvanceController::class, 'getOrderAdvanceDetails']);

        // Keep existing route for backward compatibility
        Route::get('/pos/balance/{customerId}', [POSOrderAdvanceController::class, 'getBalance']);



        // Reservation routes
        Route::post('/customer/{customer}/reservations', [ReservationController::class, 'store'])->name('customer.reservation.store');
        Route::post('/reservations/{reservation}/payments', [ReservationController::class, 'addPayment'])->name('customer.advance.payment');
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservation.index');
        // Reservation payment routes
        Route::get('/reservations/{reservation}/payment-history', [ReservationController::class, 'getPaymentHistory'])->name('reservations.payments.history');
        Route::delete('/reservations/{reservation}/payments/{payment}', [ReservationController::class, 'deletePayment'])->name('reservations.payments.delete');

        // Convert reservation to POS order
        Route::post('/reservations/{reservation}/convert-to-pos', [ReservationController::class, 'convertToPOSOrder'])->name('reservations.convert-to-pos');
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
        Route::get('/customer/{customer}/reservations', [ReservationController::class, 'getCustomerReservations'])->name('customer.reservations');
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');



        // Route::resource('products', ProductController::class);
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create-or-edit/{product?}', [ProductController::class, 'createOrEdit'])->name('products.createOrEdit');
        Route::post('products/store-or-update/{product?}', [ProductController::class, 'storeOrUpdate'])->name('products.storeOrUpdate');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('products/barcode/{productNo}', [ProductController::class, 'generateBarcode'])->name('products.barcode');
        Route::get('/gold-rates/filter/{type}', [ProductController::class, 'filterGoldRates']);
        
        // super admin route
        Route::post('products/admin/store-or-update/{product?}', [ProductController::class, 'AdminStoreOrUpdate'])->name('admin.products.storeOrUpdate');

        // Product approval
        Route::get('/products/approval', [ProductController::class, 'product_aprroval_index'])->name('products.approval.index');
        Route::get('/products/approval/{id}', [ProductController::class, 'productApproval'])->name('product.approval');
        Route::get('/products/reject/{id}', [ProductController::class, 'productReject'])->name('product.reject');
        Route::get('/products/disable', [ProductController::class, 'disabledProductShow'])->name('products.disable.show');
        Route::get('/products/disable/{id}', [ProductController::class, 'productDisable'])->name('product.disable');
        Route::get('/products/active/{id}', [ProductController::class, 'productActive'])->name('product.active');
        Route::get('/products/weight/adjust', [ProductController::class, 'weightAdjustShow'])->name('products.weight.adjust');
        Route::post('/products/weight-adjust/{product}', [ProductController::class, 'adjustWeight'])->name('products.adjustWeight');
        Route::get('/products/weight-adjust/{id}/details', [ProductController::class, 'weightAdjustDetailShow'])->name('products.weight.adjust.details');

        
        
        // barcode printer
        Route::get('products/thermal-barcode/{productNo}', [ProductController::class, 'generateThermalBarcode'])->name('products.thermal.barcode');
        Route::get('categories', [ProductCategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create-or-edit/{category?}', [ProductCategoryController::class, 'createOrEdit'])->name('categories.createOrEdit');
        Route::post('categories/store-or-update/{category?}', [ProductCategoryController::class, 'storeOrUpdate'])->name('categories.storeOrUpdate');
        Route::delete('categories/{category}', [ProductCategoryController::class, 'destroy'])->name('categories.destroy');


        Route::get('sub_categories', [ProductCategoryController::class, 'subCategoryIndex'])->name('subcategories.index');
        Route::get('sub_categories/create-or-edit/{subcategory?}', [ProductCategoryController::class, 'createOrEditSubCategory'])->name('subcategories.createOrEdit');
        Route::post('sub_categories/store-or-update/{subcategory?}', [ProductCategoryController::class, 'storeOrUpdateSubCategory'])->name('subcategories.storeOrUpdate');
        Route::delete('sub_categories/{subcategory}', [ProductCategoryController::class, 'destroySubCategory'])->name('subcategories.destroy');

        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/create-or-edit/{supplier?}', [SupplierController::class, 'createOrEdit'])->name('suppliers.createOrEdit');
        Route::post('suppliers/store-or-update/{supplier?}', [SupplierController::class, 'storeOrUpdate'])->name('suppliers.storeOrUpdate');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    
        Route::get('gold_rates', [GoldRateController::class, 'index'])->name('gold_rates.index');
        Route::get('gold_rates/create-or-edit/{goldRate?}', [GoldRateController::class, 'createOrEdit'])->name('gold_rates.createOrEdit');
        Route::post('gold_rates/store-or-update/{goldRate?}', [GoldRateController::class, 'storeOrUpdate'])->name('gold_rates.storeOrUpdate');
        Route::delete('gold_rates/{goldRate}', [GoldRateController::class, 'destroy'])->name('gold_rates.destroy');
    

        Route::get('/gold_balance', [GoldBalanceController::class, 'index'])->name('gold_balance.index');
        Route::get('/gold_balance/create', [GoldBalanceController::class, 'create'])->name('gold_balance.create');
        Route::post('/gold_balance', [GoldBalanceController::class, 'store'])->name('gold_balance.store');
        Route::get('/gold_balance/{goldBalance}', [GoldBalanceController::class, 'show'])->name('gold_balance.show');
        Route::get('/gold_balance/{goldBalance}/edit', [GoldBalanceController::class, 'edit'])->name('gold_balance.edit');
        Route::put('/gold_balance/{goldBalance}', [GoldBalanceController::class, 'update'])->name('gold_balance.update');
        Route::delete('/gold_balance/{goldBalance}', [GoldBalanceController::class, 'destroy'])->name('gold_balance.destroy');


        Route::get('gold-balance/reports/daily', [GoldBalanceController::class, 'dailyReportForm'])->name('gold_balance_form.daily_report_form');

        Route::post('gold-balance/reports/daily', [GoldBalanceController::class, 'dailyReport'])->name('gold_balance.daily_report');

        Route::resource('customers', CustomerController::class);
        Route::resource('pos_orders', POSOrderController::class);
        // Route::resource('product_categories', ProductCategoryController::class);
        // Route::resource('gold_rates', GoldRateController::class);
        // Route::resource('orders', OrderController::class);
        
        // Payment routes
        Route::post('/customers/{customer}/payments', [PaymentController::class, 'store'])->name('payments.store_from_customer');
        Route::post('/pos_orders/{posOrder}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::delete('/customers/{customer}/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy_from_customer');




        // Product merge approval routes
        Route::get('/products/merge/approval', [ProductMergeController::class, 'approvalIndex'])->name('products.merge.approval.index');
        Route::get('/products/merge/approval/{id}', [ProductMergeController::class, 'approvalShow'])->name('products.merge.approval.show');
        Route::post('/products/merge/approval/{id}/approve', [ProductMergeController::class, 'approve'])->name('products.merge.approval.approve');
        Route::post('/products/merge/approval/{id}/reject', [ProductMergeController::class, 'reject'])->name('products.merge.approval.reject');



        Route::get('/customer-management', [CustomerManagementController::class, 'index'])->name('customer.management.index');
        Route::get('/customer-management/customers', [CustomerManagementController::class, 'searchCustomers'])->name('customer.management.customers');
        // Route::get('/customer-management/customers/{customer}/details', [CustomerManagementController::class, 'getCustomerDetails'])->name('customer.management.details');
        // Route::post('/customer-management/customers/{customer}/advance', [CustomerManagementController::class, 'addAdvance'])->name('customer.management.add.advance');
        Route::post('/customer-management/customers/{customer}/reservation', [CustomerManagementController::class, 'addReservation'])->name('customer.management.add.reservation');
        Route::post('/customer-management/reservations/{reservation}/payment', [CustomerManagementController::class, 'addReservationPayment'])->name('customer.management.add.payment');
        // Route::get('/customer-management/products', [CustomerManagementController::class, 'searchProducts'])->name('customer.management.products');
        Route::get('/customer-management/customers/{id}/details', [CustomerManagementController::class, 'getCustomerDetails']);
        Route::post('/customer-management/{customerId}/advance', [CustomerManagementController::class, 'addAdvance']);
        Route::get('/customer-management/products', [CustomerManagementController::class, 'getProducts']);


      });


    // staff middleware section
    Route::middleware(['role:admin|superadmin|staff'])->group(function () {
        
        // Core POS functionality
        Route::get('/dashboard', [POSOrderController::class, 'dashboard'])->name('dashboard');
        Route::get('/product-details/{id}', [POSOrderController::class, 'getProductDetails'])->name('product.details');
        Route::post('/verify-staff-barcode', [POSOrderController::class, 'verifyStaffBarcode'])->name('verify.staff.barcode');
        
        Route::resource('customers', CustomerController::class);
        Route::resource('pos_orders', POSOrderController::class);

        // Advance data retrieval for POS
        Route::get('/pos/advance/{customerId}', [POSOrderAdvanceController::class, 'getAdvanceData']);
        Route::get('/pos/advance/{customerId}/order/{orderNo}', [POSOrderAdvanceController::class, 'getOrderAdvanceDetails']);
        Route::get('/pos/balance/{customerId}', [POSOrderAdvanceController::class, 'getBalance']);
        
        // Customer reservations (read-only)
        Route::get('/customer/{customer}/reservations', [ReservationController::class, 'getCustomerReservations'])->name('customer.reservations');
        
        // Print receipts and invoices
        Route::get('/print-invoice/{id}/{routeName}', [POSOrderController::class, 'printInvoice'])->name('print.invoice');
        Route::get('/print-receipt/{advance}/{detail}/{routeName}', [CustomerAdvanceController::class, 'printReceipt'])->name('print.receipt');
        Route::get('/print-receipt-gold/{advance}/{detail}/{routeName}', [CustomerGoldAdvanceController::class, 'printReceipt'])->name('print.receipt.gold');
        Route::get('/print-receipt-gold-cash/{cashDetailId}/{goldDetailIds}/{routeName}', [CustomerGoldCashAdvanceController::class, 'printReceipt'])->name('print.receipt.gold.cash');
        Route::get('/print-reservation/{reservationId}/{routeName}', [ReservationController::class, 'printReservationReceipt'])
        ->name('print.reservation.receipt');
        Route::get('purchase-old-gold/{purchaseOldGold}/invoice', [PurchaseOldGoldController::class, 'printInvoice'])
        ->name('purchase-old-gold.printInvoice');
        
        
        // Customer search and basic info (needed for POS dropdowns)
        Route::get('/customer-management/customers', [CustomerManagementController::class, 'searchCustomers'])->name('customer.management.customers');
        Route::get('/customer-management/customers/{id}/details', [CustomerManagementController::class, 'getCustomerDetails']);
        Route::get('/customer-management/products', [CustomerManagementController::class, 'getProducts']);
        
        // Gold rates access (read-only)
        Route::get('gold_rates', [GoldRateController::class, 'index'])->name('gold_rates.index');
        
        // Purchase old gold routes (if staff can access)
        Route::get('/purchase-old-gold/gold-rates', [PurchaseOldGoldController::class, 'getGoldRates'])->name('purchase-old-gold.get-gold-rates');


        // Route::resource('products', ProductController::class);
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/create-or-edit/{product?}', [ProductController::class, 'createOrEdit'])->name('products.createOrEdit');
        Route::post('products/store-or-update/{product?}', [ProductController::class, 'storeOrUpdate'])->name('products.storeOrUpdate');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('products/barcode/{productNo}', [ProductController::class, 'generateBarcode'])->name('products.barcode');
        Route::get('/gold-rates/filter/{type}', [ProductController::class, 'filterGoldRates']);
            
        
            // Product approval
        Route::get('/products/approval', [ProductController::class, 'product_aprroval_index'])->name('products.approval.index');
        Route::get('/products/approval/{id}', [ProductController::class, 'productApproval'])->name('product.approval');
        Route::get('/products/reject/{id}', [ProductController::class, 'productReject'])->name('product.reject');
            
            // barcode printer
        Route::get('products/thermal-barcode/{productNo}', [ProductController::class, 'generateThermalBarcode'])->name('products.thermal.barcode');

        Route::get('/products/label/{product_no}', [ProductController::class, 'printLabelView'])->name('products.label');
        Route::get('/products/weight/adjust', [ProductController::class, 'weightAdjustShow'])->name('products.weight.adjust');
        Route::post('/products/weight-adjust/{product}', [ProductController::class, 'adjustWeight'])->name('products.adjustWeight');
        Route::get('/products/weight-adjust/{id}/details', [ProductController::class, 'weightAdjustDetailShow'])->name('products.weight.adjust.details');


        // Add these routes to your routes/web.php file
        Route::prefix('purchase-old-gold')->name('purchase-old-gold.')->group(function () {
        Route::get('/', [PurchaseOldGoldController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseOldGoldController::class, 'create'])->name('create');
        Route::post('/', [PurchaseOldGoldController::class, 'store'])->name('store');
        Route::get('/{purchaseOldGold}', [PurchaseOldGoldController::class, 'show'])->name('show');
        Route::get('/{purchaseOldGold}/edit', [PurchaseOldGoldController::class, 'edit'])->name('edit');
        Route::put('/{purchaseOldGold}', [PurchaseOldGoldController::class, 'update'])->name('update');
        Route::delete('/{purchaseOldGold}', [PurchaseOldGoldController::class, 'destroy'])->name('destroy');
        
        // AJAX routes for customer search and management
        Route::get('/search/customers', [PurchaseOldGoldController::class, 'searchCustomers'])->name('search-customers');
        Route::get('/customers/all', [PurchaseOldGoldController::class, 'getAllCustomers'])->name('get-all-customers');
        Route::get('/customers/{customer}', [PurchaseOldGoldController::class, 'getCustomer'])->name('get-customer');
        Route::put('/customers/{customer}/update', [PurchaseOldGoldController::class, 'updateCustomerNIC'])->name('update-customer-nic');
        Route::get('/gold-rates', [PurchaseOldGoldController::class, 'getGoldRates'])->name('get-gold-rates');
        });

        // Customer advance routes
        Route::get('/customers/{customer}/advances', [CustomerAdvanceController::class, 'index'])->name('customer.advances.index');
        Route::get('/customer/{customer}/advance-balance', [CustomerAdvanceController::class, 'getBalance'])->name('customer.advance.balance');
        Route::post('/customer/{customer}/advance', [CustomerAdvanceController::class, 'store'])->name('customer.advance.store');
        Route::post('/pos-order/{order}/use-advance', [CustomerAdvanceController::class, 'useAdvance'])->name('customer.advance.use');
        Route::delete('/advance/{id}/cancel', [CustomerAdvanceController::class, 'cancelAdvance'])->name('customer.advance.cancel');
        Route::get('/customer/advances/{id}/order-no/suggestions', [CustomerAdvanceController::class, 'orderNoSuggestions']);


        // Customer gold advance routes
        Route::get('/customers/gold/{customer}/advances', [CustomerGoldAdvanceController::class, 'index'])->name('customer.gold.advances.index');
        Route::get('/customer/gold/{customer}/advance-balance', [CustomerGoldAdvanceController::class, 'getBalance'])->name('customer.gold.advance.balance');
        Route::post('/customer/gold/{customer}/advance', [CustomerGoldAdvanceController::class, 'store'])->name('customer.gold.advance.store');
        Route::post('/pos-order/gold/{order}/use-advance', [CustomerGoldAdvanceController::class, 'useAdvance'])->name('customer.gold.advance.use');
        Route::delete('/advance/gold/{id}/cancel', [CustomerGoldAdvanceController::class, 'cancelAdvance'])->name('customer.gold.advance.cancel');
        Route::get('/customer/gold/advances/{id}/order-no/suggestions', [CustomerGoldAdvanceController::class, 'orderNoSuggestions']);


        Route::get('/get-subcategories', [ProductMergeController::class, 'getSubcategories'])->name('get.subcategories');
                // refund routes
        // Add these routes to your web.php file
        Route::get('/customer/{customerId}/advance-balances', [CustomerManagementController::class, 'getCustomerAdvanceBalances'])
            ->name('customer.advance.balances');

        Route::post('/customer/{customerId}/advance-refund', [CustomerManagementController::class, 'processRefund'])
            ->name('customer.advance.refund');

        // Customer Cash gold advance routes
        Route::get('/customers/cash-gold/{customer}/advances', [CustomerGoldCashAdvanceController::class, 'index'])->name('customer.cash-gold.advances.index');
        Route::post('/customer/cash-gold/{customer}/advance', [CustomerGoldCashAdvanceController::class, 'store'])->name('customer.cash-gold.advance.store');
        Route::post('/pos-order/cash-gold/{order}/use-advance', [CustomerGoldCashAdvanceController::class, 'useAdvance'])->name('customer.cash-gold.advance.use');
        Route::delete('/advance/cash-gold/{id}/cancel', [CustomerGoldCashAdvanceController::class, 'cancelAdvance'])->name('customer.cash-gold.advance.cancel');
        Route::get('/customer/cash-gold/advances/{id}/order-no/suggestions', [CustomerGoldCashAdvanceController::class, 'orderNoSuggestions']);

        Route::get('/customer/cash-gold/{customerId}/advance-balance', [CustomerGoldCashAdvanceController::class, 'getCombinedBalance']);

    });
});

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Product Merge Routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/merge', [ProductMergeController::class, 'index'])->name('merge.index');
        Route::post('/merge', [ProductMergeController::class, 'merge'])->name('merge.store');
        Route::post('/super/merge', [ProductMergeController::class, 'superMerge'])->name('super.merge.store');
        Route::get('/merge/history', [ProductMergeController::class, 'history'])->name('merge.history');
    });
});

Route::fallback(function () {
    return redirect()->back()->with('error', 'The requested page could not be found.');
});

require __DIR__.'/auth.php';

