<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\ActivityLog;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:landlord');
    }

    /**
     * Display a listing of maintenance requests
     */
    public function index(Request $request)
    {
        $query = MaintenanceRequest::with(['property', 'unit', 'tenant', 'assignedTo'])
            ->whereHas('property', function($q) {
                $q->where('landlord_id', Auth::id());
            });

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Filter by property
        if ($request->has('property_id') && $request->property_id !== '') {
            $query->where('property_id', $request->property_id);
        }

        $maintenanceRequests = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get properties for filter dropdown
        $properties = Property::where('landlord_id', Auth::id())->get();

        // Get statistics
        $stats = [
            'total' => MaintenanceRequest::whereHas('property', function($q) {
                $q->where('landlord_id', Auth::id());
            })->count(),
            'pending' => MaintenanceRequest::whereHas('property', function($q) {
                $q->where('landlord_id', Auth::id());
            })->where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::whereHas('property', function($q) {
                $q->where('landlord_id', Auth::id());
            })->where('status', 'in_progress')->count(),
            'completed' => MaintenanceRequest::whereHas('property', function($q) {
                $q->where('landlord_id', Auth::id());
            })->where('status', 'completed')->count(),
        ];

        return view('landlord.maintenance.index', compact('maintenanceRequests', 'properties', 'stats'));
    }

    /**
     * Display the specified maintenance request
     */
    public function show(MaintenanceRequest $maintenanceRequest)
    {
        // Ensure the maintenance request belongs to the landlord's property
        if ($maintenanceRequest->property->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to maintenance request.');
        }

        $maintenanceRequest->load(['property', 'unit', 'tenant', 'assignedTo']);

        return view('landlord.maintenance.show', compact('maintenanceRequest'));
    }

    /**
     * Update the status of a maintenance request
     */
    public function updateStatus(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        // Ensure the maintenance request belongs to the landlord's property
        if ($maintenanceRequest->property->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to maintenance request.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        $maintenanceRequest->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
            'notes' => $request->notes,
            'completed_at' => $request->status === 'completed' ? now() : null
        ]);

        return redirect()->route('landlord.maintenance.show', $maintenanceRequest)
            ->with('success', 'Maintenance request status updated successfully.');
    }

    /**
     * Get units for a specific property (AJAX)
     */
    public function getUnits($propertyId)
    {
        $property = Property::where('id', $propertyId)
            ->where('landlord_id', Auth::id())
            ->first();

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        $units = $property->units()->select('id', 'unit_number')->get();

        return response()->json($units);
    }
}
