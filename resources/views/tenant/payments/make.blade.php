@extends('tenant.layouts.app')

@section('title', 'Make Payment - M-Pesa')



@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8 animate-fadeInUp">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Make Payment</h1>
            <p class="text-gray-600">Pay your rent securely using M-Pesa STK Push</p>
        </div>

        <!-- Payment Status Alert -->
        <div id="payment-status" class="hidden mb-6">
            <div id="status-content" class="p-4 rounded-lg"></div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Payment Form -->
            <div class="glass-card rounded-xl shadow-xl p-6 animate-slideInLeft">
                <div class="flex items-center mb-6">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-mobile-alt text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">M-Pesa Payment</h2>
                        <p class="text-gray-600 text-sm">Secure STK Push payment</p>
                    </div>
                </div>

                <form id="mpesa-payment-form" class="space-y-6">
                    @csrf
                    
                    <!-- Unit Information -->
                    @if($assignments->count() == 1)
                        @php $assignment = $assignments->first(); @endphp
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-home mr-2"></i>Your Unit Details
                            </label>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Property:</span>
                                    <span class="font-semibold text-gray-800">{{ $assignment->property->name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Unit Number:</span>
                                    <span class="font-semibold text-gray-800">{{ $assignment->unit->unit_number }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Monthly Rent:</span>
                                    <span class="font-semibold text-green-600">KES {{ number_format($assignment->monthly_rent, 2) }}</span>
                                </div>
                            </div>
                            <input type="hidden" name="unit_id" id="unit_id" value="{{ $assignment->unit->id }}" data-rent="{{ $assignment->monthly_rent }}" data-property="{{ $assignment->property->name }}">
                        </div>
                    @else
                        <!-- Multiple Units Selection -->
                        <div>
                            <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-home mr-2"></i>Select Unit
                            </label>
                            <select name="unit_id" id="unit_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="">Choose your unit...</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->unit->id }}" data-rent="{{ $assignment->monthly_rent }}" data-property="{{ $assignment->property->name }}">
                                        {{ $assignment->property->name }} - Unit {{ $assignment->unit->unit_number }} 
                                        (KES {{ number_format($assignment->monthly_rent, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                     <!-- Payment Type -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-blue-600"></i>Payment Type *
                    </label>
                    <select name="payment_type" id="payment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select payment type</option>
                        <option value="rent">🏠 Monthly Rent</option>
                        <option value="utility">⚡ Utility Bill</option>
                        <option value="maintenance">🔧 Maintenance Fee</option>
                    </select>
                    <p class="text-xs text-gray-600 mt-1">Choose what type of payment you're making</p>
                </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-money-bill-wave mr-2"></i>Amount (KES)
                        </label>
                        <input type="number" name="amount" id="amount" step="0.01" min="1" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Enter amount" required>
                        <div class="mt-2">
                            <button type="button" id="use-monthly-rent" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Use monthly rent amount
                            </button>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2"></i>M-Pesa Phone Number
                        </label>
                        <input type="tel" name="phone_number" id="phone_number" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="0712345678 or 254712345678" 
                               pattern="^(254|0)[17][0-9]{8}$" 
                               title="Enter a valid Kenyan phone number" required>
                        <p class="mt-1 text-sm text-gray-500">
                            Enter your M-Pesa registered phone number
                        </p>
                    </div>

                    <!-- Payment Button -->
                    <button type="submit" id="pay-button" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                        <i class="fas fa-mobile-alt mr-2"></i>
                        <span id="button-text">Pay with M-Pesa</span>
                        <div id="button-spinner" class="hidden ml-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Payment Info & Instructions -->
            <div class="space-y-6">
                <!-- Current Balance -->
                <div class="glass-card rounded-xl shadow-xl p-6 animate-fadeInUp">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-chart-line mr-2 text-blue-600"></i>Account Summary
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Paid:</span>
                            <span class="font-semibold text-green-600">KES {{ number_format($totalPaid, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Due:</span>
                            <span class="font-semibold text-blue-600">KES {{ number_format($totalDue, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t pt-3">
                            <span class="text-gray-600">Arrears:</span>
                            <span class="font-semibold {{ $arrears > 0 ? 'text-red-600' : 'text-green-600' }}">
                                KES {{ number_format($arrears, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payment Instructions -->
                <div class="glass-card rounded-xl shadow-xl p-6 animate-fadeInUp">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>How to Pay
                    </h3>
                    <ol class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start">
                            <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">1</span>
                            Enter the amount you want to pay
                        </li>
                        <li class="flex items-start">
                            <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">2</span>
                            Enter your M-Pesa phone number
                        </li>
                        <li class="flex items-start">
                            <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">3</span>
                            Click "Pay with M-Pesa" button
                        </li>
                        <li class="flex items-start">
                            <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">4</span>
                            Check your phone for M-Pesa prompt
                        </li>
                        <li class="flex items-start">
                            <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">5</span>
                            Press <strong>1</strong> to confirm payment when asked
                        </li>
                        <li class="flex items-start">
                            <span class="bg-blue-100 text-blue-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">6</span>
                            Enter your M-Pesa PIN to complete
                        </li>
                    </ol>
                </div>

                <!-- Security Notice -->
                <div class="glass-card rounded-xl shadow-xl p-6 animate-fadeInUp">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-shield-alt mr-2 text-green-600"></i>Security & Privacy
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            SSL encrypted transactions
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Safaricom M-Pesa secure gateway
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Instant payment confirmation
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            24/7 transaction monitoring
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Status Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 animate-fadeInUp">
        <div class="text-center">
            <div id="modal-icon" class="mx-auto mb-4"></div>
            <h3 id="modal-title" class="text-xl font-semibold mb-2"></h3>
            <p id="modal-message" class="text-gray-600 mb-6"></p>
            <div id="modal-progress" class="hidden">
                <div class="bg-gray-200 rounded-full h-2 mb-4">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000" style="width: 0%" id="progress-bar"></div>
                </div>
                <p class="text-sm text-gray-500">Checking payment status...</p>
            </div>
            <div id="modal-buttons" class="space-x-4">
                <button id="modal-close" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Close
                </button>
                <button id="check-payment-status" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Check Status
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mpesa-payment-form');
    const unitSelect = document.getElementById('unit_id');
    const amountInput = document.getElementById('amount');
    const phoneInput = document.getElementById('phone_number');
    const payButton = document.getElementById('pay-button');
    const buttonText = document.getElementById('button-text');
    const buttonSpinner = document.getElementById('button-spinner');
    const useRentButton = document.getElementById('use-monthly-rent');
    const modal = document.getElementById('payment-modal');
    const modalClose = document.getElementById('modal-close');

    // Auto-fill monthly rent amount (for dropdown)
    if (unitSelect.tagName === 'SELECT') {
        unitSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                const rent = selectedOption.getAttribute('data-rent');
                if (rent) {
                    amountInput.value = parseFloat(rent).toFixed(2);
                }
            }
        });
    }

    // Use monthly rent button
    useRentButton.addEventListener('click', function() {
        let rent = null;
        
        if (unitSelect.tagName === 'SELECT') {
            // For dropdown selection
            const selectedOption = unitSelect.options[unitSelect.selectedIndex];
            if (selectedOption.value) {
                rent = selectedOption.getAttribute('data-rent');
            }
        } else {
            // For hidden input (single unit)
            rent = unitSelect.getAttribute('data-rent');
        }
        
        if (rent) {
            amountInput.value = parseFloat(rent).toFixed(2);
        } else {
            alert('Unable to get rent amount. Please enter manually.');
        }
    });

    // Format phone number as user types
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.startsWith('0')) {
            value = '254' + value.substring(1);
        }
        if (value.length > 12) {
            value = value.substring(0, 12);
        }
        this.value = value;
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }

        initiatePayment();
    });

    // Modal close
    modalClose.addEventListener('click', function() {
        hideModal();
    });

    // Check payment status button
    document.addEventListener('click', function(e) {
        if (e.target.id === 'check-payment-status') {
            checkPaymentStatusManually();
        }
    });

    function validateForm() {
        const unitId = unitSelect.value;
        const amount = parseFloat(amountInput.value);
        const phone = phoneInput.value;

        if (!unitId) {
            showAlert('Unit information is missing', 'error');
            return false;
        }

        if (!amount || amount <= 0) {
            showAlert('Please enter a valid amount', 'error');
            return false;
        }

        if (!phone || !/^254[17][0-9]{8}$/.test(phone)) {
            showAlert('Please enter a valid Kenyan phone number', 'error');
            return false;
        }

        return true;
    }

    function initiatePayment() {
        setLoading(true);
        
        const formData = new FormData(form);
        
        fetch('/tenant/mpesa/stk-push', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            setLoading(false);
            
            if (data.success) {
                showPaymentModal('pending', data.checkout_request_id, data.transaction_id);
                startStatusCheck(data.checkout_request_id, data.transaction_id);
            } else {
                showAlert(data.message || 'Payment initiation failed', 'error');
            }
        })
        .catch(error => {
            setLoading(false);
            console.error('Payment error:', error);
            showAlert('Payment request failed. Please try again.', 'error');
        });
    }

    function startStatusCheck(checkoutRequestId, transactionId) {
        let attempts = 0;
        const maxAttempts = 18; // 3 minutes (18 * 10 seconds)
        
        const checkStatus = () => {
            if (attempts >= maxAttempts) {
                updatePaymentModal('timeout');
                // Auto-close modal after 10 seconds on timeout
                setTimeout(() => {
                    hideModal();
                    showAlert('Payment status check timed out. Please check your payment history or click "Check Status" to refresh.', 'info');
                }, 10000);
                return;
            }
            
            fetch('/tenant/mpesa/check-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    checkout_request_id: checkoutRequestId,
                    transaction_id: transactionId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updatePaymentModal('success', data);
                } else if (data.status === 'failed') {
                    updatePaymentModal('failed', data);
                } else {
                    // Still pending, check again
                    attempts++;
                    updateProgress((attempts / maxAttempts) * 100);
                    setTimeout(checkStatus, 10000); // Check every 10 seconds
                }
            })
            .catch(error => {
                console.error('Status check error:', error);
                attempts++;
                if (attempts < maxAttempts) {
                    setTimeout(checkStatus, 10000);
                } else {
                    updatePaymentModal('error');
                }
            });
        };
        
        setTimeout(checkStatus, 5000); // Start checking after 5 seconds
    }

    function setLoading(loading) {
        payButton.disabled = loading;
        if (loading) {
            buttonText.textContent = 'Processing...';
            buttonSpinner.classList.remove('hidden');
        } else {
            buttonText.textContent = 'Pay with M-Pesa';
            buttonSpinner.classList.add('hidden');
        }
    }

    function showAlert(message, type) {
        const alertDiv = document.getElementById('payment-status');
        const contentDiv = document.getElementById('status-content');
        
        const bgColor = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 
                       type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                       'bg-blue-100 border-blue-400 text-blue-700';
        
        const icon = type === 'error' ? 'fas fa-exclamation-circle' :
                    type === 'success' ? 'fas fa-check-circle' :
                    'fas fa-info-circle';
        
        contentDiv.className = `border-l-4 p-4 ${bgColor}`;
        contentDiv.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="${icon}"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm">${message}</p>
                </div>
            </div>
        `;
        
        alertDiv.classList.remove('hidden');
        
        // Auto-hide after 5 seconds for non-error messages
        if (type !== 'error') {
            setTimeout(() => {
                alertDiv.classList.add('hidden');
            }, 5000);
        }
    }

    function showPaymentModal(status, checkoutRequestId = null, transactionId = null) {
        const modalIcon = document.getElementById('modal-icon');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalProgress = document.getElementById('modal-progress');
        
        modalIcon.innerHTML = '<div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto"><i class="fas fa-mobile-alt text-blue-600 text-2xl"></i></div>';
        modalTitle.textContent = 'Payment Initiated';
        modalMessage.textContent = 'Please check your phone for the M-Pesa payment prompt and enter your PIN to complete the transaction.';
        modalProgress.classList.remove('hidden');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function updatePaymentModal(status, data = null) {
        const modalIcon = document.getElementById('modal-icon');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalProgress = document.getElementById('modal-progress');
        const modalButtons = document.getElementById('modal-buttons');
        
        modalProgress.classList.add('hidden');
        
        switch (status) {
            case 'success':
                modalIcon.innerHTML = '<div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto"><i class="fas fa-check-circle text-green-600 text-2xl"></i></div>';
                modalTitle.textContent = 'Payment Successful!';
                modalMessage.textContent = `Your payment of KES ${data.amount} has been processed successfully. Receipt: ${data.receipt_number}`;
                modalButtons.innerHTML = `
                    <button onclick="window.location.href='{{ route('tenant.payments.index') }}'" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        View Payments
                    </button>
                    <button id="modal-close" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Close
                    </button>
                `;
                break;
                
            case 'failed':
                modalIcon.innerHTML = '<div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto"><i class="fas fa-times-circle text-red-600 text-2xl"></i></div>';
                modalTitle.textContent = 'Payment Failed';
                modalMessage.textContent = data?.message || 'The payment was not completed. Please try again.';
                break;
                
            case 'timeout':
                modalIcon.innerHTML = '<div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto"><i class="fas fa-clock text-yellow-600 text-2xl"></i></div>';
                modalTitle.textContent = 'Payment Timeout';
                modalMessage.textContent = 'The payment request has timed out. Please check your payment history or try again.';
                break;
                
            case 'error':
                modalIcon.innerHTML = '<div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto"><i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i></div>';
                modalTitle.textContent = 'Connection Error';
                modalMessage.textContent = 'Unable to check payment status. Please check your payment history.';
                break;
        }
        
        // Re-attach close event listener
        document.getElementById('modal-close').addEventListener('click', hideModal);
    }

    function updateProgress(percentage) {
        const progressBar = document.getElementById('progress-bar');
        progressBar.style.width = percentage + '%';
    }

    function checkPaymentStatusManually() {
        // Refresh the page to get updated payment status
        showAlert('Checking payment status...', 'info');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }

    function hideModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Reset form if payment was successful
        if (document.getElementById('modal-title').textContent === 'Payment Successful!') {
            form.reset();
        }
    }


});
</script>
@endpush
