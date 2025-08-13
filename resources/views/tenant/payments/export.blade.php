@extends('tenant.layouts.app')

@section('title', 'Export Payment History')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-file-excel text-green-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Export Payment History</h1>
                    <p class="text-gray-600">Download your payment records as a CSV file (opens in Excel)</p>
                </div>
            </div>
        </div>

        <!-- Export Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('tenant.payments.export-excel') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Date Range Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            Start Date (Optional)
                        </label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to include all records</p>
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            End Date (Optional)
                        </label>
                        <input type="date" 
                               id="end_date" 
                               name="end_date" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to include all records</p>
                    </div>
                </div>

                <!-- Quick Date Presets -->
                <div class="border-t pt-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Quick Date Ranges</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <button type="button" 
                                onclick="setDateRange('last_month')" 
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Last Month
                        </button>
                        <button type="button" 
                                onclick="setDateRange('last_3_months')" 
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Last 3 Months
                        </button>
                        <button type="button" 
                                onclick="setDateRange('last_6_months')" 
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Last 6 Months
                        </button>
                        <button type="button" 
                                onclick="setDateRange('this_year')" 
                                class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            This Year
                        </button>
                    </div>
                </div>

                <!-- Export Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800 mb-1">Export Information</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Excel file will include all payment details</li>
                                <li>• File will be formatted with headers and styling</li>
                                <li>• Includes M-Pesa receipt numbers and transaction details</li>
                                <li>• File name will include your name and export date</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Download CSV File
                    </button>
                    
                    <a href="{{ route('tenant.payments.index') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Payments
                    </a>
                </div>
            </form>
        </div>

        <!-- Sample Preview -->
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-eye mr-2"></i>
                Excel File Preview
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-indigo-600 text-white">
                        <tr>
                            <th class="px-3 py-2 text-left">Payment Date</th>
                            <th class="px-3 py-2 text-left">Property</th>
                            <th class="px-3 py-2 text-left">Unit</th>
                            <th class="px-3 py-2 text-left">Amount (KES)</th>
                            <th class="px-3 py-2 text-left">Method</th>
                            <th class="px-3 py-2 text-left">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2">2025-08-13</td>
                            <td class="px-3 py-2">Caroline Apartment</td>
                            <td class="px-3 py-2">5</td>
                            <td class="px-3 py-2">10,000.00</td>
                            <td class="px-3 py-2">M-Pesa</td>
                            <td class="px-3 py-2">AUTO1723546318...</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-2">2025-08-13</td>
                            <td class="px-3 py-2">Caroline Apartment</td>
                            <td class="px-3 py-2">5</td>
                            <td class="px-3 py-2">999.00</td>
                            <td class="px-3 py-2">M-Pesa</td>
                            <td class="px-3 py-2">AUTO1723546319...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-500 mt-2">* This is a preview of how your Excel file will look</p>
        </div>
    </div>
</div>

<script>
function setDateRange(range) {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const today = new Date();
    
    let start, end;
    
    switch(range) {
        case 'last_month':
            start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            end = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'last_3_months':
            start = new Date(today.getFullYear(), today.getMonth() - 3, 1);
            end = today;
            break;
        case 'last_6_months':
            start = new Date(today.getFullYear(), today.getMonth() - 6, 1);
            end = today;
            break;
        case 'this_year':
            start = new Date(today.getFullYear(), 0, 1);
            end = today;
            break;
    }
    
    startDate.value = start.toISOString().split('T')[0];
    endDate.value = end.toISOString().split('T')[0];
}

// Set max date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').max = today;
    document.getElementById('end_date').max = today;
});
</script>
@endsection
