<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .receipt-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Receipt</h1>
        <p>Receipt #{{ $receipt->receipt_number }}</p>
    </div>

    <div class="content">
        <p>Dear {{ $receipt->tenant->name }},</p>
        
        <p>Thank you for your payment! This email confirms that we have received your payment and a receipt has been generated.</p>

        <div class="receipt-box">
            <div class="amount">KSh {{ number_format($receipt->amount, 2) }}</div>
            
            <div class="info-row">
                <span class="info-label">Payment Type:</span>
                <span>{{ ucfirst($receipt->payment_type) }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Payment Method:</span>
                <span>{{ strtoupper($receipt->payment_method) }}</span>
            </div>
            
            @if($receipt->mpesa_receipt_number)
            <div class="info-row">
                <span class="info-label">M-Pesa Receipt:</span>
                <span>{{ $receipt->mpesa_receipt_number }}</span>
            </div>
            @endif
            
            <div class="info-row">
                <span class="info-label">Payment Date:</span>
                <span>{{ $receipt->payment->payment_date->format('F j, Y g:i A') }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Property:</span>
                <span>{{ $receipt->property->name }}</span>
            </div>
            
            @if($receipt->unit)
            <div class="info-row">
                <span class="info-label">Unit:</span>
                <span>{{ $receipt->unit->unit_number }}</span>
            </div>
            @endif
        </div>

        <p>A detailed PDF receipt is attached to this email for your records. Please keep this receipt for your reference.</p>

        @if($receipt->description)
        <div style="margin: 20px 0; padding: 15px; background-color: #e9ecef; border-radius: 4px;">
            <strong>Notes:</strong> {{ $receipt->description }}
        </div>
        @endif

        <p>If you have any questions about this payment or need any assistance, please don't hesitate to contact us.</p>

        <div class="footer">
            <p><strong>{{ $receipt->property->landlord->name }}</strong></p>
            @if($receipt->property->landlord->phone)
                <p>Phone: {{ $receipt->property->landlord->phone }}</p>
            @endif
            <p style="margin-top: 20px; font-size: 12px;">
                This is an automated email. Please do not reply to this email address.
            </p>
        </div>
    </div>
</body>
</html>
