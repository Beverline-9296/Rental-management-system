<?php

namespace App\Http\Controllers\Landlord;
use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $properties = $user->ownedProperties()->pluck('id');
        $payments = \App\Models\Payment::whereIn('property_id', $properties)
            ->orderByDesc('payment_date')
            ->with(['tenant', 'unit', 'property', 'recordedBy'])
            ->paginate(20);

        // Fetch all active tenant assignments for units in landlord's properties
        $assignments = \App\Models\TenantAssignment::whereHas('unit', function($q) use ($properties) {
                $q->whereIn('property_id', $properties);
            })
            ->active()
            ->with(['tenant', 'unit', 'property'])
            ->get();

        // Group by tenant and unit for summary
        $tenantsSummary = [];
        foreach ($assignments as $assignment) {
            $tenant = $assignment->tenant;
            if (!$tenant) continue;
            $key = $tenant->id . '-' . $assignment->unit_id;
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
            $tenantsSummary[$key] = [
                'tenant' => $tenant,
                'property' => $assignment->property,
                'unit' => $assignment->unit,
                'total_due' => $totalDue,
                'total_paid' => $totalPaid,
                'arrears' => $arrears,
            ];
        }
        return view('landlord.payments.index', compact('payments', 'tenantsSummary'));
    }

    public function create()
    {
        $user = auth()->user();
        $properties = $user->ownedProperties()->with('units')->get();
        $tenants = \App\Models\User::where('role', 'tenant')->get();
        return view('landlord.payments.create', compact('properties', 'tenants'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
           'tenant_id' => 'required|exists:users,id',
            'unit_id' => 'nullable|exists:units,id',
            'property_id' => 'required|exists:properties,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);
        $validated['recorded_by'] = auth()->id();
        $payment = \App\Models\Payment::create($validated);
        return redirect()->route('landlord.payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $payment = \App\Models\Payment::where('id', $id)
            ->whereIn('property_id', $user->ownedProperties()->pluck('id'))
            ->with(['tenant', 'unit', 'property', 'recordedBy'])
            ->firstOrFail();
        return view('landlord.payments.show', compact('payment'));
    }

    public function showRequestForm()
    {
        $user = auth()->user();
        
        // Get all tenant assignments for landlord's properties
        $assignments = \App\Models\TenantAssignment::whereHas('unit.property', function($query) use ($user) {
            $query->where('landlord_id', $user->id);
        })
        ->active()
        ->with(['tenant', 'unit', 'property'])
        ->get();

        // Calculate payment status for each tenant
        $tenants = [];
        foreach ($assignments as $assignment) {
            $tenant = $assignment->tenant;
            if (!$tenant) continue;

            // Calculate arrears and next due date
            $totalPaid = \App\Models\Payment::where('tenant_id', $tenant->id)
                ->where('unit_id', $assignment->unit_id)
                ->where('payment_type', 'rent')
                ->sum('amount');

            $today = now();
            $start = $assignment->start_date ? $assignment->start_date->copy()->startOfMonth() : null;
            $end = $assignment->end_date && $assignment->end_date < $today ? $assignment->end_date->copy()->startOfMonth() : $today->copy()->startOfMonth();
            $months = $start ? $start->diffInMonths($end) + 1 : 0;
            $totalDue = $months * $assignment->monthly_rent;
            $arrears = max(0, $totalDue - $totalPaid);

            // Calculate next due date
            $lastPayment = \App\Models\Payment::where('tenant_id', $tenant->id)
                ->where('unit_id', $assignment->unit_id)
                ->where('payment_type', 'rent')
                ->orderBy('created_at', 'desc')
                ->first();

            $startDate = $assignment->start_date ? $assignment->start_date->copy() : $today->copy()->subMonth();
            
            if ($lastPayment) {
                $lastPaymentDate = $lastPayment->created_at->copy();
                $nextDueDate = $lastPaymentDate->copy()->addMonth();
                $assignmentDay = $startDate->day;
                $nextDueDate = $nextDueDate->startOfMonth()->addDays($assignmentDay - 1);
            } else {
                $assignmentDay = $startDate->day;
                $currentMonth = $today->copy()->startOfMonth()->addDays($assignmentDay - 1);
                
                if ($today->greaterThan($currentMonth)) {
                    $nextDueDate = $today->copy()->addMonth()->startOfMonth()->addDays($assignmentDay - 1);
                } else {
                    $nextDueDate = $currentMonth;
                }
            }

            $daysRemaining = (int) $today->diffInDays($nextDueDate, false);
            $isOverdue = $daysRemaining < 0;

            $tenants[] = [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'property_name' => $assignment->property->name,
                'unit_number' => $assignment->unit->unit_number,
                'monthly_rent' => $assignment->monthly_rent,
                'arrears' => $arrears,
                'next_due_date' => $nextDueDate,
                'days_remaining' => $daysRemaining,
                'is_overdue' => $isOverdue,
                'urgency' => $daysRemaining <= 0 ? 'overdue' : ($daysRemaining <= 3 ? 'high' : ($daysRemaining <= 7 ? 'medium' : 'low'))
            ];
        }

        // Sort by urgency (overdue first, then by days remaining)
        usort($tenants, function($a, $b) {
            if ($a['is_overdue'] && !$b['is_overdue']) return -1;
            if (!$a['is_overdue'] && $b['is_overdue']) return 1;
            return $a['days_remaining'] <=> $b['days_remaining'];
        });

        return view('landlord.payments.request', compact('tenants'));
    }

    public function sendRequest(Request $request)
    {
        $validated = $request->validate([
            'tenant_ids' => 'required|array',
            'tenant_ids.*' => 'exists:users,id',
            'message_type' => 'required|in:sms,email,both',
            'custom_message' => 'nullable|string|max:500'
        ]);

        $user = auth()->user();
        $sentCount = 0;
        $errors = [];

        foreach ($validated['tenant_ids'] as $tenantId) {
            try {
                $tenant = \App\Models\User::findOrFail($tenantId);
                
                // Get tenant assignment details
                $assignment = \App\Models\TenantAssignment::whereHas('unit.property', function($query) use ($user) {
                    $query->where('landlord_id', $user->id);
                })
                ->where('tenant_id', $tenantId)
                ->active()
                ->with(['unit', 'property'])
                ->first();

                if (!$assignment) continue;

                // Create payment request record
                \App\Models\PaymentRequest::create([
                    'landlord_id' => $user->id,
                    'tenant_id' => $tenantId,
                    'property_id' => $assignment->property->id,
                    'unit_id' => $assignment->unit->id,
                    'amount' => $assignment->monthly_rent,
                    'message_type' => $validated['message_type'],
                    'custom_message' => $validated['custom_message'],
                    'status' => 'sent',
                    'sent_at' => now()
                ]);

                // Log activity for tenant
                \App\Models\ActivityLog::logActivity(
                    $tenantId,
                    'payment_request_received',
                    'Payment request received from ' . $user->name . ' for ' . $assignment->property->name,
                    [
                        'amount' => $assignment->monthly_rent,
                        'property' => $assignment->property->name,
                        'unit' => $assignment->unit->unit_number,
                        'landlord' => $user->name
                    ],
                    'fas fa-exclamation-triangle',
                    'red'
                );

                // Send notifications based on type
                if (in_array($validated['message_type'], ['sms', 'both'])) {
                    $this->sendSmsNotification($tenant, $assignment, $validated['custom_message']);
                }

                if (in_array($validated['message_type'], ['email', 'both'])) {
                    $this->sendEmailNotification($tenant, $assignment, $validated['custom_message']);
                }

                // Log activity
                ActivityLog::logActivity(
                    $user->id,
                    'payment_request_sent',
                    "Sent payment request to {$tenant->name} for {$assignment->property->name} - Unit {$assignment->unit->unit_number}",
                    [
                        'tenant_id' => $tenantId,
                        'property_id' => $assignment->property->id,
                        'unit_id' => $assignment->unit->id,
                        'amount' => $assignment->monthly_rent,
                        'message_type' => $validated['message_type']
                    ],
                    'fas fa-paper-plane',
                    'blue'
                );

                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to send request to tenant ID {$tenantId}: " . $e->getMessage();
            }
        }

        if ($sentCount > 0) {
            $message = "Payment requests sent successfully to {$sentCount} tenant(s).";
            if (!empty($errors)) {
                $message .= " Some requests failed: " . implode(', ', $errors);
            }
            return redirect()->route('landlord.dashboard')->with('success', $message);
        } else {
            return back()->withErrors(['error' => 'Failed to send any payment requests.']);
        }
    }

    private function sendSmsNotification($tenant, $assignment, $customMessage = null)
    {
        if (!$tenant->phone) return;

        try {
            $smsService = new \App\Services\SmsService();
            
            // Calculate next due date
            $nextDueDate = $this->calculateNextDueDate($assignment);
            
            // Generate SMS message
            $message = $smsService->generatePaymentRequestMessage(
                auth()->user()->name,
                $tenant->name,
                $assignment->property->name,
                $assignment->unit->unit_number,
                $assignment->monthly_rent,
                $nextDueDate->format('M j, Y'),
                $customMessage
            );
            
            // Send SMS
            $result = $smsService->sendSms($tenant->phone, $message);
            
            if ($result['success']) {
                \Log::info('SMS notification sent successfully', [
                    'tenant_id' => $tenant->id,
                    'tenant_phone' => $tenant->phone,
                    'message_id' => $result['recipients'][0]['messageId'] ?? null
                ]);
            } else {
                \Log::error('Failed to send SMS notification', [
                    'tenant_id' => $tenant->id,
                    'error' => $result['message']
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to send SMS notification', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendEmailNotification($tenant, $assignment, $customMessage = null)
    {
        if (!$tenant->email) return;

        try {
            $landlord = auth()->user();
            $nextDueDate = $this->calculateNextDueDate($assignment);
            
            \Mail::to($tenant->email)->send(new \App\Mail\PaymentRequestMail(
                $landlord,
                $tenant,
                $assignment,
                $assignment->monthly_rent,
                $nextDueDate,
                $customMessage
            ));
            
            \Log::info('Email notification sent successfully', [
                'tenant_id' => $tenant->id,
                'tenant_email' => $tenant->email,
                'property' => $assignment->property->name,
                'unit' => $assignment->unit->unit_number
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate the next due date for a tenant assignment
     */
    private function calculateNextDueDate($assignment)
    {
        $startDate = \Carbon\Carbon::parse($assignment->start_date);
        $today = \Carbon\Carbon::today();
        
        // Get the last payment for this assignment
        $lastPayment = \App\Models\Payment::where('tenant_id', $assignment->tenant_id)
            ->where('property_id', $assignment->property_id)
            ->where('unit_id', $assignment->unit_id)
            ->orderBy('payment_date', 'desc')
            ->first();
        
        if ($lastPayment) {
            // Calculate next due date based on last payment
            $lastPaymentDate = \Carbon\Carbon::parse($lastPayment->payment_date);
            $nextDueDate = $lastPaymentDate->copy()->addMonth();
        } else {
            // No payments yet, calculate from assignment start date
            $monthsSinceStart = $startDate->diffInMonths($today);
            $nextDueDate = $startDate->copy()->addMonths($monthsSinceStart + 1);
        }
        
        return $nextDueDate;
    }
}
