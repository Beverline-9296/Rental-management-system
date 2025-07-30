@extends('landlord.layouts.app')

@section('title', 'My Properties')
@section('header', 'My Properties')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Properties Management</h1>
                    <p class="text-gray-600 mt-1">Manage your rental properties</p>
                </div>
                <a href="{{ route('landlord.properties.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium transition duration-200 flex items-center text-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Add Property
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Properties Grid -->
        @if($properties->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($properties as $property)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <!-- Property Image -->
                        <div class="relative h-48 bg-gray-200">
                            @if($property->image)
                                <img src="{{ asset('storage/properties/' . $property->image) }}" 
                                     alt="{{ $property->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <i class="fas fa-home text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            
                            <!-- Unit Status Indicator -->
                            @if($property->units_count > 0)
                                <div class="absolute top-3 right-3">
                                    @if($property->occupied_units_count == 0)
                                        <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                            All Available
                                        </span>
                                    @elseif($property->occupied_units_count == $property->units_count)
                                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                            Fully Occupied
                                        </span>
                                    @else
                                        <span class="bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                            Partially Occupied
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Property Details -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $property->name }}</h3>
                            <p class="text-gray-600 mb-3">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $property->location }}
                            </p>
                            
                            <!-- Units Summary -->
                            <div class="mb-4">
                                <span class="text-lg font-semibold text-gray-700">{{ $property->units_count }} Units</span>
                                @if($property->occupied_units_count > 0)
                                    <span class="text-sm text-gray-500">({{ $property->occupied_units_count }} occupied)</span>
                                @endif
                            </div>

                            <!-- Property Type -->
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mb-4">
                                <span><i class="fas fa-building mr-1"></i>{{ ucfirst($property->property_type) }}</span>
                                <span><i class="fas fa-home mr-1"></i>{{ $property->units_count }} {{ $property->units_count == 1 ? 'Unit' : 'Units' }}</span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2">
                                <a href="{{ route('landlord.properties.show', $property) }}" 
                                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded text-center text-sm font-medium transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ route('landlord.properties.edit', $property) }}" 
                                   class="flex-1 bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-2 rounded text-center text-sm font-medium transition duration-200">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form action="{{ route('landlord.properties.destroy', $property) }}" 
                                      method="POST" 
                                      class="flex-1"
                                      onsubmit="return confirm('Are you sure you want to delete this property?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded text-sm font-medium transition duration-200">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $properties->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="bg-white rounded-lg shadow-sm p-8 max-w-md mx-auto">
                    <i class="fas fa-home text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Properties Yet</h3>
                    <p class="text-gray-600 mb-6">Start by adding your first rental property to manage.</p>
                    <a href="{{ route('landlord.properties.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add Your First Property
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
