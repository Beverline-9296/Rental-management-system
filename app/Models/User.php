<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'id_number',
        'address',
        'emergency_contact',
        'emergency_phone',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is a landlord
     */
    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    /**
     * Check if the user is a tenant
     */
    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    /**
     * Get the properties owned by the landlord
     */
    public function ownedProperties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    /**
     * Get the tenant assignments for the user
     */
    public function tenantAssignments()
    {
        return $this->hasMany(TenantAssignment::class, 'tenant_id');
    }

    /**
     * Get the landlord's tenant assignments
     */
    public function landlordAssignments()
    {
        return $this->hasMany(TenantAssignment::class, 'landlord_id');
    }

    /**
     * Get the units assigned to the tenant
     */
    public function assignedUnits()
    {
        return $this->belongsToMany(Unit::class, 'tenant_assignments', 'tenant_id', 'unit_id')
            ->withPivot(['start_date', 'end_date', 'monthly_rent', 'status'])
            ->withTimestamps();
    }



    /**
     * Check if user has admin privileges
     */
    public function isAdmin(): bool
    {
        return $this->isLandlord();
    }
}
