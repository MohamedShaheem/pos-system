<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAdvance;
use App\Models\CustomerAdvanceDetail;
use App\Models\CustomerAdvanceUse;
use App\Models\CustomerAdvanceRefund;
use App\Models\CustomerGoldAdvance;
use App\Models\CustomerReservation;
use App\Models\CustomerReservationDetail;
use App\Models\CustomerReservationPayment;
use App\Models\GoldRate;
use App\Models\POSOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerManagementController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customers.customer_management.index', compact('customers'));
    }

    public function getAdvanceSummary($orderNo)
    {
        $cashAdvance = CustomerAdvance::where('order_no', $orderNo)->first();
        $goldAdvance = CustomerGoldAdvance::where('order_no', $orderNo)->first();

        $cashAmount = $cashAdvance ? $cashAdvance->advance_balance : 0;
        $goldAmount = $goldAdvance ? $goldAdvance->remaining_balance : 0;

        return response()->json([
            'cash' => number_format($cashAmount, 2),
            'gold' => number_format($goldAmount, 3)
        ]);
    }

    // Add these methods for refund functionality
    public function getCustomerAdvanceBalances($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            
            $advances = CustomerAdvance::where('customer_id', $customerId)
                ->with(['details', 'advanceUse', 'refunds'])
                ->get()
                ->map(function ($advance) {
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
                    return $advance['current_balance'] > 0;
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

            if ($request->advance_id) {
                $advance = CustomerAdvance::where('id', $request->advance_id)
                    ->where('customer_id', $customerId)
                    ->firstOrFail();
            } else {
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

            $totalDeposit = $advance->details->sum('amount');
            $totalUsed = $advance->advanceUse->sum('amount');
            $totalRefunded = $advance->refunds->sum('amount');
            $currentBalance = $totalDeposit - $totalUsed - $totalRefunded;

            if ($refundAmount > $currentBalance) {
                return response()->json([
                    'success' => false,
                    'message' => "Refund amount (Rs {$refundAmount}) exceeds available balance (Rs {$currentBalance})"
                ], 400);
            }

            CustomerAdvanceRefund::create([
                'customer_advance_id' => $advance->id,
                'amount' => $refundAmount,
                'notes' => $request->notes
            ]);

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

    public function customerTransactions($customerId)
{
    $customer = Customer::findOrFail($customerId);

    // Paginate POS Orders
    $posOrders = $customer->posOrders()
        ->with([
            'payments',
            'advanceUses.customerAdvance',
            'goldAdvanceUses.customerGoldAdvance.goldRate',
            'customerGoldExchanges.goldRate',
            'processedByUser'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Paginate Reservations
    $reservations = $customer->reservations()
        ->with(['reservationDetails', 'payments'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    $goldRates = GoldRate::all();

    $customer->load([
        'advances.details',
        'advances.advanceUse.posOrder',
        'advances.refunds',
        'goldAdvances.details',
        'goldAdvances.goldAdvanceUse.posOrder',
        'goldAdvances.goldRate'
    ]);

    // Categorize advances by type
    $cashOnlyAdvances = collect();
    $goldOnlyAdvances = collect();
    $combinedAdvances = collect();

    // Get advances with order_no only (exclude general advances)
    $cashAdvancesWithOrder = $customer->advances->whereNotNull('order_no')->keyBy('order_no');
    $goldAdvancesWithOrder = $customer->goldAdvances->whereNotNull('order_no')->keyBy('order_no');

    // Get all order numbers from both types
    $allOrderNumbers = $cashAdvancesWithOrder->keys()->merge($goldAdvancesWithOrder->keys())->unique();

    foreach ($allOrderNumbers as $orderNo) {
        $cashAdvance = $cashAdvancesWithOrder->get($orderNo);
        $goldAdvance = $goldAdvancesWithOrder->get($orderNo);

        if ($cashAdvance && $goldAdvance) {
            // Combined advance (both cash and gold with same order_no)
            $combinedAdvances->put($orderNo, [
                'cash' => $cashAdvance,
                'gold' => $goldAdvance
            ]);
        } elseif ($cashAdvance && !$goldAdvance) {
            // Cash only advance
            $cashOnlyAdvances->push($cashAdvance);
        } elseif (!$cashAdvance && $goldAdvance) {
            // Gold only advance
            $goldOnlyAdvances->push($goldAdvance);
        }
    }

    // Handle advances without order_no (general advances) - add them to their respective categories ONLY
    $generalCashAdvances = $customer->advances->whereNull('order_no');
    $generalGoldAdvances = $customer->goldAdvances->whereNull('order_no');

    // Add general advances to their respective categories only (not to combined)
    $cashOnlyAdvances = $cashOnlyAdvances->merge($generalCashAdvances);
    $goldOnlyAdvances = $goldOnlyAdvances->merge($generalGoldAdvances);

    // Cash advance calculations (with refunds)
    $cashAdvanceDeposits = $customer->advances->sum(fn($advance) => $advance->details->sum('amount'));
    $cashAdvanceUsage = CustomerAdvanceUse::whereIn('customer_advance_id', $customer->advances->pluck('id'))->sum('amount');
    $cashAdvanceRefunds = CustomerAdvanceRefund::whereIn('customer_advance_id', $customer->advances->pluck('id'))->sum('amount');
    $cashAdvanceBalance = $cashAdvanceDeposits - $cashAdvanceUsage - $cashAdvanceRefunds;

    // Gold advance calculations
    $goldAdvanceDeposits = $customer->goldAdvances->sum(fn($goldAdvance) => $goldAdvance->details->sum('gold_amount'));
    $goldAdvanceUsage = $customer->goldAdvances->sum(fn($goldAdvance) => $goldAdvance->goldAdvanceUse->sum('gold_amount'));
    $goldAdvanceBalance = $goldAdvanceDeposits - $goldAdvanceUsage;

    // Reservation stats
    $totalReservations = $customer->reservations()->count();
    $completedReservations = $customer->reservations()->where('status', 'completed')->count();
    $cancelledReservations = $customer->reservations()->where('status', 'cancelled')->count();
    $pendingReservations = $customer->reservations()->where('status', 'pending')->count();
    $totalReservationAmount = $customer->reservations()->sum('total_amount');
    $totalReservationPaid = $customer->reservations()->sum('paid_amount');

    // Paginate Payment Logs
    $allPayments = collect();

    // Advance Payments
    foreach ($customer->advances as $advance) {
        foreach ($advance->details as $detail) {
            $allPayments->push([
                'date' => $detail->created_at,
                'type' => 'Advance Payment',
                'reference' => $advance->order_no ?? 'General',
                'amount' => $detail->amount,
                'method' => 'Advance',
                'reference_no' => null,
                'notes' => $advance->note,
            ]);
        }
    }

    // POS Order Payments
    foreach($customer->posOrders()->with('payments')->get()->flatMap->payments as $payment) {
        $allPayments->push([
            'date' => $payment->created_at,
            'type' => 'POS Payment',
            'reference' => $payment->posOrder->invoice_no,
            'amount' => $payment->amount,
            'method' => $payment->payment_method,
            'reference_no' => $payment->reference_no,
            'notes' => $payment->notes,
        ]);
    }

    // Reservation Payments
    foreach($customer->reservations()->with('payments')->get()->flatMap->payments as $payment) {
        $allPayments->push([
            'date' => $payment->created_at,
            'type' => 'Reservation Payment',
            'reference' => 'Reservation #' . $payment->reservation_id,
            'amount' => $payment->amount,
            'method' => $payment->payment_method,
            'reference_no' => $payment->reference_no ?? '-',
            'notes' => $payment->notes,
        ]);
    }

    // Sort and paginate
    $allPayments = $allPayments->sortByDesc('date');
    $currentPage = request()->get('payments_page', 1);
    $perPage = 10;
    $paymentLogs = new \Illuminate\Pagination\LengthAwarePaginator(
        $allPayments->forPage($currentPage, $perPage),
        $allPayments->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'pageName' => 'payments_page']
    );

    return view('pos_orders.customer_transactions', compact(
        'customer',
        'posOrders',
        'reservations',
        'cashAdvanceDeposits',
        'cashAdvanceUsage', 
        'cashAdvanceBalance',
        'cashAdvanceRefunds',
        'goldAdvanceDeposits',
        'goldAdvanceUsage',
        'goldAdvanceBalance',
        'totalReservations',
        'completedReservations',
        'pendingReservations',
        'totalReservationAmount',
        'totalReservationPaid',
        'goldRates',
        'cancelledReservations',
        'cashOnlyAdvances',
        'goldOnlyAdvances',
        'combinedAdvances',
        'paymentLogs'
    ));
}

}