<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TenantAssignment;
use App\Models\Unit;
use App\Models\Property;

class TenantController extends Controller
{
    /**
     * Display the tenant dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Sample data - you'll replace with actual database queries
        $data = [
            'rental_summary' => [
                'rent_amount' => 0,
                'balance' => 0,
                'arrears' => 0
            ],
            'unit_details' => [
                'property_name' => 'Not assigned',
                'unit_number' => 'N/A',
                'rent_amount' => 0
            ],
            'recent_activities' => [],
            'upcoming_payment' => null,
            'user' => $user
        ];
        
        return view('tenant.dashboard', $data);
    }
    
    /**
     * Show payments history page
     */
    public function payments()
    {
        return view('tenant.payments');
    }
    
    /**
     * Show unit details page
     */
    public function unitDetails()
    {
        $user = Auth::user();
        
        // Get tenant's current assignment
        $assignment = TenantAssignment::with(['unit.property'])
            ->where('tenant_id', $user->id)
            ->where('status', 'active')
            ->first();
        
        $data = [
            'user' => $user,
            'assignment' => $assignment,
            'unit' => $assignment ? $assignment->unit : null,
            'property' => $assignment && $assignment->unit ? $assignment->unit->property : null
        ];
        
        return view('tenant.unit-details', $data);
    }
    
    /**
     * Show messages page
     */
    public function messages()
    {
        return view('tenant.messages');
    }
    
    /**
     * Show contact landlord page
     */
    public function contactLandlord()
    {
        return view('tenant.contact-landlord');
    }
    
    /**
     * Show settings page
     */
    public function settings()
    {
        return view('tenant.settings');
    }
    
    /**
     * Process payment (M-Pesa STK Push)
     */
    public function makePayment(Request $request)
    {
        // This will be implemented when integrating M-Pesa
        return back()->with('success', 'Payment request sent to your phone!');
    }
}
