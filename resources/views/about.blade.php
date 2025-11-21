<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>About - Rental Management Platform</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
            :root {
                --bg-primary: #ffffff;
                --bg-secondary: #f8fafc;
                --bg-card: rgba(255, 255, 255, 0.95);
                --text-primary: #1f2937;
                --text-secondary: #6b7280;
                --text-muted: #9ca3af;
                --border-color: #e5e7eb;
            }

            [data-theme="dark"] {
                --bg-primary: #0f172a;
                --bg-secondary: #1e293b;
                --bg-card: rgba(30, 41, 59, 0.95);
                --text-primary: #f1f5f9;
                --text-secondary: #cbd5e1;
                --text-muted: #94a3b8;
                --border-color: #334155;
            }

            body {
                background-color: var(--bg-primary);
                color: var(--text-primary);
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            .header-bg {
                background-color: var(--bg-card);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--border-color);
            }

            .content-card {
                background-color: var(--bg-card);
                color: var(--text-primary);
                border: 1px solid var(--border-color);
            }

            .text-gray-700 {
                color: var(--text-secondary) !important;
            }

            .text-gray-600 {
                color: var(--text-muted) !important;
            }

            .bg-blue-50, .bg-green-50 {
                background-color: var(--bg-secondary) !important;
            }

            [data-theme="dark"] .bg-blue-50 {
                background-color: rgba(59, 130, 246, 0.1) !important;
            }

            [data-theme="dark"] .bg-green-50 {
                background-color: rgba(16, 185, 129, 0.1) !important;
            }

            h1{
                color:#008080;
             }

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
    <body data-theme="light">
        <!-- Navigation Header -->
        <header class="header-bg shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-8">
                        <div class="flex-shrink-0">
                            <div class="flex items-center">
                                <img src="{{ asset('storage/properties/Screenshot 2025-08-22 070351.png') }}" alt="image" class="w-16 h-16 object-cover rounded-full shadow-md">
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <h1 class="text-2xl font-bold" style="color: var(--text-primary);">Rental</h1>
                        </div>
                        <nav class="hidden md:flex items-center space-x-6">
                            <a href="{{ url('/') }}" class="hover:text-blue-600 px-3 py-2 text-sm font-medium" style="color: var(--text-secondary);">Home</a>
                            <a href="{{ url('about') }}" class="hover:text-blue-600 px-3 py-2 text-sm font-medium" style="color: var(--text-secondary);">About</a>
                            <a href="{{ url('contact') }}" class="hover:text-blue-600 px-3 py-2 text-sm font-medium" style="color: var(--text-secondary);">Contact</a>
                        </nav>
                    </div>
                    
                    <!-- Dark Mode Toggle -->
                    <div class="flex items-center space-x-4">
                        <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg id="theme-toggle-light-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path>
                            </svg>
                            <svg id="theme-toggle-dark-icon" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="content-card rounded-xl shadow-lg p-8">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">About Rental Management Platform</h1>
                
                <div class="prose max-w-none">
                    <p class="text-lg text-gray-700 mb-6">
                        Welcome to Rental, your comprehensive solution for property and rental management. 
                        Our platform is designed to simplify the rental process for both landlords and tenants, 
                        providing a seamless experience from property listing to rent collection.
                    </p>
                    
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Our Mission</h2>
                    <p class="text-gray-700 mb-6">
                        To revolutionize the rental industry by providing an intuitive, efficient, and transparent 
                        platform that connects landlords with reliable tenants and simplifies property management.
                    </p>
                    
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Features</h2>
                    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-6">
                        <li>Easy property listing and management</li>
                        <li>Tenant screening and management</li>
                        <li>Online rent collection and payment tracking</li>
                        <li>Maintenance request system</li>
                        <li>Document storage and management</li>
                        <li>Real-time communication between landlords and tenants</li>
                    </ul>
                    
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Why Choose Us?</h2>
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-blue-700 mb-3">For Landlords</h3>
                            <p class="text-gray-700">
                                Streamline your property management, reduce vacancies, and get paid on time with our 
                                comprehensive suite of landlord tools.
                            </p>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-xl font-semibold text-green-700 mb-3">For Tenants</h3>
                            <p class="text-gray-700">
                                Find your perfect rental, pay rent online, and submit maintenance requests 
                                all in one convenient platform.
                            </p>
                        </div>
                    </div>
                     <!-- Dashboard Preview -->
            <div class="content-card rounded-xl shadow-lg p-8 mb-16">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Role-Based Dashboards</h2>
                    <p class="text-gray-600">Tailored experiences for landlords and tenants</p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Landlord Dashboard Preview -->
                    <div class="border-2 border-blue-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-blue-600 mb-4 flex items-center">
                            <i class="fas fa-user-tie mr-2"></i>
                            Landlord Dashboard
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                                <span>Total Properties</span>
                                <span class="font-bold text-blue-600">5</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                <span>Total Tenants</span>
                                <span class="font-bold text-green-600">12</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                                <span>Occupied Units</span>
                                <span class="font-bold text-orange-600">10</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-purple-50 rounded">
                                <span>Total Arrears</span>
                                <span class="font-bold text-purple-600">KSh 45,000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tenant Dashboard Preview -->
                    <div class="border-2 border-green-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-green-600 mb-4 flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            Tenant Dashboard
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                                <span>Monthly Rent</span>
                                <span class="font-bold text-blue-600">KSh 25,000</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                <span>Property</span>
                                <span class="font-bold text-green-600">Sunrise Apartments</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-orange-50 rounded">
                                <span>Unit Number</span>
                                <span class="font-bold text-orange-600">A-204</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-red-50 rounded">
                                <span>Balance</span>
                                <span class="font-bold text-red-600">KSh 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                </div>
            </div>
        </main>

        @include('partials.footer')

        <script>
            // Dark mode toggle functionality
            const themeToggle = document.getElementById('theme-toggle');
            const body = document.body;
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const darkIcon = document.getElementById('theme-toggle-dark-icon');

            // Check for saved theme preference or default to 'light'
            const currentTheme = localStorage.getItem('theme') || 'light';
            body.setAttribute('data-theme', currentTheme);

            // Update toggle state based on current theme
            if (currentTheme === 'dark') {
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            }

            // Theme toggle event listener
            themeToggle.addEventListener('click', function() {
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                // Update toggle state
                if (newTheme === 'dark') {
                    lightIcon.classList.add('hidden');
                    darkIcon.classList.remove('hidden');
                } else {
                    lightIcon.classList.remove('hidden');
                    darkIcon.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
