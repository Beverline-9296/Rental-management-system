<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'landlord_id',
        'name',
        'description',
        'location',
        'address',
        'property_type',
        'image',
        'amenities',
        'notes'
    ];

    protected $casts = [
        'amenities' => 'array'
    ];

    /**
     * Get the landlord that owns the property
     */
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get the units for the property
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Get all payments for this property
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the tenants for the property through units
     */
    public function tenants()
    {
        return $this->hasManyThrough(
            TenantAssignment::class,
            Unit::class,
            'property_id', // Foreign key on units table
            'unit_id',     // Foreign key on tenant_assignments table
            'id',          // Local key on properties table
            'id'           // Local key on units table
        )->where('status', 'active');
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-property.jpg');
    }


}
