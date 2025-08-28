<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Landlord Dashboard - Astra Spaces')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Gradient background used across dashboard */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        /* Frosted glass card */
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        /* Animations */
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

        .sidebar {
            min-height: calc(100vh - 64px);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">

    <div class="flex">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0 min-h-screen">
            <div class="flex flex-col w-64 gradient-bg text-white shadow-2xl sidebar">
                <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4 space-x-2">
                        <img src="{{ asset('storage/properties/Screenshot 2025-08-22 070351.png') }}" alt="image" class="w-10 h-10 object-cover rounded-full shadow-md">
                        <h2 class="text-lg font-semibold text-white">Landlord Panel</h2>
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
                    <a href="{{ route('landlord.maintenance.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-tools w-5"></i>
                        <span>Maintenance</span>
                    </a>
                    <a href="{{ route('landlord.messages.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-envelope w-5"></i>
                        <span>Messages</span>
                    </a>
                    <a href="{{ route('landlord.payments.index') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>Payments</span>
                    </a>
                    <a href="{{ route('landlord.profile.edit') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-user-cog w-5"></i>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route('landlord.settings') }}" class="flex items-center space-x-3 text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10 p-3 rounded-lg transition">
                        <i class="fas fa-cog w-5"></i>
                        <span>Settings</span>
                    </a>
                </nav>
                </div>
                
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 overflow-auto">
            <!-- Page heading -->
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold text-gray-900">
                            @yield('header')
                        </h1>
                        <div class="ml-4">
                            @yield('header-actions')
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @if(session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            if (window.Swal) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: @json(session('success')),
                                    confirmButtonColor: '#3085d6',
                                });
                            } else {
                                console.warn('SweetAlert2 not loaded – success popup skipped');
                            }
                        });
                    </script>
                @endif

                @if($errors->any())
                    <div class="rounded-md bg-red-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="h-5 w-5 text-red-400 fas fa-exclamation-circle"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There {{ $errors->count() > 1 ? 'were ' . $errors->count() . ' errors' : 'was 1 error' }} with your submission
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle mobile menu
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileMenuButton = document.querySelector('button[aria-controls="mobile-menu"]');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
            
            // User dropdown toggle
            const userMenuButton = document.getElementById('user-menu');
            const userMenu = userMenuButton?.nextElementSibling;
            
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function() {
                    userMenu.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
