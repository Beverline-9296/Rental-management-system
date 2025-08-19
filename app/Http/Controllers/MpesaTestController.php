<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\TenantAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MpesaTestController extends Controller
{
    /**
     * Test M-Pesa Success Scenario
     */
    public function testSuccess(Request $request)
    {
        Log::info('=== M-PESA SUCCESS TEST STARTED ===');
        
        // Create a test transaction if none exists
        $transaction = $this->createTestTransaction();
        
        // Simulate successful M-Pesa callback
        $successCallback = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => $transaction->merchant_request_id,
                    'CheckoutRequestID' => $transaction->checkout_request_id,
                    'ResultCode' => 0, // Success code
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            [
                                'Name' => 'Amount',
                                'Value' => $transaction->amount
                            ],
                            [
                                'Name' => 'MpesaReceiptNumber',
                                'Value' => 'SBX' . strtoupper(Str::random(8))
                            ],
                            [
                                'Name' => 'TransactionDate',
                                'Value' => now()->format('YmdHis')
                            ],
                            [
                                'Name' => 'PhoneNumber',
                                'Value' => $transaction->phone_number
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Process the callback
        $request->merge($successCallback);
        $mpesaController = new MpesaController(app(\App\Services\MpesaService::class));
        $response = $mpesaController->callback($request);

        // Check results
        $transaction->refresh();
        $payment = Payment::where('mpesa_transaction_id', $transaction->id)->first();

        return response()->json([
            'test' => 'SUCCESS_SCENARIO',
            'transaction_status' => $transaction->status,
            'transaction_receipt' => $transaction->mpesa_receipt_number,
            'payment_created' => $payment ? 'YES' : 'NO',
            'payment_amount' => $payment ? $payment->amount : null,
            'callback_response' => $response->getData(),
            'transaction_id' => $transaction->id
        ]);
    }

    /**
     * Test M-Pesa Failure Scenario
     */
    public function testFailure(Request $request)
    {
        Log::info('=== M-PESA FAILURE TEST STARTED ===');
        
        // Create a test transaction if none exists
        $transaction = $this->createTestTransaction();
        
        // Simulate failed M-Pesa callback
        $failureCallback = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => $transaction->merchant_request_id,
                    'CheckoutRequestID' => $transaction->checkout_request_id,
                    'ResultCode' => 1032, // Common failure code (user cancelled)
                    'ResultDesc' => 'Request cancelled by user'
                ]
            ]
        ];

        // Process the callback
        $request->merge($failureCallback);
        $mpesaController = new MpesaController(app(\App\Services\MpesaService::class));
        $response = $mpesaController->callback($request);

        // Check results
        $transaction->refresh();
        $payment = Payment::where('mpesa_transaction_id', $transaction->id)->first();

        return response()->json([
            'test' => 'FAILURE_SCENARIO',
            'transaction_status' => $transaction->status,
            'transaction_result_desc' => $transaction->result_desc,
            'payment_created' => $payment ? 'YES' : 'NO',
            'callback_response' => $response->getData(),
            'transaction_id' => $transaction->id
        ]);
    }

    /**
     * Test Timeout Scenario
     */
    public function testTimeout(Request $request)
    {
        Log::info('=== M-PESA TIMEOUT TEST STARTED ===');
        
        // Create a test transaction if none exists
        $transaction = $this->createTestTransaction();
        
        // Simulate timeout callback
        $timeoutCallback = [
            'Body' => [
                'stkCallback' => [
                    'CheckoutRequestID' => $transaction->checkout_request_id,
                    'ResultCode' => 1037, // Timeout code
                    'ResultDesc' => 'STK Push Timeout'
                ]
            ]
        ];

        // Process the timeout
        $request->merge($timeoutCallback);
        $mpesaController = new MpesaController(app(\App\Services\MpesaService::class));
        $response = $mpesaController->timeout($request);

        // Check results
        $transaction->refresh();

        return response()->json([
            'test' => 'TIMEOUT_SCENARIO',
            'transaction_status' => $transaction->status,
            'transaction_result_desc' => $transaction->result_desc,
            'callback_response' => $response->getData(),
            'transaction_id' => $transaction->id
        ]);
    }

    /**
     * Test Sandbox Detection Logic
     */
    public function testSandboxDetection()
    {
        Log::info('=== M-PESA SANDBOX DETECTION TEST ===');
        
        $transaction = $this->createTestTransaction();
        
        // Make transaction older than 2 minutes to trigger sandbox logic
        $transaction->update(['created_at' => now()->subMinutes(3)]);
        
        // Simulate sandbox callback with 1032 code
        $sandboxCallback = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => $transaction->merchant_request_id,
                    'CheckoutRequestID' => $transaction->checkout_request_id,
                    'ResultCode' => 1032, // Sandbox returns this even for success
                    'ResultDesc' => 'Request cancelled by user',
                    'CallbackMetadata' => [
                        'Item' => [
                            [
                                'Name' => 'Amount',
                                'Value' => $transaction->amount
                            ],
                            [
                                'Name' => 'MpesaReceiptNumber',
                                'Value' => 'SBX' . strtoupper(Str::random(8))
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $request = new Request();
        $request->merge($sandboxCallback);
        $mpesaController = new MpesaController(app(\App\Services\MpesaService::class));
        $response = $mpesaController->callback($request);

        $transaction->refresh();
        $payment = Payment::where('mpesa_transaction_id', $transaction->id)->first();

        return response()->json([
            'test' => 'SANDBOX_DETECTION',
            'transaction_status' => $transaction->status,
            'transaction_age_minutes' => now()->diffInMinutes($transaction->created_at),
            'payment_created' => $payment ? 'YES' : 'NO',
            'sandbox_logic_applied' => $transaction->status === 'success' ? 'YES' : 'NO',
            'transaction_id' => $transaction->id
        ]);
    }

    /**
     * Get test transaction status
     */
    public function getTestStatus($transactionId)
    {
        $transaction = MpesaTransaction::with(['payment', 'tenant', 'unit'])->find($transactionId);
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return response()->json([
            'transaction' => [
                'id' => $transaction->id,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'phone_number' => $transaction->phone_number,
                'mpesa_receipt_number' => $transaction->mpesa_receipt_number,
                'result_desc' => $transaction->result_desc,
                'created_at' => $transaction->created_at,
                'transaction_date' => $transaction->transaction_date,
            ],
            'payment' => $transaction->payment ? [
                'id' => $transaction->payment->id,
                'amount' => $transaction->payment->amount,
                'payment_date' => $transaction->payment->payment_date,
                'payment_method' => $transaction->payment->payment_method,
                'notes' => $transaction->payment->notes,
            ] : null,
            'tenant' => [
                'name' => $transaction->tenant->name,
                'email' => $transaction->tenant->email,
            ],
            'unit' => [
                'unit_number' => $transaction->unit->unit_number,
            ]
        ]);
    }

    /**
     * Create a test transaction
     */
    private function createTestTransaction()
    {
        // Get first tenant with assignment
        $assignment = TenantAssignment::with(['tenant', 'unit', 'property'])
            ->where('status', 'active')
            ->first();

        if (!$assignment) {
            throw new \Exception('No active tenant assignments found for testing');
        }

        return MpesaTransaction::create([
            'tenant_id' => $assignment->tenant_id,
            'unit_id' => $assignment->unit_id,
            'property_id' => $assignment->property_id,
            'phone_number' => '254712345678',
            'amount' => 5000.00,
            'checkout_request_id' => 'ws_CO_' . now()->format('dmY') . '_' . Str::random(10),
            'merchant_request_id' => Str::random(20),
            'account_reference' => 'RENT-TEST-' . $assignment->unit->unit_number,
            'transaction_desc' => 'Test rent payment for ' . $assignment->property->name,
            'status' => MpesaTransaction::STATUS_PENDING
        ]);
    }
}
