<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login - Rental</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="{{ asset('css/dark-mode.css') }}" rel="stylesheet">
        <style>
            :root {
                --bg-primary: #ffffff;
                --bg-secondary: #f8fafc;
                --bg-card: rgba(255, 255, 255, 0.95);
                --text-primary: #1f2937;
                --text-secondary: #6b7280;
                --text-muted: #9ca3af;
                --border-color: #e5e7eb;
                --gradient-overlay: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4));
            }

            [data-theme="dark"] {
                --bg-primary: #0f172a;
                --bg-secondary: #1e293b;
                --bg-card: rgba(30, 41, 59, 0.95);
                --text-primary: #f1f5f9;
                --text-secondary: #cbd5e1;
                --text-muted: #94a3b8;
                --border-color: #334155;
                --gradient-overlay: linear-gradient(rgba(15, 23, 42, 0.8), rgba(30, 41, 59, 0.8));
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
            
            .animate-pulse-slow {
                animation: pulse 3s ease-in-out infinite;
            }
            
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            
            .bg-buildings {
                background-image: var(--gradient-overlay), url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                transition: all 0.3s ease;
            }
            
            .glass-effect {
                background: var(--bg-card);
                backdrop-filter: blur(10px);
                border: 1px solid var(--border-color);
                color: var(--text-primary);
                transition: all 0.3s ease;
            }

            .header-bg {
                background: var(--bg-primary);
                border-bottom: 1px solid var(--border-color);
                transition: all 0.3s ease;
            }

            .nav-text {
                color: var(--text-primary);
                transition: all 0.3s ease;
            }

            .nav-link {
                color: var(--text-secondary);
                transition: all 0.3s ease;
            }

            .theme-toggle {
                position: relative;
                width: 50px;
                height: 25px;
                background: #374151;
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid #4b5563;
            }

            .theme-toggle.active {
                background: #3b82f6;
                border-color: #2563eb;
            }

            .theme-toggle::before {
                content: '';
                position: absolute;
                top: 1px;
                left: 1px;
                width: 19px;
                height: 19px;
                background: white;
                border-radius: 50%;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .theme-toggle.active::before {
                transform: translateX(23px);
            }
        </style>
    </head>
    <body class="bg-buildings min-h-screen flex items-center justify-center" data-theme="light">
        <!-- Navigation Header -->
        <header class="fixed top-0 left-0 right-0 header-bg shadow-sm z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('storage/properties/Screenshot 2025-08-22 070351.png') }}" alt="image" class="w-10 h-10 object-cover rounded-full shadow-md">
                        <h1 class="text-2xl font-bold nav-text">Rental</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-sun text-yellow-500 text-sm"></i>
                            <div class="theme-toggle" id="themeToggle"></div>
                            <i class="fas fa-moon text-blue-400 text-sm"></i>
                        </div>
                        <a href="{{ url('/') }}" class="nav-link hover:text-blue-600 transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Login Form Container -->
        <div class="w-full max-w-md px-6 py-8 animate-fadeInUp">
            <!-- Logo and Title -->
            <div class="text-center mb-8 animate-float">
                <div class="mb-4">
                    <i class="fas fa-user-circle text-6xl text-white drop-shadow-lg"></i>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2 drop-shadow-lg animate-slideInLeft">
                    Welcome Back
                </h2>
                <p class="text-gray-200 drop-shadow-md animate-slideInLeft" style="animation-delay: 0.2s;">
                    Sign in to your Rental account
                </p>
            </div>

            <!-- Login Form -->
            <div class="glass-effect rounded-2xl shadow-2xl p-8 animate-fadeInUp" style="animation-delay: 0.3s;">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-600"></i>
                            Email Address
                        </label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white bg-opacity-90"
                               placeholder="Enter your email address">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>
                            Password
                        </label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white bg-opacity-90"
                               placeholder="Enter your password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   name="remember" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" 
                               class="text-sm text-blue-600 hover:text-blue-700 transition duration-200">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 transform hover:scale-105 animate-pulse-slow">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In to Dashboard
                    </button>
                </form>

                <!-- Additional Info -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-shield-alt mr-1 text-green-600"></i>
                        Secure login powered by Rental
                    </p>
                </div>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-6 glass-effect rounded-lg p-4 text-center animate-fadeInUp" style="animation-delay: 0.5s;">
                <p class="text-sm text-gray-700 font-medium mb-2">
                    <i class="fas fa-info-circle mr-1 text-blue-600"></i>
                    Demo Credentials
                </p>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><strong>Email:</strong> example@gmail.com</p>
                    <p><strong>Password:</strong> p@12345!</p>
                </div>
            </div>
        </div>

        <script>
            // Theme toggle functionality
            const themeToggle = document.getElementById('themeToggle');
            const body = document.body;

            // Check for saved theme preference or default to 'light'
            const currentTheme = localStorage.getItem('theme') || 'light';
            body.setAttribute('data-theme', currentTheme);

            // Update toggle state based on current theme
            if (currentTheme === 'dark') {
                themeToggle.classList.add('active');
            }

            // Theme toggle event listener
            themeToggle.addEventListener('click', function() {
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                // Update toggle state
                if (newTheme === 'dark') {
                    themeToggle.classList.add('active');
                } else {
                    themeToggle.classList.remove('active');
                }
            });

            // Add theme to login form for server-side processing
            document.querySelector('form').addEventListener('submit', function() {
                const theme = localStorage.getItem('theme') || 'light';
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'preferred_theme';
                hiddenInput.value = theme;
                this.appendChild(hiddenInput);
            });
        </script>
    </body>
</html>
