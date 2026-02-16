<?php


namespace App\Http\Controllers;

use App\Models\POSOrder;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\CustomerAdvance;
use App\Models\CustomerAdvanceUse;
use App\Models\CustomerExchangeGold;
use App\Models\CustomerGoldAdvance;
use App\Models\CustomerGoldAdvanceUse;
use App\Models\CustomerGoldExchange;
use App\Models\GoldBalance;
use App\Models\GoldRate;
use App\Models\Payment;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSOrderController extends Controller
{
    public function index(Request $request)
    {
        $invoice = $request->input('invoice');
        $from = $request->input('from');
        $to = $request->input('to');

        $posOrders = POSOrder::with('customer')
            ->when($invoice, function ($query, $invoice) {
                $query->where('invoice_no', 'LIKE', "%{$invoice}%");
            })
            ->when($from, function ($query, $from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($query, $to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pos_orders.index', compact('posOrders', 'invoice', 'from', 'to'));
    }



    public function dashboard() {
        $customers = Customer::all();
        $goldRates = GoldRate::all();
        $products = Product::where('status', ['active', 'reserved'])->get();
        $taxRate = TaxRate::where('name', 'Standard Rate')->first();

        return view('dashboard', compact('customers', 'goldRates', 'taxRate', 'products'));
    }

    public function dashboard_v($id = null) {
        $customers = Customer::all();
        $goldRates = GoldRate::all();
        $products = Product::where('status', ['active', 'reserved'])->get();
        $taxRate = TaxRate::where('name', 'Standard Rate')->first();
        $draftOrder = null;

        if ($id) {
            $draftOrder = POSOrder::with('orderDetails')->find($id);
        }

        return view('dashboard_v', compact('customers', 'goldRates', 'taxRate', 'draftOrder', 'products'));
    }


    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('pos_orders.create', compact('customers', 'products'));
    }

   public function applyAdvance(Request $request, $orderId)
{
    $order = POSOrder::findOrFail($orderId);
    $customer = $order->customer;
    $requestedAmount = $request->amount;
    $orderNo = $request->order_no ?? null;

    DB::beginTransaction();
    try {
        $remaining = $requestedAmount;
        $amountUsed = 0;

        if ($orderNo) {
            // Only use the advance that matches the given order_no
            $specificAdvance = CustomerAdvance::where('customer_id', $customer->id)
                ->where('order_no', $orderNo)
                ->where('advance_balance', '>', 0)
                ->first();

            if (!$specificAdvance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available advance found for the provided order number.'
                ], 422);
            }

            $useAmount = min($specificAdvance->advance_balance, $remaining);

            CustomerAdvanceUse::create([
                'customer_advance_id' => $specificAdvance->id,
                'amount' => $useAmount,
                'pos_order_id' => $order->id
            ]);

            $specificAdvance->decrement('advance_balance', $useAmount);
            $amountUsed = $useAmount;
        } else {
            // Only use advance with order_no == NULL
            $genericAdvance = CustomerAdvance::where('customer_id', $customer->id)
                ->whereNull('order_no')
                ->where('advance_balance', '>', 0)
                ->orderBy('created_at')
                ->first();

            if (!$genericAdvance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available general advance found for this customer.'
                ], 422);
            }

            $useAmount = min($genericAdvance->advance_balance, $remaining);

            CustomerAdvanceUse::create([
                'customer_advance_id' => $genericAdvance->id,
                'amount' => $useAmount,
                'pos_order_id' => $order->id
            ]);

            $genericAdvance->decrement('advance_balance', $useAmount);
            $amountUsed = $useAmount;
        }

        // Update POSOrder
        $order->update([
            'advance' => $order->advance + $amountUsed,
            'balance' => $order->balance - $amountUsed
        ]);

        DB::commit();

        $newBalance = CustomerAdvance::where('customer_id', $customer->id)->sum('advance_balance');

        return response()->json([
            'success' => true,
            'new_balance' => $newBalance,
            'amount_used' => $amountUsed
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error applying advance: ' . $e->getMessage()
        ]);
    }
}


  public function store(Request $request)
{
    dd($request->all());
    $request->validate([
        'customer_id' => 'required',
        'total' => 'required|numeric',
        'products' => 'required|array', 
        'products.*.qty' => 'required|numeric|min:1',
        'products.*.weight' => 'required|numeric|min:0',
        'products.*.making_charges' => 'required|numeric|min:0',
        'products.*.wastage_weight' => 'required|numeric|min:0',
        'products.*.stone_weight' => 'required|numeric|min:0',
        'products.*.gold_rate' => 'required|numeric|min:0',
        'products.*.sub_total' => 'required|numeric|min:0',
        'products.*.discount' => 'nullable|numeric|min:0',
        'advance_used' => 'nullable|numeric|min:0',
        'used_cash_advance' => 'nullable|numeric|min:0',
        'used_gold_grams' => 'nullable|numeric|min:0',
        'used_gold_amount' => 'nullable|numeric|min:0',
        'used_gold_rate' => 'nullable|numeric|min:0',
        'used_gold_rate_id' => 'nullable|exists:gold_rates,id',
        'cash_advance_order_no' => 'nullable|string',
        'gold_advance_order_no' => 'nullable|string',
        'advance_usage_type' => 'nullable|in:both,cash_only,gold_only',
        'product_type' => 'nullable',
        'type' => 'nullable',
        'payment_method' => 'nullable|string',
        'is_exchange' => 'nullable',
        'exchange_gold_rates' => 'nullable|array',
        'exchange_gold_rates.*' => 'nullable|exists:gold_rates,id',
        'exchange_gold_weights' => 'nullable|array',
        'exchange_gold_weights.*' => 'nullable|numeric|min:0',
        'exchange_gold_amounts' => 'nullable|array',
        'exchange_gold_amounts.*' => 'nullable|numeric|min:0',
        'processed_by' => 'nullable',
        'card_payment' => 'nullable|numeric|min:0',
        'bank_transfer_payment' => 'nullable|numeric|min:0',
        'chq_payment' => 'nullable|numeric|min:0',
        'used_gold_auto_amount' => 'nullable|numeric|min:0',
        'used_gold_manual_amount' => 'nullable|numeric|min:0',
        'used_gold_product_grams' => 'nullable|numeric|min:0',
        'used_gold_excess_grams' => 'nullable|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        // $total = 0;
        $cashPaid = $request->advance;
        $cardPayment = $request->card_payment;
        $bankTransfer = $request->bank_transfer_payment;
        $chqPayment = $request->chq_payment;
        $advanceUsed = (float) $request->advance_used;
        $usedCashAdvance = (float) $request->used_cash_advance;
        $usedGoldGrams = (float) $request->used_gold_grams;
        $usedGoldAmount = (float) $request->used_gold_amount;
        $usedGoldRate = (float) $request->used_gold_rate;
        $usedGoldRateId = $request->used_gold_rate_id;
        $cashAdvanceOrderNo = $request->cash_advance_order_no;
        $goldAdvanceOrderNo = $request->gold_advance_order_no;
        $advanceUsageType = $request->advance_usage_type;
        $processed_by = $request->processed_by ?: auth()->id();


        // if ($request->has('advance_used') && $advanceUsed > 0) {
        //     $total = $cashPaid + $advanceUsed;
        // } else {
        //     $total = $cashPaid;
        // }

        // Handle customer creation if manual customer
        if ($request->has('add_manual_customer') && $request->manual_customer) {
            $customer = Customer::create([
                'name' => $request->manual_customer,
                'address' => '', 
                'city' => '',
                'tel' => '',
            ]);
            $customerId = $customer->id; 
        } else {
            $customerId = $request->customer_id; 
        }

        // $status = $request->total == $request->advance ? 'complete' : 'pending';
        $status = $request->balance <= 0 ? 'complete' : 'pending';
        if ($request->has('draft')) {
            $status = 'draft';
        }

        // Create the order
        $posOrder = POSOrder::create([
            'customer_id' => $customerId,
            'total' => $request->total,
            'advance' => $cashPaid,
            'cash' => $cashPaid,
            'chq' => $chqPayment,
            'card' => $cardPayment,
            'bank_transfer' => $bankTransfer,
            'balance' => $request->balance,
            'inclusive_tax' => $request->tax_rate,
            'discount' => $request->total_discount,
            'status' => $status,
            'processed_by' => $processed_by,
        ]);
        
        // Handle products (same as before)
        foreach ($request->products as $product) {
            $product['qty'] = floatval($product['qty']);
            $product['weight'] = floatval($product['weight']);
            $product['discount'] = floatval($product['discount']);
            $product['making_charges'] = floatval($product['making_charges']);
            $product['wastage_weight'] = floatval($product['wastage_weight']);
            $product['stone_weight'] = floatval($product['stone_weight']);
            $product['gold_rate'] = floatval($product['gold_rate']);
            $product['sub_total'] = floatval($product['sub_total']);

            if ($product['product_id'] == 0) {
                $newProduct = Product::create([
                    'name' => $product['name'],
                    'qty' => $product['qty'],
                    'weight' => $product['weight'],
                    'making_charges' => $product['making_charges'],
                    'wastage_weight' => $product['wastage_weight'],
                    'stone_weight' => $product['stone_weight'],
                    'gold_rate_id' => $product['gold_rate_id'],
                    'amount' => $product['sub_total'],
                    'product_category_id' => '1',
                    'status' => 'active'
                ]);
                $productId = $newProduct->id;
            } else {
                $productId = $product['product_id'];
            }

            OrderDetail::create([
                'pos_order_id' => $posOrder->id,
                'product_id' => $productId,
                'qty' => $product['qty'],
                'weight' => $product['weight'],
                'discount' => $product['discount'],
                'amount' => $product['sub_total'],
                'making_charges' => $product['making_charges'],
                'wastage_weight' => $product['wastage_weight'],
                'stone_weight' => $product['stone_weight'],
                'gold_rate' => $product['gold_rate'],
            ]);

            // Handle inventory deduction
            if ($productId != 0) {
                $existingProduct = Product::find($productId);
                if ($existingProduct) {
                    if (isset($product['type']) && $product['type'] == 1) {
                        $existingProduct->decrement('weight', $product['weight']);
                    } else {
                        $existingProduct->decrement('qty', $product['qty']);
                        $existingProduct->update(['status' => 'sold']);
                    }
                }
            }
        }

        // Handle multiple exchange gold entries
        if ($request->is_exchange == 1) {
            $exchangeRates = $request->exchange_gold_rates ?? [];
            $exchangeWeights = $request->exchange_gold_weights ?? [];
            $exchangeAmounts = $request->exchange_gold_amounts ?? [];

            // Loop through each exchange entry
            for ($i = 0; $i < count($exchangeRates); $i++) {
                $rateId = $exchangeRates[$i] ?? null;
                $weight = (float) ($exchangeWeights[$i] ?? 0);
                $amount = (float) ($exchangeAmounts[$i] ?? 0);

                // Only process entries with valid data
                if ($rateId && $weight > 0 && $amount > 0) {
                    // Create individual exchange record
                    $exchange = CustomerGoldExchange::create([
                        'customer_id'           => $customerId,
                        'gold_rate_id'          => $rateId,
                        'gold_weight'           => $weight,
                        'gold_purchased_amount' => $amount,
                        'pos_order_id'          => $posOrder->id,
                    ]);

                    // Add each exchange's weight to gold balance individually
                    $this->storeGoldBalance(
                        'Old to new gold exchange ('. ($exchange->goldRate->name) .') (Entry #' . ($i + 1) . ') - Order #' . $posOrder->id,
                        'gold_in',
                        $weight
                    );
                }
            }
        }



        // Handle advance usage based on type
        if ($advanceUsed > 0 && !$request->has('add_manual_customer')) {
            $customer = Customer::find($customerId);
            
            // Handle different advance usage types
            switch ($advanceUsageType) {
                case 'both':
                    $this->handleBothAdvanceUsage($customer, $posOrder, $usedCashAdvance, $usedGoldGrams, $usedGoldAmount, $usedGoldRate, $usedGoldRateId, $cashAdvanceOrderNo, $goldAdvanceOrderNo);
                    break;
                    
                case 'cash_only':
                    $this->handleCashOnlyAdvanceUsage($customer, $posOrder, $usedCashAdvance, $cashAdvanceOrderNo);
                    break;
                    
                case 'gold_only':
                    $this->handleGoldOnlyAdvanceUsage($customer, $posOrder, $usedGoldGrams, $usedGoldAmount, $usedGoldRate, $usedGoldRateId, $goldAdvanceOrderNo);
                    break;
            }
        }

        DB::commit();

        // Create payment record 
        if ($request->advance > 0) {
            $payment = Payment::create([
                'pos_order_id' => $posOrder->id,
                'amount' => $request->advance,
                'payment_method' => 'cash',
                'reference_no' => null,
                'notes' => null
            ]);
        }

        if ($request->card_payment > 0) {
            $payment = Payment::create([
                'pos_order_id' => $posOrder->id,
                'amount' => $request->card_payment,
                'payment_method' => 'card',
                'reference_no' => null,
                'notes' => null
            ]);
        }

        if ($request->bank_transfer_payment > 0) {
            $payment = Payment::create([
                'pos_order_id' => $posOrder->id,
                'amount' => $request->bank_transfer_payment,
                'payment_method' => 'bank_transfer',
                'reference_no' => null,
                'notes' => null
            ]);
        }

        if ($request->chq_payment > 0) {
            $payment = Payment::create([
                'pos_order_id' => $posOrder->id,
                'amount' => $request->chq_payment,
                'payment_method' => 'chq',
                'reference_no' => null,
                'notes' => null
            ]);
        }



        $routeName = 'dashboard';
        return redirect()->route('print.invoice', [
            'id' => $posOrder->id,
            'routeName' => $routeName,
        ]);

        return redirect()->route('dashboard')->with('success', 'POS Order created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error creating order: ' . $e->getMessage());
    }
}


    private function storeGoldBalance(string $description, string $type, float $amount): void
    {
        $last = GoldBalance::orderBy('created_at', 'desc')->first();
        $prevBalance = $last?->gold_balance ?? 0;

        $newBalance = $type === 'gold_in'
            ? $prevBalance + $amount
            : $prevBalance - $amount;

        if ($newBalance < 0) {
            throw new \Exception("Insufficient gold balance. Available: " . number_format($prevBalance, 3));
        }

        GoldBalance::create([
            'description' => $description,
            'gold_in' => $type === 'gold_in' ? $amount : 0,
            'gold_out' => $type === 'gold_out' ? $amount : 0,
            'gold_balance' => $newBalance,
        ]);
    }

private function handleBothAdvanceUsage($customer, $posOrder, $usedCashAdvance, $usedGoldGrams, $usedGoldAmount, $usedGoldRate, $usedGoldRateId, $cashAdvanceOrderNo, $goldAdvanceOrderNo)
{
    // Handle cash advance usage
    if ($usedCashAdvance > 0) {
        $this->processCashAdvanceUsage($customer, $posOrder, $usedCashAdvance, $cashAdvanceOrderNo);
    }
    
    // Handle gold advance usage
    if ($usedGoldGrams > 0) {
        $this->processGoldAdvanceUsage($customer, $posOrder, $usedGoldGrams, $usedGoldAmount, $usedGoldRate, $usedGoldRateId, $goldAdvanceOrderNo);
    }
}

private function handleCashOnlyAdvanceUsage($customer, $posOrder, $usedCashAdvance, $cashAdvanceOrderNo)
{
    if ($usedCashAdvance > 0) {
        $this->processCashAdvanceUsage($customer, $posOrder, $usedCashAdvance, $cashAdvanceOrderNo);
    }
}

private function handleGoldOnlyAdvanceUsage($customer, $posOrder, $usedGoldGrams, $usedGoldAmount, $usedGoldRate, $usedGoldRateId, $goldAdvanceOrderNo)
{
    if ($usedGoldGrams > 0) {
        $this->processGoldAdvanceUsage($customer, $posOrder, $usedGoldGrams, $usedGoldAmount, $usedGoldRate, $usedGoldRateId, $goldAdvanceOrderNo);
    }
}


private function processCashAdvanceUsage($customer, $posOrder, $usedCashAdvance, $cashAdvanceOrderNo)
{
    $remaining = $usedCashAdvance;
    
    if ($cashAdvanceOrderNo) {
        // Use cash advance with specific order number
        $specificAdvance = CustomerAdvance::where('customer_id', $customer->id)
            ->where('order_no', $cashAdvanceOrderNo)
            ->where('advance_balance', '>', 0)
            ->first();
            
        if ($specificAdvance) {
            $useAmount = min($specificAdvance->advance_balance, $remaining);
            
            CustomerAdvanceUse::create([
                'customer_advance_id' => $specificAdvance->id,
                'amount' => $useAmount,
                'pos_order_id' => $posOrder->id
            ]);
            
            $specificAdvance->decrement('advance_balance', $useAmount);
            $remaining -= $useAmount;
        }
    } else {
        // Use cash advance without order number (general advance)
        $generalAdvances = CustomerAdvance::where('customer_id', $customer->id)
            ->whereNull('order_no')
            ->where('advance_balance', '>', 0)
            ->orderBy('created_at')
            ->get();
            
        foreach ($generalAdvances as $advance) {
            if ($remaining <= 0) break;
            
            $useAmount = min($advance->advance_balance, $remaining);
            
            CustomerAdvanceUse::create([
                'customer_advance_id' => $advance->id,
                'amount' => $useAmount,
                'pos_order_id' => $posOrder->id
            ]);
            
            $advance->decrement('advance_balance', $useAmount);
            $remaining -= $useAmount;
        }
    }
    
    if ($remaining > 0) {
        throw new \Exception('Insufficient cash advance balance. Remaining: Rs ' . $remaining);
    }
}

private function processGoldAdvanceUsage($customer, $posOrder, $usedGoldGrams, $usedGoldAmount, $usedGoldRate, $usedGoldRateId, $goldAdvanceOrderNo)
{
    $remainingAmount = $usedGoldAmount; // Use amount instead of grams for checking
    
    if ($goldAdvanceOrderNo) {
        // Use gold advance with specific order number
        $specificGoldAdvance = CustomerGoldAdvance::where('customer_id', $customer->id)
            ->where('order_no', $goldAdvanceOrderNo)
            ->where('gold_balance', '>', 0)
            ->first();
            
        if ($specificGoldAdvance) {
            // Calculate how much we can use from this advance
            $availableGrams = $specificGoldAdvance->gold_balance;
            $availableAmount = $availableGrams * $usedGoldRate;
            $useAmount = min($availableAmount, $remainingAmount);
            $useGrams = $useAmount / $usedGoldRate;
            
            // Create gold advance usage record
            CustomerGoldAdvanceUse::create([
                'customer_gold_advance_id' => $specificGoldAdvance->id,
                'gold_amount' => $usedGoldGrams,
                'gold_rate' => $usedGoldRate,
                'amount' => $usedGoldAmount,
                'pos_order_id' => $posOrder->id
            ]);
            
            $specificGoldAdvance->decrement('gold_balance', $useGrams);
            $remainingAmount -= $useAmount;
        }
    } else {
        // Use gold advance without order number (general advance)
        $generalGoldAdvances = CustomerGoldAdvance::where('customer_id', $customer->id)
            ->whereNull('order_no')
            ->where('gold_balance', '>', 0)
            ->orderBy('created_at')
            ->get();
            
        foreach ($generalGoldAdvances as $goldAdvance) {
            if ($remainingAmount <= 0) break;
            
            // Calculate how much we can use from this advance
            $availableGrams = $goldAdvance->gold_balance;
            $availableAmount = $availableGrams * $usedGoldRate;
            $useAmount = min($availableAmount, $remainingAmount);
            $useGrams = $useAmount / $usedGoldRate;
            
            // Create gold advance usage record
            CustomerGoldAdvanceUse::create([
                'customer_gold_advance_id' => $goldAdvance->id,
                'gold_amount' => $usedGoldGrams,
                'gold_rate' => $usedGoldRate,
                'amount' => $usedGoldAmount,
                'pos_order_id' => $posOrder->id
            ]);
            
            $goldAdvance->decrement('gold_balance', $useGrams);
            $remainingAmount -= $useAmount;
        }
    }
    
    if ($remainingAmount > 0) {
        throw new \Exception('Insufficient gold advance balance. Remaining amount: Rs ' . $remainingAmount);
    }
}


    public function printInvoice($id, $routeName)
    {
        $posOrder = POSOrder::with([
            'orderDetails.product.goldRate',
            'customer',
            'payments',
            'customerGoldExchanges',
            'customerGoldExchanges.goldRate',
            'advanceUses.customerAdvance',
            'goldAdvanceUses.customerGoldAdvance.goldRate',
        ])->findOrFail($id);
    
        $route = $routeName;
        $formattedDate = $posOrder->created_at->format('d/m/Y');
    
        // Get cash advance usage details
        $cashAdvanceUsed = $posOrder->advanceUses->sum('amount');
        $cashAdvanceOrderNo = null;
        if ($posOrder->advanceUses->isNotEmpty()) {
            $cashAdvanceOrderNo = $posOrder->advanceUses->first()->customerAdvance->order_no;
        }
    
        // Get gold advance usage details
        $goldAdvanceUsed = $posOrder->goldAdvanceUses->sum('gold_amount');
        $goldAdvanceOrderNo = null;
        $goldAdvanceUsedGoldRateName = null;
    
        if ($posOrder->goldAdvanceUses->isNotEmpty()) {
            $goldAdvanceOrderNo = $posOrder->goldAdvanceUses->first()->customerGoldAdvance->order_no;
            $goldAdvanceUsedGoldRateName = $posOrder->goldAdvanceUses->first()
                ?->customerGoldAdvance?->goldRate?->name;
        }
    
        // Calculate totals for better layout (only for actual products, not including advances)
        $totalProductAmount = $posOrder->orderDetails->sum('amount');
        $totalNetWeight = $posOrder->orderDetails->sum('weight');
        $totalWastageWeight = $posOrder->orderDetails->sum('wastage_weight');
        $totalMakingCharges = $posOrder->orderDetails->sum('making_charges');
        $totalStoneWeight = $posOrder->orderDetails->sum('stone_weight');
        
        // Calculate net weight after gold advance (for display in sum row)
        $sumRowNetWeight = $totalNetWeight - $goldAdvanceUsed;
        
        // Calculate totals including gold advance for sum row
        $sumRowWastage = $totalWastageWeight; // Wastage doesn't include gold advance
        $sumRowMakingCharges = $totalMakingCharges; // Making charges don't include gold advance
        
        // Calculate net weight after gold advance
        $netWeightAfterGoldAdvance = $totalNetWeight - $goldAdvanceUsed;
        
        // Calculate gold advance amount (we are getting the gold rate from the gold ad uses tbl)
        $goldAdvanceAmount = 0;
        if ($goldAdvanceUsed > 0 && $posOrder->orderDetails->isNotEmpty()) {
            $goldRate = $posOrder->goldAdvanceUses->first()->gold_rate ?? 0;
            $goldAdvanceAmount = $goldAdvanceUsed * $goldRate;
        }
        
        // Calculate total exchanged gold (individual entries)
        $totalExchangeGoldWeight = 0;
        $totalExchangeGoldAmount = 0;

        foreach ($posOrder->customerGoldExchanges as $exchange) {
            $totalExchangeGoldWeight += $exchange->gold_weight;
            $rate = $exchange->goldRate?->rate ?? 0;
            $totalExchangeGoldAmount += $exchange->gold_weight * $rate;
        }

        // Net weight after both gold advance and exchange gold
        $netWeightAfterGoldAdvanceAndExchange = $totalNetWeight - $goldAdvanceUsed - $totalExchangeGoldWeight;

        // Calculate gold advance amount
        $goldAdvanceAmount = 0;
        if ($goldAdvanceUsed > 0 && $posOrder->goldAdvanceUses->isNotEmpty()) {
            // $goldRate = $posOrder->goldAdvanceUses->first()->gold_rate ?? 0;
            // $goldAdvanceAmount = $goldAdvanceUsed * $goldRate;
            $goldAdvanceAmount = $posOrder->goldAdvanceUses->first()->amount ?? 0;
        }

        // Amount after subtracting gold advances and exchanged gold
        $amountAfterGoldCalculation = $totalProductAmount - $goldAdvanceAmount - $totalExchangeGoldAmount;

        
        // Calculate cash payment made at purchase
        $cashPaymentAtPurchase = $posOrder->advance ??  0;
        $cardPaymentAtPurchase = $posOrder->card ??  0;
        $chqPaymentAtPurchase = $posOrder->chq ??  0;
        $bankPaymentAtPurchase = $posOrder->bank_transfer ??  0;
        $paymentMethod = ucfirst($posOrder->payments->first()?->payment_method ?? '');

        
        // Calculate final balance
        $finalBalance = $posOrder->balance;
        $discount = $posOrder->discount;
    
        return view('print.print', compact(
            'route',
            'posOrder',
            'formattedDate',
            'cashAdvanceUsed',
            'cashAdvanceOrderNo',
            'goldAdvanceUsed',
            'goldAdvanceOrderNo',
            'goldAdvanceUsedGoldRateName',
            'totalProductAmount',
            'totalNetWeight',
            'totalWastageWeight',
            'totalMakingCharges',
            'sumRowNetWeight',
            'sumRowWastage',
            'sumRowMakingCharges',
            'netWeightAfterGoldAdvance',
            'goldAdvanceAmount',
            'amountAfterGoldCalculation',
            'cashPaymentAtPurchase',
            'finalBalance',
            'discount',
            'paymentMethod',
            'totalExchangeGoldWeight',
            'totalExchangeGoldAmount',
            'netWeightAfterGoldAdvanceAndExchange',
            'cardPaymentAtPurchase',
            'chqPaymentAtPurchase',
            'bankPaymentAtPurchase',
            'totalStoneWeight'
        ));
    }

    public function holdInvoice($id)
    {
        $posOrder = POSOrder::findOrFail($id);
        $posOrder->update(['status' => 'hold']);
        return redirect()->back()->with('success', 'Invoice has been put on hold.');
    }

    public function releaseInvoice($id)
    {
        $posOrder = POSOrder::findOrFail($id);
        $posOrder->update(['status' => 'pending']);
        return redirect()->back()->with('success', 'Invoice has been released from hold.');
    }

    public function edit(POSOrder $posOrder)
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('pos_orders.edit', compact('posOrder', 'customers', 'products'));
    }

    public function update(Request $request, POSOrder $posOrder)
    {
        $request->validate([
            'customer_id' => 'required',
            'invoice_no' => 'required|unique:pos_orders,invoice_no,' . $posOrder->id,
            'total' => 'required|numeric',
        ]);

        $posOrder->update($request->all());

        $posOrder->orderDetails()->delete();

        foreach ($request->products as $product) {
            OrderDetail::create([
                'pos_order_id' => $posOrder->id,
                'product_id' => $product['product_id'],
                'qty' => $product['qty'],
                'weight' => $product['weight'],
                'amount' => $product['amount'],
                'making_charges' => $product['making_charges'],
            ]);
        }

        return redirect()->route('pos_orders.index')->with('success', 'POS Order updated successfully.');
    }

    public function destroy(POSOrder $posOrder)
    {
        $posOrder->delete();
        return redirect()->route('pos_orders.index')->with('success', 'POS Order deleted successfully.');
    }

    public function getProductDetails($productNo)
    {
        $allowedStatuses = ['active', 'merge'];
        
        $product = Product::where('product_no', $productNo)
                        ->whereIn('status', $allowedStatuses)
                        ->where('is_approved', 1)
                        ->with('goldRate')
                        ->first();
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or is not active/reserved'
            ]);
        }

        $isOutdated = false;

        // Validate only if product gold rate type is 'gold' 

        // if ($product->goldRate && $product->goldRate->type === 'gold') {
        //     $isOutdated = $product->goldRate->isOutdated();
        // }

        return response()->json([
            'success' => true,
            'data' => $product,
            'gold_rate_outdated' => $isOutdated,
        ]);
    }


    public function posOrderDetails(Request $request)
    {
        $query = POSOrder::with('orderDetails.product')->orderBy('created_at', 'desc');

        // Check if start_date and end_date are provided in the request
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('start_date'));
            $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('end_date'));
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $posOrders = $query->get(); // Fetch filtered POS orders
        return view('pos_orders.pos_order_details', compact('posOrders'));
    }


    public function show($id)
    {
        $posOrder = POSOrder::with([
        'orderDetails.product.goldRate',
        'customer',
        'payments',
        'customerGoldExchanges',
        'customerGoldExchanges.goldRate',
        'advanceUses.customerAdvance',
        'goldAdvanceUses.customerGoldAdvance.goldRate',
    ])->findOrFail($id);
   
    $formattedDate = $posOrder->created_at->format('d/m/Y');
   
    // Get cash advance usage details
    $cashAdvanceUsed = $posOrder->advanceUses->sum('amount');
    $cashAdvanceOrderNo = null;
    if ($posOrder->advanceUses->isNotEmpty()) {
        $cashAdvanceOrderNo = $posOrder->advanceUses->first()->customerAdvance->order_no;
    }
   
    // Get gold advance usage details
    $goldAdvanceUsed = $posOrder->goldAdvanceUses->sum('gold_amount');
    $goldAdvanceOrderNo = null;
    $goldAdvanceUsedGoldRateName = null;
   
    if ($posOrder->goldAdvanceUses->isNotEmpty()) {
        $goldAdvanceOrderNo = $posOrder->goldAdvanceUses->first()->customerGoldAdvance->order_no;
        $goldAdvanceUsedGoldRateName = $posOrder->goldAdvanceUses->first()
            ?->customerGoldAdvance?->goldRate?->name;
    }
   
    // Calculate totals for better layout (only for actual products, not including advances)
    $totalProductAmount = $posOrder->orderDetails->sum('amount');
    $totalNetWeight = $posOrder->orderDetails->sum('weight');
    $totalWastageWeight = $posOrder->orderDetails->sum('wastage_weight');
    $totalMakingCharges = $posOrder->orderDetails->sum('making_charges');
    
    // Calculate net weight after gold advance (for display in sum row)
    $sumRowNetWeight = $totalNetWeight - $goldAdvanceUsed;
    
    // Calculate totals including gold advance for sum row
    $sumRowWastage = $totalWastageWeight; // Wastage doesn't include gold advance
    $sumRowMakingCharges = $totalMakingCharges; // Making charges don't include gold advance
    
    // Calculate net weight after gold advance
    $netWeightAfterGoldAdvance = $totalNetWeight - $goldAdvanceUsed;
    
    // Calculate gold advance amount (we are getting the gold rate from the gold ad uses tbl)
    $goldAdvanceAmount = 0;
    if ($goldAdvanceUsed > 0 && $posOrder->orderDetails->isNotEmpty()) {
        $goldRate = $posOrder->goldAdvanceUses->first()->gold_rate ?? 0;
        $goldAdvanceAmount = $goldAdvanceUsed * $goldRate;
    }
    
    // Calculate total exchanged gold (individual entries)
    $totalExchangeGoldWeight = 0;
    $totalExchangeGoldAmount = 0;

    foreach ($posOrder->customerGoldExchanges as $exchange) {
        $totalExchangeGoldWeight += $exchange->gold_weight;
        $rate = $exchange->goldRate?->rate ?? 0;
        $totalExchangeGoldAmount += $exchange->gold_weight * $rate;
    }

    // Net weight after both gold advance and exchange gold
    $netWeightAfterGoldAdvanceAndExchange = $totalNetWeight - $goldAdvanceUsed - $totalExchangeGoldWeight;

    // Calculate gold advance amount
    $goldAdvanceAmount = 0;
    if ($goldAdvanceUsed > 0 && $posOrder->goldAdvanceUses->isNotEmpty()) {
        $goldRate = $posOrder->goldAdvanceUses->first()->gold_rate ?? 0;
        $goldAdvanceAmount = $goldAdvanceUsed * $goldRate;
    }

    // Amount after subtracting gold advances and exchanged gold
    $amountAfterGoldCalculation = $totalProductAmount - $goldAdvanceAmount - $totalExchangeGoldAmount;

    
    // Calculate cash payment made at purchase
    $cashPaymentAtPurchase = $posOrder->payments->sum('amount');
    $paymentMethod = ucfirst($posOrder->payments->first()?->payment_method ?? '');

    
    // Calculate final balance
    $finalBalance = $posOrder->balance;
    $discount = $posOrder->discount;
   
    return view('pos_orders.show', compact(
        'posOrder',
        'formattedDate',
        'cashAdvanceUsed',
        'cashAdvanceOrderNo',
        'goldAdvanceUsed',
        'goldAdvanceOrderNo',
        'goldAdvanceUsedGoldRateName',
        'totalProductAmount',
        'totalNetWeight',
        'totalWastageWeight',
        'totalMakingCharges',
        'sumRowNetWeight',
        'sumRowWastage',
        'sumRowMakingCharges',
        'netWeightAfterGoldAdvance',
        'goldAdvanceAmount',
        'amountAfterGoldCalculation',
        'cashPaymentAtPurchase',
        'finalBalance',
        'discount',
        'paymentMethod',
        'totalExchangeGoldWeight',
        'totalExchangeGoldAmount',
        'netWeightAfterGoldAdvanceAndExchange',
    ));

    }

    // Barcode input for proccessed by 
    public function verifyStaffBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);
      
        $user = User::where('barcode', $request->barcode)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid staff barcode. Please try again.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'message' => 'Staff verified successfully'
        ]);
    }
    
}
