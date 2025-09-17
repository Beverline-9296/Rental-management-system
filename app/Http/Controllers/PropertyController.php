<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::where('landlord_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('landlord.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('landlord.properties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'rent_amount' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
            'size_sqft' => 'nullable|numeric|min:0',
            'property_type' => 'required|string|in:apartment,house,studio,condo,townhouse',
            'status' => 'required|string|in:available,occupied,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        $validated['landlord_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('properties', 'public');
        }

        $property = Property::create($validated);

        return redirect()->route('properties.index')
            ->with('
        =-', 'Property created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Property $property)
    {
        // Ensure the property belongs to the authenticated landlord
        if ($property->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this property.');
        }

        return view('landlord.properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Property $property)
    {
        // Ensure the property belongs to the authenticated landlord
        if ($property->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this property.');
        }

        return view('landlord.properties.edit', compact('property'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        // Ensure the property belongs to the authenticated landlord
        if ($property->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this property.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'rent_amount' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:1',
            'bathrooms' => 'required|integer|min:1',
            'size_sqft' => 'nullable|numeric|min:0',
            'property_type' => 'required|string|in:apartment,house,studio,condo,townhouse',
            'status' => 'required|string|in:available,occupied,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($property->image) {
                Storage::disk('public')->delete($property->image);
            }
            $validated['image'] = $request->file('image')->store('properties', 'public');
        }

        $property->update($validated);

        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        // Ensure the property belongs to the authenticated landlord
        if ($property->landlord_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this property.');
        }

        // Delete image if exists
        if ($property->image) {
            Storage::disk('public')->delete($property->image);
        }

        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully!');
    }
}
