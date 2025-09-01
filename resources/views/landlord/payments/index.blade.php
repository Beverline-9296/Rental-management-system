@extends('landlord.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10">
    <h2 class="text-2xl font-bold mb-6">Payment Records</h2>
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    @if(!empty($tenantsSummary))
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-3">Tenant Arrears & Balances</h3>
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Tenant</th>
                        <th class="px-4 py-2">Property</th>
                        <th class="px-4 py-2">Unit</th>
                        <th class="px-4 py-2">Total Due (KSh)</th>
                        <th class="px-4 py-2">Total Paid (KSh)</th>
                        <th class="px-4 py-2">Arrears (KSh)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sum_due = 0; $sum_paid = 0; $sum_arrears = 0; @endphp
                    @foreach($tenantsSummary as $row)
                        @php
                            $sum_due += $row['total_due'];
                            $sum_paid += $row['total_paid'];
                            $sum_arrears += $row['arrears'];
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $row['tenant']->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $row['property']->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $row['unit']->unit_number ?? '-' }}</td>
                            <td class="px-4 py-2">{{ number_format($row['total_due'],2) }}</td>
                            <td class="px-4 py-2">{{ number_format($row['total_paid'],2) }}</td>
                            <td class="px-4 py-2 text-red-700 font-bold">{{ number_format($row['arrears'],2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-4 py-2 text-right" colspan="3">Totals:</td>
                        <td class="px-4 py-2">{{ number_format($sum_due,2) }}</td>
                        <td class="px-4 py-2">{{ number_format($sum_paid,2) }}</td>
                        <td class="px-4 py-2 text-red-700">{{ number_format($sum_arrears,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="mb-6 text-right">
        <a href="{{ route('landlord.payments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded font-semibold">Log New Payment</a>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Tenant</th>
                    <th class="px-4 py-2">Property</th>
                    <th class="px-4 py-2">Unit</th>
                    <th class="px-4 py-2">Amount (KSh)</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Method</th>
                    <th class="px-4 py-2">Recorded By</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $payment->payment_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $payment->tenant->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $payment->property->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $payment->unit->unit_number ?? '-' }}</td>
                    <td class="px-4 py-2">{{ number_format($payment->amount,2) }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($payment->payment_type === 'rent') bg-green-100 text-green-800
                            @elseif($payment->payment_type === 'deposit') bg-blue-100 text-blue-800
                            @elseif($payment->payment_type === 'utility') bg-yellow-100 text-yellow-800
                            @elseif($payment->payment_type === 'maintenance') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($payment->payment_type ?? 'rent') }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $payment->payment_method ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $payment->recordedBy->name ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('landlord.payments.show', $payment) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-6 text-gray-500">No payments recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $payments->links() }}</div>
</div>
@endsection
