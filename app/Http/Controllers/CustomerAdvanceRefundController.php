<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAdvance;
use App\Models\CustomerAdvanceRefund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerAdvanceRefundController extends Controller
{
    /**
     * Get customer advance balances for refund dropdown
     */
    public function getCustomerAdvanceBalances($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            
            // Get advances with positive balance
            $advances = CustomerAdvance::where('customer_id', $customerId)
                ->with(['details', 'advanceUse', 'refunds'])
                ->get()
                ->map(function ($advance) {
                    // Calculate current balance
                    $totalDeposit = $advance->details->sum('amount');
                    $totalUsed = $advance->advanceUse->sum('amount');
                    $totalRefunded = $advance->refunds->sum('amount');
                    $currentBalance = $totalDeposit - $totalUsed - $totalRefunded;
                    
                    return [
                        'id' => $advance->id,
                        'order_no' => $advance->order_no,
                        'current_balance' => $currentBalance,
                        'display_name' => $advance->order_no ? "Order: {$advance->order_no}" : "General Advance"
                    ];
                })
                ->filter(function ($advance) {
                    return $advance['current_balance'] > 0; // Only show advances with positive balance
                })
                ->values();

            return response()->json([
                'success' => true,
                'advances' => $advances
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching customer advance balances: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching advance balances'
            ], 500);
        }
    }

    /**
     * Process cash advance refund
     */
    public function processRefund(Request $request, $customerId)
    {
        $request->validate([
            'advance_id' => 'nullable|exists:customer_advances,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($customerId);
            $refundAmount = $request->amount;

            // Determine which advance to refund from
            if ($request->advance_id) {
                // Refund from specific advance
                $advance = CustomerAdvance::where('id', $request->advance_id)
                    ->where('customer_id', $customerId)
                    ->firstOrFail();
            } else {
                // Refund from general advance (order_no is null)
                $advance = CustomerAdvance::where('customer_id', $customerId)
                    ->whereNull('order_no')
                    ->first();
                
                if (!$advance) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No general advance found for this customer'
                    ], 400);
                }
            }

            // Calculate current balance
            $totalDeposit = $advance->details->sum('amount');
            $totalUsed = $advance->advanceUse->sum('amount');
            $totalRefunded = $advance->refunds->sum('amount');
            $currentBalance = $totalDeposit - $totalUsed - $totalRefunded;

            // Validate refund amount
            if ($refundAmount > $currentBalance) {
                return response()->json([
                    'success' => false,
                    'message' => "Refund amount (Rs {$refundAmount}) exceeds available balance (Rs {$currentBalance})"
                ], 400);
            }

            // Create refund record
            CustomerAdvanceRefund::create([
                'customer_advance_id' => $advance->id,
                'amount' => $refundAmount,
                'notes' => $request->notes
            ]);

            // Update advance balance (optional, if you maintain a balance field)
            $advance->advance_balance = $currentBalance - $refundAmount;
            $advance->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully refunded Rs {$refundAmount} from " . 
                           ($advance->order_no ? "Order: {$advance->order_no}" : "General Advance"),
                'new_balance' => $currentBalance - $refundAmount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing advance refund: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing refund: ' . $e->getMessage()
            ], 500);
        }
    }
}