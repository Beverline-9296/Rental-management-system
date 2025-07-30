@extends('layouts.app') {{-- Assuming you have a main layout file --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">My Properties</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('landlord.properties.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            + Add New Property
        </a>
    </div>

    @if ($properties->isEmpty())
        <p class="text-gray-600">You haven't added any properties yet.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($properties as $property)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if ($property->images->first())
                        <img src="{{ asset('storage/' . $property->images->first()->path) }}" alt="{{ $property->name }}" class="w-full h-48 object-cover">
                    @else
                        <img src="https://via.placeholder.com/400x200?text=No+Image" alt="No Image" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2">{{ $property->name }}</h2>
                        <p class="text-gray-600 mb-2"><i class="fas fa-map-marker-alt mr-2"></i>{{ $property->location }}</p>
                        <p class="text-gray-800 font-bold text-lg mb-4">{{ $property->currency }} {{ number_format($property->rent_amount, 2) }} / month</p>
                        <p class="text-gray-700 text-sm mb-4">Units: {{ $property->number_of_units }}</p>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('properties.show', $property) }}" class="text-blue-500 hover:underline text-sm">View Details</a>
                            <div class="flex space-x-2">
                                <a href="{{ route('properties.edit', $property) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm py-1 px-3 rounded">Edit</a>
                                <form action="{{ route('properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this property and all its associated data (units, tenants, payments)?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm py-1 px-3 rounded">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection