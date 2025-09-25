<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Rental - Rental Management Platform</title>
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
                --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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
                --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
                --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
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

            @keyframes glow {
                0%, 100% {
                    box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
                }
                50% {
                    box-shadow: 0 0 30px rgba(59, 130, 246, 0.8);
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

            .animate-glow {
                animation: glow 2s ease-in-out infinite;
            }
            
            .bg-buildings {
                background-image: var(--gradient-overlay), url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                transition: all 0.3s ease;
            }

            .theme-toggle {
                position: relative;
                width: 60px;
                height: 30px;
                background: #374151;
                border-radius: 15px;
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
                top: 2px;
                left: 2px;
                width: 22px;
                height: 22px;
                background: white;
                border-radius: 50%;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .theme-toggle.active::before {
                transform: translateX(28px);
            }

            .feature-card {
                background: var(--bg-card);
                color: var(--text-primary);
                border: 2px solid var(--border-color);
                box-shadow: var(--shadow);
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
                position: relative;
            }

            .feature-card:hover {
                box-shadow: var(--shadow-hover);
                transform: translateY(-5px);
            }

            .feature-card.border-blue {
                border-color: #3b82f6;
                box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1), 0 4px 6px -2px rgba(59, 130, 246, 0.05);
            }

            .feature-card.border-blue:hover {
                box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.2), 0 10px 10px -5px rgba(59, 130, 246, 0.1);
                border-color: #2563eb;
            }

            .feature-card.border-purple {
                border-color: #8b5cf6;
                box-shadow: 0 10px 15px -3px rgba(139, 92, 246, 0.1), 0 4px 6px -2px rgba(139, 92, 246, 0.05);
            }

            .feature-card.border-purple:hover {
                box-shadow: 0 20px 25px -5px rgba(139, 92, 246, 0.2), 0 10px 10px -5px rgba(139, 92, 246, 0.1);
                border-color: #7c3aed;
            }

            .feature-card.border-green {
                border-color: #10b981;
                box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.1), 0 4px 6px -2px rgba(16, 185, 129, 0.05);
            }

            .feature-card.border-green:hover {
                box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.2), 0 10px 10px -5px rgba(16, 185, 129, 0.1);
                border-color: #059669;
            }

            .feature-card.border-pink {
                border-color: #ec4899;
                box-shadow: 0 10px 15px -3px rgba(236, 72, 153, 0.1), 0 4px 6px -2px rgba(236, 72, 153, 0.05);
            }

            .feature-card.border-pink:hover {
                box-shadow: 0 20px 25px -5px rgba(236, 72, 153, 0.2), 0 10px 10px -5px rgba(236, 72, 153, 0.1);
                border-color: #db2777;
            }

            .feature-card.border-orange {
                border-color: #f59e0b;
                box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.1), 0 4px 6px -2px rgba(245, 158, 11, 0.05);
            }

            .feature-card.border-orange:hover {
                box-shadow: 0 20px 25px -5px rgba(245, 158, 11, 0.2), 0 10px 10px -5px rgba(245, 158, 11, 0.1);
                border-color: #d97706;
            }

            .feature-card.border-indigo {
                border-color: #6366f1;
                box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.1), 0 4px 6px -2px rgba(99, 102, 241, 0.05);
            }

            .feature-card.border-indigo:hover {
                box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.2), 0 10px 10px -5px rgba(99, 102, 241, 0.1);
                border-color: #4f46e5;
            }

            .feature-card h3 {
                color: var(--text-primary);
            }

            .feature-card p {
                color: var(--text-secondary);
            }

            .feature-card ul li {
                color: var(--text-muted);
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

            .nav-link:hover {
                color: #3b82f6;
            }

            .gradient-text {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            [data-theme="dark"] .gradient-text {
                background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            }

            [data-theme="dark"] .btn-primary {
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            }

            [data-theme="dark"] .btn-primary:hover {
                box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
            }
        </style>
    </head>
    <body class="bg-buildings min-h-screen" data-theme="light">
        <!-- Navigation Header -->
        <header class="header-bg shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-8">
                        <div class="flex-shrink-0">
                            <div class="flex items-center">
                                <img src="{{ asset('storage/properties/Screenshot 2025-08-22 070351.png') }}" alt="image" class="w-10 h-10 object-cover rounded-full shadow-md">
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <h1 class="text-2xl font-bold nav-text">Rental</h1>
                        </div>
                        <nav class="hidden md:flex items-center space-x-6">
                            <a href="{{ url('/') }}" class="nav-link px-3 py-2 text-sm font-medium">Home</a>
                            <a href="{{ url('about') }}" class="nav-link px-3 py-2 text-sm font-medium">About</a>
                            <a href="{{ url('contact') }}" class="nav-link px-3 py-2 text-sm font-medium">Contact</a>
                        </nav>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-sun text-yellow-500 text-sm"></i>
                            <div class="theme-toggle" id="themeToggle"></div>
                            <i class="fas fa-moon text-blue-400 text-sm"></i>
                        </div>
                        @auth
                            <a href="{{ route('landlord.dashboard') }}" class="btn-primary text-white px-4 py-2 rounded-lg transition duration-200">
                                Dashboard
                            </a>
                        @endauth
                    </div>
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
                    Welcome to <span class="gradient-text">Rental</span>
                </h1>
                <p class="text-xl text-gray-200 mb-8 max-w-3xl mx-auto animate-fadeInUp drop-shadow-md" style="animation-delay: 0.2s;">
                    Your comprehensive rental management platform. Streamline property management, tenant relations, and payment processing with our modern, integrated solution.
                </p>
                
                <!-- Login Button -->
                <div class="flex justify-center mb-12 animate-fadeInUp" style="animation-delay: 0.4s;">
                    <a href="{{ route('login') }}" class="btn-primary text-white px-8 py-4 rounded-lg text-lg font-semibold transition duration-200 shadow-lg animate-glow hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login to System
                    </a>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <!-- Property Management -->
                <div class="feature-card border-blue rounded-xl p-8 animate-slideInLeft" style="animation-delay: 0.1s;">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-building text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Property Management</h3>
                        <p class="mb-4">Efficiently manage multiple properties, track occupancy, and monitor property performance.</p>
                        <ul class="text-sm space-y-1">
                            <li>• Add & manage properties</li>
                            <li>• Track occupied units</li>
                            <li>• Monitor property status</li>
                        </ul>
                    </div>
                </div>

                <!-- Tenant Management -->
                <div class="feature-card border-green rounded-xl p-8 animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-users text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Tenant Management</h3>
                        <p class="mb-4">Streamline tenant registration, assignment, and communication processes.</p>
                        <ul class="text-sm space-y-1">
                            <li>• Register new tenants</li>
                            <li>• Assign to properties</li>
                            <li>• Track tenant details</li>
                        </ul>
                    </div>
                </div>

                <!-- Payment Processing -->
                <div class="feature-card border-purple rounded-xl p-8 animate-slideInRight" style="animation-delay: 0.3s;">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-credit-card text-2xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Payment Processing</h3>
                        <p class="mb-4">Integrated M-Pesa STK Push and USSD for seamless rent collection.</p>
                        <ul class="text-sm space-y-1">
                            <li>• M-Pesa integration</li>
                            <li>• USSD payments</li>
                            <li>• Automated receipts</li>
                        </ul>
                    </div>
                </div>

                <!-- Financial Tracking -->
                <div class="feature-card border-orange rounded-xl p-8 animate-slideInLeft" style="animation-delay: 0.4s;">
                    <div class="text-center">
                        <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-chart-line text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Financial Tracking</h3>
                        <p class="mb-4">Monitor rent payments, track arrears, and generate financial reports.</p>
                        <ul class="text-sm space-y-1">
                            <li>• Payment tracking</li>
                            <li>• Arrears management</li>
                            <li>• Financial reports</li>
                        </ul>
                    </div>
                </div>

                <!-- Communication Hub -->
                <div class="feature-card border-pink rounded-xl p-8 animate-fadeInUp" style="animation-delay: 0.5s;">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-envelope text-2xl text-red-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Communication Hub</h3>
                        <p class="mb-4">Facilitate seamless communication between landlords and tenants.</p>
                        <ul class="text-sm space-y-1">
                            <li>• Messaging system</li>
                            <li>• Payment requests</li>
                            <li>• Notifications</li>
                        </ul>
                    </div>
                </div>

                <!-- Mobile Responsive -->
                <div class="feature-card border-indigo rounded-xl p-8 animate-slideInRight" style="animation-delay: 0.6s;">
                    <div class="text-center">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-slow">
                            <i class="fas fa-mobile-alt text-2xl text-indigo-600"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Mobile Responsive</h3>
                        <p class="mb-4">Access your rental management system from any device, anywhere.</p>
                        <ul class="text-sm space-y-1">
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

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add intersection observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all feature cards
            document.querySelectorAll('.feature-card').forEach(card => {
                observer.observe(card);
            });
        </script>
    </body>
</html>