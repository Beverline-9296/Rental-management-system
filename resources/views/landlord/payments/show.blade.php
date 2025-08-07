@extends('landlord.layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-10">
    <h2 class="text-2xl font-bold mb-6">Payment Details</h2>
    <div class="bg-white rounded shadow p-6">
        <dl class="grid grid-cols-1 gap-4">
        
            <div>
                <dt class="font-semibold">Date</dt>
                <dd>{{ $payment->payment_date->format('Y-m-d') }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Tenant</dt>
                <dd>{{ $payment->tenant->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Property</dt>
                <dd>{{ $payment->property->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Unit</dt>
                <dd>{{ $payment->unit->unit_number ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Amount</dt>
                <dd>KSh {{ number_format($payment->amount,2) }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Payment Method</dt>
                <dd>{{ $payment->payment_method ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Notes</dt>
                <dd>{{ $payment->notes ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Recorded By</dt>
                <dd>{{ $payment->recordedBy->name ?? '-' }}</dd>
            </div>
        </dl>
        <div class="mt-6">
            <a href="{{ route('landlord.payments.index') }}" class="text-blue-600 hover:underline">Back to Payments</a>
        </div>
    </div>
</div>
@endsection
