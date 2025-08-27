<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS service provider (Africa's Talking)
    |
    */

    'api_key' => env('SMS_API_KEY'),
    'username' => env('SMS_USERNAME', 'sandbox'),
    'base_url' => env('SMS_BASE_URL', 'https://api.africastalking.com/version1/messaging'),
    'from' => env('SMS_FROM', null),
];
