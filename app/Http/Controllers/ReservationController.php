<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerReservation;
use App\Models\CustomerReservationDetail;
use App\Models\Product;
use App\Models\CustomerReservationPayment;
use App\Models\POSOrder;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use NumberToWords\NumberToWords;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = CustomerReservation::with(['customer', 'reservationDetails.product'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'completed' THEN 1 WHEN status = 'cancelled' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reservations.index', compact('reservations'));
    }

    public function printReservationReceipt($reservationId, $routeName)
{
    try {
        $reservation = CustomerReservation::with(['customer', 'reservationDetails.product'])
            ->findOrFail($reservationId);

        $customer = $reservation->customer;
        $orderNo = $reservation->id; // or use another order_no field if applicable
        $formattedDate = $reservation->created_at->format('d/m/Y');
        $receiptNo = $reservation->id;
        $route = $routeName;

        $totalCash = $reservation->paid_amount;

        // Generate comma-separated product names
        $productNames = $reservation->reservationDetails
            ->map(function ($detail) {
                return optional($detail->product)->name;
            })
            ->filter()
            ->implode(', ');

        $amountInWords = ucfirst((new NumberToWords())
            ->getNumberTransformer('en')
            ->toWords($totalCash)) . ' rupees only';

        return view('print.reservation_receipt', compact(
            'customer',
            'orderNo',
            'formattedDate',
            'receiptNo',
            'route',
            'productNames',
            'totalCash',
            'amountInWords',
            'reservation'
        ));

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error generating reservation receipt: ' . $e->getMessage()
        ], 500);
    }
}

    public function store(Request $request, $customerId)
    {
        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0.01',
            'initial_payment' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|date'
        ]);

        try {
            $customer = Customer::findOrFail($customerId);

            DB::beginTransaction();

            // Validate all products are available
            $totalCalculated = 0;

            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                if ($product->status !== 'active') {
                    throw new \Exception("Product {$product->name} is not available for reservation");
                }

                // Check if sufficient quantity is available
                if ($product->qty < $productData['quantity']) {
                    throw new \Exception("Insufficient quantity for product {$product->name}. Available: {$product->qty}, Requested: {$productData['quantity']}");
                }

                $lineTotal = $productData['quantity'] * $productData['unit_price'];
                $totalCalculated += $lineTotal;
            }

            // Validate total amount
            if (abs($totalCalculated - $request->total_amount) > 0.01) {
                throw new \Exception("Total amount mismatch. Calculated: {$totalCalculated}, Provided: {$request->total_amount}");
            }

            $initialPayment = $request->initial_payment ?? 0;
            
            // Create single reservation record
            $reservation = CustomerReservation::create([
                'customer_id' => $customer->id,
                'total_amount' => $request->total_amount,
                'paid_amount' => $initialPayment,
                'status' => ($initialPayment >= $request->total_amount) ? 'completed' : 'pending',
                'delivery_date' => $request->delivery_date,
            ]);

            // Create reservation details for each product
            foreach ($request->products as $productData) {
               $newDetail =  CustomerReservationDetail::create([
                    'reservation_id' => $reservation->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price']
                ]);
            }

            // Record initial payment if provided
            if ($initialPayment > 0) {
                CustomerReservationPayment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $initialPayment,
                    'payment_method' => 'cash',
                    'notes' => 'Initial payment for reservation'
                ]);
            }

            // Update product quantities and status
            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                // Decrement quantity
                $product->decrement('qty', $productData['quantity']);
                
                // Mark as reserved if quantity becomes 0
                if ($product->qty <= 0) {
                    $product->update(['status' => 'reserved']);
                }
            }

            $routeName = "reservation";
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Products reserved successfully',
                'reservation' => $reservation->load(['customer', 'reservationDetails']),
                'print_url' => route('print.reservation.receipt', [
                    'reservationId' => $reservation->id,
                    'routeName' => $routeName
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating reservation: ' . $e->getMessage()
            ], 500);
        }
    }


    public function convertToPOSOrder(Request $request, $reservationId)
{
    try {
        $reservation = CustomerReservation::with(['customer', 'reservationDetails.product'])->findOrFail($reservationId);
        
        // Check if reservation is completed
        if ($reservation->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed reservations can be converted to POS orders'
            ], 400);
        }

        // Check if already converted
        if ($reservation->pos_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'This reservation has already been converted to a POS order'
            ], 400);
        }

        // Validate that reservation has details
        if ($reservation->reservationDetails->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No product details found in this reservation'
            ], 400);
        }

        // Validate all products still exist
        foreach ($reservation->reservationDetails as $detail) {
            if (!$detail->product) {
                return response()->json([
                    'success' => false,
                    'message' => "Product with ID {$detail->product_id} no longer exists"
                ], 400);
            }
        }

        $taxRate = DB::table('tax_rates')->value('rate') ?? 0;

        // Calculate inclusive tax
        $inclusiveTax = ($reservation->total_amount * $taxRate) / (100 + $taxRate);

        DB::beginTransaction();

        // Create POS Order
        $posOrder = POSOrder::create([
            'customer_id' => $reservation->customer_id,
            'total' => $reservation->total_amount,
            'advance' => $reservation->paid_amount,
            'balance' => 0, // No balance remaining since it's fully paid
            'inclusive_tax' => $inclusiveTax,
            'status' => 'complete',
        ]);

        // Create Order Details for each product
        foreach ($reservation->reservationDetails as $detail) {
            // Calculate line total
            $lineTotal = $detail->quantity * $detail->unit_price;

            // Get gold rate from product's relationship
            $goldRate = $detail->product && $detail->product->goldRate 
                ? $detail->product->goldRate->rate 
                : 0;
            
            OrderDetail::create([
                'pos_order_id' => $posOrder->id,
                'product_id' => $detail->product_id,
                'qty' => $detail->quantity,
                'weight' => $detail->product->weight ?? 0, // Get from product if available
                'amount' => $lineTotal,
                'making_charges' => $detail->product->making_charges ?? 0,
                'wastage_weight' => $detail->product->wastage_weight ?? 0,
                'stone_weight' => $detail->product->stone_weight ?? 0,
                'gold_rate' => $goldRate
            ]);

            // Update product status to sold
            $detail->product->update(['status' => 'sold']);
        }

        // Update reservation with POS order reference and set delivery date
        $reservation->update([
            'pos_order_id' => $posOrder->id,
            'delivery_date' => now()->toDateString()
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Reservation successfully converted to POS order',
            'pos_order_id' => $posOrder->id,
            'invoice_no' => $posOrder->invoice_no ?? 'Generated automatically'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
    
        return response()->json([
            'success' => false,
            'message' => 'Error converting reservation: ' . $e->getMessage()
        ], 500);
    }
}


    public function cancel($reservationId)
    {
        try {
            $reservation = CustomerReservation::with('reservationDetails')->findOrFail($reservationId);

            // Check if reservation is already cancelled
            if ($reservation->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation is already cancelled.'
                ], 400);
            }

            // Check if reservation has been converted to POS order
            if ($reservation->pos_order_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel reservation that has been converted to a POS order.'
                ], 400);
            }

            DB::beginTransaction();

            $paidAmount = $reservation->paid_amount;

            // Restore product quantities and status
            foreach ($reservation->reservationDetails as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    // Restore the original quantity that was reserved
                    $product->increment('qty', $detail->quantity);
                    
                    // Set status back to active
                    $product->update(['status' => 'active']);
                }
            }

            // Update reservation status
            $reservation->update([
                'status' => 'cancelled'
            ]);

            // Record refund transaction if there was a paid amount
            if ($paidAmount > 0) {
                CustomerReservationPayment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => -$paidAmount, // Negative amount to indicate refund
                    'payment_method' => 'refund',
                    'notes' => 'Reservation cancelled – amount refunded to customer'
                ]);

                // Reset paid amount to 0
                $reservation->update(['paid_amount' => 0]);
            }

            DB::commit();

            $message = $paidAmount > 0 
                ? "Reservation cancelled successfully. Amount of Rs " . number_format($paidAmount, 2) . " refunded to customer."
                : "Reservation cancelled successfully.";

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error cancelling reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($reservationId)
    {
        try {
            $reservation = CustomerReservation::with(['customer', 'reservationDetails.product'])->findOrFail($reservationId);
            
            // Enrich reservation details with current product info
            $enrichedDetails = $reservation->reservationDetails->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'product_name' => $detail->product_name, // Uses accessor
                    'quantity' => $detail->quantity,
                    'unit_price' => $detail->unit_price,
                    'line_total' => $detail->line_total, // Uses accessor
                    'weight' => $detail->weight, // Uses accessor
                    'making_charges' => $detail->making_charges, // Uses accessor
                    'wastage_weight' => $detail->wastage_weight, // Uses accessor
                    'stone_weight' => $detail->stone_weight, // Uses accessor
                    'gold_rate' => $detail->gold_rate, // Uses accessor
                    'current_product_status' => $detail->product ? $detail->product->status : 'deleted',
                    'current_product_qty' => $detail->product ? $detail->product->qty : 0,
                ];
            });

            return response()->json([
                'success' => true,
                'reservation' => $reservation,
                'product_details' => $enrichedDetails
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching reservation details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomerReservations(Customer $customer)
    {
        $reservations = $customer->reservations()
            ->with(['payments', 'reservationDetails'])
            ->orderBy('delivery_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'reservations' => $reservations->map(function($reservation) {
                return [
                    'id' => $reservation->id,
                    'total_amount' => $reservation->total_amount,
                    'paid_amount' => $reservation->paid_amount,
                    'status' => $reservation->status,
                    'delivery_date' => $reservation->delivery_date,
                    'created_at' => $reservation->created_at,
                    'products_count' => $reservation->reservationDetails->count(),
                ];
            })
        ]);
    }

    // Other methods (addPayment, getPaymentHistory, deletePayment) remain the same
    public function addPayment(Request $request, $reservationId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'notes' => 'nullable|string'
        ]);

        try {
            $reservation = CustomerReservation::findOrFail($reservationId);
            
            // Check if reservation is already completed
            if ($reservation->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'This reservation is already completed'
                ], 400);
            }

            // Calculate remaining balance
            $remainingBalance = $reservation->total_amount - $reservation->paid_amount;
            
            // Check if payment amount exceeds remaining balance
            if ($request->amount > $remainingBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed remaining balance of Rs' . number_format($remainingBalance, 2)
                ], 400);
            }

            DB::beginTransaction();

            // Create payment record
            CustomerReservationPayment::create([
                'reservation_id' => $reservation->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes
            ]);

            // Update reservation paid amount
            $reservation->increment('paid_amount', $request->amount);
            $reservation->refresh();

            // Check if fully paid
            $isCompleted = false;
            if ($reservation->paid_amount >= $reservation->total_amount) {
                $reservation->update([
                    'status' => 'completed'
                ]);
                $isCompleted = true;
            }

            DB::commit();

            $newBalance = $reservation->total_amount - $reservation->paid_amount;

            return response()->json([
                'success' => true,
                'new_balance' => $newBalance,
                'is_completed' => $isCompleted,
                'paid_amount' => $reservation->paid_amount,
                'total_amount' => $reservation->total_amount,
                'message' => 'Payment recorded successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error recording payment: ' . $e->getMessage()
            ], 500);
        }
    }

public function getPaymentHistory($reservationId)
{
    try {
        $reservation = CustomerReservation::with(['customer', 'reservationDetails.product'])->findOrFail($reservationId);
        $payments = CustomerReservationPayment::where('reservation_id', $reservationId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'reservation' => $reservation,
            'payments' => $payments
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching payment history: ' . $e->getMessage()
        ], 500);
    }
}
    public function deletePayment(Request $request, $reservationId, $paymentId)
    {
        try {
            $reservation = CustomerReservation::findOrFail($reservationId);
            $payment = CustomerReservationPayment::where('reservation_id', $reservationId)
                ->where('id', $paymentId)
                ->firstOrFail();

            DB::beginTransaction();

            // Decrease paid amount
            $reservation->decrement('paid_amount', $payment->amount);
            $reservation->refresh();

            // Update status if needed
            if ($reservation->paid_amount < $reservation->total_amount) {
                $reservation->update(['status' => 'pending']);
            }

            // Delete payment record
            $payment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully',
                'new_balance' => $reservation->total_amount - $reservation->paid_amount,
                'paid_amount' => $reservation->paid_amount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payment: ' . $e->getMessage()
            ], 500);
        }
    }
}