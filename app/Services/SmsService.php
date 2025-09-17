<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\ActivityLog;

class SmsService
{
    private $apiKey;
    private $username;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('sms.api_key');
        $this->username = config('sms.username');
        $this->baseUrl = config('sms.base_url', 'https://api.africastalking.com/version1/messaging');
    }

    /**
     * Send SMS to a single recipient
     */
    public function sendSms($phoneNumber, $message, $from = null)
    {
        try {
            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            
            if (!$formattedPhone) {
                throw new \Exception('Invalid phone number format');
            }

            // For development/testing - log the SMS instead of sending
            if (config('app.env') === 'local' || !$this->apiKey || config('sms.development_mode', false)) {
                Log::info('SMS would be sent', [
                    'to' => $formattedPhone,
                    'message' => $message,
                    'from' => $from
                ]);
                
                return [
                    'success' => true,
                    'message' => 'SMS logged for development',
                    'recipients' => [
                        [
                            'number' => $formattedPhone,
                            'status' => 'Success',
                            'messageId' => 'dev_' . uniqid()
                        ]
                    ]
                ];
            }

            // Send actual SMS via Africa's Talking API
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'apiKey' => $this->apiKey
            ])->post($this->baseUrl, [
                'username' => $this->username,
                'to' => $formattedPhone,
                'message' => $message,
                'from' => $from
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('SMS sent successfully', [
                    'to' => $formattedPhone,
                    'response' => $data
                ]);
                
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => $data
                ];
            } else {
                throw new \Exception('SMS API request failed: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send SMS to multiple recipients
     */
    public function sendBulkSms($phoneNumbers, $message, $from = null)
    {
        $results = [];
        
        foreach ($phoneNumbers as $phoneNumber) {
            $results[] = $this->sendSms($phoneNumber, $message, $from);
        }
        
        return $results;
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Handle Kenyan phone numbers
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            // Convert 0712345678 to 254712345678
            return '254' . substr($phone, 1);
        } elseif (strlen($phone) === 9 && substr($phone, 0, 1) === '7') {
            // Convert 712345678 to 254712345678
            return '254' . $phone;
        } elseif (strlen($phone) === 12 && substr($phone, 0, 3) === '254') {
            // Already in correct format
            return $phone;
        }
        
        // Return null for invalid formats
        return null;
    }

    /**
     * Send verification code SMS
     */
    public function sendVerificationCode($phoneNumber, $verificationCode, $tenantName)
    {
        $message = "Hello {$tenantName}, your account verification code is: {$verificationCode}. This code expires in 24 hours. Use it to complete your account setup.";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send payment confirmation SMS
     */
    public function sendPaymentConfirmation($phoneNumber, $tenantName, $amount, $propertyName, $unitNumber, $receiptNumber)
    {
        $message = "Dear {$tenantName}, payment of KSh " . number_format($amount, 2) . " received for {$propertyName}, Unit {$unitNumber}. Receipt: {$receiptNumber}. Thank you!";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send payment failure SMS
     */
    public function sendPaymentFailure($phoneNumber, $tenantName, $amount, $reason = null)
    {
        $reasonText = $reason ? " Reason: {$reason}" : "";
        $message = "Dear {$tenantName}, your payment of KSh " . number_format($amount, 2) . " could not be processed.{$reasonText} Please try again or contact support.";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send rent reminder SMS
     */
    public function sendRentReminder($phoneNumber, $tenantName, $propertyName, $unitNumber, $amount, $dueDate, $daysUntilDue)
    {
        if ($daysUntilDue > 0) {
            $message = "Dear {$tenantName}, rent reminder: KSh " . number_format($amount, 2) . " due in {$daysUntilDue} days ({$dueDate}) for {$propertyName}, Unit {$unitNumber}. Please prepare payment.";
        } else {
            $daysPastDue = abs($daysUntilDue);
            $message = "Dear {$tenantName}, rent overdue: KSh " . number_format($amount, 2) . " was due {$daysPastDue} days ago for {$propertyName}, Unit {$unitNumber}. Please pay immediately.";
        }
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send maintenance update SMS
     */
    public function sendMaintenanceUpdate($phoneNumber, $tenantName, $requestId, $status, $message = null)
    {
        $statusText = ucfirst($status);
        $additionalMessage = $message ? " Message: {$message}" : "";
        $smsText = "Dear {$tenantName}, your maintenance request #{$requestId} status: {$statusText}.{$additionalMessage}";
        
        return $this->sendSms($phoneNumber, $smsText);
    }

    /**
     * Send welcome SMS to new tenant
     */
    public function sendWelcomeSms($phoneNumber, $tenantName, $propertyName, $unitNumber, $landlordName)
    {
        $message = "Welcome {$tenantName}! Your rental account has been created for {$propertyName}, Unit {$unitNumber}. Check your email for login details. Contact: {$landlordName}";
        
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Generate payment request SMS message
     */
    public function generatePaymentRequestMessage($landlordName, $tenantName, $propertyName, $unitNumber, $amount, $dueDate, $customMessage = null)
    {
        if ($customMessage) {
            return $customMessage;
        }

        return "Dear {$tenantName}, this is a payment reminder for your rent at {$propertyName}, Unit {$unitNumber}. Amount: KSh " . number_format($amount, 0) . ". Due: {$dueDate}. Please make payment at your earliest convenience. - {$landlordName}";
    }
}
