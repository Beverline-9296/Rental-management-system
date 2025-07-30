<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard - Rental</title>
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
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    <!-- Sidebar -->
    <div class="flex">
        <div class="w-64 gradient-bg text-white min-h-screen shadow-2xl">
            <div class="p-6">
                <div class="flex items-center mb-8">
                    <i class="fas fa-building text-3xl mr-3"></i>
                    <h2 class="text-xl font-bold">Rental</h2>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('landlord.dashboard') }}" class="flex items-center space-x-3 bg-white bg-opacity-20 text-white p-3 rounded-lg transition hover:bg-opacity-30">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('landlord.properties.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-building w-5"></i>
                        <span>Properties</span>
                    </a>
                    <a href="{{ route('landlord.tenants.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-users w-5"></i>
                        <span>Tenants</span>
                    </a>
                    <a href="{{ route('landlord.messages') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-envelope w-5"></i>
                        <span>Messages</span>
                    </a>
                    <a href="{{ route('landlord.payments') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>Payments</span>
                    </a>
                    <a href="{{ route('landlord.settings') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
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
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Welcome Back, Landlord</h2>
                        <p class="text-gray-600 mt-1">Hello, {{ $user->name }}! Here's your property overview</p>
                    </div>
                    <div class="flex items-center space-x-4 animate-fadeInUp">
                        <div class="flex items-center space-x-2 bg-white bg-opacity-50 px-4 py-2 rounded-full">
                            <i class="fas fa-user-circle text-2xl text-blue-600"></i>
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
                                <div class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-blue-600 bg-clip-text text-transparent">{{ $total_properties }}</div>
                                <div class="text-sm font-medium text-gray-600 mt-2">Total Properties</div>
                            </div>
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-xl">
                                <i class="fas fa-building text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold bg-gradient-to-r from-green-500 to-green-600 bg-clip-text text-transparent">{{ $total_tenants }}</div>
                                <div class="text-sm font-medium text-gray-600 mt-2">Total Tenants</div>
                            </div>
                            <div class="bg-gradient-to-r from-green-500 to-green-600 p-4 rounded-xl">
                                <i class="fas fa-users text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.3s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold bg-gradient-to-r from-orange-500 to-orange-600 bg-clip-text text-transparent">{{ $occupied_units }}</div>
                                <div class="text-sm font-medium text-gray-600 mt-2">Occupied Units</div>
                            </div>
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-4 rounded-xl">
                                <i class="fas fa-home text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.4s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-3xl font-bold bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">KSh {{ number_format($total_arrears) }}</div>
                                <div class="text-sm font-medium text-gray-600 mt-2">Total Arrears</div>
                            </div>
                            <div class="bg-gradient-to-r from-red-500 to-red-600 p-4 rounded-xl">
                                <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Activities -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border">
                            <div class="p-6 border-b">
                                <h3 class="text-lg font-semibold text-gray-800">RECENT ACTIVITIES</h3>
                            </div>
                            <div class="p-6">
                                @if(empty($recent_activities))
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-4"></i>
                                        <p>No recent activities</p>
                                    </div>
                                @else
                                    @foreach($recent_activities as $activity)
                                        <div class="flex items-center py-3 border-b last:border-b-0">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                                <i class="fas fa-bell text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-gray-800">{{ $activity['message'] }}</p>
                                                <p class="text-sm text-gray-500">{{ $activity['time'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Upcoming Payments -->
                        <div class="bg-white rounded-lg shadow-sm border mt-6">
                            <div class="p-6 border-b">
                                <h3 class="text-lg font-semibold text-gray-800">UPCOMING PAYMENTS</h3>
                            </div>
                            <div class="p-6">
                                @if(empty($upcoming_payments))
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-calendar text-4xl mb-4"></i>
                                        <p>No upcoming payments</p>
                                    </div>
                                @else
                                    @foreach($upcoming_payments as $payment)
                                        <div class="flex items-center justify-between py-3 border-b last:border-b-0">
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $payment['tenant'] }}</p>
                                                <p class="text-sm text-gray-500">Due: {{ $payment['due_date'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-green-600">KSh {{ number_format($payment['amount']) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
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
                            <button onclick="window.location='{{ route('landlord.properties.create') }}'" 
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200"> 
                                    <i class="fas fa-plus mr-2"></i>
                                ADD PROPERTY
                            </button>

                               
                                <button onclick="window.location='{{ route('landlord.tenants.create') }}'"
                                    class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-200">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    ADD TENANT
                                </button>
                                <button class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition duration-200">
                                    <i class="fas fa-money-bill mr-2"></i>
                                    RECORD PAYMENT
                                </button>
                                <button class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 transition duration-200">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    SEND PAYMENT REQUEST
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
