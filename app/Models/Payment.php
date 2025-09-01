<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $casts = [
        'payment_date' => 'datetime',
    ];
    protected $fillable = [
        'tenant_id', 'unit_id', 'property_id', 'amount', 'payment_date', 'payment_method', 'payment_type', 'notes', 'recorded_by', 'mpesa_transaction_id'
    ];

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

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function mpesaTransaction()
    {
        return $this->belongsTo(MpesaTransaction::class, 'mpesa_transaction_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }
}
