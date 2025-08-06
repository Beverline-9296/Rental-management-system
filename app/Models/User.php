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
        'date_of_birth',
        'occupation',
        'bio',
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
     * Get messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get all payments made by this tenant
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id');
    }

    /**
     * Get the total amount paid by this tenant
     */
    public function getTotalPaid()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get the total rent due for this tenant (all active assignments)
     */
    public function getTotalDue()
    {
        $totalDue = 0;
        $today = now();
        foreach ($this->tenantAssignments()->active()->get() as $assignment) {
            $start = $assignment->start_date;
            $end = $assignment->end_date && $assignment->end_date < $today ? $assignment->end_date : $today;
            $months = $start ? $start->diffInMonths($end) + 1 : 0;
            $totalDue += $months * $assignment->monthly_rent;
        }
        return $totalDue;
    }

    /**
     * Get arrears for this tenant
     */
    public function getArrears()
    {
        return max(0, $this->getTotalDue() - $this->getTotalPaid());
    }

    /**
     * Get unread messages count
     */
    public function getUnreadMessagesCountAttribute(): int
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }



    /**
     * Check if user has admin privileges
     */
    public function isAdmin(): bool
    {
        return $this->isLandlord();
    }
}
