<?php

namespace App\Policies;

use App\Models\TenantAssignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isLandlord();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $tenant): bool
    {
        // Only allow viewing if the tenant is assigned to one of the landlord's properties
        return $user->isLandlord() && 
               $tenant->tenantAssignments()->whereHas('unit.property', function($query) use ($user) {
                   $query->where('landlord_id', $user->id);
               })->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isLandlord();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $tenant): bool
    {
        // Only allow updating if the tenant is assigned to one of the landlord's properties
        return $user->isLandlord() && 
               $tenant->tenantAssignments()->whereHas('unit.property', function($query) use ($user) {
                   $query->where('landlord_id', $user->id);
               })->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $tenant): bool
    {
        // Only allow deleting if the tenant is assigned to one of the landlord's properties
        return $user->isLandlord() && 
               $tenant->tenantAssignments()->whereHas('unit.property', function($query) use ($user) {
                   $query->where('landlord_id', $user->id);
               })->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $tenant): bool
    {
        return $user->isLandlord();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $tenant): bool
    {
        return $user->hasRole('admin'); // Only admin can force delete
    }
}
