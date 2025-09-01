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
        $processedTenants = [];
        foreach ($assignments as $assignment) {
            $tenant = $assignment->tenant;
            if (!$tenant || in_array($tenant->id, $processedTenants)) continue;
            
            $processedTenants[] = $tenant->id;
            
            // Use User model methods for consistent calculation
            $arrears = $tenant->getArrears();
            $sum_arrears += $arrears;
            $tenantsSummary[] = [
                'tenant' => $tenant,
                'unit' => $assignment->unit,
                'property' => $assignment->property,
                'arrears' => $arrears,
            ];
        }

        // Calculate upcoming payments with due dates and countdown
        $upcomingPayments = [];
        foreach ($assignments as $assignment) {
            $tenant = $assignment->tenant;
            if (!$tenant) continue;
            
            $today = now();
            
            // Get tenant's payment history to understand their payment pattern
            $lastPayment = \App\Models\Payment::where('tenant_id', $tenant->id)
                ->where('unit_id', $assignment->unit_id)
                ->where('payment_type', 'rent')
                ->orderBy('created_at', 'desc')
                ->first();
            
            // Calculate next due date based on assignment start date and payment history
            $startDate = $assignment->start_date ? $assignment->start_date->copy() : $today->copy()->subMonth();
            
            if ($lastPayment) {
                // If there are payments, calculate next due date from last payment
                $lastPaymentDate = $lastPayment->created_at->copy();
                $nextDueDate = $lastPaymentDate->copy()->addMonth();
                
                // Adjust to the same day of month as assignment start date
                $assignmentDay = $startDate->day;
                $nextDueDate = $nextDueDate->startOfMonth()->addDays($assignmentDay - 1);
            } else {
                // If no payments yet, use assignment start date pattern
                $assignmentDay = $startDate->day;
                $currentMonth = $today->copy()->startOfMonth()->addDays($assignmentDay - 1);
                
                if ($today->greaterThan($currentMonth)) {
                    // If we're past this month's due date, next payment is next month
                    $nextDueDate = $today->copy()->addMonth()->startOfMonth()->addDays($assignmentDay - 1);
                } else {
                    // Payment is due this month
                    $nextDueDate = $currentMonth;
                }
            }
            
            $daysRemaining = (int) $today->diffInDays($nextDueDate, false);
            
            // Only show if payment is due within next 45 days
            if ($daysRemaining >= 0 && $daysRemaining <= 45) {
                $upcomingPayments[] = [
                    'tenant_name' => $tenant->name,
                    'unit_number' => $assignment->unit->unit_number,
                    'property_name' => $assignment->property->name,
                    'amount' => $assignment->monthly_rent,
                    'due_date' => $nextDueDate,
                    'days_remaining' => $daysRemaining,
                    'is_overdue' => $daysRemaining < 0,
                    'urgency' => $daysRemaining <= 3 ? 'high' : ($daysRemaining <= 7 ? 'medium' : 'low')
                ];
            }
        }
        
        // Sort by days remaining (most urgent first)
        usort($upcomingPayments, function($a, $b) {
            return $a['days_remaining'] <=> $b['days_remaining'];
        });
        
        // Limit to top 5 upcoming payments
        $upcomingPayments = array_slice($upcomingPayments, 0, 5);

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
            'recent_activities' => $mappedActivities,
            'upcoming_payments' => $upcomingPayments
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
        $user = Auth::user();
        return view('landlord.settings', compact('user'));
    }
}
