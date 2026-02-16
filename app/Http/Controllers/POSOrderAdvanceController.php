<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAdvance;
use App\Models\CustomerGoldAdvance;
use Illuminate\Http\Request;

class POSOrderAdvanceController extends Controller
{
   public function getAdvanceData($customerId)
{
    try {
        $customer = Customer::findOrFail($customerId);

        // General Cash Advance (sum of advance_balance where order_no is null)
        $generalCashAdvance = CustomerAdvance::where('customer_id', $customerId)
            ->whereNull('order_no')
            ->sum('advance_balance') ?? 0;

        // Get general gold advances (not just sum)
        $generalGoldAdvances = CustomerGoldAdvance::where('customer_id', $customerId)
            ->whereNull('order_no')
            ->with('goldRate:id,name,rate') // Include rate if you want it for calculation
            ->get();

        // Total gold balance
        $generalGoldAdvance = $generalGoldAdvances->sum('gold_balance') ?? 0;

        // Collect unique gold rate IDs and names used
        $generalGoldRateInfo = $generalGoldAdvances->map(function ($item) {
            return [
                'id' => $item->gold_rate_id,
                'name' => optional($item->goldRate)->name,
                'rate' => optional($item->goldRate)->rate
            ];
        })->unique('id')->values();

        // Get order numbers
        $cashOrderNos = CustomerAdvance::where('customer_id', $customerId)
            ->whereNotNull('order_no')
            ->where('advance_balance', '>', 0)
            ->distinct()
            ->pluck('order_no');

        $goldOrderNos = CustomerGoldAdvance::where('customer_id', $customerId)
            ->whereNotNull('order_no')
            ->where('gold_balance', '>', 0)
            ->distinct()
            ->pluck('order_no');

        $orderNumbers = $cashOrderNos->merge($goldOrderNos)->unique()->values();

        return response()->json([
            'success' => true,
            'data' => [
                'customer_name' => $customer->name,
                'general_cash_advance' => (float) $generalCashAdvance,
                'general_gold_advance' => (float) $generalGoldAdvance,
                'general_gold_rates' => $generalGoldRateInfo, // Send rate info to frontend
                'general_gold_rates' => $generalGoldRateInfo, // full list
                'general_gold_rate_name' => optional($generalGoldRateInfo->first())['name'],
                'general_gold_rate_id' => optional($generalGoldRateInfo->first())['id'],
                'general_gold_rate' => optional($generalGoldRateInfo->first())['rate'],
                'order_numbers' => $orderNumbers
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching advance data: ' . $e->getMessage()
        ], 500);
    }
}


   public function getOrderAdvanceDetails($customerId, $orderNo)
{
    try {
        $cashAdvance = CustomerAdvance::where('customer_id', $customerId)
            ->where('order_no', $orderNo)
            ->sum('advance_balance') ?? 0;

        $goldAdvanceModels = CustomerGoldAdvance::where('customer_id', $customerId)
            ->where('order_no', $orderNo)
            ->with('goldRate:id,name,rate')
            ->get();

        $goldAdvance = $goldAdvanceModels->sum('gold_balance') ?? 0;

        // Get first available gold rate (assuming consistent rate per order)
        $goldRateModel = $goldAdvanceModels->first()?->goldRate;

        return response()->json([
            'success' => true,
            'data' => [
                'order_no' => $orderNo,
                'cash_advance' => (float) $cashAdvance,
                'gold_advance' => (float) $goldAdvance,
                'gold_rate_id' => optional($goldRateModel)->id,
                'gold_rate' => optional($goldRateModel)->rate,
                'gold_rate_name' => optional($goldRateModel)->name,
                'cash_details' => CustomerAdvance::where('customer_id', $customerId)
                    ->where('order_no', $orderNo)
                    ->select('note', 'created_at', 'advance_balance')
                    ->get(),
                'gold_details' => $goldAdvanceModels->map(function ($item) {
                    return [
                        'note' => $item->note,
                        'created_at' => $item->created_at,
                        'gold_balance' => $item->gold_balance,
                        'gold_rate_id' => $item->gold_rate_id,
                        'gold_rate_name' => optional($item->goldRate)->name,
                        'gold_rate' => optional($item->goldRate)->rate,
                    ];
                })
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching order advance details: ' . $e->getMessage()
        ], 500);
    }
}

}