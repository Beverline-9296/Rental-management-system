<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .payment-details {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .payment-details h3 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 16px;
        }
        .detail-label {
            color: #6c757d;
            font-weight: 500;
        }
        .detail-value {
            color: #2c3e50;
            font-weight: 600;
        }
        .amount {
            color: #28a745;
            font-size: 20px;
        }
        .custom-message {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-style: italic;
        }
        .payment-methods {
            margin: 25px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .payment-methods h4 {
            margin-top: 0;
            color: #2c3e50;
        }
        .method {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .contact-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e8;
            border-radius: 5px;
        }
        .urgency-high {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }
        .urgency-overdue {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🏠 Payment Request</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Rent Payment Reminder</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear {{ $tenant->name }},
            </div>
            
            <p>This is a friendly reminder regarding your upcoming rent payment for your rental property.</p>
            
            @if($customMessage)
                <div class="custom-message">
                    <strong>Personal Message from {{ $landlord->name }}:</strong><br>
                    {{ $customMessage }}
                </div>
            @endif
            
            <div class="payment-details {{ \Carbon\Carbon::parse($dueDate)->isPast() ? 'urgency-overdue' : (\Carbon\Carbon::parse($dueDate)->diffInDays() <= 3 ? 'urgency-high' : '') }}">
                <h3>📋 Payment Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Property:</span>
                    <span class="detail-value">{{ $assignment->property->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Unit:</span>
                    <span class="detail-value">{{ $assignment->unit->unit_number }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Due Date:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($dueDate)->format('F j, Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Amount Due:</span>
                    <span class="detail-value amount">KSh {{ number_format($amount, 0) }}</span>
                </div>
            </div>
            
            @php
                $daysUntilDue = \Carbon\Carbon::parse($dueDate)->diffInDays(now(), false);
            @endphp
            
            @if($daysUntilDue < 0)
                <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0;">
                    <strong>⚠️ OVERDUE NOTICE:</strong> This payment is {{ abs($daysUntilDue) }} day(s) overdue. Please make payment immediately to avoid any late fees.
                </div>
            @elseif($daysUntilDue <= 3)
                <div style="background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 15px 0;">
                    <strong>🕒 URGENT:</strong> This payment is due in {{ $daysUntilDue }} day(s). Please arrange payment as soon as possible.
                </div>
            @endif
            
            <div class="payment-methods">
                <h4>💳 Payment Methods</h4>
                <div class="method">
                    <strong>M-Pesa:</strong> Send to Paybill 123456, Account: {{ $assignment->unit->unit_number }}
                </div>
                <div class="method">
                    <strong>Bank Transfer:</strong> Contact landlord for bank details
                </div>
                <div class="method">
                    <strong>Cash/Cheque:</strong> Deliver to landlord directly
                </div>
            </div>
            
            <div class="contact-info">
                <h4>📞 Landlord Contact Information</h4>
                <p><strong>Name:</strong> {{ $landlord->name }}</p>
                <p><strong>Email:</strong> {{ $landlord->email }}</p>
                @if($landlord->phone)
                    <p><strong>Phone:</strong> {{ $landlord->phone }}</p>
                @endif
            </div>
            
            <p style="margin-top: 25px;">
                If you have already made this payment, please disregard this notice. If you have any questions or need to discuss payment arrangements, please contact your landlord directly.
            </p>
            
            <p style="margin-top: 20px; color: #6c757d;">
                Thank you for your prompt attention to this matter.
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated payment reminder sent on behalf of {{ $landlord->name }}.</p>
            <p style="margin-top: 10px; font-size: 12px;">
                Generated on {{ now()->format('F j, Y \a\t g:i A') }}
            </p>
        </div>
    </div>
</body>
</html>
