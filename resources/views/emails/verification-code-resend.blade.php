<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Verification Code</title>
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
            background-color: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .verification-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 5px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            display: inline-block;
        }
        .footer {
            background-color: #374151;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 14px;
        }
        .warning {
            color: #dc2626;
            font-weight: bold;
            margin-top: 10px;
        }
        .info {
            background-color: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 New Verification Code</h1>
        <p>Your account verification code has been resent</p>
    </div>

    <div class="content">
        <h2>Hello {{ $user->name }},</h2>
        
        <p>You requested a new verification code for your rental management account. Here is your new code:</p>

        <div class="verification-box">
            <h3>Your Verification Code</h3>
            <div class="code">{{ $verificationCode }}</div>
            <p class="warning">⚠️ This code expires in 24 hours</p>
        </div>

        <div class="info">
            <strong>📋 How to use this code:</strong>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li>Go to the verification page</li>
                <li>Enter the 6-digit code above</li>
                <li>Set your new password</li>
                <li>Click "Verify Account" to complete the process</li>
            </ol>
        </div>

        <p><strong>Account Details:</strong></p>
        <ul>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Name:</strong> {{ $user->name }}</li>
        </ul>

        <p style="margin-top: 30px;">If you didn't request this verification code, please contact your landlord immediately.</p>
    </div>

    <div class="footer">
        <p>This is an automated message from the Rental Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>
