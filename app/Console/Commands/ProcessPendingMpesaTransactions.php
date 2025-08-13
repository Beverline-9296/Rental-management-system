<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MpesaTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPendingMpesaTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpesa:process-pending {--timeout=3 : Minutes after which to mark transactions as successful}';

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
                        'result_desc' => 'Payment auto-processed after timeout',
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
                        'notes' => 'M-Pesa payment auto-processed - Receipt: ' . $receiptNumber,
                        'recorded_by' => $transaction->tenant_id,
                        'mpesa_transaction_id' => $transaction->id
                    ]);
                });

                $this->line("✅ Processed transaction ID: {$transaction->id} (KES {$transaction->amount})");
                $processed++;

            } catch (\Exception $e) {
                $this->error("❌ Failed to process transaction ID: {$transaction->id} - {$e->getMessage()}");
                Log::error("Failed to process M-Pesa transaction {$transaction->id}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("\n=== Processing Complete ===");
        $this->info("Processed: {$processed}");
        $this->info("Failed: {$failed}");

        // Log the activity
        Log::info("M-Pesa auto-processing completed", [
            'processed' => $processed,
            'failed' => $failed,
            'timeout_minutes' => $timeoutMinutes
        ]);

        return 0;
    }
}
