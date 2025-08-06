@extends('landlord.layouts.app')

@section('content')
<div class="min-h-screen gradient-bg py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Glass-card Header -->
        <div class="glass-card shadow-lg border-b border-white border-opacity-20 mb-10 animate-fadeInUp">
            <div class="flex items-center justify-between px-8 py-6">
                <div class="animate-slideInLeft">
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Properties Overview</h2>
                    <p class="text-gray-600 mt-1">Hello, {{ Auth::user()->name }}! Here’s your property overview.</p>
                </div>
                <div class="flex items-center space-x-4 animate-fadeInUp">
                    <div class="flex items-center space-x-2 bg-white bg-opacity-50 px-4 py-2 rounded-full">
                        <i class="fas fa-user-circle text-2xl text-blue-600"></i>
                        <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-full hover:from-red-600 hover:to-red-700 transition duration-200 shadow-lg">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <!-- Summary Stats Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10 animate-fadeIn">
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 flex items-center justify-between animate-fadeInUp" style="animation-delay: 0.1s;">
                <div>
                    <div class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-blue-600 bg-clip-text text-transparent">{{ $stats['total_properties'] }}</div>
                    <div class="text-sm font-medium text-gray-600 mt-2">Total Properties</div>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-xl">
                    <i class="fas fa-building text-2xl text-white"></i>
                </div>
            </div>
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 flex items-center justify-between animate-fadeInUp" style="animation-delay: 0.2s;">
                <div>
                    <div class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-purple-600 bg-clip-text text-transparent">{{ $stats['total_units'] }}</div>
                    <div class="text-sm font-medium text-gray-600 mt-2">Total Units</div>
                </div>
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4 rounded-xl">
                    <i class="fas fa-layer-group text-2xl text-white"></i>
                </div>
            </div>
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 flex items-center justify-between animate-fadeInUp" style="animation-delay: 0.3s;">
                <div>
                    <div class="text-3xl font-bold bg-gradient-to-r from-green-500 to-green-600 bg-clip-text text-transparent">{{ $stats['occupied_units'] }}</div>
                    <div class="text-sm font-medium text-gray-600 mt-2">Occupied Units</div>
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-600 p-4 rounded-xl">
                    <i class="fas fa-door-open text-2xl text-white"></i>
                </div>
            </div>
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 flex items-center justify-between animate-fadeInUp" style="animation-delay: 0.4s;">
                <div>
                    <div class="text-3xl font-bold bg-gradient-to-r from-yellow-500 to-yellow-600 bg-clip-text text-transparent">{{ $stats['vacant_units'] }}</div>
                    <div class="text-sm font-medium text-gray-600 mt-2">Vacant Units</div>
                </div>
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-4 rounded-xl">
                    <i class="fas fa-door-closed text-2xl text-white"></i>
                </div>
            </div>
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 flex items-center justify-between animate-fadeInUp" style="animation-delay: 0.5s;">
                <div>
                    <div class="text-3xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">{{ $stats['total_rent'] }}</div>
                    <div class="text-sm font-medium text-gray-600 mt-2">Total Rent Value</div>
                </div>
                <div class="bg-gradient-to-r from-pink-500 to-pink-600 p-4 rounded-xl">
                    <i class="fas fa-coins text-2xl text-white"></i>
                </div>
            </div>
        </div>

        </div>

        @if (session('success'))
            <div class="glass-card backdrop-blur-xl bg-white/20 border border-white/30 shadow-2xl bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6 shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Properties Grid -->
            <div class="lg:col-span-2">
                @if ($properties->isEmpty())
                    <div class="glass-card backdrop-blur-xl bg-white/20 border border-white/30 shadow-2xl p-12 rounded-2xl text-center shadow-xl">
                        <i class="fas fa-building text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-white font-bold mb-2">No properties found</h3>
                        <p class="text-gray-600 mb-6">Start building your rental portfolio by adding your first property.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 animate-fadeIn">
                        @foreach ($properties as $property)
                            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:scale-[1.03] transition duration-300 overflow-hidden animate-fadeInUp relative" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-2xl font-extrabold text-white drop-shadow-sm">{{ $property->name }}</h2>
                                    @php
                                        $vacant = $property->units->where('status', 'available')->count();
                                        $occupied = $property->units->where('status', 'occupied')->count();
                                    @endphp
                                    @if($vacant === 0 && $occupied > 0)
                                        <span class="inline-block bg-gradient-to-r from-green-400 to-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">Fully Occupied</span>
                                    @elseif($vacant > 0)
                                        <span class="inline-block bg-gradient-to-r from-yellow-400 to-yellow-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">Vacant Units: {{ $vacant }}</span>
                                    @else
                                        <span class="inline-block bg-gradient-to-r from-gray-400 to-gray-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">No Units</span>
                                    @endif
                                </div>
                                <div class="relative w-full h-48 mb-4">
                                    @if ($property->images->first())
                                        <img src="{{ asset('storage/' . $property->images->first()->path) }}" alt="{{ $property->name }}" class="w-full h-48 object-cover rounded-xl">
                                    @else
                                        <img src="https://via.placeholder.com/400x200?text=No+Image" alt="No Image" class="w-full h-48 object-cover rounded-xl">
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-indigo-700/60 to-transparent rounded-xl"></div>
                                </div>
                                <div class="flex items-center gap-2 mb-2">
                                    <i class="fas fa-map-marker-alt text-indigo-300"></i>
                                    <span class="text-white/90">{{ $property->location }}</span>
                                </div>
                                <p class="text-white font-bold text-lg mb-4">{{ $property->currency }} {{ number_format($property->rent_amount, 2) }} / month</p>
                                <p class="text-white/90 text-sm mb-4">Units: {{ $property->number_of_units }}</p>
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('properties.show', $property) }}"
                                       class="text-purple-600 hover:text-purple-800 font-medium text-sm transition duration-200">
                                        <i class="fas fa-eye mr-1"></i>View Details
                                    </a>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('properties.edit', $property) }}"
                                           class="bg-gradient-to-r from-indigo-400 to-purple-600 hover:from-indigo-500 hover:to-purple-700 text-white text-sm py-2 px-4 rounded-lg transition duration-200 shadow-xl hover:shadow-2xl focus:outline-none focus:ring-2 focus:ring-purple-400">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <form action="{{ route('properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this property and all its associated data (units, tenants, payments)?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-gradient-to-r from-pink-500 to-red-600 hover:from-pink-600 hover:to-red-700 text-white text-sm py-2 px-4 rounded-lg transition duration-200 shadow-xl hover:shadow-2xl focus:outline-none focus:ring-2 focus:ring-red-400">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @if($vacant === 0 && $occupied > 0)
                                <span class="inline-block bg-gradient-to-r from-green-400 to-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">Fully Occupied</span>
                            @elseif($vacant > 0)
                                <span class="inline-block bg-gradient-to-r from-yellow-400 to-yellow-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">Vacant Units: {{ $vacant }}</span>
                            @else
                                <span class="inline-block bg-gradient-to-r from-gray-400 to-gray-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">No Units</span>
                            @endif
                        </div>
                        <div class="relative w-full h-48 mb-4">
                            <img src="https://via.placeholder.com/400x200?text=No+Image" alt="No Image" class="w-full h-48 object-cover rounded-xl">
                            <div class="absolute inset-0 bg-gradient-to-t from-indigo-700/60 to-transparent rounded-xl"></div>
                        </div>
                    @endif
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
    <h2 class="text-2xl font-extrabold text-white drop-shadow-sm">{{ $property->name }}</h2>
    @php
        $vacant = $property->units->where('status', 'available')->count();
        $occupied = $property->units->where('status', 'occupied')->count();
    @endphp
    @if($vacant === 0 && $occupied > 0)
        <span class="inline-block bg-gradient-to-r from-green-400 to-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">Fully Occupied</span>
    @elseif($vacant > 0)
        <span class="inline-block bg-gradient-to-r from-yellow-400 to-yellow-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">Vacant Units: {{ $vacant }}</span>
    @else
        <span class="inline-block bg-gradient-to-r from-gray-400 to-gray-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">No Units</span>
    @endif
</div>
                        <div class="flex items-center gap-2 mb-2">
    <i class="fas fa-map-marker-alt text-indigo-300"></i>
    <span class="text-white/90">{{ $property->location }}</span>
</div>
                        <p class="text-white font-bold font-bold text-lg mb-4">{{ $property->currency }} {{ number_format($property->rent_amount, 2) }} / month</p>
                        <p class="text-white/90 text-sm mb-4">Units: {{ $property->number_of_units }}</p>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('properties.show', $property) }}" 
                               class="text-purple-600 hover:text-purple-800 font-medium text-sm transition duration-200">
                                <i class="fas fa-eye mr-1"></i>View Details
                            </a>
                            <div class="flex space-x-2">
                                <a href="{{ route('properties.edit', $property) }}" 
                                   class="bg-gradient-to-r from-indigo-400 to-purple-600 hover:from-indigo-500 hover:to-purple-700 text-white text-sm py-2 px-4 rounded-lg transition duration-200 shadow-xl hover:shadow-2xl focus:outline-none focus:ring-2 focus:ring-purple-400">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form action="{{ route('properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this property and all its associated data (units, tenants, payments)?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-gradient-to-r from-pink-500 to-red-600 hover:from-pink-600 hover:to-red-700 text-white text-sm py-2 px-4 rounded-lg transition duration-200 shadow-xl hover:shadow-2xl focus:outline-none focus:ring-2 focus:ring-red-400">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
.glass-card backdrop-blur-xl bg-white/20 border border-white/30 shadow-2xl {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out forwards;
}

.gradient-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection