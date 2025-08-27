<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TenantAssignment;
use App\Models\Unit;
use App\Models\Property;
use App\Models\ActivityLog;

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
        $totalRentPaid = $user->getTotalRentPaid();
        $totalDepositsPaid = $user->getTotalDepositsPaid();
        $totalDue = $user->getTotalDue();
        $arrears = $user->getArrears();
        $balance = $totalRentPaid - $totalDue; // Positive if overpaid, negative if owing
        
        // Get recent payments (last 5)
        $recentPayments = $user->payments()
            ->orderByDesc('payment_date')
            ->with(['unit', 'property'])
            ->limit(5)
            ->get();
        
        // Get payment requests for this tenant
        $paymentRequests = \App\Models\PaymentRequest::where('tenant_id', $user->id)
            ->with(['landlord', 'property', 'unit'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        // Calculate upcoming payment
        $upcomingPayment = null;
        if ($assignment) {
            $upcomingPayment = $this->calculateUpcomingPayment($user, $assignment);
        }
        
        // Get recent activities including all tenant activity types
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->whereIn('activity_type', [
                'login',
                'profile_updated', 
                'payment_completed',
                'payment_initiated',
                'maintenance_request',
                'message_sent',
                'payment_request_received'
            ])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($activity) {
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
        
        $data = [
            'rental_summary' => [
                'rent_amount' => $assignment ? $assignment->monthly_rent : 0,
                'balance' => abs($balance), // Show absolute value
                'arrears' => $arrears,
                'total_rent_paid' => $totalRentPaid,
                'total_deposits_paid' => $totalDepositsPaid
            ],
            'unit_details' => [
                'property_name' => $assignment && $assignment->unit ? $assignment->unit->property->name : 'Not assigned',
                'unit_number' => $assignment && $assignment->unit ? $assignment->unit->unit_number : 'N/A',
                'rent_amount' => $assignment ? $assignment->monthly_rent : 0
            ],
            'recent_activities' => $recentActivities,
            'recent_payments' => $recentPayments, // Keep original payments for the sidebar
            'payment_requests' => $paymentRequests,
            'upcoming_payment' => $upcomingPayment,
            'user' => $user,
            'arrears' => $arrears // Add direct arrears variable for compatibility
        ];
        
        return view('tenant.dashboard', $data);
    }
    
    /**
     * Calculate upcoming payment for tenant
     */
    private function calculateUpcomingPayment($user, $assignment)
    {
        // Get the last payment for this tenant
        $lastPayment = $user->payments()
            ->where('property_id', $assignment->property_id)
            ->where('unit_id', $assignment->unit_id)
            ->orderBy('payment_date', 'desc')
            ->first();
        
        $startDate = \Carbon\Carbon::parse($assignment->start_date);
        $today = \Carbon\Carbon::today();
        
        if ($lastPayment) {
            // Calculate next due date based on last payment
            $lastPaymentDate = \Carbon\Carbon::parse($lastPayment->payment_date);
            $nextDueDate = $lastPaymentDate->copy()->addMonth();
        } else {
            // No payments yet, calculate from assignment start date
            $monthsSinceStart = $startDate->diffInMonths($today);
            $nextDueDate = $startDate->copy()->addMonths($monthsSinceStart + 1);
        }
        
        // Calculate days until due
        $daysUntilDue = $today->diffInDays($nextDueDate, false);
        
        // Determine urgency
        $urgency = 'low';
        if ($daysUntilDue < 0) {
            $urgency = 'overdue';
        } elseif ($daysUntilDue <= 3) {
            $urgency = 'high';
        } elseif ($daysUntilDue <= 7) {
            $urgency = 'medium';
        }
        
        return [
            'amount' => $assignment->monthly_rent,
            'due_date' => $nextDueDate,
            'days_remaining' => $daysUntilDue,
            'urgency' => $urgency,
            'property_name' => $assignment->property->name,
            'unit_number' => $assignment->unit->unit_number
        ];
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
        $user = Auth::user();
        return view('tenant.settings', compact('user'));
    }
    
    /**
     * Update tenant settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);
        
        // Update user information
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->national_id = $validated['national_id'];
        
        // Update password if provided
        if (!empty($validated['new_password'])) {
            $user->password = bcrypt($validated['new_password']);
        }
        
        $user->save();
        
        // Log the profile update activity
        ActivityLog::logActivity(
            $user->id,
            'profile_updated',
            'Profile information updated',
            [
                'updated_fields' => array_keys($validated)
            ],
            'fas fa-user-edit',
            'blue'
        );
        
        return redirect()->route('tenant.settings')->with('success', 'Settings updated successfully!');
    }
    
    /**
     * Process payment (M-Pesa STK Push)
     */
    public function makePayment(Request $request)
    {
        $user = Auth::user();
        
        // Log the payment initiation activity
        ActivityLog::logActivity(
            $user->id,
            'payment_initiated',
            'Initiated payment request via M-Pesa',
            ['method' => 'mpesa'],
            'fas fa-mobile-alt',
            'green'
        );
        
        // This will be implemented when integrating M-Pesa
        return back()->with('success', 'Payment request sent to your phone!');
    }
}
