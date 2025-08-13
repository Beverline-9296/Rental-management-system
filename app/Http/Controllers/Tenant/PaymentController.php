<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\TenantAssignment;
use App\Models\MpesaTransaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $payments = $user->payments()->orderByDesc('payment_date')->with(['unit', 'property', 'recordedBy'])->get();
        $totalPaid = $user->getTotalPaid();
        $totalDue = $user->getTotalDue();
        $arrears = $user->getArrears();
        
        // Get M-Pesa transactions
        $mpesaTransactions = MpesaTransaction::where('tenant_id', $user->id)
            ->orderByDesc('created_at')
            ->with(['unit', 'property'])
            ->get();
        
        return view('tenant.payments.index', compact('payments', 'totalPaid', 'totalDue', 'arrears', 'mpesaTransactions'));
    }

    public function makePayment()
    {
        $user = auth()->user();
        
        // Get active tenant assignments
        $assignments = TenantAssignment::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with(['unit', 'property'])
            ->get();
        
        if ($assignments->isEmpty()) {
            return redirect()->route('tenant.payments.index')
                ->with('error', 'You are not currently assigned to any unit.');
        }
        
        return view('tenant.payments.make-payment', compact('assignments'));
    }

    public function make()
    {
        $user = auth()->user();
        
        // Get active tenant assignments
        $assignments = TenantAssignment::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with(['unit', 'property'])
            ->get();
        
        if ($assignments->isEmpty()) {
            return redirect()->route('tenant.payments.index')
                ->with('error', 'You are not currently assigned to any unit.');
        }

        // Calculate financial summary
        $totalPaid = $user->getTotalPaid();
        $totalDue = $user->getTotalDue();
        $arrears = $user->getArrears();
        
        return view('tenant.payments.make', compact('assignments', 'totalPaid', 'totalDue', 'arrears'));
    }

    /**
     * Export tenant payment history to CSV
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Get payments with filters
        $query = $user->payments()->with(['unit', 'property', 'mpesaTransaction', 'recordedBy']);
        
        if ($startDate) {
            $query->whereDate('payment_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('payment_date', '<=', $endDate);
        }
        
        $payments = $query->orderBy('payment_date', 'desc')->get();
        
        // Create CSV content
        $csvData = [];
        
        // Add headers
        $csvData[] = [
            'Payment Date',
            'Property Name',
            'Unit Number',
            'Amount (KES)',
            'Payment Method',
            'Payment Type',
            'M-Pesa Receipt Number',
            'Status',
            'Notes',
            'Recorded By',
            'Created At'
        ];
        
        // Add payment data
        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '',
                $payment->property->name ?? 'N/A',
                $payment->unit->unit_number ?? 'N/A',
                number_format($payment->amount, 2),
                ucfirst($payment->payment_method ?? ''),
                ucfirst($payment->payment_type ?? ''),
                $payment->mpesaTransaction->mpesa_receipt_number ?? 'N/A',
                'Completed',
                $payment->notes ?? '',
                $payment->recordedBy->name ?? 'System',
                $payment->created_at->format('Y-m-d H:i')
            ];
        }
        
        // Generate filename
        $fileName = 'payment_history_' . str_replace(' ', '_', $user->name) . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        // Create CSV response
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper Excel UTF-8 support
            fwrite($file, "\xEF\xBB\xBF");
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);
    }

    /**
     * Show export form
     */
    public function showExportForm()
    {
        return view('tenant.payments.export');
    }
}
