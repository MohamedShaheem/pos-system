<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Advance Receipt - {{ $receiptNo }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 20px;
            padding-top: 200px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .receipt-details .row {
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 180px;
        }
        .footer-note {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
        .signature-section {
            margin-top: 40px;
            text-align: right;
        }
        .signature-line {
            border-top: 2px dotted #000;
            width: 200px;
            margin-left: auto;
            margin-bottom: 5px;
        }
        .terms {
            font-size: 11px;
            margin-top: 20px;
            text-align: center;
            color: #666;
        }
        @media print {
            body {
                margin: 0;
            }
            .receipt-container {
                max-width: 100%;
                margin: 0;
                padding: 15mm;
            }
        }
    </style>
</head>
<body>
<div class="receipt-container">
    <!-- Receipt Info -->
    <div class="receipt-header">
        <div><strong>Receipt No:</strong> {{ $receiptNo }}</div>
         <div style="font-size: 22px; text-decoration: underline;"><strong>Advance Bill</strong></div>
        <div><strong>Date:</strong> {{ $formattedDate }}</div>
    </div>

    <!-- Details -->
    <div class="receipt-details">
        <div class="row">
            <div class="col-4 detail-label">Received From:</div>
            <div class="col-8">{{ $customer->name }}</div>
        </div>
        <div class="row">
            <div class="col-4 detail-label">Contact Number:</div>
            <div class="col-8">{{ $customer->tel ?? '-' }}</div>
        </div>
        <div class="row">
            <div class="col-4 detail-label">Order No:</div>
            <div class="col-8">{{ $orderNo }}</div>
        </div>
        <div class="row">
            <div class="col-4 detail-label">Advance For:</div>
            <div class="col-8">{{ $note ?? 'A/D Cash and Gold' }}</div>
        </div>

        <!-- Cash Advance -->
        <div class="row">
            <div class="col-4 detail-label">Cash Advance:</div>
            <div class="col-8">Rs. {{ number_format($totalCash, 2) }}</div>
        </div>

        <!-- Gold Advance -->
        <div class="row">
            <div class="col-4 detail-label">Gold Advance:</div>
            <div class="col-8">
                @forelse ($goldDetails as $goldDetail)
                    <div>{{ number_format($goldDetail->gold_amount, 3) }} g ({{ $goldDetail->customerGoldAdvance->goldRate->name ?? '' }})</div>
                @empty
                    <div>No gold advance</div>
                @endforelse
            </div>
        </div>

        <!-- Amount in Words -->
        <div class="row">
            <div class="col-4 detail-label">Amount in Words:</div>
            <div class="col-8">{{ $amountInWords }}</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        <p><strong>Note:</strong> Please collect your order/item within 14 days. Delays may affect pricing or require a new order.</p>
    </div>

    <div class="signature-section">
        <div class="signature-line"></div>
        <div>Authorized Signature</div>
    </div>

    <div class="terms">
        <p>Thank you for your business!</p>
        <p>This receipt is valid for advance payment verification.</p>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
       
        // Get customer ID from the advance/receipt data
        const customerId = {{ $customer->id }};
        const route = "{{ $route }}";
       
        let redirectUrl = "{{ route('dashboard') }}"; // fallback
       
        if (route === 'customer-transaction') {
            redirectUrl = `/customer-transactions/${customerId}`;
        }
       
        window.onafterprint = function() {
            window.location.replace(redirectUrl);
        };
       
        setTimeout(function() {
            window.location.replace(redirectUrl);
        }, 1000);
       
        setTimeout(function() {
            if (!document.hidden) {
                window.location.replace(redirectUrl);
            }
        }, 3000);
    };
</script>
</body>
</html>
