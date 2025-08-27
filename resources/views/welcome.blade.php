<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Rental - Rental Management Platform</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            @keyframes fadeInUp {
                from { 
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes slideInLeft {
                from {
                    opacity: 0;
                    transform: translateX(-50px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(50px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
            }
            
            @keyframes float {
                0%, 100% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-10px);
                }
            }
            
            .animate-fadeInUp {
                animation: fadeInUp 0.8s ease-out;
            }
            
            .animate-slideInLeft {
                animation: slideInLeft 0.8s ease-out;
            }
            
            .animate-slideInRight {
                animation: slideInRight 0.8s ease-out;
            }
            
            .animate-pulse-slow {
                animation: pulse 3s ease-in-out infinite;
            }
            
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            
            .bg-buildings {
                background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }
        </style>
    </head>
    <body class="bg-buildings min-h-screen">
        <!-- Navigation Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-8">
                        <div class="flex-shrink-0">
                            <div class="flex items-center">
                                <img src="{{ asset('storage/properties/Screenshot 2025-08-22 070351.png') }}" alt="image" class="w-10 h-10 object-cover rounded-full shadow-md">
                            </div></div>
                        <div class="flex-shrink-0">
                            <h1 class="text-2xl font-bold text-gray-900">Rental</h1>
                        </div>
                        <nav class="hidden md:flex items-center space-x-6">
                            <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">Home</a>
                            <a href="{{ url('about') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">About</a>
                            <a href="{{ url('contact') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">Contact</a>
                        </nav>
                    </div>
                    @auth
                        <nav class="flex items-center space-x-4">
                            <a href="{{ route('landlord.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                                Dashboard
                            </a>
                        </nav>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-16">
                <div class="mb-8 animate-float">
                    <i class="fas fa-home text-6xl text-white mb-4 drop-shadow-lg"></i>
                </div>
                <h1 class="text-5xl font-extrabold text-white mb-6 animate-fadeInUp drop-shadow-lg">
                    Welcome to <span class="text-blue-300">Rental</span>
                </h1>
                <p class="text-xl text-gray-200 mb-8 max-w-3xl mx-auto animate-fadeInUp drop-shadow-md" style="animation-delay: 0.2s;">
                    Your comprehensive rental management platform. Streamline property management, tenant relations, and payment processing with our modern, integrated solution.
                </p>
                
                <!-- Login Button -->
                <div class="flex justify-center mb-12 animate-fadeInUp" style="animation-delay: 0.4s;">
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-700 transition duration-200 shadow-lg animate-pulse-slow hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login to System
                    </a>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <!-- Property Management -->
                <div class="bg-white bg-opacity-95 rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300 animate-slideInLeft" style="animation-delay: 0.1s;">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-building text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Property Management</h3>
                        <p class="text-gray-600 mb-4">Efficiently manage multiple properties, track occupancy, and monitor property performance.</p>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Add & manage properties</li>
                            <li>• Track occupied units</li>
                            <li>• Monitor property status</li>
                        </ul>
                    </div>
                </div>

                <!-- Tenant Management -->
                <div class="bg-white bg-opacity-95 rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-users text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Tenant Management</h3>
                        <p class="text-gray-600 mb-4">Streamline tenant registration, assignment, and communication processes.</p>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Register new tenants</li>
                            <li>• Assign to properties</li>
                            <li>• Track tenant details</li>
                        </ul>
                    </div>
                </div>

                <!-- Payment Processing -->
                <div class="bg-white bg-opacity-95 rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300 animate-slideInRight" style="animation-delay: 0.3s;">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-credit-card text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Payment Processing</h3>
                        <p class="text-gray-600 mb-4">Integrated M-Pesa STK Push and USSD for seamless rent collection.</p>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• M-Pesa integration</li>
                            <li>• USSD payments</li>
                            <li>• Automated receipts</li>
                        </ul>
                    </div>
                </div>

                <!-- Financial Tracking -->
                <div class="bg-white bg-opacity-95 rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300 animate-slideInLeft" style="animation-delay: 0.4s;">
                    <div class="text-center">
                        <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-chart-line text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Financial Tracking</h3>
                        <p class="text-gray-600 mb-4">Monitor rent payments, track arrears, and generate financial reports.</p>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Payment tracking</li>
                            <li>• Arrears management</li>
                            <li>• Financial reports</li>
                        </ul>
                    </div>
                </div>

                <!-- Communication Hub -->
                <div class="bg-white bg-opacity-95 rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.5s;">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-envelope text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Communication Hub</h3>
                        <p class="text-gray-600 mb-4">Facilitate seamless communication between landlords and tenants.</p>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Messaging system</li>
                            <li>• Payment requests</li>
                            <li>• Notifications</li>
                        </ul>
                    </div>
                </div>

                <!-- Mobile Responsive -->
                <div class="bg-white bg-opacity-95 rounded-xl shadow-lg p-8 hover:shadow-xl transition duration-300 animate-slideInRight" style="animation-delay: 0.6s;">
                    <div class="text-center">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-mobile-alt text-2xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Mobile Responsive</h3>
                        <p class="text-gray-600 mb-4">Access your rental management system from any device, anywhere.</p>
                        <ul class="text-sm text-gray-500 space-y-1">
                            <li>• Mobile-first design</li>
                            <li>• Cross-platform access</li>
                            <li>• Real-time updates</li>
                        </ul>
                    </div>
                </div>
            </div>




        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-8 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex items-center justify-center mb-4">
                    <i class="fas fa-building text-2xl text-blue-400 mr-3"></i>
                    <h3 class="text-xl font-bold">Rental</h3>
                </div>
                <p class="text-gray-400 mb-4">Revolutionizing rental management with modern technology</p>
                <div class="flex justify-center space-x-6 text-sm text-gray-400">
                    <span>• M-Pesa Integration</span>
                    <span>• USSD Support</span>
                    <span>• Real-time Updates</span>
                    <span>• Mobile Responsive</span>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} Rental. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
