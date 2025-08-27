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
            if (config('app.env') === 'local' || !$this->apiKey) {
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
