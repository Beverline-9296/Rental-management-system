<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    /**
     * Create a new controller instance.
     */
    /**
     * Get available units for a property
     *
     * @param int $propertyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableUnits($propertyId)
    {
        $units = Unit::where('property_id', $propertyId)
            ->where('status', 'vacant')
            ->select(['id', 'unit_number', 'type', 'rent_amount', 'bedrooms', 'bathrooms'])
            ->get();
            
        return response()->json($units);
    }

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:landlord');
    }

    /**
     * Display a listing of the properties.
     */
    public function index()
    {
        $properties = Property::where('landlord_id', Auth::id())
            ->with(['units'])
            ->withCount(['units', 'units as occupied_units_count' => function($query) {
                $query->where('status', 'occupied');
            }])
            ->latest()
            ->paginate(10);

        // Calculate stats
        $allProperties = Property::where('landlord_id', Auth::id())->with('units')->get();
        $totalProperties = $allProperties->count();
        $totalUnits = $allProperties->flatMap->units->count();
        $occupiedUnits = $allProperties->flatMap->units->where('status', 'occupied')->count();
        $vacantUnits = $allProperties->flatMap->units->where('status', 'available')->count();
        $totalRent = $allProperties->sum('rent_amount');

        $stats = [
            'total_properties' => $totalProperties,
            'total_units' => $totalUnits,
            'occupied_units' => $occupiedUnits,
            'vacant_units' => $vacantUnits,
            'total_rent' => $totalRent,
        ];

        return view('landlord.properties.index', compact('properties', 'stats'));
    }

    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        $propertyTypes = [
            'apartment' => 'Apartment',
            'house' => 'House',
            'townhouse' => 'Townhouse',
            'condo' => 'Condo',
            'duplex' => 'Duplex',
            'villa' => 'Villa',
            'other' => 'Other'
        ];

        return view('landlord.properties.create', compact('propertyTypes'));
    }

    /**
     * Store a newly created property in storage.
     */
    public function store(Request $request)
    {
        // Validate property data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'property_type' => 'required|string|in:apartment,house,townhouse,condo,duplex,villa,other',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'amenities' => 'nullable|array',
            'notes' => 'nullable|string',
            'units' => 'required|array|min:1',
            'units.*.unit_number' => 'required|string|max:50',
            'units.*.type' => 'required|string|in:apartment,studio,bed-sitter,single-room,commercial',
            'units.*.bedrooms' => 'required|integer|min:0',
            'units.*.bathrooms' => 'required|numeric|min:0',
            'units.*.rent_amount' => 'required|numeric|min:0',
            'units.*.deposit_amount' => 'nullable|numeric|min:0',
            'units.*.features' => 'nullable|string',
            'units.*.notes' => 'nullable|string',
        ]);

        // Start database transaction
        return \DB::transaction(function () use ($request, $validated) {
            try {
                // Handle file upload
                if ($request->hasFile('image')) {
                    $imageName = time() . '_' . Str::slug($request->name) . '.' . $request->image->extension();
                    $request->image->storeAs('properties', $imageName, 'public');
                    $validated['image'] = $imageName;
                }

                // Add landlord ID and process amenities
                $validated['landlord_id'] = Auth::id();
                if (isset($validated['amenities'])) {
                    $validated['amenities'] = array_filter($validated['amenities']);
                }

                // Remove units from property data
                $unitsData = $validated['units'];
                unset($validated['units']);

                // Create the property
                $property = Property::create($validated);

                // Log the property creation activity
                ActivityLog::logActivity(
                    Auth::id(),
                    'property_created',
                    'Created new property: ' . $property->name,
                    [
                        'property_id' => $property->id,
                        'property_name' => $property->name,
                        'property_type' => $property->property_type,
                        'location' => $property->location,
                        'units_count' => count($unitsData)
                    ],
                    'fas fa-building',
                    'green'
                );

                // Create units for the property
                foreach ($unitsData as $unitData) {
                    // Process features (comma-separated string to array)
                    if (!empty($unitData['features'])) {
                        $features = array_map('trim', explode(',', $unitData['features']));
                        $unitData['features'] = array_filter($features); // Remove empty values
                    } else {
                        $unitData['features'] = [];
                    }

                    // Map form field to database field
                    $unitData['unit_type'] = $unitData['type'];
                    unset($unitData['type']);
                    
                    // Set default status and property ID
                    $unitData['status'] = 'available';
                    $unitData['property_id'] = $property->id;

                    // Create the unit
                    $property->units()->create($unitData);
                }

                return redirect()->route('landlord.properties.show', $property)
                    ->with('success', 'Property and units created successfully!');

            } catch (\Exception $e) {
                \Log::error('Error creating property: ' . $e->getMessage());
                return back()->withInput()
                    ->with('error', 'An error occurred while creating the property. Please try again.');
            }
        });
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property)
    {
        $this->authorize('view', $property);
        
        $property->load(['units' => function($query) {
            $query->withCount('tenantAssignments');
        }]);

        $unitsByStatus = [
            'available' => $property->units->where('status', 'available'),
            'occupied' => $property->units->where('status', 'occupied'),
            'maintenance' => $property->units->where('status', 'maintenance'),
        ];

        return view('landlord.properties.show', compact('property', 'unitsByStatus'));
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property)
    {
        $this->authorize('update', $property);
        
        $propertyTypes = [
            'apartment' => 'Apartment',
            'house' => 'House',
            'townhouse' => 'Townhouse',
            'condo' => 'Condo',
            'duplex' => 'Duplex',
            'villa' => 'Villa',
            'other' => 'Other'
        ];

        $unitTypes = [
            'apartment' => 'Apartment',
            'studio' => 'Studio',
            'bed-sitter' => 'Bed-sitter',
            'single-room' => 'Single Room',
            'commercial' => 'Commercial Space'
        ];

        // Load the property with its units
        $property->load('units');

        return view('landlord.properties.edit', compact('property', 'propertyTypes', 'unitTypes'));
    }

    /**
     * Update the specified property in storage.
     */
    public function update(Request $request, Property $property)
    {
        \Log::info('PropertyController update method called', [
            'property_id' => $property->id,
            'user_id' => auth()->id(),
            'request_method' => $request->method(),
            'request_url' => $request->url()
        ]);

        $this->authorize('update', $property);
        
        \Log::info('Starting property update', [
            'property_id' => $property->id, 
            'request_method' => $request->method(),
            'request_data' => $request->except(['_token', '_method', 'image']),
            'has_units' => $request->has('units'),
            'units_count' => $request->has('units') ? count($request->input('units', [])) : 0
        ]);
        
        // Start database transaction
        return \DB::transaction(function () use ($request, $property) {
            try {
                \Log::info('About to validate request data');
                
                // Validate property data
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'property_type' => 'required|string|in:apartment,house,townhouse,condo,duplex,villa,other',
                    'description' => 'nullable|string',
                    'location' => 'required|string|max:255',
                    'address' => 'required|string|max:255',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'amenities' => 'nullable|array',
                    'notes' => 'nullable|string',
                    'remove_image' => 'nullable|boolean',
                    'units' => 'required|array|min:1',
                    'units.*.id' => 'nullable|exists:units,id,property_id,' . $property->id,
                    'units.*.unit_number' => 'required|string|max:50',
                    'units.*.type' => 'required|string|in:apartment,studio,bed-sitter,single-room,commercial',
                    'units.*.bedrooms' => 'required|integer|min:0',
                    'units.*.bathrooms' => 'required|numeric|min:0',
                    'units.*.rent_amount' => 'required|numeric|min:0',
                    'units.*.deposit_amount' => 'nullable|numeric|min:0',
                    'units.*.features' => 'nullable|string',
                    'units.*.notes' => 'nullable|string',
                ]);

                \Log::info('Validation passed, validated data:', [
                    'property_fields' => array_keys($validated),
                    'units_count' => count($validated['units'] ?? [])
                ]);

                // Handle file upload
                // Handle file upload
                if ($request->hasFile('image')) {
                    // Delete old image if exists
                    if ($property->image) {
                        Storage::disk('public')->delete('properties/' . $property->image);
                    }
                    
                    $imageName = time() . '_' . Str::slug($request->name) . '.' . $request->image->extension();
                    $request->image->storeAs('properties', $imageName, 'public');
                    $validated['image'] = $imageName;
                } elseif ($request->has('remove_image') && $request->remove_image) {
                    // Remove the image if the remove_image checkbox is checked
                    if ($property->image) {
                        Storage::disk('public')->delete('properties/' . $property->image);
                        $validated['image'] = null;
                    }
                }

                // Process amenities
                if (isset($validated['amenities'])) {
                    $validated['amenities'] = array_filter($validated['amenities']);
                } else {
                    $validated['amenities'] = [];
                }

                // Validate unique unit numbers within this property
                $unitNumbers = array_column($validated['units'], 'unit_number');
                if (count($unitNumbers) !== count(array_unique($unitNumbers))) {
                    return back()->withInput()->with('error', 'Unit numbers must be unique within the property.');
                }

                // Remove units from property data before updating
                $unitsData = $validated['units'];
                unset($validated['units']);

                // Update the property
                \Log::info('About to update property with data:', $validated);
                $property->update($validated);
                \Log::info('Property updated successfully');

                // Log the property update activity
                ActivityLog::logActivity(
                    Auth::id(),
                    'property_updated',
                    'Updated property: ' . $property->name,
                    [
                        'property_id' => $property->id,
                        'property_name' => $property->name,
                        'units_updated' => count($unitsData)
                    ],
                    'fas fa-edit',
                    'blue'
                );

                // Get existing unit IDs to track which ones to keep
                $existingUnitIds = $property->units->pluck('id')->toArray();
                $updatedUnitIds = [];

                // Update or create units
                foreach ($unitsData as $unitData) {
                    // Process features (comma-separated string to array)
                    if (!empty($unitData['features'])) {
                        $features = array_map('trim', explode(',', $unitData['features']));
                        $unitData['features'] = array_filter($features); // Remove empty values
                    } else {
                        $unitData['features'] = [];
                    }

                    // Map form field to database field
                    if (isset($unitData['type'])) {
                        $unitData['unit_type'] = $unitData['type'];
                        unset($unitData['type']);
                    }

                    if (isset($unitData['id'])) {
                        // Update existing unit
                        $unit = $property->units()->find($unitData['id']);
                        if ($unit) {
                            $unit->update($unitData);
                            $updatedUnitIds[] = $unit->id;
                        }
                    } else {
                        // Create new unit
                        $unitData['property_id'] = $property->id;
                        $unitData['status'] = 'available'; // Default status for new units
                        $unit = $property->units()->create($unitData);
                        $updatedUnitIds[] = $unit->id;
                    }
                }

                // Delete units that were not included in the update
                $unitsToDelete = array_diff($existingUnitIds, $updatedUnitIds);
                if (!empty($unitsToDelete)) {
                    // Don't delete units that have active tenants
                    $property->units()
                        ->whereIn('id', $unitsToDelete)
                        ->where('status', 'available')
                        ->delete();
                }

                \Log::info('Property update completed successfully');
                return redirect()->route('landlord.properties.edit', $property)
                    ->with('success', 'Property updated successfully!');

            } catch (\Exception $e) {
                \Log::error('Error updating property', [
                    'property_id' => $property->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]);
                
                return back()->withInput()->with('error', 'Error: ' . $e->getMessage() . ' (Line: ' . $e->getLine() . ')');
            }
        });
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);
        
        return \DB::transaction(function () use ($property) {
            try {
                // Delete all units associated with this property first
                $property->units()->delete();
                
                // Delete property image if exists
                if ($property->image) {
                    Storage::disk('public')->delete('properties/' . $property->image);
                }
                
                // Log the property deletion activity
                ActivityLog::logActivity(
                    Auth::id(),
                    'property_deleted',
                    'Deleted property: ' . $property->name,
                    [
                        'property_name' => $property->name,
                        'property_type' => $property->property_type,
                        'location' => $property->location
                    ],
                    'fas fa-trash',
                    'red'
                );

                // Delete the property
                $property->delete();
                
                return redirect()->route('landlord.properties.index')
                    ->with('success', 'Property and all its units deleted successfully!');
                    
            } catch (\Exception $e) {
                \Log::error('Error deleting property: ' . $e->getMessage());
                return back()->with('error', 'An error occurred while deleting the property. Please try again.');
            }
        });
    }
}
