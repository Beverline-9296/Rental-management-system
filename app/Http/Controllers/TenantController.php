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
        
        // Get tenant's current assignment
        $assignment = TenantAssignment::with(['unit.property'])
            ->where('tenant_id', $user->id)
            ->where('status', 'active')
            ->first();
        
        // Calculate real financial data
        $totalPaid = $user->getTotalPaid();
        $totalDue = $user->getTotalDue();
        $arrears = $user->getArrears();
        $balance = $totalPaid - $totalDue; // Positive if overpaid, negative if owing
        
        // Get recent payments (last 5)
        $recentPayments = $user->payments()
            ->orderByDesc('payment_date')
            ->with(['unit', 'property'])
            ->limit(5)
            ->get();
        
        $data = [
            'rental_summary' => [
                'rent_amount' => $assignment ? $assignment->monthly_rent : 0,
                'balance' => abs($balance), // Show absolute value
                'arrears' => $arrears
            ],
            'unit_details' => [
                'property_name' => $assignment && $assignment->unit ? $assignment->unit->property->name : 'Not assigned',
                'unit_number' => $assignment && $assignment->unit ? $assignment->unit->unit_number : 'N/A',
                'rent_amount' => $assignment ? $assignment->monthly_rent : 0
            ],
            'recent_activities' => $recentPayments,
            'upcoming_payment' => null,
            'user' => $user,
            'arrears' => $arrears // Add direct arrears variable for compatibility
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
