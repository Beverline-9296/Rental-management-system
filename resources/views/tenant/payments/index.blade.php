@extends('tenant.layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">My Payment History</h2>
        <a href="{{ route('tenant.payments.export') }}" 
           class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
            <i class="fas fa-file-excel mr-2"></i>
            Export to Excel
        </a>
    </div>
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded shadow p-4">
            <div class="text-gray-500 text-sm">Total Rent Paid</div>
            <div class="text-lg font-bold text-green-700">KSh {{ number_format($totalRentPaid, 2) }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-gray-500 text-sm">Total Deposits Paid</div>
            <div class="text-lg font-bold text-blue-700">KSh {{ number_format($totalDepositsPaid, 2) }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-gray-500 text-sm">Total Rent Due</div>
            <div class="text-lg font-bold text-purple-700">KSh {{ number_format($totalDue, 2) }}</div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-gray-500 text-sm">Arrears</div>
            <div class="text-lg font-bold text-red-700">KSh {{ number_format($arrears, 2) }}</div>
        </div>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto mt-8">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Property</th>
                    <th class="px-4 py-2">Unit</th>
                    <th class="px-4 py-2">Payment Type</th>
                    <th class="px-4 py-2">Amount (KSh)</th>
                    <th class="px-4 py-2">Method</th>
                    <th class="px-4 py-2">Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $payment->payment_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $payment->property->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $payment->unit->unit_number ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($payment->payment_type === 'rent') bg-green-100 text-green-800
                            @elseif($payment->payment_type === 'deposit') bg-blue-100 text-blue-800
                            @elseif($payment->payment_type === 'utility') bg-yellow-100 text-yellow-800
                            @elseif($payment->payment_type === 'maintenance') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($payment->payment_type ?? 'other') }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ number_format($payment->amount,2) }}</td>
                    <td class="px-4 py-2">{{ $payment->payment_method ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $payment->recordedBy->name ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">No payments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
