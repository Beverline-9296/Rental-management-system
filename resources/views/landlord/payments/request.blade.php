@extends('landlord.layouts.app')

@section('title', 'Send Payment Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Send Payment Request</h1>
                <p class="text-gray-600 mt-2">Select tenants and send payment reminders via SMS or email</p>
            </div>
            <a href="{{ route('landlord.dashboard') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('landlord.payments.send-request') }}" method="POST" id="paymentRequestForm">
            @csrf
            
            <!-- Tenant Selection -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Select Tenants</h2>
                    <div class="flex gap-2">
                        <button type="button" id="selectAll" class="text-blue-600 hover:text-blue-800 text-sm">
                            Select All
                        </button>
                        <span class="text-gray-400">|</span>
                        <button type="button" id="selectNone" class="text-blue-600 hover:text-blue-800 text-sm">
                            Select None
                        </button>
                        <span class="text-gray-400">|</span>
                        <button type="button" id="selectOverdue" class="text-red-600 hover:text-red-800 text-sm">
                            Select Overdue
                        </button>
                    </div>
                </div>

                @if(empty($tenants))
                    <div class="text-center py-8">
                        <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No tenants found in your properties.</p>
                    </div>
                @else
                    <div class="grid gap-4">
                        @foreach($tenants as $tenant)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors tenant-card" 
                                 data-urgency="{{ $tenant['urgency'] }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="tenant_ids[]" 
                                               value="{{ $tenant['id'] }}" 
                                               id="tenant_{{ $tenant['id'] }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded tenant-checkbox">
                                        <label for="tenant_{{ $tenant['id'] }}" class="ml-3 cursor-pointer flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h3 class="font-semibold text-gray-900">{{ $tenant['name'] }}</h3>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $tenant['property_name'] }} - Unit {{ $tenant['unit_number'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $tenant['email'] }} | {{ $tenant['phone'] }}
                                                    </p>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-semibold text-lg text-gray-900">
                                                        KSh {{ number_format($tenant['monthly_rent'], 0) }}
                                                    </div>
                                                    @if($tenant['arrears'] > 0)
                                                        <div class="text-sm text-red-600">
                                                            Arrears: KSh {{ number_format($tenant['arrears'], 0) }}
                                                        </div>
                                                    @endif
                                                    <div class="text-sm text-gray-600">
                                                        Due: {{ \Carbon\Carbon::parse($tenant['next_due_date'])->format('M j, Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="ml-4">
                                        @if($tenant['urgency'] === 'overdue')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                {{ abs($tenant['days_remaining']) }} days overdue
                                            </span>
                                        @elseif($tenant['urgency'] === 'high')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $tenant['days_remaining'] }} days left
                                            </span>
                                        @elseif($tenant['urgency'] === 'medium')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $tenant['days_remaining'] }} days left
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                {{ $tenant['days_remaining'] }} days left
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Message Configuration -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Message Configuration</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Message Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Delivery Method</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="message_type" value="sms" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" checked>
                                <span class="ml-2 text-sm text-gray-700">
                                    <i class="fas fa-sms mr-1"></i>SMS Only
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="message_type" value="email" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">
                                    <i class="fas fa-envelope mr-1"></i>Email Only
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="message_type" value="both" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">
                                    <i class="fas fa-paper-plane mr-1"></i>Both SMS & Email
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Custom Message -->
                    <div>
                        <label for="custom_message" class="block text-sm font-medium text-gray-700 mb-2">
                            Custom Message (Optional)
                        </label>
                        <textarea name="custom_message" 
                                  id="custom_message" 
                                  rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Add a personal message to your payment request..."></textarea>
                        <p class="text-xs text-gray-500 mt-1">Leave blank to use default message template</p>
                    </div>
                </div>
            </div>

            <!-- Selected Summary -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6" id="selectedSummary" style="display: none;">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <span class="text-blue-800">
                        <span id="selectedCount">0</span> tenant(s) selected for payment request
                    </span>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        id="submitBtn"
                        disabled>
                    <i class="fas fa-paper-plane mr-2"></i>
                    Send Payment Requests
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.tenant-checkbox');
    const selectAllBtn = document.getElementById('selectAll');
    const selectNoneBtn = document.getElementById('selectNone');
    const selectOverdueBtn = document.getElementById('selectOverdue');
    const selectedSummary = document.getElementById('selectedSummary');
    const selectedCount = document.getElementById('selectedCount');
    const submitBtn = document.getElementById('submitBtn');

    function updateSummary() {
        const checked = document.querySelectorAll('.tenant-checkbox:checked');
        const count = checked.length;
        
        selectedCount.textContent = count;
        selectedSummary.style.display = count > 0 ? 'block' : 'none';
        submitBtn.disabled = count === 0;
    }

    // Add event listeners to checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSummary);
    });

    // Select All
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSummary();
    });

    // Select None
    selectNoneBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSummary();
    });

    // Select Overdue
    selectOverdueBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            const card = checkbox.closest('.tenant-card');
            const urgency = card.dataset.urgency;
            checkbox.checked = urgency === 'overdue';
        });
        updateSummary();
    });

    // Form submission confirmation
    document.getElementById('paymentRequestForm').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.tenant-checkbox:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Please select at least one tenant to send payment request.');
            return;
        }

        const messageType = document.querySelector('input[name="message_type"]:checked').value;
        const count = checked.length;
        
        if (!confirm(`Are you sure you want to send payment requests to ${count} tenant(s) via ${messageType.toUpperCase()}?`)) {
            e.preventDefault();
        }
    });

    // Initial update
    updateSummary();
});
</script>
@endsection
