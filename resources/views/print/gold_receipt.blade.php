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
            width: 160px;
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
            <div class="col-8">{{ $customer->tel ?? '' }}</div>
        </div>
        @if ($advance->order_no)
        <div class="row">
            <div class="col-4 detail-label">Order No:</div>
            <div class="col-8">{{ $advance->order_no }}</div>
        </div>
        @endif
        <div class="row">
            <div class="col-4 detail-label">Advance For:</div>
            <div class="col-8">{{ $detail->note ?? 'A/D Gold' }}</div>
        </div>
        <div class="row">
            <div class="col-4 detail-label">Gold Amount:</div>
            <div class="col-8">{{ number_format($amount, 3) }} g</div>
        </div>
        <div class="row">
            <div class="col-4 detail-label">Amount in Words:</div>
            <div class="col-8">{{ $amountInWords }}</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        <p><strong>Note:</strong> Please collect your order/item within 14 days from the due date. 
        Prices may change or a new order may be required thereafter.</p>
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
