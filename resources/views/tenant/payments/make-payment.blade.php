@extends('layouts.tenant')

@section('title', 'Make Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Make Rent Payment</h1>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form id="mpesa-payment-form" class="space-y-6">
                @csrf
                
                <!-- Unit Selection -->
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">Select Unit</label>
                    <select name="unit_id" id="unit_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose a unit</option>
                        @foreach($assignments as $assignment)
                            <option value="{{ $assignment->unit->id }}" data-rent="{{ $assignment->monthly_rent }}">
                                {{ $assignment->property->name }} - Unit {{ $assignment->unit->unit_number }} (KSh {{ number_format($assignment->monthly_rent) }}/month)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">M-Pesa Phone Number</label>
                    <input type="tel" name="phone_number" id="phone_number" required 
                           placeholder="e.g., 0712345678 or 254712345678"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-600 mt-1">Enter the phone number registered with M-Pesa</p>
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (KSh)</label>
                    <input type="number" name="amount" id="amount" required min="1" step="0.01"
                           placeholder="Enter amount"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="mt-2">
                        <button type="button" id="use-monthly-rent" class="text-blue-600 hover:text-blue-800 text-sm">
                            Use Monthly Rent Amount
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" id="pay-button" 
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="button-text">Pay with M-Pesa</span>
                        <span id="button-spinner" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Status Messages -->
            <div id="status-message" class="mt-4 hidden"></div>
        </div>

        <!-- Payment Instructions -->
        <div class="bg-blue-50 rounded-lg p-6 mt-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">Payment Instructions</h3>
            <ol class="list-decimal list-inside text-blue-700 space-y-2">
                <li>Select your unit from the dropdown</li>
                <li>Enter your M-Pesa registered phone number</li>
                <li>Enter the amount you want to pay</li>
                <li>Click "Pay with M-Pesa" button</li>
                <li>You will receive an STK push on your phone</li>
                <li>Enter your M-Pesa PIN to complete the payment</li>
                <li>You will receive a confirmation SMS from M-Pesa</li>
            </ol>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mpesa-payment-form');
    const unitSelect = document.getElementById('unit_id');
    const amountInput = document.getElementById('amount');
    const useRentButton = document.getElementById('use-monthly-rent');
    const payButton = document.getElementById('pay-button');
    const buttonText = document.getElementById('button-text');
    const buttonSpinner = document.getElementById('button-spinner');
    const statusMessage = document.getElementById('status-message');

    // Use monthly rent amount
    useRentButton.addEventListener('click', function() {
        const selectedOption = unitSelect.options[unitSelect.selectedIndex];
        if (selectedOption.value) {
            const rent = selectedOption.getAttribute('data-rent');
            amountInput.value = rent;
        } else {
            showMessage('Please select a unit first', 'error');
        }
    });

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        // Validate form
        if (!data.unit_id || !data.phone_number || !data.amount) {
            showMessage('Please fill in all required fields', 'error');
            return;
        }

        // Show loading state
        setLoadingState(true);

        try {
            const response = await fetch('/api/mpesa/stk-push', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Authorization': 'Bearer ' + getAuthToken()
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showMessage('STK Push sent! Please check your phone and enter your M-Pesa PIN.', 'success');
                
                // Start checking payment status
                checkPaymentStatus(result.checkout_request_id);
            } else {
                showMessage(result.message || 'Payment failed. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Payment error:', error);
            showMessage('Network error. Please check your connection and try again.', 'error');
        } finally {
            setLoadingState(false);
        }
    });

    function setLoadingState(loading) {
        payButton.disabled = loading;
        if (loading) {
            buttonText.classList.add('hidden');
            buttonSpinner.classList.remove('hidden');
        } else {
            buttonText.classList.remove('hidden');
            buttonSpinner.classList.add('hidden');
        }
    }

    function showMessage(message, type) {
        statusMessage.className = `mt-4 p-4 rounded-md ${type === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'}`;
        statusMessage.textContent = message;
        statusMessage.classList.remove('hidden');
    }

    async function checkPaymentStatus(checkoutRequestId) {
        let attempts = 0;
        const maxAttempts = 30; // Check for 5 minutes (30 * 10 seconds)
        
        const interval = setInterval(async () => {
            attempts++;
            
            try {
                const response = await fetch('/api/mpesa/check-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Authorization': 'Bearer ' + getAuthToken()
                    },
                    body: JSON.stringify({ checkout_request_id: checkoutRequestId })
                });

                const result = await response.json();

                if (result.success) {
                    if (result.status === 'success') {
                        clearInterval(interval);
                        showMessage(`Payment successful! Receipt: ${result.mpesa_receipt_number}`, 'success');
                        
                        // Redirect to payments page after 3 seconds
                        setTimeout(() => {
                            window.location.href = '{{ route("tenant.payments.index") }}';
                        }, 3000);
                    } else if (result.status === 'failed' || result.status === 'cancelled') {
                        clearInterval(interval);
                        showMessage(`Payment ${result.status}: ${result.result_desc}`, 'error');
                    }
                }
            } catch (error) {
                console.error('Status check error:', error);
            }

            if (attempts >= maxAttempts) {
                clearInterval(interval);
                showMessage('Payment status check timed out. Please check your payment history.', 'error');
            }
        }, 10000); // Check every 10 seconds
    }

    function getAuthToken() {
        // For Laravel Sanctum, you might need to get the token differently
        // This is a placeholder - adjust based on your auth implementation
        return document.querySelector('meta[name="api-token"]')?.getAttribute('content') || '';
    }
});
</script>
@endsection
