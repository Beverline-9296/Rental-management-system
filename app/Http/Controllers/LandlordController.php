<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordController extends Controller
{
    /**
     * Display the landlord dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get recent properties with their unit counts
        $properties = \App\Models\Property::where('landlord_id', $user->id)
            ->withCount('units')
            ->withCount(['units as occupied_units' => function($query) {
                $query->where('status', 'occupied');
            }])
            ->withCount(['units as vacant_units' => function($query) {
                $query->where('status', 'vacant');
            }])
            ->latest()
            ->take(5)
            ->get();
            
        // Get summary statistics
        $total_properties = \App\Models\Property::where('landlord_id', $user->id)->count();
        
        // Get total occupied units
        $total_occupied_units = \App\Models\Unit::whereHas('property', function($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })
            ->where('status', 'occupied')
            ->count();
            
        // Get total unique tenants
        $total_tenants = \App\Models\TenantAssignment::whereHas('unit.property', function($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })
            ->where('status', 'active')
            ->distinct('tenant_id')
            ->count('tenant_id');
        
        // Calculate real total arrears using same logic as PaymentController
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
            // Calculate full months only for clean amounts
            $start = $assignment->start_date ? $assignment->start_date->copy()->startOfMonth() : null;
            $end = $assignment->end_date && $assignment->end_date < $today ? $assignment->end_date->copy()->startOfMonth() : $today->copy()->startOfMonth();
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

        // Get recent activities for the authenticated user
        $userId = Auth::id();
        \Log::info('Fetching activities for user ID: ' . $userId);
        
        // Debug: Check all activities in database
        $allActivities = ActivityLog::orderByDesc('created_at')->limit(10)->get();
        \Log::info('Total activities in database: ' . $allActivities->count());
        \Log::info('Sample activities:', $allActivities->pluck('user_id', 'activity_type')->toArray());
        
        $recentActivities = ActivityLog::getRecentActivities($userId, 5);
        \Log::info('Found landlord activities count: ' . $recentActivities->count());
        
        // Also get activities for tenants under this landlord's properties
        $tenantIds = \App\Models\TenantAssignment::whereHas('unit.property', function($query) {
            $query->where('landlord_id', Auth::id());
        })->pluck('tenant_id')->unique();
        
        $tenantActivities = ActivityLog::whereIn('user_id', $tenantIds)
            ->whereIn('activity_type', ['payment_completed', 'maintenance_request', 'login'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
        
        \Log::info('Found tenant activities count: ' . $tenantActivities->count());
        
        // Combine landlord and tenant activities
        $allActivities = $recentActivities->concat($tenantActivities)
            ->sortByDesc('created_at')
            ->take(5);
        
        $mappedActivities = $allActivities->map(function ($activity) {
            return [
                'description' => $activity->description,
                'time' => $activity->created_at->diffForHumans(),
                'date' => $activity->created_at->format('M d, Y'),
                'icon' => $activity->icon ?? 'fas fa-info-circle',
                'color' => $activity->color ?? 'blue',
                'type' => $activity->activity_type,
                'metadata' => $activity->metadata ?? []
            ];
        })->toArray();
        
        return view('landlord.dashboard', [
            'properties' => $properties,
            'user' => $user,
            'total_properties' => $total_properties,
            'total_tenants' => $total_tenants,
            'occupied_units' => $total_occupied_units,
            'sum_arrears' => $sum_arrears,
            'recent_activities' => $mappedActivities
        ]);
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
