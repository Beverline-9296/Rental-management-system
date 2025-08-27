# Payment Request System Documentation

## Overview
The payment request system allows landlords to send payment reminders to tenants via SMS and/or email with comprehensive tracking and logging capabilities.

## Features Implemented

### 1. **Payment Request Management**
- **Route**: `/landlord/payments/request` (GET) - Display payment request form
- **Route**: `/landlord/payments/send-request` (POST) - Send payment requests
- **Controller**: `Landlord\PaymentController@showRequestForm` and `sendRequest`

### 2. **Tenant Selection Interface**
- Interactive tenant selection with payment status
- Urgency indicators (overdue, high, medium, low priority)
- Bulk selection options (All, None, Overdue only)
- Real-time selection summary

### 3. **Multi-Channel Notifications**
- **SMS**: Via Africa's Talking API with phone number formatting
- **Email**: Rich HTML templates with payment details
- **Both**: Combined SMS and email delivery

### 4. **Payment Request Tracking**
- Database table: `payment_requests`
- Status tracking: sent, delivered, read, paid, failed
- Timestamps for each status change
- Activity logging for audit trail

## Database Schema

### PaymentRequest Model
```php
- landlord_id (foreign key to users)
- tenant_id (foreign key to users)
- property_id (foreign key to properties)
- unit_id (foreign key to units)
- amount (decimal)
- message_type (enum: sms, email, both)
- custom_message (text, nullable)
- status (enum: sent, delivered, read, paid, failed)
- sent_at, delivered_at, read_at, paid_at (timestamps)
```

## Configuration

### Environment Variables
Add to your `.env` file:
```env
# SMS Configuration (Africa's Talking)
SMS_API_KEY=your_api_key_here
SMS_USERNAME=sandbox
SMS_BASE_URL=https://api.africastalking.com/version1/messaging
SMS_FROM=your_sender_id
```

### Email Configuration
Ensure your mail configuration is set up in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@yourapp.com"
MAIL_FROM_NAME="Your App Name"
```

## Services Created

### 1. **SmsService** (`app/Services/SmsService.php`)
- Phone number formatting (Kenyan format support)
- Africa's Talking API integration
- Development mode logging
- Bulk SMS capability
- Message template generation

### 2. **PaymentRequestMail** (`app/Mail/PaymentRequestMail.php`)
- Rich HTML email template
- Payment details and urgency indicators
- Landlord contact information
- Custom message support

## Usage

### For Landlords:
1. Navigate to Dashboard
2. Click "SEND PAYMENT REQUEST" button
3. Select tenants from the list
4. Choose delivery method (SMS, Email, or Both)
5. Add custom message (optional)
6. Send requests

### Payment Request Features:
- **Smart Due Date Calculation**: Based on tenant assignment start date and payment history
- **Urgency Classification**: Overdue, high (≤3 days), medium (≤7 days), low priority
- **Bulk Operations**: Select all, none, or only overdue tenants
- **Real-time Feedback**: Success/error messages and logging

## Development Notes

### SMS Integration
- In development mode, SMS messages are logged instead of sent
- Production requires valid Africa's Talking API credentials
- Phone numbers are automatically formatted to international format

### Email Templates
- Responsive HTML design with urgency indicators
- Payment method suggestions included
- Landlord contact information displayed
- Custom branding support

### Error Handling
- Comprehensive logging for debugging
- Graceful failure handling
- User-friendly error messages
- Activity logging for audit trails

## Testing

### Development Testing:
1. Ensure server is running: `php artisan serve`
2. Login as a landlord
3. Navigate to payment request page
4. Select tenants and send requests
5. Check logs for SMS/email delivery confirmation

### Production Setup:
1. Configure SMS API credentials
2. Set up SMTP email service
3. Test with small tenant group first
4. Monitor logs for any issues

## Security Features
- Role-based access (landlord only)
- Tenant ID validation
- CSRF protection
- Input sanitization
- Activity logging for accountability

## Future Enhancements
- Payment request analytics dashboard
- Automated recurring reminders
- Tenant response tracking
- Integration with payment gateways
- WhatsApp messaging support
