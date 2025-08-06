@extends('tenant.layouts.app')

@section('title', 'Submit Maintenance Request')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Submit Maintenance Request</h1>
                    <p class="text-gray-600 mt-1">Report an issue that needs attention</p>
                </div>
                <a href="{{ route('tenant.maintenance.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                    Back to Requests
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('tenant.maintenance.store') }}" method="POST">
                @csrf

                <!-- Unit Selection -->
                <div class="mb-6">
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Unit *
                    </label>
                    <select id="unit_id" name="unit_id" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            required>
                        <option value="">Choose your unit</option>
                        @foreach($assignments as $assignment)
                            <option value="{{ $assignment->unit->id }}" {{ old('unit_id') == $assignment->unit->id ? 'selected' : '' }}>
                                {{ $assignment->unit->property->name }} - Unit {{ $assignment->unit->unit_number }}
                                ({{ ucfirst(str_replace('-', ' ', $assignment->unit->unit_type)) }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($assignments->isEmpty())
                        <p class="mt-1 text-sm text-yellow-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            You don't have any active unit assignments. Please contact your landlord.
                        </p>
                    @endif
                </div>

                <!-- Priority -->
                <div class="mb-6">
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority Level *
                    </label>
                    <select id="priority" name="priority" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            required>
                        <option value="">Select priority</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                            Low - Non-urgent, can wait a few days
                        </option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>
                            Medium - Should be addressed within a week
                        </option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                            High - Urgent, needs immediate attention
                        </option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description *
                    </label>
                    <textarea id="description" name="description" rows="6" 
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                              placeholder="Please describe the issue in detail. Include location within the unit, what's not working, when it started, etc."
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Be as specific as possible to help us resolve the issue quickly.
                    </p>
                </div>

                <!-- Priority Guidelines -->
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Priority Guidelines:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><strong>High:</strong> Safety hazards, no water/electricity, security issues, major leaks</li>
                        <li><strong>Medium:</strong> Appliance malfunctions, minor leaks, heating/cooling issues</li>
                        <li><strong>Low:</strong> Cosmetic issues, minor repairs, non-essential items</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('tenant.maintenance.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition duration-200"
                            @if($assignments->isEmpty()) disabled @endif>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>

        <!-- Contact Information -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Emergency Situations</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>For urgent emergencies (gas leaks, electrical hazards, flooding, break-ins), 
                           contact emergency services immediately and then notify your landlord.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
