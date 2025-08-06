<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'unit_number',
        'unit_type',
        'bedrooms',
        'bathrooms',
        'size_sqft',
        'rent_amount',
        'deposit_amount',
        'status',
        'features',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'features' => 'array',
        'rent_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'size_sqft' => 'decimal:2',
    ];

    /**
     * Get the property that owns the unit.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the tenant assignments for the unit.
     */
    public function tenantAssignments(): HasMany
    {
        return $this->hasMany(TenantAssignment::class);
    }

    /**
     * Get all payments for this unit
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the current tenant assignment.
     */
    public function currentTenant()
    {
        return $this->hasOne(TenantAssignment::class)
            ->where('status', 'active')
            ->latest();
    }

    /**
     * Check if the unit is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if the unit is occupied.
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    /**
     * Check if the unit is under maintenance.
     */
    public function isUnderMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }
}
