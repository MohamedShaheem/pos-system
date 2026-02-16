<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerGoldAdvance;
use App\Models\CustomerGoldAdvanceDetail;
use App\Models\GoldBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerGoldAdvanceController extends Controller
{
    // public function getBalance($customerId)
    // {
    //     try {
    //         $customer = Customer::findOrFail($customerId);

    //         // Total Gold Advance Balance (sum of gold_balance from CustomerGoldAdvance)
    //         $balance = CustomerGoldAdvance::where('customer_id', $customerId)->sum('gold_balance');

    //         return response()->json([
    //             'success' => true,
    //             'balance' => $balance,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error fetching balance: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    //gold store
    public function store(Request $request, $customerId)
    {
        $request->validate([
            'gold_amount' => 'required|numeric|min:0.01',
            'gold_rate'   => 'required|exists:gold_rates,id',
            'order_no'    => 'nullable|string',
            'note'       => 'nullable|string',
            'routeName' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($customerId);
            $orderNo = $request->order_no;
            $rateId = $request->gold_rate;
            $goldAmount = $request->gold_amount;

            // Log into GoldBalance
            $this->storeGoldBalance(
                "Gold Advance from {$customer->name}" . ($orderNo ? " (Order #$orderNo)" : ""),
                'gold_in',
                $goldAmount
            );

            // Find existing advance
            $existingAdvance = CustomerGoldAdvance::where('customer_id', $customer->id)
                ->where('gold_rate_id', $rateId)
                ->when($orderNo, fn($q) => $q->where('order_no', $orderNo))
                ->when(!$orderNo, fn($q) => $q->whereNull('order_no'))
                ->first();

            if ($existingAdvance) {
                // Add to existing advance
                $detail = CustomerGoldAdvanceDetail::create([
                    'customer_gold_advance_id' => $existingAdvance->id,
                    'gold_amount' => $goldAmount,
                    'note' => $request->note
                ]);

               
                $existingAdvance->gold_balance += $goldAmount;
                $existingAdvance->save();

                $advance = $existingAdvance;
                $message = 'Gold amount added to existing advance.';
            } else {
                // Create new advance
                $advance = CustomerGoldAdvance::create([
                    'customer_id' => $customer->id,
                    'order_no' => $orderNo,
                    'gold_balance' => $goldAmount,
                    'gold_rate_id' => $rateId,
                ]);

                $detail = CustomerGoldAdvanceDetail::create([
                    'customer_gold_advance_id' => $advance->id,
                    'gold_amount' => $goldAmount,
                    'note' => $request->note,
                ]);
                $message = 'New gold advance created.';
            }
            $totalGoldBalance = CustomerGoldAdvance::where('customer_id', $customer->id)->sum('gold_balance');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'gold_balance' => $advance->gold_balance,
                'new_gold_balance' => $totalGoldBalance,
                'advance_id' => $advance->id,
                'detail_id' => $detail->id,
                'order_no' => $advance->order_no,
                'print_url' => route('print.receipt.gold', [
                    'advance' => $advance->id,
                    'detail' => $detail->id,
                    'routeName' => $request->routeName
                ]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error recording gold advance: ' . $e->getMessage()
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

public function printReceipt($advanceId, $detailId, $routeName)
{
    $advance = CustomerGoldAdvance::with('customer')->findOrFail($advanceId);
    $detail = $advance->details()->findOrFail($detailId);
    $customer = $advance->customer;
    $orderNo = $advance->order_no;
    $route = $routeName;
    $formattedDate = $detail->created_at->format('d/m/Y');
    $receiptNo = $detail->id;
    $amount = $detail->gold_amount; // e.g., 5.300

    // Split integer and fractional parts
    $grams = floor($amount);
    $milligrams = round(($amount - $grams) * 1000);

    $numberToWords = new \NumberToWords\NumberToWords();
    $numberTransformer = $numberToWords->getNumberTransformer('en');

    $amountInWords = ucfirst($numberTransformer->toWords($grams)) . ' grams';
    if ($milligrams > 0) {
        $amountInWords .= ' and ' . $numberTransformer->toWords($milligrams) . ' milligrams';
    }
    $amountInWords .= ' only';

    return view('print.gold_receipt', compact(
        'advance',
        'customer',
        'formattedDate',
        'receiptNo',
        'orderNo',
        'amount',
        'route',
        'amountInWords',
        'detail'
    ));
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
        $orderNo = CustomerGoldAdvance::whereNotNull('order_no')
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
