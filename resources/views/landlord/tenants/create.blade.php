@extends('landlord.layouts.app')

@section('title', 'Add New Tenant')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Tenant</h1>
        <a href="{{ route('landlord.tenants.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Back to Tenants
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('landlord.tenants.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h2>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="id_number" class="block text-sm font-medium text-gray-700">ID/Passport Number *</label>
                        <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('id_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h2>
                    
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Physical Address *</label>
                        <textarea name="address" id="address" rows="2" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Emergency Contact Name *</label>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('emergency_contact')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="emergency_phone" class="block text-sm font-medium text-gray-700">Emergency Contact Phone *</label>
                        <input type="tel" name="emergency_phone" id="emergency_phone" value="{{ old('emergency_phone') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('emergency_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Tenancy Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="unit_id" class="block text-sm font-medium text-gray-700">Assign to Unit *</label>
                        <select name="unit_id" id="unit_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select a unit</option>
                            @forelse($availableUnits as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->property->name }} - {{ $unit->unit_number }} ({{ $unit->unit_type }})
                                </option>
                            @empty
                                <option value="" disabled>No available units found</option>
                            @endforelse
                        </select>
                        @error('unit_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($availableUnits->isEmpty())
                            <p class="mt-2 text-sm text-yellow-600">
                                No available units found. Please add a unit before assigning a tenant.
                                <a href="{{ route('landlord.properties.create') }}" class="text-blue-600 hover:underline">Add Property</a> or check existing properties for available units.
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', now()->addYear()->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="monthly_rent" class="block text-sm font-medium text-gray-700">Monthly Rent (KSh) *</label>
                        <input type="number" step="0.01" min="0" name="monthly_rent" id="monthly_rent" value="{{ old('monthly_rent', $availableUnits->first() ? $availableUnits->first()->property->rent_amount : '') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('monthly_rent')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deposit_amount" class="block text-sm font-medium text-gray-700">Deposit Amount (KSh) *</label>
                        <input type="number" step="0.01" min="0" name="deposit_amount" id="deposit_amount" value="{{ old('deposit_amount', $availableUnits->first() ? $availableUnits->first()->property->rent_amount : '') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('deposit_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-sm text-gray-500">
                        A welcome email with login credentials will be sent to the tenant's email address.
                    </p>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('landlord.tenants.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Add Tenant
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
            // In a real app, you would fetch this data via AJAX
            // For now, we'll use the values from the selected option
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.rent) {
                document.getElementById('monthly_rent').value = selectedOption.dataset.rent;
                document.getElementById('deposit_amount').value = selectedOption.dataset.deposit || selectedOption.dataset.rent;
            }
        }
    });
</script>
@endpush
@endsection
