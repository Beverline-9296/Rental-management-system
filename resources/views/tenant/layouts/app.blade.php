<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tenant Dashboard - Rental')</title>
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
    @stack('styles')
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
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-h-screen">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
