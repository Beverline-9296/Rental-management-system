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
        $mpesaService = app(MpesaService::class);
        
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
                $transactionAge = max(0, $transaction->created_at->diffInMinutes(now(), false));
                $queryResponse = $mpesaService->stkQuery($transaction->checkout_request_id);
                $resultCode = isset($queryResponse['ResultCode']) ? (string) $queryResponse['ResultCode'] : null;

                if ($resultCode === '0') {
                    DB::transaction(function () use ($transaction) {
                        if ($transaction->isPending()) {
                            $transaction->markAsSuccess(['ResultCode' => 0, 'ResultDesc' => 'Success']);
                        }

                        Payment::firstOrCreate(
                            ['mpesa_transaction_id' => $transaction->id],
                            [
                                'tenant_id' => $transaction->tenant_id,
                                'unit_id' => $transaction->unit_id,
                                'property_id' => $transaction->property_id,
                                'amount' => $transaction->amount,
                                'payment_date' => now(),
                                'payment_method' => 'mpesa',
                                'payment_type' => $transaction->payment_type,
                                'notes' => 'M-Pesa payment (job reconciliation)',
                                'recorded_by' => $transaction->tenant_id,
                            ]
                        );
                    });
                } else {
                    $knownFailureCodes = ['1', '17', '1032', '1037', '2001'];
                    $hardTimeoutReached = $transactionAge >= ($this->timeoutMinutes + 2);

                    if (in_array((string) $resultCode, $knownFailureCodes, true) || $hardTimeoutReached) {
                        if ($transaction->isPending()) {
                            $transaction->markAsFailed([
                                'ResultCode' => $resultCode ?? '1032',
                                'ResultDesc' => $queryResponse['ResultDesc'] ?? 'Transaction timed out',
                            ]);
                        }
                    }
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
