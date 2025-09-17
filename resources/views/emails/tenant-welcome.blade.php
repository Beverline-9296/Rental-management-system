<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Your Rental Account</title>
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
        .credentials-box {
            background-color: white;
            border: 2px solid #4f46e5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .verification-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .code {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            text-align: center;
            letter-spacing: 3px;
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
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
        }
        .info {
            color: #059669;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Your Rental Account</h1>
        <p>Your account has been created successfully!</p>
    </div>

    <div class="content">
        <h2>Hello {{ $tenant->name }},</h2>
        
        <p>Your landlord has created a rental account for you. Below are your login credentials and verification information:</p>

        <div class="credentials-box">
            <h3>🔐 Login Credentials</h3>
            <p><strong>Email:</strong> {{ $tenant->email }}</p>
            <p><strong>Temporary Password:</strong> <span class="code">{{ $password }}</span></p>
        </div>

        <div class="verification-box">
            <h3>🔒 Verification Code</h3>
            <p>For security purposes, you'll need this verification code when you log in for the first time:</p>
            <div class="code">{{ $verificationCode }}</div>
            <p class="warning">⚠️ This code expires in 24 hours</p>
        </div>

        <h3>📋 Next Steps:</h3>
        <ol>
            <li>Visit the rental management system login page</li>
            <li>Log in using your email and the temporary password above</li>
            <li>Enter the verification code when prompted</li>
            <li>You'll be asked to change your password on first login</li>
        </ol>

        <h3>🏠 Your Rental Information:</h3>
        @if($tenant->tenantAssignments->first())
            @php $assignment = $tenant->tenantAssignments->first(); @endphp
            <ul>
                <li><strong>Property:</strong> {{ $assignment->unit->property->name ?? 'N/A' }}</li>
                <li><strong>Unit:</strong> {{ $assignment->unit->unit_number ?? 'N/A' }}</li>
                <li><strong>Monthly Rent:</strong> KSh {{ number_format($assignment->monthly_rent ?? 0, 2) }}</li>
                <li><strong>Start Date:</strong> {{ $assignment->start_date ? $assignment->start_date->format('F j, Y') : 'N/A' }}</li>
            </ul>
        @endif

        <p class="info">💡 Keep this email safe - you'll need these credentials to access your account.</p>
        
        <p>If you have any questions or need assistance, please contact your landlord.</p>
    </div>

    <div class="footer">
        <p>This is an automated message from the Rental Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>
