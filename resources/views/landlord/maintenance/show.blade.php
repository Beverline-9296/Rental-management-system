@extends('landlord.layouts.app')

@section('title', 'Maintenance Request Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Maintenance Request #{{ $maintenanceRequest->id }}</h1>
                    <p class="text-gray-600 mt-1">Manage maintenance request details</p>
                </div>
                <a href="{{ route('landlord.maintenance.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Request Details -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $maintenanceRequest->description }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority</label>
                                <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($maintenanceRequest->priority === 'high') bg-red-100 text-red-800
                                    @elseif($maintenanceRequest->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($maintenanceRequest->priority) }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <span class="mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($maintenanceRequest->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($maintenanceRequest->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($maintenanceRequest->status === 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                                </span>
                            </div>
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
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $maintenanceRequest->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Update Status Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Request</h3>
                    
                    <form action="{{ route('landlord.maintenance.update-status', $maintenanceRequest) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select id="status" name="status" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                                    <option value="pending" {{ $maintenanceRequest->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $maintenanceRequest->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $maintenanceRequest->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="rejected" {{ $maintenanceRequest->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
                                <select id="assigned_to" name="assigned_to" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">Select assignee</option>
                                    <option value="{{ auth()->id() }}" {{ $maintenanceRequest->assigned_to == auth()->id() ? 'selected' : '' }}>
                                        Myself
                                    </option>
                                </select>
                                @error('assigned_to')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea id="notes" name="notes" rows="3" 
                                      class="w-full border border-gray-300 rounded-md px-3 py-2"
                                      placeholder="Add any notes or comments...">{{ old('notes', $maintenanceRequest->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Update Request
                        </button>
                    </form>
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

                <!-- Tenant Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tenant Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="text-sm text-gray-900">{{ $maintenanceRequest->tenant->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="text-sm text-gray-900">{{ $maintenanceRequest->tenant->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="text-sm text-gray-900">{{ $maintenanceRequest->tenant->phone_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
