<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\POSOrder;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function store(Request $request, $customerId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,chq,card,bank_transfer,other',
            'reference_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            $customer = Customer::findOrFail($customerId);
            
            // Check if payment amount exceeds total remaining balance
            if ($request->amount > $customer->total_balance) {
                return redirect()->back()
                    ->with('error', 'Payment amount cannot exceed total remaining balance of Rs' . number_format($customer->total_balance, 2));
            }

            DB::beginTransaction();
            
            $remainingAmount = $request->amount;
            
            // Get unpaid orders ordered by creation date (oldest first)
            $orders = $customer->posOrders()
            ->where('status', '!=', 'hold')
            ->where('balance', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();


            if ($orders->isEmpty()) {
                DB::rollback();
                return redirect()->back()
                    ->with('error', 'No unpaid orders found for this customer.');
            }

            foreach ($orders as $order) {
                if ($remainingAmount <= 0) break;

                $orderRemainingBalance = $order->balance;
                $paymentAmount = min($remainingAmount, $orderRemainingBalance);

                if ($paymentAmount > 0) {
                    $payment = Payment::create([
                        'pos_order_id' => $order->id,
                        'amount' => $paymentAmount,
                        'payment_method' => $request->payment_method,
                        'reference_no' => $request->reference_no,
                        'notes' => $request->notes,
                        'is_credit_payment' => 1
                    ]);

                    if($request->payment_method == 'cash'){
                        $order->increment('advance', $paymentAmount);
                        $order->increment('cash', $paymentAmount);
                        $order->decrement('balance', $paymentAmount);
                    }
                    elseif($request->payment_method == 'card'){
                        $order->increment('card', $paymentAmount);
                        $order->decrement('balance', $paymentAmount);
                    }
                    elseif($request->payment_method == 'chq'){
                        $order->increment('chq', $paymentAmount);
                        $order->decrement('balance', $paymentAmount);
                    }
                    elseif($request->payment_method == 'bank_transfer'){
                        $order->increment('bank_transfer', $paymentAmount);
                        $order->decrement('balance', $paymentAmount);
                    }
                    // $order->save();

                    // Update order status if fully paid
                    $newRemainingBalance = $orderRemainingBalance - $paymentAmount;
                    if ($newRemainingBalance <= 0) {
                        $order->update(['status' => 'complete']);
                    }

                    $remainingAmount -= $paymentAmount;
                }
            }

            DB::commit();
            
            return redirect()->route('customer.transactions', $customerId)
                ->with('success', 'Payment of Rs' . number_format($request->amount, 2) . ' recorded successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    public function destroy($customerId, $paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            $posOrder = $payment->posOrder;
            
            // Verify the payment belongs to the customer
            if ($posOrder->customer_id != $customerId) {
                return redirect()->back()
                    ->with('error', 'Payment does not belong to this customer.');
            }

            DB::beginTransaction();
            
            $payment->delete();
            
            // Update order status if no longer fully paid
            $remainingBalance = $posOrder->total - $posOrder->advance - $posOrder->payments()->sum('amount');
            if ($remainingBalance > 0 && $posOrder->status === 'complete') {
                $posOrder->update(['status' => 'pending']);
            }
            
            DB::commit();
            
            return redirect()->route('customer.transactions', $customerId)
                ->with('success', 'Payment deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }
}