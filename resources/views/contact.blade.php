<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Contact Us - Rental Management Platform</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
        @include('partials.header')

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-8 text-center">Contact Us</h1>
                
                <div class="grid md:grid-cols-2 gap-12">
                    <!-- Contact Form -->
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Send us a Message</h2>
                        <form action="#" method="POST" class="space-y-6">
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
                            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Get in Touch</h2>
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
                                    <h3 class="text-lg font-medium text-gray-900">Our Office</h3>
                                    <p class="mt-1 text-gray-600">123 Rental Street, Nairobi, Kenya</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-phone-alt text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Phone</h3>
                                    <p class="mt-1 text-gray-600">+254 700 000000</p>
                                    <p class="text-gray-600">Mon - Fri, 9:00 AM - 5:00 PM</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">Email</h3>
                                    <p class="mt-1 text-gray-600">info@rentalplatform.com</p>
                                    <p class="text-gray-600">support@rentalplatform.com</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Follow Us</h3>
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
    </body>
</html>
