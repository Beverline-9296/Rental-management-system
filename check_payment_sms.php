<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payment;
use App\Models\MpesaTransaction;

echo "=== Recent Payment & SMS Check ===\n\n";

// Check recent payments
echo "Recent Payments:\n";
$payments = Payment::with(['tenant', 'mpesaTransaction'])
    ->latest()
    ->take(5)
    ->get();

foreach ($payments as $payment) {
    echo "Payment ID: {$payment->id}\n";
    echo "Tenant: {$payment->tenant->name}\n";
    echo "Phone: {$payment->tenant->phone_number}\n";
    echo "Amount: KES {$payment->amount}\n";
    echo "Date: {$payment->created_at}\n";
    if ($payment->mpesaTransaction) {
        echo "M-Pesa Receipt: {$payment->mpesaTransaction->mpesa_receipt_number}\n";
    }
    echo "---\n";
}

// Check recent M-Pesa transactions
echo "\nRecent M-Pesa Transactions:\n";
$mpesaTransactions = MpesaTransaction::latest()->take(5)->get();

foreach ($mpesaTransactions as $transaction) {
    echo "Transaction ID: {$transaction->id}\n";
    echo "Phone: {$transaction->phone_number}\n";
    echo "Amount: KES {$transaction->amount}\n";
    echo "Result Code: {$transaction->result_code}\n";
    echo "Status: " . ($transaction->result_code == '0' ? 'SUCCESS' : 'FAILED') . "\n";
    echo "Date: {$transaction->created_at}\n";
    echo "---\n";
}

// Test SMS service directly
echo "\nTesting SMS Service:\n";
$smsService = new \App\Services\SmsService();

// Get a recent payment to test SMS
$recentPayment = Payment::with(['tenant', 'tenant.tenantAssignments.unit.property'])
    ->latest()
    ->first();

if ($recentPayment && $recentPayment->tenant) {
    echo "Testing SMS for recent payment...\n";
    echo "Tenant: {$recentPayment->tenant->name}\n";
    echo "Phone: {$recentPayment->tenant->phone_number}\n";
    
    $assignment = $recentPayment->tenant->tenantAssignments()
        ->with(['unit.property'])
        ->first();
    
    if ($assignment) {
        $result = $smsService->sendPaymentConfirmation(
            $recentPayment->tenant->phone_number,
            $recentPayment->tenant->name,
            $recentPayment->amount,
            $assignment->unit->property->name,
            $assignment->unit->unit_number,
            'TEST-' . time()
        );
        
        echo "SMS Result:\n";
        print_r($result);
    }
} else {
    echo "No recent payments found to test SMS.\n";
}
