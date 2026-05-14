<?php 

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPendingMpesaTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpesa:process-pending {--timeout=0.5 : Minutes after which to check and process pending transactions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending M-Pesa transactions and create payment records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeoutMinutes = max((float) $this->option('timeout'), 1);
        $cutoffTime = now()->subMinutes($timeoutMinutes);
        $mpesaService = app(MpesaService::class);
        
        // Get pending transactions older than timeout period
        $pendingTransactions = MpesaTransaction::where('status', 'pending')
            ->where('created_at', '<=', $cutoffTime)
            ->get();

        if ($pendingTransactions->isEmpty()) {
            $this->info('No pending transactions to process.');
            return 0;
        }

        $this->info("Found {$pendingTransactions->count()} pending transactions to process.");

        $processed = 0;
        $failed = 0;
        $successful = 0;

        foreach ($pendingTransactions as $transaction) {
            try {
                $transactionAge = max(0, $transaction->created_at->diffInMinutes(now(), false));
                
                $this->line("Processing transaction ID: {$transaction->id} (Age: {$transactionAge} min)");
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
                                'notes' => 'M-Pesa payment (scheduled reconciliation)',
                                'recorded_by' => $transaction->tenant_id,
                            ]
                        );
                    });
                    
                    $this->line("SUCCESS: Transaction ID {$transaction->id} finalized.");
                    $successful++;
                } else {
                    $knownFailureCodes = ['1', '17', '1032', '1037', '2001'];
                    $hardTimeoutReached = $transactionAge >= ($timeoutMinutes + 2);

                    if (in_array((string) $resultCode, $knownFailureCodes, true) || $hardTimeoutReached) {
                        if ($transaction->isPending()) {
                            $transaction->markAsFailed([
                                'ResultCode' => $resultCode ?? '1032',
                                'ResultDesc' => $queryResponse['ResultDesc'] ?? 'Transaction timed out',
                            ]);
                        }

                        $this->line("FAILED: Transaction ID {$transaction->id} closed as failed.");
                        $failed++;
                    } else {
                        $this->line("WAIT: Transaction ID {$transaction->id} still pending at gateway.");
                    }
                }

                $processed++;

            } catch (\Exception $e) {
                $this->error("ERROR: Failed processing transaction ID {$transaction->id} - {$e->getMessage()}");
                Log::error("Failed to process M-Pesa transaction {$transaction->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\nProcessing Complete");
        $this->info("Total Processed: {$processed}");
        $this->info("Successful: {$successful}");
        $this->info("Failed: {$failed}");

        // Log the activity
        Log::info("M-Pesa auto-processing completed", [
            'processed' => $processed,
            'successful' => $successful,
            'failed' => $failed,
            'timeout_minutes' => $timeoutMinutes
        ]);

        return 0;
    }
}

