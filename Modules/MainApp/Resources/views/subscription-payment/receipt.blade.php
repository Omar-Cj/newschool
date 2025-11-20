<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['title'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            color: #333;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            color: #2c3e50;
        }
        .invoice-number {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
        }
        .info-label {
            font-weight: bold;
            width: 40%;
        }
        .info-value {
            width: 60%;
            text-align: right;
        }
        .amount-section {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 30px 0;
            border: 2px solid #2c3e50;
            text-align: center;
        }
        .amount-label {
            font-size: 16px;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #27ae60;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #333;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: #27ae60;
            color: white;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <h1>PAYMENT RECEIPT</h1>
            <div class="invoice-number">{{ $data['payment']->invoice_number }}</div>
            <div style="margin-top: 10px;">
                <span class="status-badge">PAID</span>
            </div>
        </div>

        <!-- School Information -->
        <div class="section">
            <div class="section-title">School Information</div>
            <div class="info-row">
                <div class="info-label">School Name:</div>
                <div class="info-value">{{ $data['payment']->school->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $data['payment']->school->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $data['payment']->school->phone }}</div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="section">
            <div class="section-title">Payment Details</div>
            <div class="info-row">
                <div class="info-label">Payment Date:</div>
                <div class="info-value">{{ dateFormat($data['payment']->payment_date) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Method:</div>
                <div class="info-value">{{ $data['payment']->getPaymentMethodLabel() }}</div>
            </div>
            @if($data['payment']->reference_number)
            <div class="info-row">
                <div class="info-label">Reference Number:</div>
                <div class="info-value">{{ $data['payment']->reference_number }}</div>
            </div>
            @endif
            @if($data['payment']->transaction_id)
            <div class="info-row">
                <div class="info-label">Transaction ID:</div>
                <div class="info-value">{{ $data['payment']->transaction_id }}</div>
            </div>
            @endif
        </div>

        <!-- Subscription Details -->
        <div class="section">
            <div class="section-title">Subscription Details</div>
            <div class="info-row">
                <div class="info-label">Package:</div>
                <div class="info-value">{{ $data['payment']->subscription->package->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Subscription Expiry:</div>
                <div class="info-value">{{ dateFormat($data['payment']->subscription->expiry_date) }}</div>
            </div>
        </div>

        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-label">Total Amount Paid</div>
            <div class="amount-value">{{ number_format($data['payment']->amount, 2) }}</div>
        </div>

        <!-- Approval Information -->
        <div class="section">
            <div class="section-title">Approval Information</div>
            <div class="info-row">
                <div class="info-label">Approved By:</div>
                <div class="info-value">{{ $data['payment']->approver->name ?? 'Admin' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Approved Date:</div>
                <div class="info-value">{{ dateFormat($data['payment']->approved_at) }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>Generated on {{ dateFormat(now()) }}</p>
            <p>Thank you for your payment!</p>
        </div>
    </div>
</body>
</html>
