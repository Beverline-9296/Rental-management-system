<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Display receipts for authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'tenant') {
            $receipts = Receipt::where('tenant_id', $user->id)
                ->with(['payment', 'property', 'unit'])
                ->orderBy('receipt_date', 'desc')
                ->paginate(20);
        } else {
            // For landlords, show receipts for their properties
            $propertyIds = $user->ownedProperties()->pluck('id');
            $receipts = Receipt::whereIn('property_id', $propertyIds)
                ->with(['payment', 'tenant', 'property', 'unit'])
                ->orderBy('receipt_date', 'desc')
                ->paginate(20);
        }
        
        return view('receipts.index', compact('receipts'));
    }

    /**
     * Show specific receipt
     */
    public function show(Receipt $receipt)
    {
        $user = Auth::user();
        
        // Check authorization
        if ($user->role === 'tenant' && $receipt->tenant_id !== $user->id) {
            abort(403, 'Unauthorized access to receipt');
        }
        
        if ($user->role === 'landlord') {
            $propertyIds = $user->ownedProperties()->pluck('id');
            if (!$propertyIds->contains($receipt->property_id)) {
                abort(403, 'Unauthorized access to receipt');
            }
        }
        
        $receipt->load(['payment', 'tenant', 'property', 'unit']);
        
        return view('receipts.show', compact('receipt'));
    }

    /**
     * Generate and download PDF receipt
     */
    public function downloadPdf(Receipt $receipt)
    {
        $user = Auth::user();
        
        // Check authorization
        if ($user->role === 'tenant' && $receipt->tenant_id !== $user->id) {
            abort(403, 'Unauthorized access to receipt');
        }
        
        if ($user->role === 'landlord') {
            $propertyIds = $user->ownedProperties()->pluck('id');
            if (!$propertyIds->contains($receipt->property_id)) {
                abort(403, 'Unauthorized access to receipt');
            }
        }
        
        $receipt->load(['payment', 'tenant', 'property', 'unit']);
        
        // Update status to downloaded
        $receipt->update(['status' => 'downloaded']);
        
        $pdf = Pdf::loadView('receipts.pdf', compact('receipt'));
        
        return $pdf->download('receipt-' . $receipt->receipt_number . '.pdf');
    }

    /**
     * Generate receipt from payment (used internally)
     */
    public static function generateFromPayment(Payment $payment)
    {
        // Check if receipt already exists
        if ($payment->receipt) {
            return $payment->receipt;
        }
        
        // Load necessary relationships
        $payment->load(['tenant', 'property', 'unit', 'mpesaTransaction']);
        
        return Receipt::createFromPayment($payment);
    }

    /**
     * Resend receipt via email
     */
    public function resendEmail(Receipt $receipt)
    {
        $user = Auth::user();
        
        // Check authorization
        if ($user->role === 'tenant' && $receipt->tenant_id !== $user->id) {
            abort(403, 'Unauthorized access to receipt');
        }
        
        if ($user->role === 'landlord') {
            $propertyIds = $user->ownedProperties()->pluck('id');
            if (!$propertyIds->contains($receipt->property_id)) {
                abort(403, 'Unauthorized access to receipt');
            }
        }
        
        try {
            // Send receipt via email
            \Mail::to($receipt->tenant->email)->send(new \App\Mail\ReceiptMail($receipt));
            
            $receipt->update(['status' => 'sent']);
            
            return back()->with('success', 'Receipt sent successfully to ' . $receipt->tenant->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send receipt email: ' . $e->getMessage());
            return back()->with('error', 'Failed to send receipt. Please try again.');
        }
    }
}
