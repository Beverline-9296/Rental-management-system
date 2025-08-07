@extends('landlord.layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <h2 class="text-2xl font-bold mb-6">Log Rent Payment</h2>
    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('landlord.payments.store') }}" class="bg-white p-6 rounded shadow" id="payment-form">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Property *</label>
            <select name="property_id" id="property_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Property</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Unit</label>
            <select name="unit_id" id="unit_id" class="w-full border rounded px-3 py-2">
                <option value="">Select Unit (optional)</option>
                @foreach($properties as $property)
                    @foreach($property->units as $unit)
                        <option value="{{ $unit->id }}" data-property="{{ $property->id }}">{{ $unit->unit_number }}</option>
                    @endforeach
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Tenant *</label>
            <select name="tenant_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Select Tenant</option>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Payment Type *</label>
            <select name="payment_type" class="w-full border rounded px-3 py-2" required>
                <option value="rent" selected>Rent</option>
                <option value="deposit">Deposit</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Amount (KSh) *</label>
            <input type="number" name="amount" min="1" step="0.00" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Payment Date *</label>
            <input type="date" name="payment_date" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Payment Method</label>
            <input type="text" name="payment_method" class="w-full border rounded px-3 py-2" placeholder="e.g., Cash, Bank, M-Pesa">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Notes</label>
            <textarea name="notes" class="w-full border rounded px-3 py-2" rows="2" placeholder="Optional notes..."></textarea>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold">Save Payment</button>
        <a href="{{ route('landlord.payments.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter units by selected property
    const propertySelect = document.getElementById('property_id');
    const unitSelect = document.getElementById('unit_id');
    if(propertySelect && unitSelect) {
        propertySelect.addEventListener('change', function() {
            const selectedProperty = this.value;
            Array.from(unitSelect.options).forEach(option => {
                if (!option.value) return; // Skip placeholder
                option.style.display = option.getAttribute('data-property') === selectedProperty ? '' : 'none';
            });
            unitSelect.value = '';
        });
        // Trigger change on page load to filter units
        propertySelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
