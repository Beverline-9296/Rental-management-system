<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPendingMpesaJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 60; // 1 minutes
    public $tries = 3;

    private $timeoutMinutes;

    /**
     * Create a new job instance.
     */
    public function __construct($timeoutMinutes = 0.5)
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
                $transactionAge = now()->diffInMinutes($transaction->created_at);
                
                // Only process transactions older than 0.5 minutes (30 seconds)
                if ($transactionAge >= 0.5 || $transactionAge < 0) {
                    // For sandbox: Mark ALL transactions as FAILED by default
                    // This is safer - only successful callbacks will mark as success
                    $transaction->update([
                        'status' => 'failed',
                        'transaction_date' => now(),
                        'result_desc' => 'Transaction timed out or was cancelled',
                        'result_code' => '1032'
                    ]);
                    
                    Log::info("M-Pesa transaction {$transaction->id} marked as failed due to timeout (sandbox safe mode)");
                }

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
