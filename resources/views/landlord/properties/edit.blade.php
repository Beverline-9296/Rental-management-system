@extends('landlord.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Property</h1>
                    <p class="text-gray-600 mt-1">Update property information</p>
                </div>
                <a href="{{ route('landlord.properties.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Properties
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <form id="property-edit-form" action="{{ route('landlord.properties.update', $property) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                <!-- Current Image Display -->
                @if($property->image)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('storage/properties/' . $property->image) }}" alt="{{ $property->name }}" class="h-24 w-24 object-cover rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600">Current property image</p>
                                <p class="text-xs text-gray-500">Upload a new image below to replace this one</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Property Image -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $property->image ? 'Update Property Image' : 'Property Image' }}
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <div id="image-preview" class="hidden">
                                <img id="preview-img" class="mx-auto h-32 w-auto rounded-lg" src="#" alt="Preview">
                            </div>
                            <div id="upload-placeholder">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Property Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Property Name *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $property->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., Sunset Apartments Unit 2A"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Property Type -->
                    <div>
                        <label for="property_type" class="block text-sm font-medium text-gray-700 mb-2">Property Type *</label>
                        <select id="property_type" 
                                name="property_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            <option value="">Select Property Type</option>
                            <option value="apartment" {{ old('property_type', $property->property_type) == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="house" {{ old('property_type', $property->property_type) == 'house' ? 'selected' : '' }}>House</option>
                            <option value="studio" {{ old('property_type', $property->property_type) == 'studio' ? 'selected' : '' }}>Studio</option>
                            <option value="condo" {{ old('property_type', $property->property_type) == 'condo' ? 'selected' : '' }}>Condo</option>
                            <option value="townhouse" {{ old('property_type', $property->property_type) == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                        </select>
                        @error('property_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                        <input type="text" 
                               id="location" 
                               name="location" 
                               value="{{ old('location', $property->location) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., Westlands, Nairobi"
                               required>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address', $property->address) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="123 Main Street, Nairobi"
                               required>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>


                </div>

                <!-- Units Section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Units Information</h3>
                    <p class="text-sm text-gray-500 mb-4">Manage the units in this property. You can add, edit, or remove units as needed.</p>
                    
                    <div id="units-container">
                        @foreach($property->units as $index => $unit)
                            <div class="unit-entry bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-sm font-medium text-gray-700">Unit {{ $index + 1 }}</h4>
                                    <button type="button" class="text-red-500 hover:text-red-700 remove-unit">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <input type="hidden" name="units[{{ $index }}][id]" value="{{ $unit->id }}">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Number *</label>
                                        <input type="text" name="units[{{ $index }}][unit_number]" 
                                               value="{{ old('units.'.$index.'.unit_number', $unit->unit_number) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                               placeholder="e.g., A101" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="units[{{ $index }}][type]" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                            @foreach($unitTypes as $key => $label)
                                                <option value="{{ $key }}" {{ old('units.'.$index.'.type', $unit->unit_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms *</label>
                                        <input type="number" name="units[{{ $index }}][bedrooms]" 
                                               value="{{ old('units.'.$index.'.bedrooms', $unit->bedrooms) }}" 
                                               min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Bathrooms *</label>
                                        <input type="number" name="units[{{ $index }}][bathrooms]" 
                                               value="{{ old('units.'.$index.'.bathrooms', $unit->bathrooms) }}" 
                                               min="0" step="0.5" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Rent (KSh) *</label>
                                        <input type="number" name="units[{{ $index }}][rent_amount]" 
                                               value="{{ old('units.'.$index.'.rent_amount', $unit->rent_amount) }}" 
                                               min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Deposit (KSh)</label>
                                        <input type="number" name="units[{{ $index }}][deposit_amount]" 
                                               value="{{ old('units.'.$index.'.deposit_amount', $unit->deposit_amount) }}" 
                                               min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Features (comma separated)</label>
                                        <input type="text" name="units[{{ $index }}][features]" 
                                               value="{{ old('units.'.$index.'.features', is_array($unit->features) ? implode(', ', $unit->features) : $unit->features) }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                               placeholder="e.g., parking, balcony, furnished">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="units[{{ $index }}][notes]" rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('units.'.$index.'.notes', $unit->notes) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        <!-- Empty unit template for adding new units -->
                        <div id="unit-template" class="hidden">
                            <div class="unit-entry bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-sm font-medium text-gray-700">New Unit</h4>
                                    <button type="button" class="text-red-500 hover:text-red-700 remove-unit">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Number *</label>
                                        <input type="text" name="units[__INDEX__][unit_number]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                               placeholder="e.g., A101" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="units[__INDEX__][type]" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                            @foreach($unitTypes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms *</label>
                                        <input type="number" name="units[__INDEX__][bedrooms]" min="0" value="1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Bathrooms *</label>
                                        <input type="number" name="units[__INDEX__][bathrooms]" min="0" value="1" step="0.5"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Rent (KSh) *</label>
                                        <input type="number" name="units[__INDEX__][rent_amount]" min="0" step="0.01"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Deposit (KSh)</label>
                                        <input type="number" name="units[__INDEX__][deposit_amount]" min="0" step="0.01"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Features (comma separated)</label>
                                        <input type="text" name="units[__INDEX__][features]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                               placeholder="e.g., parking, balcony, furnished">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="units[__INDEX__][notes]" rows="2"
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="button" id="add-unit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i> Add Another Unit
                        </button>
                    </div>
                </div>

                <!-- Address -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Property Location</h3>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Full Address *</label>
                    <textarea id="address" 
                              name="address" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Enter the complete address of the property"
                              required>{{ old('address', $property->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Describe the property features, amenities, and any other relevant details">{{ old('description', $property->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amenities -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $amenities = [
                                'parking' => 'Parking',
                                'wifi' => 'WiFi',
                                'security' => '24/7 Security',
                                'gym' => 'Gym',
                                'pool' => 'Swimming Pool',
                                'garden' => 'Garden',
                                'balcony' => 'Balcony',
                                'furnished' => 'Furnished',
                                'air_conditioning' => 'Air Conditioning',
                                'elevator' => 'Elevator',
                                'laundry' => 'Laundry',
                                'backup_power' => 'Backup Power'
                            ];
                            $selectedAmenities = old('amenities', $property->amenities ?? []);
                        @endphp
                        @foreach($amenities as $key => $label)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $key }}"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       {{ in_array($key, $selectedAmenities) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Notes -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Any additional notes or special instructions">{{ old('notes', $property->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('landlord.properties.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition duration-200">
                        Update Property
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add form submission handler
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript loaded successfully');
    const form = document.getElementById('property-edit-form');
    console.log('Form found:', form ? 'Yes' : 'No');
    
    // Refresh CSRF token every 30 minutes to prevent 419 errors
    setInterval(function() {
        fetch('/csrf-token')
            .then(response => response.json())
            .then(data => {
                const tokenInput = document.querySelector('input[name="_token"]');
                if (tokenInput && data.token) {
                    tokenInput.value = data.token;
                    console.log('CSRF token refreshed');
                }
            })
            .catch(error => console.log('Token refresh failed:', error));
    }, 30 * 60 * 1000); // 30 minutes
    
    // Add form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submission started');
            
            // Check if we have at least one unit
            const unitEntries = document.querySelectorAll('.unit-entry:not(#unit-template)');
            console.log('Found unit entries:', unitEntries.length);
            
            if (unitEntries.length === 0) {
                e.preventDefault();
                alert('Please add at least one unit to the property.');
                return false;
            }
            
            console.log('Form validation passed, submitting...');
            return true;
        });
    }
});

    // Add new unit row
    const addUnitBtn = document.getElementById('add-unit');
    if (addUnitBtn) {
        addUnitBtn.addEventListener('click', addNewUnit);
    }

    // Initialize unit index
    let unitIndex = {{ count($property->units) }};
    
    // Add event delegation for remove buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-unit')) {
            e.preventDefault();
            const unitEntry = e.target.closest('.unit-entry');
            if (unitEntry && unitEntry.id !== 'unit-template') {
                // If it's an existing unit, add a hidden input to mark it for deletion
                const unitId = unitEntry.querySelector('input[name$="[id]"]');
                if (unitId && unitId.value) {
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = `units[${unitId.value}][_delete]`;
                    deleteInput.value = '1';
                    form.appendChild(deleteInput);
                }
                unitEntry.remove();
            }
        }
    });

    // Function to add a new unit
    function addNewUnit() {
        const container = document.getElementById('units-container');
        const template = document.getElementById('unit-template');
        
        if (!container || !template) return;
        
        // Create a new unit entry
        const newUnitDiv = document.createElement('div');
        newUnitDiv.className = 'unit-entry bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200';
        
        // Clone the template content and update indices
        const templateContent = template.innerHTML.replace(/__INDEX__/g, unitIndex);
        newUnitDiv.innerHTML = templateContent;
        
        // Add the new unit to the container
        container.insertBefore(newUnitDiv, template);
        
        // Increment the index for the next unit
        unitIndex++;
        
        console.log('Added new unit, current index:', unitIndex);
    }
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('upload-placeholder').classList.add('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
