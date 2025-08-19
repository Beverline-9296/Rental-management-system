<?php 

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use App\Models\ActivityLog;
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
        $timeoutMinutes = $this->option('timeout');
        $cutoffTime = now()->subMinutes($timeoutMinutes);
        
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
                $isSandbox = config('mpesa.environment') === 'sandbox';
                $transactionAge = now()->diffInMinutes($transaction->created_at);
                
                $this->line("🔍 Processing transaction ID: {$transaction->id} (Age: {$transactionAge} min)");
                
                // Smart sandbox logic: Only assume success for transactions that haven't been explicitly failed
                if ($isSandbox && ($transactionAge >= 2 || $transactionAge < 0)) {
                    // Mark as successful and create payment
                    DB::transaction(function () use ($transaction) {
                        $receiptNumber = 'SBX' . time() . rand(1000, 9999);
                        
                        $transaction->update([
                            'status' => 'success',
                            'mpesa_receipt_number' => $receiptNumber,
                            'transaction_date' => now(),
                            'result_desc' => 'Payment completed successfully (sandbox)',
                            'result_code' => '0'
                        ]);
                        
                        // Create payment record
                        $payment = Payment::create([
                            'tenant_id' => $transaction->tenant_id,
                            'unit_id' => $transaction->unit_id,
                            'property_id' => $transaction->property_id,
                            'amount' => $transaction->amount,
                            'payment_date' => now(),
                            'payment_method' => 'mpesa',
                            'payment_type' => $transaction->payment_type,
                            'notes' => 'M-Pesa payment - Receipt: ' . $receiptNumber,
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
                                'receipt' => $receiptNumber,
                                'unit_id' => $transaction->unit_id,
                                'payment_type' => $transaction->payment_type
                            ],
                            'fas fa-check-circle',
                            'green'
                        );
                    });
                    
                    $this->line("✅ SUCCESS: Transaction ID: {$transaction->id} (KES {$transaction->amount}) - Payment recorded");
                    $successful++;
                } else {
                    // Process failed transaction (no receipt number means user didn't complete payment)
                    $transaction->update([
                        'status' => 'failed',
                        'transaction_date' => now(),
                        'result_desc' => $transactionAge >= $timeoutMinutes ? 'Transaction timed out' : 'Payment cancelled by user',
                        'result_code' => $transactionAge >= $timeoutMinutes ? '1032' : '1'
                    ]);
                    
                    $this->line("❌ FAILED: Transaction ID: {$transaction->id} (KES {$transaction->amount}) - " . 
                        ($transactionAge >= $timeoutMinutes ? 'Timeout' : 'User cancelled'));
                    $failed++;
                }

                $processed++;

            } catch (\Exception $e) {
                $this->error("❌ ERROR: Failed to process transaction ID: {$transaction->id} - {$e->getMessage()}");
                Log::error("Failed to process M-Pesa transaction {$transaction->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\n=== Processing Complete ===");
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

