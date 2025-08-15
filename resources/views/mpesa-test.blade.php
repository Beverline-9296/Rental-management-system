<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Integration Testing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">M-Pesa Integration Testing Dashboard</h1>
            
            <!-- Test Results Display -->
            <div id="results" class="mb-8"></div>
            
            <!-- Test Buttons -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <button onclick="runTest('success')" 
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    Test Success Scenario
                </button>
                
                <button onclick="runTest('failure')" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    Test Failure Scenario
                </button>
                
                <button onclick="runTest('timeout')" 
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    Test Timeout Scenario
                </button>
                
                <button onclick="runTest('sandbox')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    Test Sandbox Detection
                </button>
            </div>
            
            <!-- Instructions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Testing Instructions</h2>
                <div class="space-y-4">
                    <div class="border-l-4 border-green-500 pl-4">
                        <h3 class="font-semibold text-green-700">Success Scenario</h3>
                        <p class="text-gray-600">Simulates a successful M-Pesa payment with ResultCode 0. Should create a payment record and mark transaction as successful.</p>
                    </div>
                    
                    <div class="border-l-4 border-red-500 pl-4">
                        <h3 class="font-semibold text-red-700">Failure Scenario</h3>
                        <p class="text-gray-600">Simulates a failed M-Pesa payment with ResultCode 1032 (user cancelled). Should mark transaction as failed without creating payment.</p>
                    </div>
                    
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <h3 class="font-semibold text-yellow-700">Timeout Scenario</h3>
                        <p class="text-gray-600">Simulates M-Pesa timeout callback. Should mark transaction as cancelled.</p>
                    </div>
                    
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h3 class="font-semibold text-blue-700">Sandbox Detection</h3>
                        <p class="text-gray-600">Tests the sandbox detection logic that handles M-Pesa sandbox quirks where successful payments return ResultCode 1032.</p>
                    </div>
                </div>
            </div>
            
            <!-- Real Payment Test -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Real Payment Test</h2>
                <p class="text-gray-600 mb-4">To test with real M-Pesa STK Push, visit the payment page:</p>
                <a href="{{ route('tenant.payments.make') }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 inline-block">
                    Go to Payment Page
                </a>
            </div>
        </div>
    </div>

    <script>
        async function runTest(testType) {
            const resultsDiv = document.getElementById('results');
            
            // Show loading
            resultsDiv.innerHTML = `
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                    <div class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Running ${testType} test...
                    </div>
                </div>
            `;
            
            try {
                const response = await fetch(`/api/mpesa/test/${testType}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                // Display results
                const bgColor = getResultColor(result.transaction_status);
                resultsDiv.innerHTML = `
                    <div class="${bgColor} border rounded-lg p-6 mb-4">
                        <h3 class="text-lg font-semibold mb-4">Test Results: ${result.test}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold mb-2">Transaction Details:</h4>
                                <ul class="space-y-1 text-sm">
                                    <li><strong>Status:</strong> ${result.transaction_status}</li>
                                    <li><strong>Transaction ID:</strong> ${result.transaction_id}</li>
                                    ${result.transaction_receipt ? `<li><strong>Receipt:</strong> ${result.transaction_receipt}</li>` : ''}
                                    ${result.transaction_result_desc ? `<li><strong>Result:</strong> ${result.transaction_result_desc}</li>` : ''}
                                    ${result.transaction_age_minutes ? `<li><strong>Age (minutes):</strong> ${result.transaction_age_minutes}</li>` : ''}
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-2">Payment Details:</h4>
                                <ul class="space-y-1 text-sm">
                                    <li><strong>Payment Created:</strong> ${result.payment_created}</li>
                                    ${result.payment_amount ? `<li><strong>Amount:</strong> KSh ${result.payment_amount}</li>` : ''}
                                    ${result.sandbox_logic_applied ? `<li><strong>Sandbox Logic:</strong> ${result.sandbox_logic_applied}</li>` : ''}
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button onclick="getTransactionDetails(${result.transaction_id})" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm">
                                View Full Details
                            </button>
                        </div>
                    </div>
                `;
                
            } catch (error) {
                console.error('Test error:', error);
                resultsDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
            }
        }
        
        async function getTransactionDetails(transactionId) {
            try {
                const response = await fetch(`/api/mpesa/test/status/${transactionId}`);
                const result = await response.json();
                
                // Create modal-like display
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                modal.innerHTML = `
                    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Transaction Details</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">✕</button>
                        </div>
                        <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto">${JSON.stringify(result, null, 2)}</pre>
                    </div>
                `;
                document.body.appendChild(modal);
                
            } catch (error) {
                alert('Error fetching details: ' + error.message);
            }
        }
        
        function getResultColor(status) {
            switch(status) {
                case 'success':
                    return 'bg-green-100 border-green-400 text-green-700';
                case 'failed':
                    return 'bg-red-100 border-red-400 text-red-700';
                case 'cancelled':
                    return 'bg-yellow-100 border-yellow-400 text-yellow-700';
                case 'pending':
                    return 'bg-blue-100 border-blue-400 text-blue-700';
                default:
                    return 'bg-gray-100 border-gray-400 text-gray-700';
            }
        }
    </script>
</body>
</html>
