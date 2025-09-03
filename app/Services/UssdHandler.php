<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\TenantAssignment;
use App\Http\Controllers\MpesaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UssdHandler
{
    private $sessionId;
    private $phoneNumber;
    private $text;
    private $user;

    public function __construct($phoneNumber, $sessionId, $text)
    {
        $this->sessionId = $sessionId;
        $this->phoneNumber = $phoneNumber;
        $this->text = $text;
        $this->user = $this->getUserByPhone($phoneNumber);
    }

    /**
     * Main handler method called by controller
     */
    public function handle()
    {
        return $this->processRequest();
    }

    /**
     * Process USSD request and return appropriate response
     */
    public function processRequest()
    {
        try {
            Log::info('USSD Request Processing', [
                'session_id' => $this->sessionId,
                'phone' => $this->phoneNumber,
                'text' => $this->text,
                'user_found' => $this->user ? true : false
            ]);

            // Check if user is registered
            if (!$this->user) {
                return $this->unregisteredUserResponse();
            }

        // Parse menu navigation
        $menuPath = explode('*', $this->text);
        
        if (empty($this->text)) {
            return $this->mainMenu();
        }

            switch ($menuPath[0]) {
                case '1':
                    return $this->handleBalanceMenu($menuPath);
                case '2':
                    return $this->handleLastPaymentMenu($menuPath);
                case '3':
                    return $this->handleRentPaymentMenu($menuPath);
                case '4':
                    return $this->handleContactMenu();
                case '5':
                    return $this->handlePaymentHistoryMenu($menuPath);
                case '0':
                    return $this->exitMenu();
                default:
                    return $this->invalidOptionMenu();
            }
        } catch (\Exception $e) {
            Log::error('USSD Processing Error', [
                'session_id' => $this->sessionId,
                'phone' => $this->phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return "END System error. Please try again later.";
        }
    }

    /**
     * Main menu
     */
    private function mainMenu()
    {
        // Get active tenant assignment
        $assignment = $this->user->tenantAssignments->where('status', 'active')->first();
        $propertyName = $assignment && $assignment->unit ? $assignment->unit->property->name : 'N/A';
        $unitNumber = $assignment && $assignment->unit ? $assignment->unit->unit_number : 'N/A';

        $response = "CON Welcome {$this->user->name}\n";
        $response .= "Property: {$propertyName}\n";
        $response .= "Unit: {$unitNumber}\n\n";
        $response .= "1. Check Balance\n";
        $response .= "2. Last Payment\n";
        $response .= "3. Pay Rent\n";
        $response .= "4. Contact Info\n";
        $response .= "5. Payment History\n";
        $response .= "0. Exit";

        return $response;
    }

    /**
     * Handle balance checking menu
     */
    private function handleBalanceMenu($menuPath)
    {
        if (count($menuPath) == 1) {
            // Show balance details
            $assignment = $this->user->tenantAssignments->where('status', 'active')->first();
            if (!$assignment) {
                return "END No active rental assignment found.";
            }

            $monthlyRent = $assignment->monthly_rent;
            $totalPaid = $this->user->getTotalRentPaid();
            $totalDue = $this->user->getTotalDue();
            $arrears = $this->user->getArrears();
            $nextDueDate = $this->getNextDueDate($assignment);

            $response = "END BALANCE SUMMARY\n";
            $response .= "Monthly Rent: KES " . number_format($monthlyRent) . "\n";
            $response .= "Total Paid: KES " . number_format($totalPaid) . "\n";
            $response .= "Total Due: KES " . number_format($totalDue) . "\n";
            $response .= "Outstanding: KES " . number_format($arrears) . "\n";
            
            if ($nextDueDate) {
                $response .= "Next Due: " . $nextDueDate->format('M d, Y') . "\n";
            }

            if ($arrears > 0) {
                $response .= "\nPlease clear outstanding balance.";
            } else {
                $response .= "\nAccount is up to date!";
            }

            return $response;
        }

        return $this->invalidOptionMenu();
    }

    /**
     * Handle last payment viewing menu
     */
    private function handleLastPaymentMenu($menuPath)
    {
        if (count($menuPath) == 1) {
            // Show last payment details
            $lastPayment = $this->user->payments()
                ->where('payment_type', 'rent')
                ->orderByDesc('payment_date')
                ->first();

            if (!$lastPayment) {
                return "END No payment records found.";
            }

            $response = "END LAST PAYMENT\n";
            $response .= "Date: " . $lastPayment->payment_date->format('M d, Y') . "\n";
            $response .= "Amount: KES " . number_format($lastPayment->amount) . "\n";
            $response .= "Method: " . ucfirst($lastPayment->payment_method) . "\n";
            $response .= "Type: " . ucfirst($lastPayment->payment_type) . "\n";
            if ($lastPayment->mpesaTransaction) {
                $response .= "Reference: " . $lastPayment->mpesaTransaction->mpesa_receipt_number . "\n";
                $response .= "Status: " . ucfirst($lastPayment->mpesaTransaction->status);
            } else {
                $response .= "Reference: Manual Payment\n";
                $response .= "Status: Completed";
            }

            return $response;
        }

        return $this->invalidOptionMenu();
    }

    /**
     * Handle rent payment initiation menu
     */
    private function handleRentPaymentMenu($menuPath)
    {
        $assignment = $this->user->tenantAssignments->where('status', 'active')->first();
        if (!$assignment) {
            return "END No active rental assignment found.";
        }

        if (count($menuPath) == 1) {
            // Show simplified payment options
            $monthlyRent = $assignment->monthly_rent;

            $response = "CON RENT PAYMENT\n";
            $response .= "1. Pay Monthly Rent (KES " . number_format($monthlyRent) . ")\n";
            $response .= "2. Custom Amount\n";
            $response .= "0. Back to Main Menu";

            return $response;
        }

        if (count($menuPath) == 2) {
            $option = $menuPath[1];
            $monthlyRent = $assignment->monthly_rent;

            switch ($option) {
                case '1':
                    // Pay monthly rent
                    return $this->initiatePayment($monthlyRent, 'monthly rent');
                case '2':
                    // Custom amount
                    return "CON Enter amount to pay:\n(Minimum: KES 10)";
                case '0':
                    return $this->mainMenu();
                default:
                    return $this->invalidOptionMenu();
            }
        }

        if (count($menuPath) == 3) {
            // Handle custom amount input
            $amount = intval($menuPath[2]);
            
            if ($amount < 10) {
                return "END Invalid amount. Minimum payment is KES 10.";
            }

            return $this->initiatePayment($amount, 'custom amount');
        }

        return $this->invalidOptionMenu();
    }

    /**
     * Handle payment history menu
     */
    private function handlePaymentHistoryMenu($menuPath)
    {
        if (count($menuPath) == 1) {
            // Show payment history options
            $response = "CON PAYMENT HISTORY\n";
            $response .= "1. Last 5 Payments\n";
            $response .= "2. Last 10 Payments\n";
            $response .= "3. Current Year\n";
            $response .= "4. All Payments\n";
            $response .= "0. Back to Main Menu";

            return $response;
        }

        if (count($menuPath) == 2) {
            $option = $menuPath[1];

            switch ($option) {
                case '1':
                    return $this->showPaymentHistory(5);
                case '2':
                    return $this->showPaymentHistory(10);
                case '3':
                    return $this->showPaymentHistoryByYear(Carbon::now()->year);
                case '4':
                    return $this->showPaymentHistory(null); // All payments
                case '0':
                    return $this->mainMenu();
                default:
                    return $this->invalidOptionMenu();
            }
        }

        return $this->invalidOptionMenu();
    }

    /**
     * Handle contact information menu
     */
    private function handleContactMenu()
    {
        $assignment = $this->user->tenantAssignments->where('status', 'active')->first();
        if (!$assignment) {
            return "END No rental assignment found.";
        }

        $landlord = $assignment->unit->property->landlord;
        
        $response = "END LANDLORD CONTACT\n";
        $response .= "Name: " . $landlord->name . "\n";
        $response .= "Phone: " . $landlord->phone . "\n";
        $response .= "Email: " . $landlord->email . "\n\n";
        $response .= "Property: " . $assignment->unit->property->name;

        return $response;
    }

    /**
     * Initiate M-Pesa payment
     */
    private function initiatePayment($amount, $description)
    {
        Log::info('USSD Payment Request', [
            'phone' => $this->phoneNumber,
            'amount' => $amount,
            'description' => $description,
            'user_id' => $this->user->id
        ]);

        // Trigger actual M-Pesa STK Push
        $this->sendMpesaSTK($amount);

        $response = "END PAYMENT REQUEST SENT\n";
        $response .= "Amount: KES " . number_format($amount) . "\n";
        $response .= "Type: " . ucfirst($description) . "\n\n";
        $response .= "Check your phone for M-Pesa prompt.\n";
        $response .= "Enter your M-Pesa PIN to complete payment.";

        return $response;
    }

    /**
     * Send M-Pesa STK Push
     */
    private function sendMpesaSTK($amount)
    {
        try {
            // Use MpesaService directly
            $mpesaService = app(\App\Services\MpesaService::class);
            
            // Call STK Push with proper parameters
            $result = $mpesaService->stkPush(
                $this->phoneNumber,
                $amount,
                'RENT-' . $this->user->id,
                'Rent Payment via USSD'
            );

            Log::info('USSD M-Pesa STK Push Result', [
                'phone' => $this->phoneNumber,
                'amount' => $amount,
                'user_id' => $this->user->id,
                'result' => $result
            ]);

            // Check if STK Push was successful
            if (isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
                // Get active tenant assignment for proper database relations
                $assignment = $this->user->tenantAssignments->where('status', 'active')->first();
                
                if ($assignment) {
                    // Create M-Pesa transaction record with proper schema
                    $mpesaTransaction = \App\Models\MpesaTransaction::create([
                        'tenant_id' => $this->user->id,
                        'unit_id' => $assignment->unit_id,
                        'property_id' => $assignment->unit->property_id,
                        'phone_number' => $this->phoneNumber,
                        'amount' => $amount,
                        'merchant_request_id' => $result['MerchantRequestID'] ?? null,
                        'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
                        'account_reference' => 'RENT-' . $this->user->id,
                        'transaction_desc' => 'Rent Payment via USSD',
                        'payment_type' => 'rent',
                        'status' => \App\Models\MpesaTransaction::STATUS_PENDING
                    ]);

                    Log::info('USSD M-Pesa Transaction Created', [
                        'transaction_id' => $mpesaTransaction->id,
                        'checkout_request_id' => $result['CheckoutRequestID'] ?? null,
                        'tenant_id' => $this->user->id,
                        'unit_id' => $assignment->unit_id
                    ]);
                }

                return $result;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('USSD M-Pesa STK Push Failed', [
                'phone' => $this->phoneNumber,
                'amount' => $amount,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }


    /**
     * Exit menu
     */
    private function exitMenu()
    {
        return "END Thank you for using our rental management system. Have a great day!";
    }

    /**
     * Invalid option menu
     */
    private function invalidOptionMenu()
    {
        return "END Invalid option selected. Please try again.";
    }

    /**
     * Unregistered user response
     */
    private function unregisteredUserResponse()
    {
        return "END Phone number not registered in our system. Please contact your landlord for assistance.";
    }

    /**
     * Get user by phone number
     */
    private function getUserByPhone($phoneNumber)
    {
        // Clean phone number formats
        $cleanPhone = preg_replace('/^\+254/', '0', $phoneNumber);
        $cleanPhone = preg_replace('/^254/', '0', $cleanPhone);
        
        // Also try with +254 format
        $internationalPhone = '+254' . substr($cleanPhone, 1);
        $shortPhone = '254' . substr($cleanPhone, 1);
        
        Log::info('USSD Phone lookup', [
            'original' => $phoneNumber,
            'clean' => $cleanPhone,
            'international' => $internationalPhone,
            'short' => $shortPhone
        ]);
        
        return User::where('phone_number', $cleanPhone)
            ->orWhere('phone_number', $phoneNumber)
            ->orWhere('phone_number', $internationalPhone)
            ->orWhere('phone_number', $shortPhone)
            ->where('role', 'tenant')
            ->with(['tenantAssignments' => function($query) {
                $query->where('status', 'active')->with(['unit.property.landlord']);
            }])
            ->first();
    }

    /**
     * Show payment history with optional limit
     */
    private function showPaymentHistory($limit = null)
    {
        $query = $this->user->payments()
            ->where('payment_type', 'rent')
            ->orderByDesc('payment_date')
            ->with('mpesaTransaction');

        if ($limit) {
            $query->limit($limit);
        }

        $payments = $query->get();

        if ($payments->isEmpty()) {
            return "END No payment records found.";
        }

        $response = "END PAYMENT HISTORY\n";
        $response .= "Total Records: " . $payments->count() . "\n\n";

        foreach ($payments as $index => $payment) {
            $response .= ($index + 1) . ". " . $payment->payment_date->format('M d, Y') . "\n";
            $response .= "   Amount: KES " . number_format($payment->amount) . "\n";
            $response .= "   Method: " . ucfirst($payment->payment_method) . "\n";
            
            if ($payment->mpesaTransaction) {
                $response .= "   Ref: " . $payment->mpesaTransaction->mpesa_receipt_number . "\n";
                $response .= "   Status: " . ucfirst($payment->mpesaTransaction->status) . "\n";
            } else {
                $response .= "   Ref: Manual Payment\n";
                $response .= "   Status: Completed\n";
            }
            
            // Add separator for readability, but keep within USSD limits
            if ($index < $payments->count() - 1) {
                $response .= "\n";
            }
        }

        // Calculate total amount paid
        $totalPaid = $payments->sum('amount');
        $response .= "\nTotal Paid: KES " . number_format($totalPaid);

        return $response;
    }

    /**
     * Show payment history for specific year
     */
    private function showPaymentHistoryByYear($year)
    {
        $payments = $this->user->payments()
            ->where('payment_type', 'rent')
            ->whereYear('payment_date', $year)
            ->orderByDesc('payment_date')
            ->with('mpesaTransaction')
            ->get();

        if ($payments->isEmpty()) {
            return "END No payment records found for year {$year}.";
        }

        $response = "END PAYMENT HISTORY - {$year}\n";
        $response .= "Records: " . $payments->count() . "\n\n";

        foreach ($payments as $index => $payment) {
            $response .= ($index + 1) . ". " . $payment->payment_date->format('M d') . "\n";
            $response .= "   KES " . number_format($payment->amount) . "\n";
            $response .= "   " . ucfirst($payment->payment_method) . "\n";
            
            if ($payment->mpesaTransaction && $payment->mpesaTransaction->mpesa_receipt_number) {
                $response .= "   " . $payment->mpesaTransaction->mpesa_receipt_number . "\n";
            }
            
            if ($index < $payments->count() - 1) {
                $response .= "\n";
            }
        }

        // Calculate total for the year
        $totalPaid = $payments->sum('amount');
        $response .= "\nYear Total: KES " . number_format($totalPaid);

        return $response;
    }

    /**
     * Get next due date for rent
     */
    private function getNextDueDate($assignment)
    {
        if (!$assignment->start_date) {
            return null;
        }

        $startDate = $assignment->start_date;
        $today = Carbon::now();
        
        // Calculate next due date (assuming monthly rent due on same day each month)
        $nextDue = $startDate->copy();
        
        while ($nextDue <= $today) {
            $nextDue->addMonth();
        }
        
        return $nextDue;
    }
}
