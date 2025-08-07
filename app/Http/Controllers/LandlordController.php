<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordController extends Controller
{
    /**
     * Display the landlord dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get real data from database
        $properties = Property::where('landlord_id', Auth::id());
        $total_properties = $properties->count();
        $occupied_units = $properties->where('status', 'occupied')->count();
        $available_units = $properties->where('status', 'available')->count();
        // Calculate real total arrears for all tenants
        $propertyIds = $properties->pluck('id');
        $assignments = \App\Models\TenantAssignment::whereHas('unit', function($q) use ($propertyIds) {
            $q->whereIn('property_id', $propertyIds);
        })
        ->active()
        ->with(['tenant', 'unit', 'property'])
        ->get();

        $sum_arrears = 0;
        foreach ($assignments as $assignment) {
            $tenant = $assignment->tenant;
            if (!$tenant) continue;
            $totalPaid = \App\Models\Payment::where('tenant_id', $tenant->id)
                ->where('unit_id', $assignment->unit_id)
                ->where('payment_type', 'rent')
                ->sum('amount');
            $today = now();
            $start = $assignment->start_date;
            $end = $assignment->end_date && $assignment->end_date < $today ? $assignment->end_date : $today;
            $months = $start ? $start->diffInMonths($end) + 1 : 0;
            $totalDue = $months * $assignment->monthly_rent;
            $arrears = max(0, $totalDue - $totalPaid);
            $sum_arrears += $arrears;
            $tenantsSummary[] = [
                'tenant' => $tenant,
                'unit' => $assignment->unit,
                'property' => $assignment->property,
                'arrears' => $arrears,
            ];
        }

        $data = [
            'total_properties' => $total_properties,
            'total_tenants' => $occupied_units, // Assuming 1 tenant per occupied unit
            'occupied_units' => $occupied_units,
            'available_units' => $available_units,
            'sum_arrears' => $sum_arrears,
            'recent_activities' => [], // You can implement this later
            'upcoming_payments' => [], // You can implement this later
            'user' => $user
        ];
        
        return view('landlord.dashboard', $data);
    }
    
    /**
     * Show properties management page
     */
    public function properties()
    {
        return redirect()->route('properties.index');
    }
    
    /**
     * Show tenants management page
     */
    public function tenants()
    {
        return view('landlord.tenants');
    }
    
    /**
     * Show messages page
     */
    public function messages()
    {
        return view('landlord.messages');
    }
    
    /**
     * Show payments page
     */
    public function payments()
    {
        return view('landlord.payments.index');
    }
    
    /**
     * Show settings page
     */
    public function settings()
    {
        return view('landlord.settings');
    }
}
