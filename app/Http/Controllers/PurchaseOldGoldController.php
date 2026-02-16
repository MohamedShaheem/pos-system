<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOldGold;
use App\Models\PurchaseOldGoldDetail;
use App\Models\Customer;
use App\Models\GoldBalance;
use App\Models\GoldRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOldGoldController extends Controller
{
    public function index()
    {
        $purchases = PurchaseOldGold::with(['customer', 'details'])
            ->orderBy('created_at', 'desc')
            ->paginate(4);

        return view('purchase-old-gold.index', compact('purchases'));
    }

    public function create()
    {
        // Using compact method to pass data to view
        $customers = Customer::orderBy('name')->get(['id', 'name', 'tel', 'nic']);
        $goldRates = GoldRate::whereIn('type', ['gold', 'silver'])->get();


        
        return view('purchase-old-gold.create', compact('customers', 'goldRates'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'customer_nic' => 'nullable|string|max:20',
            'gold_items' => 'required|array|min:1',
            'gold_items.*.gold_rate_id' => 'required|exists:gold_rates,id',
            'gold_items.*.gold_gram' => 'required|numeric|min:0.01',
            'gold_items.*.gold_purchased_amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Update customer NIC if provided
            if ($request->customer_nic) {
                Customer::where('id', $request->customer_id)
                    ->update(['nic' => $request->customer_nic]);
            }

            // Create the main purchase record
            $purchaseOldGold = PurchaseOldGold::create([
                'customer_id' => $request->customer_id,
            ]);

            // Create the purchase details
            foreach ($request->gold_items as $item) {
                $detail = PurchaseOldGoldDetail::create([
                    'purchase_old_gold_id' => $purchaseOldGold->id,
                    'gold_rate_id' => $item['gold_rate_id'],
                    'gold_gram' => $item['gold_gram'],
                    'gold_purchased_amount' => $item['gold_purchased_amount'],
                ]);
                       
                $this->storeGoldBalance(
                    'Old gold purchase (' . $detail->goldRate->name . ') - Purchase #' . $purchaseOldGold->invoice_no,
                    'gold_in',
                    $item['gold_gram']
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase old gold record created successfully!',
                // 'redirect' => route('purchase-old-gold.show', $purchaseOldGold->id)
                'print_url' => route('purchase-old-gold.printInvoice', [
                'purchaseOldGold' => $purchaseOldGold->id,
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error creating purchase record: ' . $e->getMessage()
            ], 500);
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


    public function printInvoice(PurchaseOldGold $purchaseOldGold)
    {
        $purchaseOldGold->load(['customer', 'details.goldRate']);

        $totalGrams = $purchaseOldGold->details->sum('gold_gram');
        $totalAmount = $purchaseOldGold->details->sum('gold_purchased_amount');
        $formattedDate = $purchaseOldGold->created_at->format('d/m/Y');

        return view('print.print_purchase_invoice', compact(
            'purchaseOldGold',
            'totalGrams',
            'totalAmount',
            'formattedDate'
        ));
    }


    public function show(PurchaseOldGold $purchaseOldGold)
    {
        $purchaseOldGold->load(['customer', 'details.goldRate']);
        
        $totalAmount = $purchaseOldGold->details->sum('gold_purchased_amount');
        $totalGrams = $purchaseOldGold->details->sum('gold_gram');

        return view('purchase-old-gold.show', compact('purchaseOldGold', 'totalAmount', 'totalGrams'));
    }

    public function edit(PurchaseOldGold $purchaseOldGold)
    {
        $purchaseOldGold->load(['customer', 'details.goldRate']);
        $customers = Customer::orderBy('name')->get(['id', 'name', 'phone', 'nic']);
        $goldRates = GoldRate::orderBy('name')->get();
        
        return view('purchase-old-gold.edit', compact('purchaseOldGold', 'customers', 'goldRates'));
    }

    public function update(Request $request, PurchaseOldGold $purchaseOldGold)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'customer_nic' => 'nullable|string|max:20',
            'gold_items' => 'required|array|min:1',
            'gold_items.*.gold_rate_id' => 'required|exists:gold_rates,id',
            'gold_items.*.gold_gram' => 'required|numeric|min:0.01',
            'gold_items.*.gold_purchased_amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Update customer NIC if provided
            if ($request->customer_nic) {
                Customer::where('id', $request->customer_id)
                    ->update(['nic' => $request->customer_nic]);
            }

            // Update the main purchase record
            $purchaseOldGold->update([
                'customer_id' => $request->customer_id,
            ]);

            // Delete existing details
            $purchaseOldGold->details()->delete();

            // Create new details
            foreach ($request->gold_items as $item) {
                PurchaseOldGoldDetail::create([
                    'purchase_old_gold_id' => $purchaseOldGold->id,
                    'gold_rate_id' => $item['gold_rate_id'],
                    'gold_gram' => $item['gold_gram'],
                    'gold_purchased_amount' => $item['gold_purchased_amount'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase old gold record updated successfully!',
                'redirect' => route('purchase-old-gold.show', $purchaseOldGold->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error updating purchase record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(PurchaseOldGold $purchaseOldGold)
    {
        DB::beginTransaction();
        
        try {
            $purchaseOldGold->details()->delete();
            $purchaseOldGold->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase old gold record deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting purchase record: ' . $e->getMessage()
            ], 500);
        }
    }

    // API Methods for AJAX requests
    public function searchCustomers(Request $request)
    {
        $search = $request->get('search', '');
        
        $customers = Customer::where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('nic', 'like', "%{$search}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'phone', 'nic']);

        return response()->json($customers);
    }

    public function getCustomer(Customer $customer)
    {
        return response()->json($customer);
    }

    public function updateCustomerNIC(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'nic' => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $customer->update(['nic' => $request->nic]);

        return response()->json([
            'success' => true,
            'message' => 'Customer NIC updated successfully',
            'customer' => $customer
        ]);
    }

    public function getGoldRates()
    {
        $goldRates = GoldRate::orderBy('name')->get();
        return response()->json($goldRates);
    }

    // Helper method to get all customers (can be used for API calls)
    public function getAllCustomers()
    {
        $customers = Customer::orderBy('name')
            ->get(['id', 'name', 'phone', 'nic']);
        
        return response()->json($customers);
    }
}