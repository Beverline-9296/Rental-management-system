@extends('landlord.layouts.app')

@section('title', $property->name)
@section('header', $property->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $property->name }}</h1>
                    <p class="text-gray-600 mt-1">Property Details</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('landlord.properties.edit', $property) }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium transition duration-200 flex items-center text-sm">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Property
                    </a>
                    <a href="{{ route('landlord.properties.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium transition duration-200 flex items-center text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Properties
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Property Image -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="relative h-96 bg-gray-200">
                        @if($property->image)
                            <img src="{{ asset('storage/properties/' . $property->image) }}" 
                                 alt="{{ $property->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <div class="text-center">
                                    <i class="fas fa-home text-6xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-500">No image available</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            @if($property->units->count() > 0)
                                @php
                                    $occupiedUnits = $unitsByStatus['occupied']->count();
                                    $totalUnits = $property->units->count();
                                @endphp
                                @if($occupiedUnits == 0)
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        All Available
                                    </span>
                                @elseif($occupiedUnits == $totalUnits)
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        Fully Occupied
                                    </span>
                                @else
                                    <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        Partially Occupied
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Property Description -->
                @if($property->description)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Description</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $property->description }}</p>
                    </div>
                @endif

                <!-- Amenities -->
                @if($property->amenities && count($property->amenities) > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Amenities</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @php
                                $amenityLabels = [
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
                            @foreach($property->amenities as $amenity)
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ $amenityLabels[$amenity] ?? ucfirst(str_replace('_', ' ', $amenity)) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Units Section -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Units ({{ $property->units->count() }})</h3>
                    
                    @if($property->units->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($property->units as $unit)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="text-lg font-semibold text-gray-900">Unit {{ $unit->unit_number }}</h4>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($unit->status === 'available') bg-green-100 text-green-800
                                            @elseif($unit->status === 'occupied') bg-blue-100 text-blue-800
                                            @elseif($unit->status === 'maintenance') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($unit->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Type:</span>
                                            <span class="font-medium">{{ ucfirst(str_replace('-', ' ', $unit->unit_type)) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Rent:</span>
                                            <span class="font-medium text-blue-600">KSh {{ number_format($unit->rent_amount, 2) }}</span>
                                        </div>
                                        @if($unit->deposit_amount)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Deposit:</span>
                                                <span class="font-medium">KSh {{ number_format($unit->deposit_amount, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Bedrooms:</span>
                                            <span class="font-medium"><i class="fas fa-bed mr-1"></i>{{ $unit->bedrooms }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Bathrooms:</span>
                                            <span class="font-medium"><i class="fas fa-bath mr-1"></i>{{ $unit->bathrooms }}</span>
                                        </div>
                                        @if($unit->features && count($unit->features) > 0)
                                            <div class="mt-3">
                                                <span class="text-gray-600 text-xs">Features:</span>
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach($unit->features as $feature)
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $feature }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if($unit->notes)
                                            <div class="mt-3">
                                                <span class="text-gray-600 text-xs">Notes:</span>
                                                <p class="text-xs text-gray-700 mt-1">{{ $unit->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No units found for this property.</p>
                    @endif
                </div>

                <!-- Additional Notes -->
                @if($property->notes)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Additional Notes</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $property->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Property Details Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Property Details</h3>
                    
                    <div class="space-y-4">
                        <!-- Property Type -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Property Type</span>
                            <span class="font-medium text-gray-900">{{ ucfirst($property->property_type) }}</span>
                        </div>

                        <!-- Total Units -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Total Units</span>
                            <span class="font-medium text-gray-900">
                                <i class="fas fa-home mr-1"></i>{{ $property->units->count() }}
                            </span>
                        </div>

                        <!-- Available Units -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Available Units</span>
                            <span class="font-medium text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>{{ $unitsByStatus['available']->count() }}
                            </span>
                        </div>

                        <!-- Occupied Units -->
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-600">Occupied Units</span>
                            <span class="font-medium text-blue-600">
                                <i class="fas fa-users mr-1"></i>{{ $unitsByStatus['occupied']->count() }}
                            </span>
                        </div>

                        <!-- Maintenance Units -->
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Under Maintenance</span>
                            <span class="font-medium text-yellow-600">
                                <i class="fas fa-tools mr-1"></i>{{ $unitsByStatus['maintenance']->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Location</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Area</label>
                            <p class="font-medium text-gray-900">{{ $property->location }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm text-gray-600">Full Address</label>
                            <p class="font-medium text-gray-900">{{ $property->address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('landlord.properties.edit', $property) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Property
                        </a>
                        
                        <form action="{{ route('landlord.properties.destroy', $property) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this property? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                Delete Property
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Property Info -->
                <div class="bg-gray-100 rounded-lg p-4 mt-6">
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><strong>Created:</strong> {{ $property->created_at->format('M d, Y') }}</p>
                        <p><strong>Last Updated:</strong> {{ $property->updated_at->format('M d, Y') }}</p>
                        <p><strong>Property ID:</strong> #{{ $property->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
