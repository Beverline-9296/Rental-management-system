# M-Pesa STK Push Setup Guide

## Environment Variables

Add the following variables to your `.env` file:

```env
# M-Pesa Configuration
MPESA_ENVIRONMENT=sandbox
MPESA_CONSUMER_KEY=your_consumer_key_here
MPESA_CONSUMER_SECRET=your_consumer_secret_here
MPESA_SHORTCODE=174379
MPESA_PASSKEY=your_passkey_here
MPESA_CALLBACK_URL=https://yourdomain.com/api/mpesa/callback
MPESA_TIMEOUT_URL=https://yourdomain.com/api/mpesa/timeout
```

## Getting M-Pesa Credentials

### For Sandbox (Testing)
1. Go to [Safaricom Developer Portal](https://developer.safaricom.co.ke/)
2. Create an account and log in
3. Create a new app
4. Select "Lipa Na M-Pesa Online" API
5. Get your Consumer Key and Consumer Secret
6. Use test shortcode: `174379`
7. Use test passkey: `bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919`

### For Production
1. Apply for Go-Live on Safaricom Developer Portal
2. Get your production credentials
3. Update environment to `production`
4. Use your actual shortcode and passkey

## Testing Phone Numbers (Sandbox)
- Use: `254708374149` or `254711111111`
- These will simulate successful payments in sandbox

## Callback URL Setup
Your callback URL must be publicly accessible. For local development:
1. Use ngrok: `ngrok http 8000`
2. Update `MPESA_CALLBACK_URL` with the ngrok URL

## Features Implemented

### 1. STK Push Payment
- Tenants can initiate M-Pesa payments
- Real-time payment status checking
- Automatic payment recording

### 2. Payment Tracking
- All M-Pesa transactions are logged
- Payment status updates via callbacks
- Integration with existing payment system

### 3. Security
- CSRF protection
- Input validation
- Secure callback handling

## Usage

### For Tenants
1. Navigate to "Make Payment" page
2. Select unit and enter phone number
3. Enter payment amount
4. Click "Pay with M-Pesa"
5. Enter M-Pesa PIN on phone
6. Payment is automatically recorded

### API Endpoints
- `POST /api/mpesa/stk-push` - Initiate payment
- `POST /api/mpesa/callback` - M-Pesa callback
- `POST /api/mpesa/check-status` - Check payment status

## Database Tables
- `mpesa_transactions` - Stores all M-Pesa transaction data
- `payments` - Updated to link with M-Pesa transactions

## Troubleshooting

### Common Issues
1. **Invalid Credentials**: Check your consumer key/secret
2. **Callback Not Working**: Ensure URL is publicly accessible
3. **Phone Number Format**: Use 254XXXXXXXXX format
4. **Timeout**: Payments timeout after 5 seconds

### Logs
Check Laravel logs for M-Pesa related errors:
```bash
tail -f storage/logs/laravel.log | grep -i mpesa
```
