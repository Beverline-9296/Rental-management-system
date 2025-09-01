<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .receipt-number {
            font-size: 14px;
            color: #666;
        }
        .content {
            margin: 20px 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        .info-label {
            font-weight: bold;
            width: 40%;
        }
        .info-value {
            width: 60%;
            text-align: right;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .payment-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            background-color: #28a745;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .two-column {
            width: 100%;
        }
        .two-column td {
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Rental Management System</div>
        <div class="receipt-title">PAYMENT RECEIPT</div>
        <div class="receipt-number">Receipt #{{ $receipt->receipt_number }}</div>
        <div style="margin-top: 10px; font-size: 14px;">
            Date: {{ $receipt->receipt_date->format('F j, Y') }}
        </div>
    </div>

    <div class="content">
        <div class="payment-summary">
            <div class="info-row">
                <span class="info-label">Amount Paid:</span>
                <span class="info-value amount">KSh {{ number_format($receipt->amount, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Type:</span>
                <span class="info-value">{{ ucfirst($receipt->payment_type) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Method:</span>
                <span class="info-value">{{ strtoupper($receipt->payment_method) }}</span>
            </div>
            @if($receipt->mpesa_receipt_number)
            <div class="info-row">
                <span class="info-label">M-Pesa Receipt:</span>
                <span class="info-value">{{ $receipt->mpesa_receipt_number }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Payment Date:</span>
                <span class="info-value">{{ $receipt->payment->payment_date->format('F j, Y g:i A') }}</span>
            </div>
        </div>

        <table class="two-column">
            <tr>
                <td>
                    <div class="section">
                        <div class="section-title">Tenant Information</div>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $receipt->tenant->name }}</span>
                        </div>
                        @if($receipt->tenant->email)
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $receipt->tenant->email }}</span>
                        </div>
                        @endif
                        @if($receipt->tenant->phone)
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $receipt->tenant->phone }}</span>
                        </div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="section">
                        <div class="section-title">Property Information</div>
                        <div class="info-row">
                            <span class="info-label">Property:</span>
                            <span class="info-value">{{ $receipt->property->name }}</span>
                        </div>
                        @if($receipt->property->address)
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $receipt->property->address }}</span>
                        </div>
                        @endif
                        @if($receipt->unit)
                        <div class="info-row">
                            <span class="info-label">Unit:</span>
                            <span class="info-value">{{ $receipt->unit->unit_number }}</span>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div class="section">
            <div class="section-title">Landlord Information</div>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $receipt->property->landlord->name }}</span>
            </div>
            @if($receipt->property->landlord->phone)
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $receipt->property->landlord->phone }}</span>
            </div>
            @endif
        </div>

        @if($receipt->description)
        <div class="section">
            <div class="section-title">Notes</div>
            <p>{{ $receipt->description }}</p>
        </div>
        @endif

        <div class="section">
            <div class="section-title">Receipt Status</div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="status-badge">{{ ucfirst($receipt->status) }}</span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Generated:</span>
                <span class="info-value">{{ $receipt->created_at->format('F j, Y g:i A') }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Thank you for your payment!</strong></p>
        <p>This is an official receipt for your payment. Please keep this for your records.</p>
        <p>Generated on {{ now()->format('F j, Y g:i A') }}</p>
    </div>
</body>
</html>
