@extends('landlord.layouts.app')

@section('title', 'Edit Tenant: ' . $tenant->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Tenant</h1>
        <a href="{{ route('landlord.tenants.show', $tenant) }}" class="text-gray-600 hover:text-gray-900">
            &larr; Back to Tenant
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('landlord.tenants.update', $tenant) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900">Personal Information</h2>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $tenant->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $tenant->email) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone *</label>
                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $tenant->phone_number) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900">Emergency Contact</h2>
                    
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact', $tenant->emergency_contact) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="emergency_phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                        <input type="tel" name="emergency_phone" id="emergency_phone" value="{{ old('emergency_phone', $tenant->emergency_phone) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Tenancy Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $currentUnitId = $tenant->tenantAssignments->first() ? $tenant->tenantAssignments->first()->unit_id : null;
                        $currentAssignment = $tenant->tenantAssignments->first();
                    @endphp
                    
                    <div>
                        <label for="unit_id" class="block text-sm font-medium text-gray-700">Unit *</label>
                        <select name="unit_id" id="unit_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select a unit</option>
                            @foreach($availableUnits as $unit)
                                <option value="{{ $unit->id }}" 
                                    {{ old('unit_id', $currentUnitId) == $unit->id ? 'selected' : '' }}
                                    data-rent="{{ $unit->property->rent_amount }}">
                                    {{ $unit->property->name }} - {{ $unit->unit_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="active" {{ old('status', $currentAssignment ? $currentAssignment->status : '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="terminated" {{ old('status', $currentAssignment ? $currentAssignment->status : '') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            <option value="expired" {{ old('status', $currentAssignment ? $currentAssignment->status : '') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                        <input type="date" name="start_date" id="start_date" 
                            value="{{ old('start_date', $currentAssignment ? $currentAssignment->start_date->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                        <input type="date" name="end_date" id="end_date" 
                            value="{{ old('end_date', $currentAssignment ? $currentAssignment->end_date->format('Y-m-d') : now()->addYear()->format('Y-m-d')) }}" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="monthly_rent" class="block text-sm font-medium text-gray-700">Monthly Rent (KSh) *</label>
                        <input type="number" step="0.01" min="0" name="monthly_rent" id="monthly_rent" 
                            value="{{ old('monthly_rent', $currentAssignment ? $currentAssignment->monthly_rent : ($availableUnits->first() ? $availableUnits->first()->property->rent_amount : '')) }}" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="deposit_amount" class="block text-sm font-medium text-gray-700">Deposit (KSh) *</label>
                        <input type="number" step="0.01" min="0" name="deposit_amount" id="deposit_amount" 
                            value="{{ old('deposit_amount', $currentAssignment ? $currentAssignment->deposit_amount : ($availableUnits->first() ? $availableUnits->first()->property->deposit_amount : '')) }}" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('landlord.tenants.show', $tenant) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Tenant
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-update rent and deposit based on selected unit
    document.getElementById('unit_id').addEventListener('change', function() {
        const unitId = this.value;
        if (unitId) {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.rent) {
                document.getElementById('monthly_rent').value = selectedOption.dataset.rent;
                document.getElementById('deposit_amount').value = selectedOption.dataset.rent; // Same as rent for deposit
            }
        }
    });
</script>
@endpush
@endsection
