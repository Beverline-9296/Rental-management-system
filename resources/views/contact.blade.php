<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Contact Us - Rental Management Platform</title>
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

            input, textarea {
                background-color: var(--bg-secondary);
                color: var(--text-primary);
                border-color: var(--border-color);
            }

            [data-theme="dark"] input,
            [data-theme="dark"] textarea {
                background-color: rgba(30, 41, 59, 0.5);
                border-color: var(--border-color);
            }

            .bg-blue-100 {
                background-color: var(--bg-secondary) !important;
            }

            [data-theme="dark"] .bg-blue-100 {
                background-color: rgba(59, 130, 246, 0.1) !important;
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
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="content-card rounded-xl shadow-lg p-8">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-8 text-center">Contact Us</h1>
                <div class="grid md:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div>
                        <h2 class="text-2xl font-semibold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-6">Send us a Message</h2>
                        <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                                <input type="text" id="name" name="name" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                <input type="text" id="subject" name="subject" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Your Message</label>
                                <textarea id="message" name="message" rows="5" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                            
                            <div>
                                <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200 w-full sm:w-auto">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <div>
                            <h2 class="text-2xl font-semibold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-6">Get in Touch</h2>
                            <p class="text-gray-700 mb-6">
                                Have questions or need assistance? Our team is here to help you with any inquiries 
                                about our rental management platform.
                            </p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Our Office</h3>
                                    <p class="mt-1 text-gray-600">123 Rental Street, Nairobi, Kenya</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-phone-alt text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Phone</h3>
                                    <p class="mt-1 text-gray-600">+254 700 000000</p>
                                    <p class="text-gray-600">Mon - Fri, 9:00 AM - 5:00 PM</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Email</h3>
                                    <p class="mt-1 text-gray-600">info@rentalplatform.com</p>
                                    <p class="text-gray-600">support@rentalplatform.com</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <h3 class="text-lg font-medium bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-3">Follow Us</h3>
                            <div class="flex space-x-4">
                                <a href="#" class="text-gray-600 hover:text-blue-600">
                                    <i class="fab fa-facebook-f text-2xl"></i>
                                </a>
                                <a href="#" class="text-gray-600 hover:text-blue-400">
                                    <i class="fab fa-twitter text-2xl"></i>
                                </a>
                                <a href="#" class="text-gray-600 hover:text-pink-600">
                                    <i class="fab fa-instagram text-2xl"></i>
                                </a>
                                <a href="#" class="text-gray-600 hover:text-blue-700">
                                    <i class="fab fa-linkedin-in text-2xl"></i>
                                </a>
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
