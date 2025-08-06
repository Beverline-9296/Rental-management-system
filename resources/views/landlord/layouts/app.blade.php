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
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 gradient-bg text-white shadow-2xl sidebar">
                <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                    <div class="flex items-center flex-shrink-0 px-4">
                        <h2 class="text-lg font-semibold text-white">Landlord Panel</h2>
                    </div>
                    <div class="mt-5 flex-1 flex flex-col">
                        <nav class="flex-1 px-2 space-y-1">
                            <a href="{{ route('landlord.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('landlord.dashboard') ? 'bg-indigo-600 text-white' : 'text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10' }}">
                                <i class="fas fa-tachometer-alt mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('landlord.dashboard') ? 'text-white' : '' }}"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('landlord.properties.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('landlord.properties.*') ? 'bg-indigo-600 text-white' : 'text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10' }}">
                                <i class="fas fa-building mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('landlord.properties.*') ? 'text-white' : '' }}"></i>
                                Properties
                            </a>
                            <a href="{{ route('landlord.tenants.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('landlord.tenants.*') ? 'bg-indigo-600 text-white' : 'text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10' }}">
                                <i class="fas fa-users mr-3 text-gray-400 group-hover:text-gray-500 {{ request()->routeIs('landlord.tenants.*') ? 'text-white' : '' }}"></i>
                                Tenants
                            </a>
                            <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10">
                                <i class="fas fa-money-bill-wave mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                Payments
                            </a>
                            <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10">
                                <i class="fas fa-file-invoice-dollar mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                Invoices
                            </a>
                            <a href="#" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-blue-100 hover:text-white hover:bg-white hover:bg-opacity-10">
                                <i class="fas fa-chart-line mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                Reports
                            </a>
                        </nav>
                    </div>
                </div>
                <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                    <a href="#" class="flex-shrink-0 w-full group block">
                        <div class="flex items-center">
                            <div>
                                <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700">
                                    View profile
                                </p>
                            </div>
                        </div>
                    </a>
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
