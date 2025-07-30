<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use App\Models\Lease;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone_number'],
            'id_number' => ['required', 'string', 'max:50', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'property_id' => ['required', 'exists:properties,id'],
            'unit_id' => [
                'required', 
                'exists:units,id',
                function ($attribute, $value, $fail) use ($request) {
                    $unit = \App\Models\Unit::find($value);
                    if ($unit && $unit->status !== 'vacant') {
                        $fail('The selected unit is not available.');
                    }
                },
            ],
            'move_in_date' => ['required', 'date', 'after_or_equal:today'],
            'lease_duration' => ['required', 'in:6,12,24'],
            'terms' => ['accepted'],
        ]);

        // Start a database transaction
        return DB::transaction(function () use ($validated) {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone'],
                'id_number' => $validated['id_number'],
                'password' => Hash::make($validated['password']),
                'role' => 'tenant',
            ]);

            // Find the unit and update its status
            $unit = Unit::findOrFail($validated['unit_id']);
            $unit->update(['status' => 'occupied']);

            // Create a lease for the tenant
            $moveInDate = Carbon::parse($validated['move_in_date']);
            $endDate = (clone $moveInDate)->addMonths($validated['lease_duration']);

            Lease::create([
                'unit_id' => $unit->id,
                'tenant_id' => $user->id,
                'start_date' => $moveInDate,
                'end_date' => $endDate,
                'monthly_rent' => $unit->rent_amount,
                'deposit' => $unit->deposit_amount,
                'status' => 'active',
                'lease_number' => 'LEASE-' . strtoupper(Str::random(8)),
            ]);

            // Fire the registered event
            event(new Registered($user));

            // Log the user in
            Auth::login($user);

            // Redirect to the tenant dashboard
            return redirect()->route('tenant.dashboard')
                ->with('success', 'Registration successful! Welcome to your dashboard.');
        });
    }
}
