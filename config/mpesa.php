<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for M-Pesa STK Push integration
    |
    */

    'environment' => env('MPESA_ENVIRONMENT', 'sandbox'), // 'sandbox' or 'production'
    
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    
    'shortcode' => env('MPESA_SHORTCODE'),
    
    'passkey' => env('MPESA_PASSKEY'),
    
    'callback_url' => env('MPESA_CALLBACK_URL', env('APP_URL') . '/api/mpesa/callback'),
    
    'timeout_url' => env('MPESA_TIMEOUT_URL', env('APP_URL') . '/api/mpesa/timeout'),
];
