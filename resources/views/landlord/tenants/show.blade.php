@extends('landlord.layouts.app')

@section('title', 'Tenant Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tenant Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('landlord.tenants.edit', $tenant) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                Edit Tenant
            </a>
            <a href="{{ route('landlord.tenants.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="text-sm text-gray-900">{{ $tenant->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="text-sm text-gray-900">{{ $tenant->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <p class="text-sm text-gray-900">{{ $tenant->phone_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ID Number</label>
                    <p class="text-sm text-gray-900">{{ $tenant->id_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <p class="text-sm text-gray-900">{{ $tenant->address }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Emergency Contact</label>
                    <p class="text-sm text-gray-900">{{ $tenant->emergency_contact }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Emergency Phone</label>
                    <p class="text-sm text-gray-900">{{ $tenant->emergency_phone }}</p>
                </div>
            </div>
        </div>

        <!-- Rental Information -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Rental Information</h2>
            @if($tenant->tenantAssignments->count() > 0)
                @foreach($tenant->tenantAssignments as $assignment)
                    <div class="border-b border-gray-200 pb-4 mb-4 last:border-b-0 last:pb-0 last:mb-0">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Property</label>
                                <p class="text-sm text-gray-900">{{ $assignment->unit->property->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Unit Number</label>
                                <p class="text-sm text-gray-900">{{ $assignment->unit->unit_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Monthly Rent</label>
                                <p class="text-sm text-gray-900">KSh {{ number_format($assignment->monthly_rent ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <p class="text-sm text-gray-900">{{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <p class="text-sm text-gray-900">{{ $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date)->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                @php
                                    $statusClass = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'terminated' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-yellow-100 text-yellow-800',
                                    ][$assignment->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($assignment->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500">No rental assignments found.</p>
            @endif
        </div>
    </div>

    <!-- Account Information -->
    <div class="mt-6 bg-white shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <p class="text-sm text-gray-900 capitalize">{{ $tenant->role }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Verified</label>
                <p class="text-sm text-gray-900">
                    @if($tenant->email_verified_at)
                        <span class="text-green-600">✓ Verified</span>
                    @else
                        <span class="text-red-600">✗ Not Verified</span>
                    @endif
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Member Since</label>
                <p class="text-sm text-gray-900">{{ $tenant->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 flex justify-end space-x-4">
        <form action="{{ route('landlord.tenants.reset-password', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('Generate a new password for this tenant?');">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                Reset Password
            </button>
        </form>
        <form action="{{ route('landlord.tenants.destroy', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this tenant? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                Delete Tenant
            </button>
        </form>
    </div>
</div>
@endsection
