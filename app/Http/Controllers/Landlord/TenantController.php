<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use App\Models\TenantAssignment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:landlord');
    }

    /**
     * Display a listing of the tenants.
     */
    public function index()
    {
        $tenants = User::where('role', 'tenant')
            ->whereHas('tenantAssignments', function($query) {
                $query->where('landlord_id', auth()->id());
            })
            ->with(['tenantAssignments.unit.property'])
            ->latest()
            ->paginate(10);

        return view('landlord.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        $availableUnits = Unit::where('status', 'available')
            ->whereHas('property', function($query) {
                $query->where('landlord_id', auth()->id());
            })
            ->with('property')
            ->get();

        return view('landlord.tenants.create', compact('availableUnits'));
    }

    /**
     * Store a newly created tenant in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:20',
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
        ]);

        // Use database transaction to ensure data consistency
        return \DB::transaction(function () use ($validated, $request) {
            try {
                // Generate a random password
                $password = Str::random(12);
                
                // Create the tenant user with role 'tenant'
                $tenant = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($password),
                    'role' => 'tenant', // Explicitly set role as tenant
                    'phone_number' => $validated['phone_number'],
                    'id_number' => $validated['id_number'],
                    'address' => $validated['address'],
                    'emergency_contact' => $validated['emergency_contact'],
                    'emergency_phone' => $validated['emergency_phone'],
                ]);

                // Verify tenant was created with correct role
                if (!$tenant->isTenant()) {
                    throw new \Exception('Failed to create tenant with correct role');
                }

                // Create tenant assignment
                $assignment = TenantAssignment::create([
                    'unit_id' => $validated['unit_id'],
                    'tenant_id' => $tenant->id,
                    'landlord_id' => auth()->id(),
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'monthly_rent' => $validated['monthly_rent'],
                    'deposit_amount' => $validated['deposit_amount'],
                    'status' => 'active',
                ]);

                // Update unit status to occupied
                $unit = Unit::findOrFail($validated['unit_id']);
                $unit->status = 'occupied';
                $unit->save();

                // Log the tenant assignment activity
                ActivityLog::logActivity(
                    auth()->id(),
                    'tenant_assigned',
                    'Assigned new tenant: ' . $tenant->name . ' to ' . $unit->property->name . ' - Unit ' . $unit->unit_number,
                    [
                        'tenant_id' => $tenant->id,
                        'tenant_name' => $tenant->name,
                        'unit_id' => $unit->id,
                        'unit_number' => $unit->unit_number,
                        'property_name' => $unit->property->name,
                        'monthly_rent' => $validated['monthly_rent']
                    ],
                    'fas fa-user-plus',
                    'green'
                );

                // Log successful tenant creation
                \Log::info('Tenant created successfully', [
                    'tenant_id' => $tenant->id,
                    'tenant_email' => $tenant->email,
                    'tenant_role' => $tenant->role,
                    'unit_id' => $validated['unit_id'],
                    'landlord_id' => auth()->id()
                ]);

                // TODO: Send welcome email with login credentials
                // Mail::to($tenant->email)->send(new TenantWelcome($tenant, $password));

                return redirect()->route('landlord.tenants.index')
                    ->with('success', "Tenant '{$tenant->name}' added successfully! They have been assigned to unit and their account is ready.")
                    ->with('tenant_password', $password)
                    ->with('tenant_email', $tenant->email);
                    
            } catch (\Exception $e) {
                \Log::error('Error creating tenant: ' . $e->getMessage(), [
                    'request_data' => $validated,
                    'landlord_id' => auth()->id()
                ]);
                
                return back()->withInput()
                    ->with('error', 'Failed to create tenant. Please try again. Error: ' . $e->getMessage());
            }
        });
    }

    /**
     * Display the specified tenant.
     */
    public function show(User $tenant)
    {
        $this->authorize('view', $tenant);
        
        $tenant->load(['tenantAssignments.unit.property']);
        
        return view('landlord.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(User $tenant)
    {
        $this->authorize('update', $tenant);
        
        $tenant->load('tenantAssignments.unit');
        $availableUnits = Unit::where('status', 'available')
            ->orWhere('id', $tenant->tenantAssignments->first()?->unit_id)
            ->whereHas('property', function($query) {
                $query->where('landlord_id', auth()->id());
            })
            ->with('property')
            ->get();
            
        return view('landlord.tenants.edit', compact('tenant', 'availableUnits'));
    }

    /**
     * Update the specified tenant in storage.
     */
    public function update(Request $request, User $tenant)
    {
        $this->authorize('update', $tenant);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($tenant->id),
            ],
            'phone_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:20',
            'unit_id' => 'required|exists:units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,terminated,expired',
        ]);

        // Update tenant details
        $tenant->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'id_number' => $validated['id_number'],
            'address' => $validated['address'],
            'emergency_contact' => $validated['emergency_contact'],
            'emergency_phone' => $validated['emergency_phone'],
        ]);

        // Update or create tenant assignment
        $assignment = $tenant->tenantAssignments()->firstOrNew([], [
            'tenant_id' => $tenant->id,
            'landlord_id' => auth()->id(),
        ]);

        // If unit is changing, update the old unit status
        if ($assignment->exists && $assignment->unit_id != $validated['unit_id']) {
            $oldUnit = Unit::find($assignment->unit_id);
            if ($oldUnit) {
                $oldUnit->status = 'available';
                $oldUnit->save();
            }
        }

        // Update assignment
        $assignment->fill([
            'unit_id' => $validated['unit_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'monthly_rent' => $validated['monthly_rent'],
            'deposit_amount' => $validated['deposit_amount'],
            'status' => $validated['status'],
        ])->save();

        // Update new unit status
        $unit = Unit::findOrFail($validated['unit_id']);
        $unit->status = $validated['status'] === 'active' ? 'occupied' : 'available';
        $unit->save();

        // Log the tenant update activity
        ActivityLog::logActivity(
            auth()->id(),
            'tenant_updated',
            'Updated tenant: ' . $tenant->name,
            [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'unit_id' => $unit->id,
                'unit_number' => $unit->unit_number,
                'status' => $validated['status']
            ],
            'fas fa-user-edit',
            'blue'
        );

        return redirect()->route('landlord.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified tenant from storage.
     */
    public function destroy(User $tenant)
    {
        $this->authorize('delete', $tenant);
        
        // Log the tenant removal activity
        ActivityLog::logActivity(
            auth()->id(),
            'tenant_removed',
            'Removed tenant: ' . $tenant->name,
            [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'tenant_email' => $tenant->email
            ],
            'fas fa-user-minus',
            'red'
        );

        // End any active assignments
        $tenant->tenantAssignments()->update(['status' => 'terminated']);
        
        // Mark associated units as available
        $unitIds = $tenant->tenantAssignments()->pluck('unit_id');
        Unit::whereIn('id', $unitIds)->update(['status' => 'available']);
        
        // Soft delete the user
        $tenant->delete();
        
        return redirect()->route('landlord.tenants.index')
            ->with('success', 'Tenant removed successfully!');
    }

    /**
     * Reset password for a tenant
     */
    public function resetPassword(User $tenant)
    {
        $this->authorize('update', $tenant);
        
        // Generate a new random password
        $newPassword = Str::random(12);
        
        // Update the tenant's password
        $tenant->update([
            'password' => Hash::make($newPassword)
        ]);
        
        // Log the password reset
        Log::info('Tenant password reset', [
            'tenant_id' => $tenant->id,
            'tenant_email' => $tenant->email,
            'reset_by_landlord' => auth()->id()
        ]);
        
        return redirect()->route('landlord.tenants.index')
            ->with('success', "Password reset successfully for {$tenant->name}!")
            ->with('tenant_password', $newPassword)
            ->with('tenant_email', $tenant->email);
    }
}
