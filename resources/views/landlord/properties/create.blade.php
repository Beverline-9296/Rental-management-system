@extends('landlord.layouts.app')

@section('title', 'Add New Property')
@section('header', 'Add New Property')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Add New Property</h1>
                    <p class="text-gray-600 mt-1">Create a new rental property listing</p>
                </div>
                <a href="{{ route('landlord.properties.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium transition duration-200 flex items-center text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Properties
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <form action="{{ route('landlord.properties.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                <!-- Property Image -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Image</label>
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
                               value="{{ old('name') }}"
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
                            <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="house" {{ old('property_type') == 'house' ? 'selected' : '' }}>House</option>
                            <option value="studio" {{ old('property_type') == 'studio' ? 'selected' : '' }}>Studio</option>
                            <option value="condo" {{ old('property_type') == 'condo' ? 'selected' : '' }}>Condo</option>
                            <option value="townhouse" {{ old('property_type') == 'townhouse' ? 'selected' : '' }}>Townhouse</option>
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
                               value="{{ old('location') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., Westlands, Nairobi"
                               required>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Full Address *</label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., 123 Main Street, Westlands, Nairobi"
                               required>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Units Section -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Units Information</h3>
                    <p class="text-sm text-gray-500 mb-4">Add one or more units to this property. You can add more units later.</p>
                    
                    <div id="units-container">
                        <!-- Unit fields will be added here -->
                        <div class="unit-entry bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Number *</label>
                                    <input type="text" name="units[0][unit_number]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                           placeholder="e.g., A101" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                    <select name="units[0][type]" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="apartment">Apartment</option>
                                        <option value="studio">Studio</option>
                                        <option value="bed-sitter">Bed-sitter</option>
                                        <option value="single-room">Single Room</option>
                                        <option value="commercial">Commercial</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms *</label>
                                    <input type="number" name="units[0][bedrooms]" min="0" value="1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bathrooms *</label>
                                    <input type="number" name="units[0][bathrooms]" min="0" value="1" step="0.5"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Rent (KSh) *</label>
                                    <input type="number" name="units[0][rent_amount]" min="0" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deposit (KSh)</label>
                                    <input type="number" name="units[0][deposit_amount]" min="0" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Features (comma separated)</label>
                                    <input type="text" name="units[0][features]" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                           placeholder="e.g., parking, balcony, furnished">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="units[0][notes]" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
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
                              required>{{ old('address') }}</textarea>
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
                              placeholder="Describe the property features, amenities, and any other relevant details">{{ old('description') }}</textarea>
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
                        @endphp
                        @foreach($amenities as $key => $label)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $key }}"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       {{ in_array($key, old('amenities', [])) ? 'checked' : '' }}>
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
                              placeholder="Any additional notes or special instructions">{{ old('notes') }}</textarea>
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
                        Create Property
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Add new unit row
let unitCount = 1;
document.getElementById('add-unit').addEventListener('click', function() {
    const container = document.getElementById('units-container');
    const newUnit = document.createElement('div');
    newUnit.className = 'unit-entry bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200';
    newUnit.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <h4 class="text-sm font-medium text-gray-700">Unit ${unitCount + 1}</h4>
            <button type="button" class="text-red-500 hover:text-red-700 remove-unit">
                <i class="fas fa-times"></i> Remove
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Number *</label>
                <input type="text" name="units[${unitCount}][unit_number]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                       placeholder="e.g., A102" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                <select name="units[${unitCount}][type]" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="apartment">Apartment</option>
                    <option value="studio">Studio</option>
                    <option value="bed-sitter">Bed-sitter</option>
                    <option value="single-room">Single Room</option>
                    <option value="commercial">Commercial</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bedrooms *</label>
                <input type="number" name="units[${unitCount}][bedrooms]" min="0" value="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bathrooms *</label>
                <input type="number" name="units[${unitCount}][bathrooms]" min="0" value="1" step="0.5"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Rent (KSh) *</label>
                <input type="number" name="units[${unitCount}][rent_amount]" min="0" step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deposit (KSh)</label>
                <input type="number" name="units[${unitCount}][deposit_amount]" min="0" step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Features (comma separated)</label>
                <input type="text" name="units[${unitCount}][features]" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                       placeholder="e.g., parking, balcony, furnished">
            </div>
            <div class="md:col-span-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="units[${unitCount}][notes]" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
            </div>
        </div>
    `;
    
    // Add remove button functionality
    const removeBtn = newUnit.querySelector('.remove-unit');
    removeBtn.addEventListener('click', function() {
        container.removeChild(newUnit);
        unitCount--;
    });
    
    container.appendChild(newUnit);
    unitCount++;
});

// Handle remove unit buttons
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-unit')) {
        const unitEntry = e.target.closest('.unit-entry');
        if (unitEntry) {
            unitEntry.remove();
        }
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
