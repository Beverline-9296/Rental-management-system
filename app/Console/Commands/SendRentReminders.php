<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantAssignment;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendRentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rent:send-reminders {--days=3 : Days before rent due date to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS rent reminders to tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysBeforeDue = (int) $this->option('days');
        $smsService = new SmsService();
        
        $this->info("Sending rent reminders for payments due in {$daysBeforeDue} days...");
        
        // Get active tenant assignments
        $assignments = TenantAssignment::where('status', 'active')
            ->with(['tenant', 'unit.property'])
            ->get();
        
        $remindersSent = 0;
        $errors = 0;
        
        foreach ($assignments as $assignment) {
            try {
                // Calculate next rent due date (assuming monthly rent on the same day each month)
                $startDate = $assignment->start_date;
                $today = Carbon::today();
                
                // Find the next rent due date
                $nextDueDate = $this->calculateNextRentDueDate($startDate, $today);
                $daysUntilDue = $today->diffInDays($nextDueDate, false);
                
                // Send reminder if rent is due in specified days or overdue
                if ($daysUntilDue <= $daysBeforeDue && $daysUntilDue >= -30) { // Don't send for very old overdue
                    $tenant = $assignment->tenant;
                    $unit = $assignment->unit;
                    $property = $unit->property;
                    
                    if ($tenant->phone_number) {
                        $result = $smsService->sendRentReminder(
                            $tenant->phone_number,
                            $tenant->name,
                            $property->name,
                            $unit->unit_number,
                            $assignment->monthly_rent,
                            $nextDueDate->format('M j, Y'),
                            $daysUntilDue
                        );
                        
                        if ($result['success']) {
                            $remindersSent++;
                            $this->info("✓ Reminder sent to {$tenant->name} ({$tenant->phone_number})");
                            
                            Log::info('Rent reminder SMS sent', [
                                'tenant_id' => $tenant->id,
                                'tenant_name' => $tenant->name,
                                'property' => $property->name,
                                'unit' => $unit->unit_number,
                                'amount' => $assignment->monthly_rent,
                                'due_date' => $nextDueDate->format('Y-m-d'),
                                'days_until_due' => $daysUntilDue
                            ]);
                        } else {
                            $errors++;
                            $this->error("✗ Failed to send reminder to {$tenant->name}: {$result['message']}");
                        }
                    } else {
                        $this->warn("⚠ No phone number for tenant: {$tenant->name}");
                    }
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Error processing reminder for assignment {$assignment->id}: {$e->getMessage()}");
                Log::error('Rent reminder error', [
                    'assignment_id' => $assignment->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("\n📊 Summary:");
        $this->info("Reminders sent: {$remindersSent}");
        $this->info("Errors: {$errors}");
        
        return Command::SUCCESS;
    }
    
    /**
     * Calculate the next rent due date based on the start date
     */
    private function calculateNextRentDueDate(Carbon $startDate, Carbon $today): Carbon
    {
        $dayOfMonth = $startDate->day;
        $currentMonth = $today->month;
        $currentYear = $today->year;
        
        // Try current month first
        $dueDate = Carbon::create($currentYear, $currentMonth, min($dayOfMonth, Carbon::create($currentYear, $currentMonth)->daysInMonth));
        
        // If due date has passed this month, move to next month
        if ($dueDate->lt($today)) {
            $dueDate->addMonth();
            // Adjust for months with fewer days
            $dueDate->day = min($dayOfMonth, $dueDate->daysInMonth);
        }
        
        return $dueDate;
    }
}
