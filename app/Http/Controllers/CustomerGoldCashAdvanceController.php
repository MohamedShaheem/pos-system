<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAdvance;
use App\Models\CustomerAdvanceDetail;
use App\Models\CustomerGoldAdvance;
use App\Models\CustomerGoldAdvanceDetail;
use App\Models\GoldBalance;
use App\Models\GoldRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerGoldCashAdvanceController extends Controller
{
    // Cash and Gold balance 
    public function getCombinedBalance($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);

            $cashBalance = CustomerAdvance::where('customer_id', $customerId)->sum('advance_balance');
            $goldBalance = CustomerGoldAdvance::where('customer_id', $customerId)->sum('gold_balance');

            return response()->json([
                'success' => true,
                'cash_balance' => $cashBalance,
                'gold_balance' => $goldBalance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching balances: ' . $e->getMessage()
            ], 500);
        }
    }

// gold and chas store
public function store(Request $request, $customerId)
{
    $request->validate([
        'cash_amount' => 'required|numeric|min:0.01',
        'gold_entries' => 'required|array|min:1',
        'gold_entries.*.carat_id' => 'required|exists:gold_rates,id',
        'gold_entries.*.gram' => 'required|numeric|min:0.01',
        'cash_gold_order_no' => 'required|string|max:255',
        'cash_gold_note' => 'nullable|string|max:1000',
        'routeName' => 'nullable|string'
    ]);

    try {
        DB::beginTransaction();

        $customer = Customer::findOrFail($customerId);
        $orderNo = trim($request->cash_gold_order_no);
        $note = $request->cash_gold_note;
        $cashAmount = $request->cash_amount;
        $goldEntries = $request->gold_entries;

        /**
         * === CASH ADVANCE ===
         */
        $cashAdvance = CustomerAdvance::where('customer_id', $customerId)
            ->where('order_no', $orderNo)
            ->first();

        if (!$cashAdvance) {
            $cashAdvance = CustomerAdvance::create([
                'customer_id' => $customerId,
                'order_no' => $orderNo,
                'note' => $note,
                'advance_balance' => $cashAmount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            if ($note) {
                $cashAdvance->note = $cashAdvance->note
                    ? $cashAdvance->note . ' | ' . $note
                    : $note;
            }
            $cashAdvance->advance_balance += $cashAmount;
            $cashAdvance->save();
        }

        $cashDetail = CustomerAdvanceDetail::create([
            'customer_advance_id' => $cashAdvance->id,
            'amount' => $cashAmount,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /**
         * === GOLD ADVANCE ===
         */
        $addedGoldDetailIds = [];

        foreach ($goldEntries as $entry) {
            $goldRateId = $entry['carat_id'];
            $goldGram = $entry['gram'];

            $goldAdvance = CustomerGoldAdvance::where('customer_id', $customerId)
                ->where('order_no', $orderNo)
                ->where('gold_rate_id', $goldRateId)
                ->first();

            if (!$goldAdvance) {
                $goldAdvance = CustomerGoldAdvance::create([
                    'customer_id' => $customerId,
                    'order_no' => $orderNo,
                    'gold_rate_id' => $goldRateId,
                    'note' => $note,
                    'gold_balance' => $goldGram,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $goldAdvance->gold_balance += $goldGram;
                if ($note) {
                    $goldAdvance->note = $goldAdvance->note
                        ? $goldAdvance->note . ' | ' . $note
                        : $note;
                }
                $goldAdvance->updated_at = now();
                $goldAdvance->save();
            }

            $goldDetail = CustomerGoldAdvanceDetail::create([
                'customer_gold_advance_id' => $goldAdvance->id,
                'gold_amount' => $goldGram,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $addedGoldDetailIds[] = $goldDetail->id;

            // Log to gold balance
            $goldRate = GoldRate::find($goldRateId);
            $caratName = $goldRate ? $goldRate->name : 'Unknown Carat';

            $this->storeGoldBalance(
                "Gold Advance ({$caratName}) from {$customer->name} (Order #{$orderNo})",
                'gold_in',
                $goldGram
            );
        }

        DB::commit();

        $totalCashBalance = CustomerAdvance::where('customer_id', $customerId)->sum('advance_balance');
        $totalGoldBalance = CustomerGoldAdvance::where('customer_id', $customerId)->sum('gold_balance');

        return response()->json([
            'success' => true,
            'message' => "Cash + Gold advance stored successfully. Cash: Rs.{$cashAmount}, Gold: " . collect($goldEntries)->sum('gram') . "g",
            'order_no' => $orderNo,
            'cash_advance_id' => $cashAdvance->id,
            'cash_detail_id' => $cashDetail->id,
            'gold_detail_ids' => $addedGoldDetailIds,
            'new_cash_balance' => $totalCashBalance,
            'new_gold_balance' => $totalGoldBalance,
            'print_url' => route('print.receipt.gold.cash', [
                'cashDetailId' => $cashDetail->id,
                'goldDetailIds' => implode(',', $addedGoldDetailIds),
                'routeName' => $request->routeName
            ]),
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error storing advance: ' . $e->getMessage()
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

public function printReceipt($cashDetailId, $goldDetailIds, $routeName)
{
    try {
        // Get cash detail with proper relationship
        $cashDetail = CustomerAdvanceDetail::with(['customerAdvance.customer'])
            ->findOrFail($cashDetailId);

        // Parse gold detail IDs
        $goldDetailIdsArray = explode(',', $goldDetailIds);

        // Get gold details with proper relationships
        $goldDetails = CustomerGoldAdvanceDetail::with([
            'customerGoldAdvance.customer', 
            'customerGoldAdvance.goldRate'
        ])
        ->whereIn('id', $goldDetailIdsArray)
        ->get();

        // Get customer from cash detail
        $customer = $cashDetail->customerAdvance->customer;
        
        // Get order number from cash advance
        $orderNo = $cashDetail->customerAdvance->order_no;
        
        // Get note from cash advance
        $note = $cashDetail->customerAdvance->note;
        
        // Format date
        $formattedDate = $cashDetail->created_at->format('d/m/Y');
        
        // Generate receipt number
        $receiptNo = strtoupper($orderNo);
        
        $route = $routeName;

        // Calculate totals
        $totalCash = $cashDetail->amount;
        $totalGold = $goldDetails->sum('gold_amount');

        // Convert amount to words (make sure you have NumberToWords package installed)
        $amountInWords = ucfirst((new \NumberToWords\NumberToWords())
            ->getNumberTransformer('en')
            ->toWords($totalCash)) . ' rupees only';

        return view('print.cash_gold_receipt', compact(
            'cashDetail',
            'goldDetails',
            'customer',
            'formattedDate',
            'receiptNo',
            'orderNo',
            'note',
            'totalCash',
            'totalGold',
            'route',
            'amountInWords'
        ));

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error generating receipt: ' . $e->getMessage()
        ], 500);
    }
}


}