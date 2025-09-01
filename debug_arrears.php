<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check beverline Omondi's payments
$tenant = \App\Models\User::where('name', 'beverline Omondi')->first();

if ($tenant) {
    echo "Tenant: {$tenant->name} (ID: {$tenant->id})\n";
    echo "================================\n";
    
    // Get all payments
    $allPayments = $tenant->payments()->get();
    echo "All Payments:\n";
    foreach ($allPayments as $payment) {
        echo "- ID: {$payment->id}, Type: " . ($payment->payment_type ?? 'NULL') . ", Amount: KSh " . number_format($payment->amount, 2) . "\n";
    }
    
    echo "\nRent Calculation Methods:\n";
    echo "Total Due: KSh " . number_format($tenant->getTotalDue(), 2) . "\n";
    echo "Total Rent Paid: KSh " . number_format($tenant->getTotalRentPaid(), 2) . "\n";
    echo "Arrears: KSh " . number_format($tenant->getArrears(), 2) . "\n";
    
    // Check what payments are being counted as rent
    $rentPayments = $tenant->payments()
        ->where(function($query) {
            $query->where('payment_type', 'rent')
                  ->orWhereNull('payment_type');
        })
        ->whereNotIn('payment_type', ['deposit', 'utility', 'maintenance'])
        ->get();
    
    echo "\nPayments counted as RENT:\n";
    foreach ($rentPayments as $payment) {
        echo "- ID: {$payment->id}, Type: " . ($payment->payment_type ?? 'NULL') . ", Amount: KSh " . number_format($payment->amount, 2) . "\n";
    }
    
} else {
    echo "Tenant 'beverline Omondi' not found\n";
}
?>
