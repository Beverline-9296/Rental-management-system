<?php

namespace App\Http\Controllers\Landlord;

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
}
