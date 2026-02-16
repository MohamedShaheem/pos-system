<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Old Gold Purchase Invoice - {{ $purchaseOldGold->invoice_no }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            padding-top: 180px;
            margin: 20px;
            padding-top: 200px;
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
            margin-bottom: 6px;
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
            border: none;
            padding: 2px 8px;
            text-align: center;
        }
        .invoice-table th {
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
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
        .rs-col {
            min-width: 100px;
            text-align: right !important;
        }
        .empty-row {
            height: 20px;
        }
        .subtotal-row {
            font-weight: bold;
            border-top: 1px solid #333;
        }
        .balance-row {
            font-weight: bold;
            border-top: 2px solid #333;
            border-bottom: 1px solid #333;
        }
        .footer-info {
            margin-top: 30px;
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
            <div><strong>Customer Name:</strong> {{ $purchaseOldGold->customer->name }}</div>
            <div><strong>NIC:</strong> {{ $purchaseOldGold->customer->nic ?? 'N/A' }}</div>
            <div><strong>Address:</strong> {{ $purchaseOldGold->customer->address ?? '' }},{{ $purchaseOldGold->customer->city ?? '' }}</div>
            <div><strong>Contact:</strong> {{ $purchaseOldGold->customer->tel ?? 'N/A' }}</div>
        </div>
        <div style="font-size: 28px; text-decoration: underline; color: blue;">
            <strong>Purchase Bill</strong>
        </div>
        <div class="date-section">
            <div><strong>Invoice No:</strong> {{ $purchaseOldGold->invoice_no }}</div>
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
            <th class="rs-col">Amount (Rs)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($purchaseOldGold->details as $detail)
            <tr>
                <td class="description-col">Old Gold Purchase</td>
                <td>{{ $detail->goldRate->name ?? 'N/A' }}</td>
                <td>{{ number_format($detail->gold_gram, 3) }}</td>
                <td>-</td>
                <td class="rs-col">{{ number_format($detail->gold_purchased_amount, 2) }}</td>
            </tr>
        @endforeach

        <!-- Empty spacing rows -->
        <tr class="empty-row"><td colspan="5"></td></tr>
        <tr class="empty-row"><td colspan="5"></td></tr>

        <!-- Total Row -->
        <tr class="subtotal-row">
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td><strong>Total</strong></td>
            <td class="rs-col"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
        </tr>

        <!-- Cash Paid Row -->
        <tr class="balance-row">
            <td class="description-col"></td>
            <td></td>
            <td></td>
            <td><strong>Cash Paid</strong></td>
            <td class="rs-col"><strong>{{ number_format(($totalAmount - ($discount ?? 0)), 2) }}</strong></td>
        </tr>
        </tbody>
    </table>

    <!-- Footer Information -->
    <div class="footer-info mt-5">
        <!-- Signature Section -->
        <div class="d-flex justify-content-between mt-4">
            <div class="text-center">
                <div style="border-top: 2px dotted #000; width: 150px; margin-bottom: 2px;"></div>
                <strong>Customer Signature</strong>
            </div>
            <div class="text-center">
                <div style="border-top: 2px dotted #000; width: 150px; margin-bottom: 2px;"></div>
                <strong>Authorized Signature</strong>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();

        window.onafterprint = function() {
            window.location.href = "{{ route('purchase-old-gold.index') }}";
        };

        setTimeout(function() {
            window.location.href = "{{ route('purchase-old-gold.index') }}";
        }, 1000);
    };
</script>
</body>
</html>
