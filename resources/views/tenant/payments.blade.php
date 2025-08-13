<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Payments - Rental</title>
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
                <div class="flex items-center mb-8">
                    <i class="fas fa-building text-3xl mr-3"></i>
                    <h2 class="text-xl font-bold">Rental</h2>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('tenant.dashboard') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('tenant.payments.index') }}" class="flex items-center space-x-3 bg-white bg-opacity-20 text-white p-3 rounded-lg transition hover:bg-opacity-30">
                        <i class="fas fa-credit-card w-5"></i>
                        <span class="font-medium">My Payments</span>
                    </a>
                    <a href="{{ route('tenant.unit-details') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-home w-5"></i>
                        <span>Unit Details</span>
                    </a>
                    <a href="{{ route('tenant.messages.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-envelope w-5"></i>
                        <span>Messages</span>
                    </a>
                    <a href="{{ route('tenant.contact-landlord') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-phone w-5"></i>
                        <span>Contact Landlord</span>
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
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">My Payments</h2>
                        <p class="text-gray-600 mt-1">Track your rent payments and payment history</p>
                    </div>
                    <div class="flex items-center space-x-4 animate-fadeInUp">
                        <div class="flex items-center space-x-2 bg-white bg-opacity-50 px-4 py-2 rounded-full">
                            <i class="fas fa-user-circle text-2xl text-green-600"></i>
                            <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
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

            <!-- Payments Content -->
            <div class="p-8">
                <!-- Payment Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Total Paid -->
                    <div class="glass-card rounded-2xl shadow-2xl p-6 animate-fadeInUp">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Paid</p>
                                <p class="text-2xl font-bold text-green-600">KSh 0</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Outstanding Balance -->
                    <div class="glass-card rounded-2xl shadow-2xl p-6 animate-fadeInUp" style="animation-delay: 0.1s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Outstanding Balance</p>
                                <p class="text-2xl font-bold text-red-600">KSh 0</p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Next Payment Due -->
                    <div class="glass-card rounded-2xl shadow-2xl p-6 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Next Payment Due</p>
                                <p class="text-2xl font-bold text-blue-600">--</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Make Payment Section -->
                <div class="glass-card rounded-2xl shadow-2xl p-8 mb-8 animate-fadeInUp" style="animation-delay: 0.3s;">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-credit-card text-3xl text-blue-600 mr-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Make a Payment</h3>
                    </div>
                    
                    <form action="{{ route('tenant.make-payment') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Payment Amount (KSh)</label>
                                <input type="number" id="amount" name="amount" min="1" step="0.01" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter amount" required>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">M-Pesa Phone Number</label>
                                <input type="tel" id="phone" name="phone" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="254712345678" required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Payment Description (Optional)</label>
                            <input type="text" id="description" name="description" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Rent for January 2024">
                        </div>
                        
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-6 rounded-lg hover:from-blue-600 hover:to-blue-700 transition duration-200 shadow-lg font-medium">
                            <i class="fas fa-mobile-alt mr-2"></i>
                            Pay via M-Pesa
                        </button>
                    </form>
                </div>

                <!-- Payment History -->
                <div class="glass-card rounded-2xl shadow-2xl p-8 animate-fadeInUp" style="animation-delay: 0.4s;">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-history text-3xl text-purple-600 mr-4"></i>
                            <h3 class="text-2xl font-bold text-gray-800">Payment History</h3>
                        </div>
                        <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                    </div>
                    
                    <!-- Payment History Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Date</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Amount</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Method</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Reference</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Sample Payment Row (will be replaced with actual data) -->
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-4 text-gray-600">--</td>
                                    <td class="py-4 px-4 text-gray-600">--</td>
                                    <td class="py-4 px-4 text-gray-600">--</td>
                                    <td class="py-4 px-4 text-gray-600">--</td>
                                    <td class="py-4 px-4">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-600">
                                            No payments yet
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">--</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                        <h4 class="text-xl font-medium text-gray-600 mb-2">No Payment History</h4>
                        <p class="text-gray-500">Your payment history will appear here once you make your first payment.</p>
                    </div>
                </div>

                <!-- Payment Methods Info -->
                <div class="glass-card rounded-2xl shadow-2xl p-8 mt-8 animate-fadeInUp" style="animation-delay: 0.5s;">
                    <div class="flex items-center mb-6">
                        <i class="fas fa-info-circle text-3xl text-indigo-600 mr-4"></i>
                        <h3 class="text-2xl font-bold text-gray-800">Payment Information</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-700 mb-4">Accepted Payment Methods</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-mobile-alt text-green-600 mr-3"></i>
                                    <span class="text-gray-700">M-Pesa Mobile Money</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-blue-600 mr-3"></i>
                                    <span class="text-gray-700">USSD Banking</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-university text-purple-600 mr-3"></i>
                                    <span class="text-gray-700">Bank Transfer</span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-lg font-semibold text-gray-700 mb-4">Payment Guidelines</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li>• Rent is due on the 1st of every month</li>
                                <li>• Late payments may incur additional charges</li>
                                <li>• Keep your payment receipts for records</li>
                                <li>• Contact landlord for payment issues</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif
</body>
</html>
