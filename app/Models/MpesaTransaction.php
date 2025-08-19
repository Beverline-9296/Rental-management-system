<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MpesaTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'property_id',
        'phone_number',
        'amount',
        'checkout_request_id',
        'merchant_request_id',
        'mpesa_receipt_number',
        'transaction_date',
        'status',
        'result_code',
        'result_desc',
        'account_reference',
        'transaction_desc',
        'payment_type',
        'callback_data'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'callback_data' => 'array',
        'amount' => 'decimal:2'
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'mpesa_transaction_id');
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuccess()
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function markAsSuccess($callbackData)
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'result_code' => $callbackData['ResultCode'] ?? 0,
            'result_desc' => $callbackData['ResultDesc'] ?? 'Success',
            'mpesa_receipt_number' => $this->extractReceiptNumber($callbackData),
            'transaction_date' => now(),
            'callback_data' => $callbackData
        ]);
    }

    public function markAsFailed($callbackData)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'result_code' => $callbackData['ResultCode'] ?? 1,
            'result_desc' => $callbackData['ResultDesc'] ?? 'Failed',
            'callback_data' => $callbackData
        ]);
    }

    private function extractReceiptNumber($callbackData)
    {
        if (isset($callbackData['CallbackMetadata']['Item'])) {
            foreach ($callbackData['CallbackMetadata']['Item'] as $item) {
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    return $item['Value'];
                }
            }
        }
        return null;
    }
}
