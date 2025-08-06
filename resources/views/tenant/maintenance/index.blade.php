@extends('tenant.layouts.app')

@section('title', 'My Maintenance Requests')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Maintenance Requests</h1>
                    <p class="text-gray-600 mt-1">Submit and track your maintenance requests</p>
                </div>
                <a href="{{ route('tenant.maintenance.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-plus mr-2"></i>New Request
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-tools text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Requests</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-cog text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['in_progress'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Requests -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Your Maintenance Requests</h3>
            </div>

            @if($maintenanceRequests->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($maintenanceRequests as $request)
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-lg font-medium text-gray-900">
                                            Request #{{ $request->id }}
                                        </h4>
                                        <div class="flex space-x-2">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($request->priority === 'high') bg-red-100 text-red-800
                                                @elseif($request->priority === 'medium') bg-yellow-100 text-yellow-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($request->priority) }} Priority
                                            </span>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                                @elseif($request->status === 'completed') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <p class="text-sm text-gray-600 mb-1">
                                            <i class="fas fa-building mr-1"></i>{{ $request->property->name }} - Unit {{ $request->unit->unit_number }}
                                        </p>
                                        <p class="text-sm text-gray-900">{{ $request->description }}</p>
                                    </div>

                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center space-x-4">
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                Submitted: {{ $request->created_at->format('M d, Y') }}
                                            </span>
                                            @if($request->completed_at)
                                                <span>
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Completed: {{ $request->completed_at->format('M d, Y') }}
                                                </span>
                                            @endif
                                            @if($request->assignedTo)
                                                <span>
                                                    <i class="fas fa-user mr-1"></i>
                                                    Assigned to: {{ $request->assignedTo->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <a href="{{ route('tenant.maintenance.show', $request) }}" 
                                           class="text-blue-600 hover:text-blue-900 font-medium">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($maintenanceRequests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $maintenanceRequests->links() }}
                    </div>
                @endif
            @else
                <div class="p-6 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-tools text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No maintenance requests yet</h3>
                    <p class="text-gray-500 mb-4">You haven't submitted any maintenance requests.</p>
                    <a href="{{ route('tenant.maintenance.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Submit Your First Request
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
