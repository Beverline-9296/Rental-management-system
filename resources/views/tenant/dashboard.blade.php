<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard - Rental</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }
        .animate-slideInLeft { animation: slideInLeft 0.6s ease-out; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-navy-900 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 gradient-bg text-white min-h-screen shadow-2xl">
            <div class="p-6">
                <div class="flex items-center mb-8 space-x-2">
                    <img src="{{ asset('storage/properties/Screenshot 2025-08-22 070351.png') }}" alt="image" class="w-10 h-10 object-cover rounded-full shadow-md">
                    <h2 class="text-xl font-bold">Rental</h2>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('tenant.dashboard') }}" class="flex items-center space-x-3 bg-white bg-opacity-20 text-white p-3 rounded-lg transition hover:bg-opacity-30">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('tenant.unit-details') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-home w-5"></i>
                        <span>Unit Details</span>
                    </a>
                    <a href="{{ route('tenant.maintenance.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-tools w-5"></i>
                        <span>Maintenance</span>
                    </a>
                    <a href="{{ route('tenant.messages.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-envelope w-5"></i>
                        <span>Messages</span>
                    </a>
                    <a href="{{ route('tenant.payments.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>My Payments Records</span>
                    </a>
                    <a href="{{ route('tenant.payments.make') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>Make Payment</span>
                    </a>
                    <a href="{{ route('receipts.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-receipt w-5"></i>
                        <span>My Receipts</span>
                    </a>
                    <a href="{{ route('tenant.contact-landlord') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-phone w-5"></i>
                        <span>Contact Landlord</span>
                    </a>
                    <a href="{{ route('tenant.profile.edit') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-user-cog w-5"></i>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route('tenant.settings') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-cog w-5"></i>
                        <span>Settings</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition w-full">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-h-screen">
            <!-- Header -->
            <header class="glass-card shadow-lg border-b border-white border-opacity-20">
                <div class="flex items-center justify-between px-8 py-6">
                    <div class="animate-slideInLeft">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">Welcome Back, Tenant</h2>
                        <p class="text-gray-600 mt-1">Hello, {{ $user->name }}! Here's your rental overview</p>
                    </div>
                    <div class="flex items-center space-x-4 animate-fadeInUp">
                        <div class="flex items-center space-x-2 bg-white bg-opacity-50 px-4 py-2 rounded-full">
                            <i class="fas fa-user-circle text-2xl text-green-600"></i>
                            <span class="font-medium text-gray-700">{{ $user->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-full hover:from-red-600 hover:to-red-700 transition duration-200 shadow-lg">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="p-8 overflow-y-auto">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.1s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-blue-600 bg-clip-text text-transparent">KSh {{ number_format($rental_summary['rent_amount']) }}</div>
                                <div class="text-sm font-medium text-gray-600 mt-2">Monthly Rent</div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-xl">
                                <i class="fas fa-home text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold bg-gradient-to-r from-green-500 to-green-600 bg-clip-text text-transparent">KSh {{ number_format($rental_summary['balance']) }}</div>
                                <div class="text-sm font-medium text-gray-600 mt-2">Account Balance</div>
                            </div>
                            <div class="bg-gradient-to-r from-green-500 to-green-600 p-4 rounded-xl">
                                <i class="fas fa-wallet text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.3s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-purple-600 bg-clip-text text-transparent">0</div>
                                <div class="text-sm font-medium text-gray-600 mt-2">New Messages</div>
                            </div>
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4 rounded-xl">
                                <i class="fas fa-envelope text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.4s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">KSh {{ number_format($arrears, 2) }}</div>
                                <div class="text-sm font-medium text-gray-600 mt-2">Outstanding Arrears</div>
                            </div>
                            <div class="bg-gradient-to-r from-red-500 to-red-600 p-4 rounded-xl">
                                <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Recent Activities -->
                    <div class="lg:col-span-2">
                        <div class="glass-card rounded-2xl shadow-xl animate-fadeInUp" style="animation-delay: 0.5s;">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent flex items-center">
                                    <i class="fas fa-clock mr-3 text-blue-600"></i>
                                    Recent Activities
                                </h3>
                            </div>
                            <div class="p-6">
                                @if(empty($recent_activities))
                                    <div class="text-center py-12 text-gray-500">
                                        <div class="bg-gradient-to-r from-blue-100 to-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-inbox text-3xl text-blue-600"></i>
                                        </div>
                                        <p class="text-lg font-medium mb-2">No Recent Activities</p>
                                        <p class="text-sm text-gray-400">Your activity history will appear here</p>
                                    </div>
                                @else
                                    @foreach($recent_activities as $activity)
                                        <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 rounded-lg px-4 transition duration-200">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center mr-4">
                                                    <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}-600"></i>
                                                </div>
                                                <div>
                                                    <span class="text-gray-700 font-medium block">{{ $activity['description'] }}</span>
                                                    <span class="text-xs text-gray-500">{{ $activity['date'] }}</span>
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ $activity['time'] }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Upcoming Payment -->
                        <div class="bg-white rounded-lg shadow-sm border mt-6">
                            <div class="p-6 border-b">
                                <h3 class="text-lg font-semibold text-gray-800">UPCOMING PAYMENT</h3>
                            </div>
                            <div class="p-6">
                                @if(!$upcoming_payment)
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-calendar-check text-4xl mb-4"></i>
                                        <p>No upcoming payments</p>
                                    </div>
                                @else
                                    @php
                                        $bgColor = 'bg-yellow-50 border-yellow-200';
                                        $textColor = 'text-yellow-800';
                                        $amountColor = 'text-yellow-600';
                                        $statusText = 'Due Soon';
                                        
                                        if ($upcoming_payment['urgency'] === 'overdue') {
                                            $bgColor = 'bg-red-50 border-red-200';
                                            $textColor = 'text-red-800';
                                            $amountColor = 'text-red-600';
                                            $statusText = 'Overdue';
                                        } elseif ($upcoming_payment['urgency'] === 'high') {
                                            $bgColor = 'bg-orange-50 border-orange-200';
                                            $textColor = 'text-orange-800';
                                            $amountColor = 'text-orange-600';
                                            $statusText = 'Due Very Soon';
                                        } elseif ($upcoming_payment['urgency'] === 'medium') {
                                            $bgColor = 'bg-yellow-50 border-yellow-200';
                                            $textColor = 'text-yellow-800';
                                            $amountColor = 'text-yellow-600';
                                            $statusText = 'Due Soon';
                                        } else {
                                            $bgColor = 'bg-blue-50 border-blue-200';
                                            $textColor = 'text-blue-800';
                                            $amountColor = 'text-blue-600';
                                            $statusText = 'Upcoming';
                                        }
                                    @endphp
                                    
                                    <div class="border rounded-lg p-4 {{ $bgColor }}">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-1">
                                                    <h4 class="font-semibold {{ $textColor }} mr-2">Rent Payment</h4>
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $bgColor }} {{ $textColor }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </div>
                                                <div class="text-sm text-gray-600 space-y-1">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-building text-gray-400 w-4 mr-2"></i>
                                                        <span>{{ $upcoming_payment['property_name'] }} - Unit {{ $upcoming_payment['unit_number'] }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-calendar text-gray-400 w-4 mr-2"></i>
                                                        <span>Due: {{ \Carbon\Carbon::parse($upcoming_payment['due_date'])->format('M j, Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <div class="text-lg font-bold {{ $amountColor }} mb-1">
                                                    KSh {{ number_format($upcoming_payment['amount'], 0) }}
                                                </div>
                                                <div class="text-sm font-medium {{ $textColor }}">
                                                    @if($upcoming_payment['days_remaining'] < 0)
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        {{ abs($upcoming_payment['days_remaining']) }} days overdue
                                                    @elseif($upcoming_payment['days_remaining'] == 0)
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Due Today
                                                    @elseif($upcoming_payment['days_remaining'] == 1)
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Due Tomorrow
                                                    @else
                                                        <i class="fas fa-calendar mr-1"></i>
                                                        {{ $upcoming_payment['days_remaining'] }} days left
                                                    @endif
                                                </div>
                                                <a href="{{ route('tenant.payments.make') }}" 
                                                   class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition-colors">
                                                    Make Payment
                                                </a>
                                            </div>
                                        </div>
                                        
                                        @if($upcoming_payment['days_remaining'] <= 7)
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="text-gray-500">Payment Progress</span>
                                                    <span class="font-medium {{ $textColor }}">
                                                        {{ $upcoming_payment['days_remaining'] <= 0 ? 'Overdue' : $upcoming_payment['days_remaining'] . ' days remaining' }}
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                    @php
                                                        $progress = $upcoming_payment['days_remaining'] <= 0 ? 100 : 
                                                                   (30 - $upcoming_payment['days_remaining']) / 30 * 100;
                                                        $progressColor = $upcoming_payment['days_remaining'] <= 0 ? 'bg-red-500' : 
                                                                        ($upcoming_payment['days_remaining'] <= 3 ? 'bg-orange-500' : 'bg-blue-500');
                                                    @endphp
                                                    <div class="h-2 rounded-full {{ $progressColor }}" 
                                                         style="width: {{ min(100, max(0, $progress)) }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions Sidebar -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="p-6 border-b">
                                <h3 class="text-lg font-semibold text-gray-800">ACTIONS</h3>
                            </div>
                            <div class="p-6 space-y-4">
                            <a href="{{ route('tenant.payments.make') }}" 
                                class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-mobile-alt mr-2"></i>
                                    MAKE PAYMENT
                            </a>
                                
                                
                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Quick Info</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Property:</span>
                                            <span class="font-medium">{{ $unit_details['property_name'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Unit:</span>
                                            <span class="font-medium">{{ $unit_details['unit_number'] }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Monthly Rent:</span>
                                            <span class="font-medium text-green-600">KSh {{ number_format($unit_details['rent_amount']) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Balance:</span>
                                            <span class="font-medium text-blue-600">KSh {{ number_format($rental_summary['balance']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History Preview -->
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="p-6 border-b">
                                <h3 class="text-lg font-semibold text-gray-800">RECENT PAYMENTS</h3>
                            </div>
                            <div class="p-6">
                                @if(isset($recent_payments) && $recent_payments->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($recent_payments as $payment)
                                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-credit-card text-green-600 text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">{{ ucfirst($payment->payment_type) }} Payment</p>
                                                        <p class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</p>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-semibold text-green-600">+KSh {{ number_format($payment->amount) }}</p>
                                                    <p class="text-xs text-gray-500">{{ ucfirst($payment->payment_method) }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('tenant.payments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All Payments</a>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-gray-500">
                                        <i class="fas fa-history text-2xl mb-2"></i>
                                        <p class="text-sm">No payment history</p>
                                        <a href="{{ route('tenant.payments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All Payments</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Requests Section -->
                @if(!empty($payment_requests) && count($payment_requests) > 0)
                    <div class="mb-8">
                        <div class="glass-card rounded-2xl shadow-xl animate-fadeInUp" style="animation-delay: 0.3s;">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-xl font-bold bg-gradient-to-r from-red-600 to-orange-600 bg-clip-text text-transparent flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>
                                    Payment Requests
                                </h3>
                            </div>
                            <div class="p-6">
                                @foreach($payment_requests as $request)
                                    <div class="border rounded-lg p-4 mb-4 last:mb-0 {{ $request->status === 'sent' ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-2">
                                                    <h4 class="font-semibold text-gray-900 mr-2">Payment Request from {{ $request->landlord->name }}</h4>
                                                    @if($request->status === 'sent')
                                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                            New
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-600 space-y-1">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-building text-gray-400 w-4 mr-2"></i>
                                                        <span>{{ $request->property->name }} - Unit {{ $request->unit->unit_number }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-calendar text-gray-400 w-4 mr-2"></i>
                                                        <span>Sent: {{ $request->created_at->format('M d, Y \a\t g:i A') }}</span>
                                                    </div>
                                                    @if($request->custom_message)
                                                        <div class="flex items-start mt-2">
                                                            <i class="fas fa-comment text-gray-400 w-4 mr-2 mt-0.5"></i>
                                                            <span class="italic">"{{ $request->custom_message }}"</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <div class="text-lg font-bold text-red-600 mb-1">
                                                    KSh {{ number_format($request->amount, 0) }}
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    {{ ucfirst($request->message_type) }}
                                                </div>
                                                <a href="{{ route('tenant.payments.make') }}" 
                                                   class="inline-block mt-2 bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded transition-colors">
                                                    Make Payment
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.fixed.top-4').remove();
            }, 5000);
        </script>
    @endif
</body>
</html>
