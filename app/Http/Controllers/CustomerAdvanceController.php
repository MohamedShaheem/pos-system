<?php
// Updated CustomerAdvanceController
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAdvance;
use App\Models\CustomerAdvanceDetail;
use App\Models\CustomerAdvanceUse;
use App\Models\POSOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAdvanceController extends Controller
{
    public function index(Customer $customer)
    {
        $advances = CustomerAdvance::where('customer_id', $customer->id)
            ->with(['customer', 'details', 'advanceUse.posOrder'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total balance
        $totalBalance = $this->calculateCustomerBalance($customer->id);

        return response()->json([
            'customer' => $customer,
            'advances' => $advances->map(function ($advance) {
                return [
                    'id' => $advance->id,
                    'order_no' => $advance->order_no,
                    'note' => $advance->note,
                    'created_at' => $advance->created_at,
                    'total_deposit' => $advance->total_deposit,
                    'total_used' => $advance->total_used,
                    'remaining_balance' => $advance->remaining_balance,
                    'advance_balance' => $advance->advance_balance,
                    'details' => $advance->details,
                    'uses' => $advance->advanceUse
                ];
            }),
            'total_balance' => $totalBalance
        ]);
    }

    public function getBalance($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            $balance = CustomerAdvance::where('customer_id', $customerId)->sum('advance_balance');
            
            return response()->json([
                'success' => true,
                'balance' => $balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching balance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printReceipt($advanceId, $detailId, $routeName)
    {
        $advance = CustomerAdvance::with('customer','details')->findOrFail($advanceId);
        $detail = $advance->details()->findOrFail($detailId);
        $customer = $advance->customer;
        $orderNo = $advance->order_no;
        $formattedDate = $detail->created_at->format('d/m/Y');
        $receiptNo = $detail->id; // Or use your own receipt numbering logic
        $amount = $detail->amount;
        $route = $routeName;
        $numberToWords = new \NumberToWords\NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $amountInWords = ucfirst($numberTransformer->toWords($amount)) . ' rupees only';

        return view('print.receipt', compact(
            'advance',
            'customer',
            'formattedDate',
            'receiptNo',
            'amount',
            'orderNo',
            'route',
            'amountInWords',
            'detail'
        ));
    }

    //cash store
public function store(Request $request, $customerId)
{
    $request->validate([
        'amount' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string',
        'order_no' => 'nullable|string',
        'routeName' => 'nullable|string'
    ]);

    try {
        DB::beginTransaction();

        $customer = Customer::findOrFail($customerId);

        $orderNo = $request->order_no ?? null;

        // CASE 1: If order_no is provided, find matching record
        if ($orderNo) {
            $existingAdvance = CustomerAdvance::where('customer_id', $customer->id)
                ->where('order_no', $orderNo)
                ->first();
        } else {
            // CASE 2: order_no not provided, find advance with NULL order_no
            $existingAdvance = CustomerAdvance::where('customer_id', $customer->id)
                ->whereNull('order_no')
                ->first();
        }

        if ($existingAdvance) {
            // Add to existing advance
            $newDetail = CustomerAdvanceDetail::create([
                'customer_advance_id' => $existingAdvance->id,
                'amount' => $request->amount,
                'note' => $request->notes
            ]);

            $existingAdvance->increment('advance_balance', $request->amount);

            $advance = $existingAdvance;
            $message = 'Amount added to existing advance';
        } else {
            // No matching record found, create new
            $advance = CustomerAdvance::create([
                'customer_id' => $customer->id,
                'order_no' => $orderNo, // will be NULL if not provided
                // 'note' => $request->notes,
                'advance_balance' => $request->amount
            ]);

            $newDetail = CustomerAdvanceDetail::create([
                'customer_advance_id' => $advance->id,
                'amount' => $request->amount,
                'note' => $request->notes,
            ]);

            $message = 'New advance added successfully';
        }

        // Recalculate total customer advance balance
        $newBalance = $this->calculateCustomerBalance($customer->id);

        // Ensure stored balance reflects recalculated logic
        $advance->advance_balance = $advance->remaining_balance;
        $advance->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'new_balance' => $newBalance,
            'message' => $message,
            'advance_id' => $advance->id,
            'order_no' => $advance->order_no,
            'is_existing_order' => isset($existingAdvance),
            'print_url' => route('print.receipt', [
                'advance' => $advance->id,
                'detail' => $newDetail->id,
                'routeName' => $request->routeName
            ])
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error adding advance: ' . $e->getMessage()
        ], 500);
    }
}


    public function useAdvance(Request $request, $orderId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'order_no' => 'nullable|string' // Add order_no support
        ]);

        try {
            DB::beginTransaction();

            $order = POSOrder::findOrFail($orderId);
            $customer = $order->customer;
            $requestedAmount = $request->amount;
            $orderNo = $request->order_no ?? null;

            // Check total available balance
            $availableBalance = CustomerAdvance::where('customer_id', $customer->id)->sum('advance_balance');

            if ($availableBalance < $requestedAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient advance balance. Available: ' . $availableBalance
                ]);
            }

            $remaining = $requestedAmount;

            // Priority 1: Use advance with specific order_no if provided
            if ($orderNo) {
                $specificAdvance = CustomerAdvance::where('customer_id', $customer->id)
                    ->where('order_no', $orderNo)
                    ->where('advance_balance', '>', 0)
                    ->first();

                if ($specificAdvance) {
                    $useAmount = min($specificAdvance->advance_balance, $remaining);

                    CustomerAdvanceUse::create([
                        'customer_advance_id' => $specificAdvance->id,
                        'amount' => $useAmount,
                        'pos_order_id' => $order->id
                    ]);

                    $specificAdvance->decrement('advance_balance', $useAmount);
                    $remaining -= $useAmount;
                }
            }

            // Priority 2: Use advances with order_no (if any remaining amount)
            if ($remaining > 0) {
                $advancesWithOrderNo = CustomerAdvance::where('customer_id', $customer->id)
                    ->whereNotNull('order_no')
                    ->where('advance_balance', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                foreach ($advancesWithOrderNo as $advance) {
                    if ($remaining <= 0) break;

                    $useAmount = min($advance->advance_balance, $remaining);

                    CustomerAdvanceUse::create([
                        'customer_advance_id' => $advance->id,
                        'amount' => $useAmount,
                        'pos_order_id' => $order->id
                    ]);

                    $advance->decrement('advance_balance', $useAmount);
                    $remaining -= $useAmount;
                }
            }

            // Priority 3: Use advances without order_no (if any remaining amount)
            if ($remaining > 0) {
                $advancesWithoutOrderNo = CustomerAdvance::where('customer_id', $customer->id)
                    ->whereNull('order_no')
                    ->where('advance_balance', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                foreach ($advancesWithoutOrderNo as $advance) {
                    if ($remaining <= 0) break;

                    $useAmount = min($advance->advance_balance, $remaining);

                    CustomerAdvanceUse::create([
                        'customer_advance_id' => $advance->id,
                        'amount' => $useAmount,
                        'pos_order_id' => $order->id
                    ]);

                    $advance->decrement('advance_balance', $useAmount);
                    $remaining -= $useAmount;
                }
            }

            $amountUsed = $requestedAmount - $remaining;

            // Recalculate balance
            $newBalance = CustomerAdvance::where('customer_id', $customer->id)->sum('advance_balance');

            DB::commit();

            return response()->json([
                'success' => true,
                'new_balance' => $newBalance,
                'amount_used' => $amountUsed,
                'message' => 'Advance used successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error using advance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelAdvance($id)
    {
        try {
            DB::beginTransaction();

            $advance = CustomerAdvance::with(['details', 'advanceUse'])->findOrFail($id);
            
            // Check if advance has been used
            if ($advance->advanceUse()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel advance that has already been used.'
                ]);
            }

            $customer = $advance->customer;

            // Delete the advance and its details (uses should be empty based on check above)
            $advance->details()->delete();
            $advance->delete();

            // Recalculate customer balance
            $newBalance = $this->calculateCustomerBalance($customer->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'new_balance' => $newBalance,
                'message' => 'Advance cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling advance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate total available balance for a customer
     */
    private function calculateCustomerBalance($customerId)
    {
        return CustomerAdvance::where('customer_id', $customerId)->sum('advance_balance');
    }

    /**
     * Get detailed advance history for a customer
     */
    public function getAdvanceHistory($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            
            $advances = CustomerAdvance::where('customer_id', $customerId)
                ->with(['details', 'advanceUse.posOrder'])
                ->orderBy('created_at', 'desc')
                ->get();

            $history = [];
            
            foreach ($advances as $advance) {
                // Add deposit entries
                foreach ($advance->details as $detail) {
                    $history[] = [
                        'id' => $advance->id,
                        'type' => 'deposit',
                        'amount' => $detail->amount,
                        'order_no' => $advance->order_no,
                        'note' => $advance->note,
                        'pos_order_id' => null,
                        'created_at' => $detail->created_at,
                        'reference' => 'Deposit #' . $detail->id
                    ];
                }
                
                // Add usage entries
                foreach ($advance->advanceUse as $use) {
                    $history[] = [
                        'id' => $advance->id,
                        'type' => 'usage',
                        'amount' => $use->amount,
                        'order_no' => $advance->order_no,
                        'note' => 'Used for POS Order #' . ($use->posOrder->invoice_no ?? $use->pos_order_id),
                        'pos_order_id' => $use->pos_order_id,
                        'created_at' => $use->created_at,
                        'reference' => 'Usage #' . $use->id
                    ];
                }
            }
            
            // Sort by created_at desc
            usort($history, function ($a, $b) {
                return $b['created_at'] <=> $a['created_at'];
            });

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'history' => $history,
                'total_balance' => $this->calculateCustomerBalance($customerId),
                'advances_summary' => $this->getAdvancesSummary($customerId)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get advances summary for a customer
     */
    private function getAdvancesSummary($customerId)
    {
        $advances = CustomerAdvance::where('customer_id', $customerId)->get();
        
        $summary = [
            'total_advances' => $advances->count(),
            'advances_with_order_no' => $advances->whereNotNull('order_no')->count(),
            'advances_without_order_no' => $advances->whereNull('order_no')->count(),
            'total_balance' => $advances->sum('advance_balance'),
            'balance_with_order_no' => $advances->whereNotNull('order_no')->sum('advance_balance'),
            'balance_without_order_no' => $advances->whereNull('order_no')->sum('advance_balance'),
        ];

        return $summary;
    }

    /**
     * Get customer advances grouped by order_no
     */
    public function getAdvancesByOrderNo($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            
            $advancesWithOrderNo = CustomerAdvance::where('customer_id', $customerId)
                ->whereNotNull('order_no')
                ->with(['details', 'advanceUse'])
                ->orderBy('order_no')
                ->get()
                ->groupBy('order_no');

            $advancesWithoutOrderNo = CustomerAdvance::where('customer_id', $customerId)
                ->whereNull('order_no')
                ->with(['details', 'advanceUse'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'advances_with_order_no' => $advancesWithOrderNo,
                'advances_without_order_no' => $advancesWithoutOrderNo,
                'total_balance' => $this->calculateCustomerBalance($customerId)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching advances: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available balance for a specific order_no
     */
    public function getOrderBalance($customerId, $orderNo)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            
            $orderBalance = CustomerAdvance::where('customer_id', $customerId)
                ->where('order_no', $orderNo)
                ->sum('advance_balance');

            return response()->json([
                'success' => true,
                'customer' => $customer,
                'order_no' => $orderNo,
                'balance' => $orderBalance
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching order balance: ' . $e->getMessage()
            ], 500);
        }
    }

public function orderNoSuggestions(Request $request, $id)
{
    $search = $request->get('query');
    
    // Validate input
    if (empty($search)) {
        return response()->json(['suggestion' => null]);
    }

    try {
        // Filter by customer ID if you want customer-specific suggestions
        $orderNo = CustomerAdvance::whereNotNull('order_no')
            ->where('customer_id', $id) // Filter by customer ID
            ->where('order_no', 'like', $search . '%') // start-with match
            ->orderBy('order_no')
            ->pluck('order_no')
            ->first();

        return response()->json(['suggestion' => $orderNo]);
        
    } catch (\Exception $e) {
        return response()->json(['suggestion' => null], 500);
    }
}

}