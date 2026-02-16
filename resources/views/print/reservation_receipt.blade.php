<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewel Plaza - Reservation Receipt - {{ $receiptNo }}</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 20px;
            padding-top: 200px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            padding-top: 50px; 
        }
        
        .receipt-header {
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: bold;
            width: 130px;
        }

        .detail-value {
            flex: 1;
            border-bottom: 0.2px solid #000;
            min-height: 20px;
            padding-left: 5px;
        }

        .gold-detail {
            margin-left: 20px;
        }

        .footer-note {
            font-size: 11px;
            text-align: center;
            border-top: 1px solid #000;
        }

        .signature-section {
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            width: 150px;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 40px;
            margin-bottom: 5px;
        }

        .terms {
            font-size: 10px;
            margin-top: 10px;
            text-align: center;
            color: #666;
        }

        @page {
            size: A5;
            margin: 0;
        }

        @media print {
            body {
                width: 148mm;
                height: 210mm;
                margin: 0;
                padding: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
    <div class="receipt-header">
        <div class="receipt-no">Receipt No: {{ $receiptNo }}</div>
         <div style="font-size: 22px; text-decoration: underline;"><strong>Reservation Bill</strong></div>
        <div class="date">Date: {{ $formattedDate }}</div>
    </div>

    <div class="receipt-details">
        <div class="detail-row">
            <div class="detail-label">RECEIVED FROM:</div>
            <div class="detail-value">{{ $customer->name }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">CONTACT NUMBER:</div>
            <div class="detail-value">{{ $customer->tel ?? '' }}</div>
        </div>

        {{-- <div class="detail-row">
            <div class="detail-label">ORDER NO:</div>
            <div class="detail-value">{{ $orderNo }}</div>
        </div> --}}

        <div class="detail-row">
            <div class="detail-label">ADVANCE FOR:</div>
            <div class="detail-value">{{ $productNames }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">INITIAL PAYMENT:</div>
            <div class="detail-value">Rs {{ number_format($totalCash, 2) }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">AMOUNT IN WORDS:</div>
            <div class="detail-value">{{ $amountInWords }}</div>
        </div>
    </div>

    <div class="footer-note">
        <p><strong>Note:</strong> Please collect your order/item within 14 days from the due date. If not, there will be some changes in the prices or you have to make another order.</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Authorized Signature</div>
        </div>
    </div>

    <div class="terms">
        <p>Thank you for your business!</p>
        <p>This receipt is valid for advance payment verification.</p>
    </div>
    </div>
    <script>
        window.onload = function () {
            window.print();

            const customerId = {{ $customer->id }};
            const route = "{{ $route }}";

            let redirectUrl = "{{ route('dashboard') }}";

            if (route === 'customer-transaction') {
                redirectUrl = `/customer-transactions/${customerId}`;
            }

            window.onafterprint = function () {
                window.location.replace(redirectUrl);
            };

            setTimeout(function () {
                window.location.replace(redirectUrl);
            }, 1000);

            setTimeout(function () {
                if (!document.hidden) {
                    window.location.replace(redirectUrl);
                }
            }, 3000);
        };
    </script>
</body>
</html>
