<?php

// Simple test to check Africa's Talking API directly
$apiKey = 'atsk_ac633d1c73cbdd975c69917c6053e0effeb3e81208efa3235d008581375f67fec942ce69';
$username = 'sandbox';
$phoneNumber = '254707605631'; // Change to your number
$message = 'Test SMS from Rental System via direct API call';

echo "Testing Africa's Talking API directly...\n";
echo "Phone: $phoneNumber\n";
echo "Message: $message\n\n";

$postData = [
    'username' => $username,
    'to' => $phoneNumber,
    'message' => $message,
    'from' => 'RentalSys'
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.africastalking.com/version1/messaging',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postData),
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        'apiKey: ' . $apiKey
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
curl_close($curl);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}
echo "Response: $response\n";

if ($httpCode == 201) {
    echo "✅ SMS API call successful! Check your phone.\n";
} else {
    echo "❌ SMS API call failed.\n";
}
