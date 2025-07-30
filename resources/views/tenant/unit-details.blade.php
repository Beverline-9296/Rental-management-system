<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Details - Rental</title>
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
                    <a href="{{ route('tenant.payments') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>My Payments</span>
                    </a>
                    <a href="{{ route('tenant.unit-details') }}" class="flex items-center space-x-3 bg-white bg-opacity-20 text-white p-3 rounded-lg transition hover:bg-opacity-30">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-medium">Unit Details</span>
                    </a>
                    <a href="{{ route('tenant.messages') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
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
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">Unit Details</h2>
                        <p class="text-gray-600 mt-1">View your rental unit information</p>
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

            <!-- Unit Details Content -->
            <div class="p-8">
                @if($assignment && $unit && $property)
                    <!-- Unit Information Card -->
                    <div class="glass-card rounded-2xl shadow-2xl p-8 mb-8 animate-fadeInUp">
                        <div class="flex items-center mb-6">
                            <i class="fas fa-home text-3xl text-blue-600 mr-4"></i>
                            <h3 class="text-2xl font-bold text-gray-800">Your Rental Unit</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Property Information -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-700 border-b border-gray-200 pb-2">Property Information</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Property Name:</span>
                                        <span class="font-medium text-gray-800">{{ $property->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Address:</span>
                                        <span class="font-medium text-gray-800">{{ $property->address }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Property Type:</span>
                                        <span class="font-medium text-gray-800 capitalize">{{ $property->type }}</span>
                                    </div>
                                    @if($property->description)
                                    <div class="mt-4">
                                        <span class="text-gray-600">Description:</span>
                                        <p class="text-gray-800 mt-1">{{ $property->description }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Unit Information -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-700 border-b border-gray-200 pb-2">Unit Information</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Unit Number:</span>
                                        <span class="font-medium text-gray-800">{{ $unit->unit_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Unit Type:</span>
                                        <span class="font-medium text-gray-800 capitalize">{{ $unit->type }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Monthly Rent:</span>
                                        <span class="font-bold text-green-600 text-lg">KSh {{ number_format($unit->rent_amount) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                                            {{ $unit->status === 'occupied' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($unit->status) }}
                                        </span>
                                    </div>
                                    @if($unit->description)
                                    <div class="mt-4">
                                        <span class="text-gray-600">Unit Description:</span>
                                        <p class="text-gray-800 mt-1">{{ $unit->description }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lease Information Card -->
                    <div class="glass-card rounded-2xl shadow-2xl p-8 mb-8 animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="flex items-center mb-6">
                            <i class="fas fa-file-contract text-3xl text-purple-600 mr-4"></i>
                            <h3 class="text-2xl font-bold text-gray-800">Lease Agreement</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Lease Start Date:</span>
                                    <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($assignment->lease_start_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Lease End Date:</span>
                                    <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($assignment->lease_end_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Lease Duration:</span>
                                    <span class="font-medium text-gray-800">
                                        {{ \Carbon\Carbon::parse($assignment->lease_start_date)->diffInMonths(\Carbon\Carbon::parse($assignment->lease_end_date)) }} months
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Security Deposit:</span>
                                    <span class="font-medium text-gray-800">KSh {{ number_format($assignment->security_deposit) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Assignment Status:</span>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                                        {{ $assignment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time Remaining:</span>
                                    <span class="font-medium text-gray-800">
                                        {{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($assignment->lease_end_date)) }} days
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="glass-card rounded-2xl shadow-2xl p-8 animate-fadeInUp" style="animation-delay: 0.4s;">
                        <div class="flex items-center mb-6">
                            <i class="fas fa-bolt text-3xl text-yellow-600 mr-4"></i>
                            <h3 class="text-2xl font-bold text-gray-800">Quick Actions</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('tenant.payments') }}" class="flex items-center justify-center p-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition duration-200 shadow-lg">
                                <i class="fas fa-credit-card mr-3"></i>
                                <span class="font-medium">Make Payment</span>
                            </a>
                            <a href="{{ route('tenant.contact-landlord') }}" class="flex items-center justify-center p-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition duration-200 shadow-lg">
                                <i class="fas fa-phone mr-3"></i>
                                <span class="font-medium">Contact Landlord</span>
                            </a>
                            <a href="{{ route('tenant.messages') }}" class="flex items-center justify-center p-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition duration-200 shadow-lg">
                                <i class="fas fa-envelope mr-3"></i>
                                <span class="font-medium">Messages</span>
                            </a>
                        </div>
                    </div>

                @else
                    <!-- No Assignment Found -->
                    <div class="glass-card rounded-2xl shadow-2xl p-12 text-center animate-fadeInUp">
                        <div class="mb-6">
                            <i class="fas fa-home text-6xl text-gray-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">No Unit Assigned</h3>
                        <p class="text-gray-600 mb-8">You are not currently assigned to any rental unit. Please contact your landlord for assistance.</p>
                        <a href="{{ route('tenant.contact-landlord') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition duration-200 shadow-lg">
                            <i class="fas fa-phone mr-2"></i>
                            Contact Landlord
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
