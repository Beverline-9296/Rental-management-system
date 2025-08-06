@extends('tenant.layouts.app')

@section('title', 'Maintenance Request Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Request #{{ $maintenanceRequest->id }}</h1>
                    <p class="text-gray-600 mt-1">Maintenance request details</p>
                </div>
                <a href="{{ route('tenant.maintenance.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    Back to My Requests
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Request Details -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Request Details</h3>
                        <div class="flex space-x-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($maintenanceRequest->priority === 'high') bg-red-100 text-red-800
                                @elseif($maintenanceRequest->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($maintenanceRequest->priority) }} Priority
                            </span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($maintenanceRequest->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($maintenanceRequest->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($maintenanceRequest->status === 'completed') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $maintenanceRequest->description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Submitted</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>

                            @if($maintenanceRequest->completed_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Completed</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->completed_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            @endif
                        </div>

                        @if($maintenanceRequest->assignedTo)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Assigned To</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $maintenanceRequest->assignedTo->name }}</p>
                            </div>
                        @endif

                        @if($maintenanceRequest->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Landlord Notes</label>
                                <p class="mt-1 text-sm text-gray-900 bg-blue-50 p-3 rounded-md border border-blue-200">{{ $maintenanceRequest->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Timeline</h3>
                    
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <!-- Submitted -->
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-plus text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Request submitted</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $maintenanceRequest->created_at->format('M d, Y g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <!-- In Progress (if applicable) -->
                            @if(in_array($maintenanceRequest->status, ['in_progress', 'completed']))
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-cog text-white text-xs"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Work started</p>
                                                    @if($maintenanceRequest->assignedTo)
                                                        <p class="text-xs text-gray-400">Assigned to {{ $maintenanceRequest->assignedTo->name }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $maintenanceRequest->updated_at->format('M d, Y g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            <!-- Completed (if applicable) -->
                            @if($maintenanceRequest->status === 'completed')
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Request completed</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $maintenanceRequest->completed_at->format('M d, Y g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            <!-- Rejected (if applicable) -->
                            @if($maintenanceRequest->status === 'rejected')
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-times text-white text-xs"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Request rejected</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $maintenanceRequest->updated_at->format('M d, Y g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Property & Unit Info -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Property Details</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Property</label>
                            <p class="text-sm text-gray-900">{{ $maintenanceRequest->property->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit</label>
                            <p class="text-sm text-gray-900">Unit {{ $maintenanceRequest->unit->unit_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit Type</label>
                            <p class="text-sm text-gray-900">{{ ucfirst(str_replace('-', ' ', $maintenanceRequest->unit->unit_type)) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <p class="text-sm text-gray-900">{{ $maintenanceRequest->property->location }}</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Need Help?</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>If you have questions about this request or need to report an emergency, contact your landlord directly.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
