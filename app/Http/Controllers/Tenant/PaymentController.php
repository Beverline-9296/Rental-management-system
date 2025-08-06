<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
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
        return view('tenant.payments.index', compact('payments', 'totalPaid', 'totalDue', 'arrears'));
    }
}
