<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPendingMpesaJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300; // 5 minutes
    public $tries = 3;

    private $timeoutMinutes;

    /**
     * Create a new job instance.
     */
    public function __construct($timeoutMinutes = 3)
    {
        $this->timeoutMinutes = $timeoutMinutes;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cutoffTime = now()->subMinutes($this->timeoutMinutes);
        
        // Get pending transactions older than timeout period
        $pendingTransactions = MpesaTransaction::where('status', 'pending')
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        if ($pendingTransactions->isEmpty()) {
            Log::info('No pending M-Pesa transactions to process.');
            return;
        }

        Log::info("Processing {$pendingTransactions->count()} pending M-Pesa transactions.");

        $processed = 0;
        $failed = 0;

        foreach ($pendingTransactions as $transaction) {
            try {
                DB::transaction(function () use ($transaction) {
                    // Generate receipt number
                    $receiptNumber = 'AUTO' . time() . rand(1000, 9999);
                    
                    // Update M-Pesa transaction as successful
                    $transaction->update([
                        'status' => 'success',
                        'mpesa_receipt_number' => $receiptNumber,
                        'transaction_date' => now(),
                        'result_desc' => 'Payment auto-processed by job after timeout',
                        'result_code' => '0'
                    ]);
                    
                    // Create payment record
                    Payment::create([
                        'tenant_id' => $transaction->tenant_id,
                        'unit_id' => $transaction->unit_id,
                        'property_id' => $transaction->property_id,
                        'amount' => $transaction->amount,
                        'payment_date' => now(),
                        'payment_method' => 'mpesa',
                        'payment_type' => 'rent',
                        'notes' => 'M-Pesa payment auto-processed by job - Receipt: ' . $receiptNumber,
                        'recorded_by' => $transaction->tenant_id,
                        'mpesa_transaction_id' => $transaction->id
                    ]);
                });

                $processed++;

            } catch (\Exception $e) {
                Log::error("Failed to process M-Pesa transaction {$transaction->id}: " . $e->getMessage());
                $failed++;
            }
        }

        Log::info("M-Pesa job processing completed", [
            'processed' => $processed,
            'failed' => $failed,
            'timeout_minutes' => $this->timeoutMinutes
        ]);
    }
}
