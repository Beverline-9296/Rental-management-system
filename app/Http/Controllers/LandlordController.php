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
        $total_arrears = $properties->sum('rent_amount'); // This would be calculated differently in real app
        
        $data = [
            'total_properties' => $total_properties,
            'total_tenants' => $occupied_units, // Assuming 1 tenant per occupied unit
            'occupied_units' => $occupied_units,
            'available_units' => $available_units,
            'total_arrears' => $total_arrears,
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
        return view('landlord.payments');
    }
    
    /**
     * Show settings page
     */
    public function settings()
    {
        return view('landlord.settings');
    }
}
