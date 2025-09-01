<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'payment_id',
        'tenant_id',
        'property_id',
        'unit_id',
        'amount',
        'payment_method',
        'payment_type',
        'mpesa_receipt_number',
        'description',
        'receipt_date',
        'receipt_data',
        'status'
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'receipt_data' => 'array',
        'amount' => 'decimal:2'
    ];

    // Relationships
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Generate unique receipt number
    public static function generateReceiptNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        // Format: RCP-YYYY-MM-XXXX
        $prefix = "RCP-{$year}-{$month}-";
        
        // Get the last receipt number for this month
        $lastReceipt = self::where('receipt_number', 'like', $prefix . '%')
            ->orderBy('receipt_number', 'desc')
            ->first();
        
        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->receipt_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Create receipt from payment
    public static function createFromPayment(Payment $payment)
    {
        $receiptData = [
            'tenant_name' => $payment->tenant->name,
            'tenant_email' => $payment->tenant->email,
            'tenant_phone' => $payment->tenant->phone,
            'property_name' => $payment->property->name,
            'property_address' => $payment->property->address,
            'unit_number' => $payment->unit ? $payment->unit->unit_number : null,
            'landlord_name' => $payment->property->landlord->name,
            'landlord_phone' => $payment->property->landlord->phone,
            'payment_date' => $payment->payment_date->format('Y-m-d H:i:s'),
        ];

        return self::create([
            'receipt_number' => self::generateReceiptNumber(),
            'payment_id' => $payment->id,
            'tenant_id' => $payment->tenant_id,
            'property_id' => $payment->property_id,
            'unit_id' => $payment->unit_id,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'payment_type' => $payment->payment_type ?? 'rent',
            'mpesa_receipt_number' => $payment->mpesa_transaction ? $payment->mpesa_transaction->mpesa_receipt_number : null,
            'description' => $payment->notes,
            'receipt_date' => $payment->payment_date,
            'receipt_data' => $receiptData,
            'status' => 'generated'
        ]);
    }
}
