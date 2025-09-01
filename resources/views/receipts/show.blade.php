@extends(auth()->user()->role === 'landlord' ? 'landlord.layouts.app' : 'tenant.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Receipt Details</h1>
            <div class="space-x-3">
                <a href="{{ route('receipts.download', $receipt) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                @if(auth()->user()->role === 'landlord' && $receipt->tenant->email)
                    <form action="{{ route('receipts.resend-email', $receipt) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-envelope mr-2"></i>Resend Email
                        </button>
                    </form>
                @endif
                <a href="{{ route('receipts.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Receipts
                </a>
            </div>
        </div>

        <!-- Receipt Content -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Payment Receipt</h2>
                        <p class="text-gray-600 mt-1">Receipt #{{ $receipt->receipt_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Receipt Date</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $receipt->receipt_date->format('M j, Y') }}</p>
                        <p class="text-sm text-gray-500">{{ $receipt->receipt_date->format('g:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Payment Details -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-semibold text-green-600 text-lg">KSh {{ number_format($receipt->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Type:</span>
                                <span class="font-medium">{{ ucfirst($receipt->payment_type) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Method:</span>
                                <span class="font-medium">{{ strtoupper($receipt->payment_method) }}</span>
                            </div>
                            @if($receipt->mpesa_receipt_number)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">M-Pesa Receipt:</span>
                                    <span class="font-medium text-green-600">{{ $receipt->mpesa_receipt_number }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Date:</span>
                                <span class="font-medium">{{ $receipt->payment->payment_date->format('M j, Y g:i A') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tenant & Property Details -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Details</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-gray-600 block">Tenant:</span>
                                <span class="font-medium">{{ $receipt->tenant->name }}</span>
                                @if($receipt->tenant->email)
                                    <br><span class="text-sm text-gray-500">{{ $receipt->tenant->email }}</span>
                                @endif
                                @if($receipt->tenant->phone)
                                    <br><span class="text-sm text-gray-500">{{ $receipt->tenant->phone }}</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-gray-600 block">Property:</span>
                                <span class="font-medium">{{ $receipt->property->name }}</span>
                                @if($receipt->property->address)
                                    <br><span class="text-sm text-gray-500">{{ $receipt->property->address }}</span>
                                @endif
                            </div>
                            @if($receipt->unit)
                                <div>
                                    <span class="text-gray-600 block">Unit:</span>
                                    <span class="font-medium">{{ $receipt->unit->unit_number }}</span>
                                </div>
                            @endif
                            <div>
                                <span class="text-gray-600 block">Landlord:</span>
                                <span class="font-medium">{{ $receipt->property->landlord->name }}</span>
                                @if($receipt->property->landlord->phone)
                                    <br><span class="text-sm text-gray-500">{{ $receipt->property->landlord->phone }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($receipt->description)
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes</h3>
                        <p class="text-gray-700">{{ $receipt->description }}</p>
                    </div>
                @endif

                <!-- Status -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Receipt Status</h3>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                                {{ $receipt->status === 'generated' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($receipt->status === 'sent' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($receipt->status) }}
                            </span>
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            <p>Generated: {{ $receipt->created_at->format('M j, Y g:i A') }}</p>
                            @if($receipt->updated_at != $receipt->created_at)
                                <p>Last Updated: {{ $receipt->updated_at->format('M j, Y g:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
