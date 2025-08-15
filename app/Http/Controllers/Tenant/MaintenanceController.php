<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\TenantAssignment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:tenant');
    }

    /**
     * Display a listing of tenant's maintenance requests
     */
    public function index()
    {
        $maintenanceRequests = MaintenanceRequest::with(['property', 'unit', 'assignedTo'])
            ->where('tenant_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total' => MaintenanceRequest::where('tenant_id', Auth::id())->count(),
            'pending' => MaintenanceRequest::where('tenant_id', Auth::id())->where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::where('tenant_id', Auth::id())->where('status', 'in_progress')->count(),
            'completed' => MaintenanceRequest::where('tenant_id', Auth::id())->where('status', 'completed')->count(),
        ];

        return view('tenant.maintenance.index', compact('maintenanceRequests', 'stats'));
    }

    /**
     * Show the form for creating a new maintenance request
     */
    public function create()
    {
        // Get tenant's current assignments
        $assignments = TenantAssignment::with(['unit.property'])
            ->where('tenant_id', Auth::id())
            ->where('status', 'active')
            ->get();

        return view('tenant.maintenance.create', compact('assignments'));
    }

    /**
     * Store a newly created maintenance request
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high'
        ]);

        // Verify the tenant has access to this unit
        $assignment = TenantAssignment::where('tenant_id', Auth::id())
            ->where('unit_id', $request->unit_id)
            ->where('status', 'active')
            ->first();

        if (!$assignment) {
            return back()->withErrors(['unit_id' => 'You do not have access to this unit.']);
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'property_id' => $assignment->unit->property_id,
            'unit_id' => $request->unit_id,
            'tenant_id' => Auth::id(),
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'pending'
        ]);

        // Log the maintenance request activity
        ActivityLog::logActivity(
            Auth::id(),
            'maintenance_request',
            'Submitted ' . $request->priority . ' priority maintenance request',
            [
                'request_id' => $maintenanceRequest->id,
                'priority' => $request->priority,
                'unit' => $assignment->unit->unit_number
            ],
            'fas fa-tools',
            'orange'
        );

        return redirect()->route('tenant.maintenance.index')
            ->with('success', 'Maintenance request submitted successfully.');
    }

    /**
     * Display the specified maintenance request
     */
    public function show(MaintenanceRequest $maintenanceRequest)
    {
        // Ensure the maintenance request belongs to the authenticated tenant
        if ($maintenanceRequest->tenant_id !== Auth::id()) {
            abort(403, 'Unauthorized access to maintenance request.');
        }

        $maintenanceRequest->load(['property', 'unit', 'assignedTo']);

        return view('tenant.maintenance.show', compact('maintenanceRequest'));
    }
}
