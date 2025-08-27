<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\MpesaService;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use App\Models\TenantAssignment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MpesaController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Initiate STK Push
     */
    public function stkPush(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'unit_id' => 'required|exists:units,id',
            'payment_type' => 'required|string|in:rent,deposit,utility,maintenance,other'
        ]);

        try {
            $user = auth()->user();
            $unitId = $request->unit_id;
            
            // Get tenant assignment
            $assignment = TenantAssignment::where('tenant_id', $user->id)
                ->where('unit_id', $unitId)
                ->where('status', 'active')
                ->with(['unit', 'property'])
                ->first();

            if (!$assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this unit'
                ], 400);
            }

            $paymentType = $request->payment_type;
            $accountReference = strtoupper($paymentType) . '-' . $assignment->unit->unit_number . '-' . $user->id;
            $transactionDesc = ucfirst($paymentType) . ' payment for ' . $assignment->property->name . ' - Unit ' . $assignment->unit->unit_number;

            // Initiate STK Push
            $response = $this->mpesaService->stkPush(
                $request->phone_number,
                $request->amount,
                $accountReference,
                $transactionDesc
            );

            if (isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
                // Save transaction record as pending initially
                $transaction = MpesaTransaction::create([
                    'tenant_id' => $user->id,
                    'unit_id' => $unitId,
                    'property_id' => $assignment->property->id,
                    'phone_number' => $request->phone_number,
                    'amount' => $request->amount,
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'merchant_request_id' => $response['MerchantRequestID'],
                    'account_reference' => $accountReference,
                    'transaction_desc' => $transactionDesc,
                    'payment_type' => $paymentType,
                    'status' => MpesaTransaction::STATUS_PENDING
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'STK Push sent successfully. Please enter your M-Pesa PIN to complete payment.',
                    'checkout_request_id' => $response['CheckoutRequestID'],
                    'transaction_id' => $transaction->id
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $response['errorMessage'] ?? 'Failed to initiate payment'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('STK Push Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Handle M-Pesa callback
     */
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback Received: ', [
            'method' => $request->method(),
            'data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            // Handle GET requests (for testing or sandbox verification)
            if ($request->isMethod('GET')) {
                Log::info('GET request to M-Pesa callback endpoint - likely for testing/verification');
                return response()->json([
                    'ResultCode' => 0, 
                    'ResultDesc' => 'Callback endpoint is active',
                    'message' => 'M-Pesa callback endpoint is working properly'
                ]);
            }

            // Handle POST requests (actual M-Pesa callbacks)
            $callbackData = $request->all();
            
            if (!isset($callbackData['Body']['stkCallback'])) {
                Log::error('Invalid callback structure');
                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback']);
            }

            $stkCallback = $callbackData['Body']['stkCallback'];
            $checkoutRequestId = $stkCallback['CheckoutRequestID'];
            
            // Find the transaction
            $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();
            
            if (!$transaction) {
                Log::error('Transaction not found for CheckoutRequestID: ' . $checkoutRequestId);
                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Transaction not found']);
            }

            if ($stkCallback['ResultCode'] == 0) {
                // Payment successful
                DB::transaction(function () use ($transaction, $stkCallback) {
                    // Update M-Pesa transaction
                    $transaction->markAsSuccess($stkCallback);
                    
                    // Create payment record
                    $payment = Payment::create([
                        'tenant_id' => $transaction->tenant_id,
                        'unit_id' => $transaction->unit_id,
                        'property_id' => $transaction->property_id,
                        'amount' => $transaction->amount,
                        'payment_date' => now(),
                        'payment_method' => 'mpesa',
                        'payment_type' => $transaction->payment_type,
                        'notes' => 'M-Pesa payment - Receipt: ' . $transaction->mpesa_receipt_number,
                        'recorded_by' => $transaction->tenant_id,
                        'mpesa_transaction_id' => $transaction->id
                    ]);

                    // Log the successful payment activity
                    ActivityLog::logActivity(
                        $transaction->tenant_id,
                        'payment_completed',
                        'Payment of KSh ' . number_format($transaction->amount) . ' completed successfully via M-Pesa',
                        [
                            'payment_id' => $payment->id,
                            'amount' => $transaction->amount,
                            'method' => 'mpesa',
                            'receipt' => $transaction->mpesa_receipt_number,
                            'unit_id' => $transaction->unit_id
                        ],
                        'fas fa-check-circle',
                        'green'
                    );
                });

                Log::info('Payment processed successfully for transaction: ' . $transaction->id);
            } else {
                // Payment failed
                $transaction->markAsFailed($stkCallback);
                Log::info('Payment failed for transaction: ' . $transaction->id . ' - ' . $stkCallback['ResultDesc']);
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);

        } catch (\Exception $e) {
            Log::error('Callback processing error: ' . $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Processing failed']);
        }
    }

    /**
     * Handle M-Pesa timeout
     */
    public function timeout(Request $request)
    {
        Log::info('M-Pesa Timeout: ', [
            'method' => $request->method(),
            'data' => $request->all()
        ]);
        
        try {
            // Handle GET requests (for testing or verification)
            if ($request->isMethod('GET')) {
                Log::info('GET request to M-Pesa timeout endpoint - likely for testing/verification');
                return response()->json([
                    'ResultCode' => 0, 
                    'ResultDesc' => 'Timeout endpoint is active',
                    'message' => 'M-Pesa timeout endpoint is working properly'
                ]);
            }

            // Handle POST requests (actual M-Pesa timeouts)
            $timeoutData = $request->all();
            
            if (isset($timeoutData['Body']['stkCallback']['CheckoutRequestID'])) {
                $checkoutRequestId = $timeoutData['Body']['stkCallback']['CheckoutRequestID'];
                
                $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();
                
                if ($transaction && $transaction->isPending()) {
                    $transaction->update([
                        'status' => MpesaTransaction::STATUS_CANCELLED,
                        'result_desc' => 'Transaction timeout'
                    ]);
                }
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        } catch (\Exception $e) {
            Log::error('Timeout processing error: ' . $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Processing failed']);
        }
    }

    /**
     * Check transaction status
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'checkout_request_id' => 'required|string'
        ]);

        try {
            $transaction = MpesaTransaction::where('checkout_request_id', $request->checkout_request_id)
                ->where('tenant_id', auth()->id())
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'mpesa_receipt_number' => $transaction->mpesa_receipt_number,
                'transaction_date' => $transaction->transaction_date,
                'result_desc' => $transaction->result_desc
            ]);

        } catch (\Exception $e) {
            Log::error('Status check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check status'
            ], 500);
        }
    }
}
