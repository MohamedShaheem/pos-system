<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Invoice - {{ $posOrder->invoice_no }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            padding-top: 200px; 
            margin: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: none;
            outline: none;
            padding: 20px;
        }
        .header-info {
            margin-bottom: 20px;
        }
        .header-info div {
            margin-bottom: 8px;
        }
        .date-section {
            text-align: right;
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th,
        .invoice-table td {
            border: none; /* Removed all borders */
            padding: 2px 8px; /* Reduced vertical padding from 8px to 2px */
            text-align: center;
        }
        .invoice-table th {
            background-color: transparent; /* Removed background color */
            font-weight: bold;
            border-bottom: 1px solid #333; /* Only bottom border for header */
            padding-bottom: 8px;
            margin-bottom: 5px;
        }
        .description-col {
            text-align: left !important;
            min-width: 200px;
        }
        .net-wt-col {
            min-width: 80px;
        }
        .wastage-col {
            min-width: 80px;
        }
        .making-col {
            min-width: 100px;
        }
        .rs-col {
            min-width: 100px;
            text-align: right !important;
        }
        .footer-info {
            margin-top: 20px;
        }
        .empty-row {
            height: 20px; /* Reduced from 40px to 20px */
        }
        .total-row {
            font-weight: bold;
            background-color: transparent; /* Removed background */
            border-top: 1px solid #333; /* Added top border for totals */
        }
        .advance-row {
            color: blue;
        }
        .subtotal-row {
            background-color: transparent; /* Removed background */
            font-weight: bold;
            border-top: 1px solid #333; /* Added subtle top border */
        }
        .balance-row {
            background-color: transparent; /* Removed background */
            font-weight: bold;
            border-top: 2px solid #333; /* Stronger border for final amount */
            border-bottom: 1px solid #333;
        }
        .amount-hidden {
            visibility: hidden;
        }
        
        /* Add spacing only for important sections */
        .section-divider {
            border-top: 1px solid #ddd;
            margin-top: 10px;
            padding-top: 5px;
        }
        
        /* Reduce line height for tighter spacing */
        .invoice-table tbody tr {
            line-height: 1.2;
        }
        
        @media print {
            body {
                margin: 0;
            }
            .invoice-container {
                border: none;
                margin: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <!-- Header with Date -->
    <div style="display: flex; justify-content: space-between;">
        <div class="header-info">
            <div><strong>Customer Name:</strong> {{ $posOrder->customer->name }}</div>
            <div><strong>Address:</strong> {{ $posOrder->customer->address }}, {{ $posOrder->customer->city }}</div>
            <div><strong>Contact:</strong> {{ $posOrder->customer->tel ?? 'N/A' }}</div>
        </div>
        <div class="date-section">
            <div><strong>Date:</strong> {{ $formattedDate }}</div>
        </div>
    </div>

    <!-- Invoice Table -->
    <table class="invoice-table">
        <thead>
        <tr>
            <th class="description-col">Description</th>
            <th>Ct.</th>
            <th class="net-wt-col">Net Wt</th>
            <th class="wastage-col">Wastage</th>
            <th >St.W</th> {{--  stone weight --}}
            <th></th>
            <th class="rs-col">Amount (Rs)</th>
        </tr>
        </thead>
        <tbody>
        <!-- Product Rows -->
        @foreach($posOrder->orderDetails as $detail)
        @php
            $productDiscount = $detail->discount;
            $productAmount = $detail->amount;
            $productFinalAmount = $productAmount + $productDiscount;
        @endphp
            <tr>
                <td class="description-col">{{ $detail->product->name }} ({{ $detail->product->product_no }})</td>
                <td>{{ $detail->product->goldRate->name ?? 'N/A' }}</td>
                <td>{{ number_format($detail->weight, 3) }}</td>
                <td>{{ number_format($detail->wastage_weight, 3) }}</td>
                <td>{{ number_format($detail->stone_weight, 3) }}</td>
                <td></td>
                @if($goldAdvanceUsed == 0)
                <td class="rs-col">{{ number_format($productFinalAmount, 2) }}</td>
                @endif
            </tr>
        @endforeach

        <!-- Gold Advance Used Row -->
        @if($goldAdvanceUsed > 0)
            <tr class="advance-row">
                <td class="description-col">
                    A/D Gold
                    @if($goldAdvanceOrderNo)
                        <small>(Order No: {{ $goldAdvanceOrderNo }})</small>
                    @endif
                </td>
                <td>{{ $goldAdvanceUsedGoldRateName ?? 'N/A' }}</td>
                <td>- {{ number_format($goldAdvanceUsed, 3) }}</td>
                <td></td>
                <td></td>
                <td></td>
                
                <td class="rs-col amount-hidden">{{ number_format($goldAdvanceAmount, 2) }}</td>
            </tr>
        @endif

        @php
            // Calculate total exchange gold amount and weight
            $totalExchangeGoldWeight = $posOrder->customerGoldExchanges->sum('gold_weight');
            $totalExchangeGoldAmount = $posOrder->customerGoldExchanges->sum('gold_purchased_amount');
            $totalDiscountAmount = $posOrder->discount;

            // Adjusted net weight after gold advance and exchange gold
            $adjustedNetWeight = $totalNetWeight - $goldAdvanceUsed;

            $subtotalWeight = $adjustedNetWeight + $totalWastageWeight - $totalStoneWeight;

            // Subtotal before cash advance deduction
            $subtotalAmount = $totalProductAmount - $goldAdvanceAmount + $totalDiscountAmount;

            // Amount after cash advance deduction
            $afterAdvancesAmount = $subtotalAmount - $cashAdvanceUsed - $totalExchangeGoldAmount; // this subtotal

            $afterAdvancesDiscountSubtotalAmount = $subtotalAmount - $cashAdvanceUsed - $totalExchangeGoldAmount - $totalDiscountAmount; // this subtotal after dicount deducted
        @endphp

        @if($goldAdvanceUsed > 0) 
        <!-- Subtotal Row -->
        <tr class="subtotal-row">
            <td class="description-col"></td>
            <td></td>
            <td><strong>{{ number_format($adjustedNetWeight, 3) }}</strong></td>
            <td><strong>{{ number_format($totalWastageWeight, 3) }}</strong></td>
            <td><strong>{{  number_format($totalStoneWeight, 3)  }}<strong></td>
            <td><strong>{{  number_format($subtotalWeight, 3)  }}</strong></td>
            <td class="rs-col"><strong>{{ number_format($subtotalAmount, 2) }}</strong></td>
        </tr>
        @endif

        <!-- Cash Advance Used Row -->
        @if($cashAdvanceUsed > 0)
            <tr class="advance-row">
                <td class="description-col">
                    A/D Cash
                    @if($cashAdvanceOrderNo)
                        <small>(Order No: {{ $cashAdvanceOrderNo }})</small>
                    @endif
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td >- {{ number_format($cashAdvanceUsed, 2) }}</td>
                <td class="rs-col"></td>

            </tr>
        @endif



        @php
            $totalGoldPurchaseAmount = $posOrder->customerGoldExchanges->sum('gold_purchased_amount') ?? 0;
        @endphp
        <!-- Exchange Gold Rows -->
        @if($posOrder->customerGoldExchanges->isNotEmpty())
           

            @foreach($posOrder->customerGoldExchanges as $exchange)
                <tr class="advance-row">
                    <td class="description-col">Exchange Item</td>
                    <td>{{ $exchange->goldRate->name ?? 'N/A' }}</td>
                    <td>{{ number_format($exchange->gold_weight, 3) }}</td>
                    <td>0.000</td>
                    <td></td>
                    <td>- {{ number_format($exchange->gold_purchased_amount, 2) }} </td>
                    <td class="rs-col" >
                         {{-- @if($loop->last)
                           - {{ number_format($totalGoldPurchaseAmount, 2) }}
                         @endif --}}
                    </td>
                </tr>
            @endforeach
        @endif


        @php
            $totolAdandExchange = $totalGoldPurchaseAmount + $cashAdvanceUsed;
        @endphp

        {{-- Ad and exchange amount cal row --}}
         @if($cashAdvanceUsed > 0 || $posOrder->customerGoldExchanges->isNotEmpty())

        <tr>
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="border-top: 1px solid black;"><strong>- {{ number_format($totolAdandExchange, 2) }}</strong></td>
            <td class="rs-col" style="border-top: 1px solid black;"><strong>{{ number_format($subtotalAmount, 2) }}</strong></td>
        </tr>
        @endif

        <!-- Final Subtotal after all advances and exchange calculated and Discount added -->
        <tr class="subtotal-row">
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Sub total</strong></td>
                <td></td>
            @if($afterAdvancesAmount < 0)
                <td class="rs-col" style="color: blue;"><strong>{{ number_format($afterAdvancesAmount, 2) }}</strong></td>
            @else
                <td class="rs-col"><strong>{{ number_format($afterAdvancesAmount, 2) }}</strong></td>
            @endif
        </tr>
        
        @if($discount > 0)
        <!-- Discount Row -->
        <tr class="empty-row">
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Discount</strong></td>
            <td></td>
            <td class="rs-col"><strong>- {{ number_format($discount, 2) }}</strong></td>
        </tr>
        @endif


        <!-- Final Balance -->
        <tr>
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-size: 14px; font-weight: 600;"><strong>Balance</strong></td>
            <td></td>
            @if($afterAdvancesDiscountSubtotalAmount < 0)
                <td class="rs-col" style="font-size: 14px; font-weight: 600; color: blue; border-top: 1px solid black;"><strong>{{ number_format($afterAdvancesDiscountSubtotalAmount, 2) }}</strong></td>
            @else
                <td class="rs-col" style="font-size: 14px; font-weight: 600; border-top: 1px solid black;"><strong>{{ number_format($afterAdvancesDiscountSubtotalAmount, 2) }}</strong></td>
            @endif
        </tr>


        <!-- Chq Payment Row -->
        @if($chqPaymentAtPurchase > 0)
        <tr>
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Cheque</strong></td>
            <td></td>
            <td class="rs-col"><strong>- {{ number_format($chqPaymentAtPurchase, 2) }}</strong></td>
        </tr>
        @endif

        <!-- bank transfer Payment Row -->
        @if($bankPaymentAtPurchase > 0)
        <tr>
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Bank Transfer</strong></td>
            <td></td>
            <td class="rs-col"><strong>- {{ number_format($bankPaymentAtPurchase, 2) }}</strong></td>
        </tr>
        @endif

        <!-- Card Payment Row -->
        @if($cardPaymentAtPurchase > 0)
        <tr>
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Card Payment</strong></td>
            <td></td>
            <td class="rs-col"><strong>- {{ number_format($cardPaymentAtPurchase, 2) }}</strong></td>
        </tr>
        @endif

        <!-- Cash Payment Row -->
        <tr>
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Cash payment</strong></td>
            <td></td>
            @if($cashPaymentAtPurchase == 0)
                <td class="rs-col"><strong>0.00</strong></td>
            @else
                <td class="rs-col"><strong>- {{ number_format($cashPaymentAtPurchase, 2) }}</strong></td>
            @endif
        </tr>


        <!-- Amount Due Row -->
        <tr class="balance-row">
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td></td>
            <td><strong>Amount Due</strong></td>
            <td></td>

            @if($finalBalance < 0)
                <td class="rs-col" style="color: blue;"><strong>{{ number_format($finalBalance, 2) }}</strong></td>
            @else
                <td class="rs-col"><strong>{{ number_format($finalBalance, 2) }}</strong></td>
            @endif
        </tr>
        </tbody>
    </table>

    <!-- Footer Information -->
    <div class="footer-info mt-5 d-flex justify-content-between">
        <div><strong>Invoice No:</strong> {{ $posOrder->invoice_no }}</div>
        <div class="text-end">
            <div style="border-top: 2px dotted #000; width: 150px; margin-bottom: 2px;"></div>
            <strong>Authorized Signature</strong>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
       
        // Get customer ID from the advance/receipt data
        const customerId = {{ $posOrder->customer->id }};

        const route = "{{ $route }}"; // Fixed: Added quotes around route variable
       
        // Determine the redirect URL based on available customer data
        let redirectUrl = "{{ route('dashboard') }}"; // fallback
       
        if (route === 'customer-transaction') { // Fixed: Use strict equality
            // Redirect to customer transactions page
            redirectUrl = `/customer-transactions/${customerId}`;
        }
       
        // Redirect after print dialog is handled
        window.onafterprint = function() {
            // Use window.location.replace() to avoid opening new tabs
            window.location.replace(redirectUrl);
        };
       
        // Fallback if user cancels print dialog
        setTimeout(function() {
            window.location.replace(redirectUrl);
        }, 1000);
       
        // Additional fallback for better browser compatibility
        // This handles cases where onafterprint might not work properly
        setTimeout(function() {
            if (!document.hidden) { // Only redirect if page is still visible
                window.location.replace(redirectUrl);
            }
        }, 3000);
    };
</script>
</body>
</html>