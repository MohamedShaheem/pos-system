<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POSOrder;
use App\Models\Customer;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\CustomerGoldExchange;
use App\Models\CustomerAdvanceUse;
use App\Models\CustomerGoldAdvanceUse;
use App\Models\CustomerAdvance;
use App\Models\CustomerGoldAdvance;
use App\Models\PurchaseOldGold;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncController extends Controller
{
    /**
     * Fetch orders data for synchronization
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchOrders(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:date'
            ]);

            $startDate = Carbon::parse($validated['date'])->startOfDay();
            $endDate = isset($validated['end_date']) 
                ? Carbon::parse($validated['end_date'])->endOfDay()
                : Carbon::parse($validated['date'])->endOfDay();

            // Fetch orders within the date range
            $orders = PosOrder::with([
                'customer',
                'orderDetails.product.goldRate',
                'orderDetails.product.category',
                'payments'
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();


            // Fetch purchases
            $purchases = PurchaseOldGold::with([
                'customer',
                'details.goldRate'
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'invoice_no' => $purchase->invoice_no,
                    'customer_id' => $purchase->customer_id,
                    'customer_name' => $purchase->customer->name ?? null,
                    'status' => $purchase->status,
                    'details' => $purchase->details->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'gold_rate_id' => $detail->gold_rate_id,
                            'gold_rate_name' => $detail->goldRate->name ?? null,
                            'gold_rate' => $detail->goldRate->rate ?? null,
                            'gold_gram' => $detail->gold_gram,
                            'gold_purchased_amount' => $detail->gold_purchased_amount
                        ];
                    }),
                    'created_at' => $purchase->created_at,
                    'updated_at' => $purchase->updated_at
                ];
            });

            // Fetch advances
            $advances = CustomerAdvance::with(['customer', 'details'])
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->orderBy('updated_at', 'asc')
                ->get()
                ->map(function ($advance) use ($startDate, $endDate) {
                    return [
                        'id' => $advance->id,
                        'order_no' => $advance->order_no ?? null,
                        'customer_id' => $advance->customer_id,
                        'customer_name' => $advance->customer->name ?? null,
                        // Filter details by updated_at within the same range
                        'details' => $advance->details
                            ->filter(function ($detail) use ($startDate, $endDate) {
                                return $detail->updated_at >= $startDate && $detail->updated_at <= $endDate;
                            })
                            ->map(function ($detail) {
                                return [
                                    'id' => $detail->id,
                                    'amount' => $detail->amount,
                                    'created_at' => $detail->created_at,
                                    'updated_at' => $detail->updated_at
                                ];
                            }),
                        'created_at' => $advance->created_at,
                        'updated_at' => $advance->updated_at
                    ];
                });

            $responseData = [];

            foreach ($orders as $order) {
                // Get gold exchanges for this order
                $goldExchanges = CustomerGoldExchange::with([
                    'customer',
                    'goldRate'
                ])
                ->where('pos_order_id', $order->id)
                ->get()
                ->map(function ($exchange) {
                    return [
                        'id' => $exchange->id,
                        'customer_id' => $exchange->customer_id,
                        'customer_name' => $exchange->customer->name ?? null,
                        'gold_rate_id' => $exchange->gold_rate_id,
                        'gold_rate_name' => $exchange->goldRate->name ?? null,
                        'gold_rate_type' => $exchange->goldRate->type ?? null,
                        'gold_rate' => $exchange->goldRate->rate ?? null,
                        'gold_weight' => $exchange->gold_weight,
                        'gold_purchased_amount' => $exchange->gold_purchased_amount,
                        'created_at' => $exchange->created_at
                    ];
                });

                // Get cash advance uses for this order
                $advanceUses = CustomerAdvanceUse::with([
                    'customerAdvance.customer'
                ])
                ->where('pos_order_id', $order->id)
                ->get()
                ->map(function ($use) {
                    return [
                        'id' => $use->id,
                        'customer_advance_id' => $use->customer_advance_id,
                        'customer_id' => $use->customerAdvance->customer_id ?? null,
                        'customer_name' => $use->customerAdvance->customer->name ?? null,
                        'order_no' => $use->customerAdvance->order_no ?? null,
                        'amount' => $use->amount,
                        'created_at' => $use->created_at
                    ];
                });

                // Get gold advance uses for this order
                $goldAdvanceUses = CustomerGoldAdvanceUse::with([
                    'customerGoldAdvance.customer',
                    'customerGoldAdvance.goldRate'
                ])
                ->where('pos_order_id', $order->id)
                ->get()
                ->map(function ($use) {
                    return [
                        'id' => $use->id,
                        'customer_gold_advance_id' => $use->customer_gold_advance_id,
                        'customer_id' => $use->customerGoldAdvance->customer_id ?? null,
                        'customer_name' => $use->customerGoldAdvance->customer->name ?? null,
                        'order_no' => $use->customerGoldAdvance->order_no ?? null,
                        'gold_amount' => $use->gold_amount,
                        'gold_rate' => $use->gold_rate,
                        'amount' => $use->amount,
                        'created_at' => $use->created_at
                    ];
                });

                // Format order details
                $orderDetails = $order->orderDetails->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'product_id' => $detail->product_id,
                        'product_name' => $detail->product->name ?? null,
                        'product_no' => $detail->product->product_no ?? null,
                        'category' => $detail->product->category->name ?? null,
                        'qty' => $detail->qty,
                        'weight' => $detail->weight,
                        'wastage_weight' => $detail->wastage_weight,
                        'stone_weight' => $detail->stone_weight,
                        'gold_rate' => $detail->gold_rate,
                        'making_charges' => $detail->making_charges,
                        'discount' => $detail->discount,
                        'amount' => $detail->amount
                    ];
                });

                // Format payments
                $payments = $order->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'reference_no' => $payment->reference_no,
                        'notes' => $payment->notes,
                        'is_credit_payment' => $payment->is_credit_payment,
                        'created_at' => $payment->created_at
                    ];
                });

                $creditPayments = Payment::with('posOrder:id,invoice_no')
                ->where('is_credit_payment', 1)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'invoice_no' => $payment->posOrder->invoice_no ?? null,
                        'pos_order_id' => $payment->pos_order_id,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'reference_no' => $payment->reference_no,
                        'notes' => $payment->notes,
                        'created_at' => $payment->created_at,
                    ];
                });


                // Build complete order data
                $responseData[] = [
                    'order' => [
                        'id' => $order->id,
                        'invoice_no' => $order->invoice_no,
                        'customer_id' => $order->customer_id,
                        'customer' => [
                            'id' => $order->customer->id,
                            'name' => $order->customer->name,
                            'address' => $order->customer->address,
                            'city' => $order->customer->city,
                            'tel' => $order->customer->tel,
                            'email' => $order->customer->email,
                            'nic' => $order->customer->nic
                        ],
                        'total' => $order->total,
                        'advance' => $order->advance,
                        'cash' => $order->cash,
                        'chq' => $order->chq,
                        'card' => $order->card,
                        'bank_transfer' => $order->bank_transfer,
                        'balance' => $order->balance,
                        'inclusive_tax' => $order->inclusive_tax,
                        'discount' => $order->discount,
                        'status' => $order->status,
                        'processed_by' => $order->processed_by,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at,
                        'is_credit_invoice' => $order->is_credit_invoice
                    ],
                    'order_details' => $orderDetails,
                    'payments' => $payments,
                    'gold_exchanges' => $goldExchanges,
                    'advance_uses' => $advanceUses,
                    'gold_advance_uses' => $goldAdvanceUses,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully',
                'data' => [
                    'date_range' => [
                        'start' => $startDate->toDateString(),
                        'end' => $endDate->toDateString()
                    ],
                    'total_orders' => count($responseData),
                    'orders' => $responseData,
                    'total_purchases' => $purchases->count(),
                    'purchases' => $purchases,
                    'total_advances' => $advances->count(),
                    'advances' => $advances,
                    'total_credit_payments' => $creditPayments->count(),
                    'credit_payments' => $creditPayments,
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}