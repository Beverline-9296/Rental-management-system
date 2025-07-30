<footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Astra Spaces</h3>
                <p class="text-gray-600 text-sm">Your trusted partner in property management solutions.</p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="{{ url('/') }}" class="text-gray-600 hover:text-blue-600 text-sm">Home</a></li>
                    <li><a href="{{ url('about') }}" class="text-gray-600 hover:text-blue-600 text-sm">About Us</a></li>
                    <li><a href="{{ url('contact') }}" class="text-gray-600 hover:text-blue-600 text-sm">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">Legal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 text-sm">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 text-sm">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-center text-sm text-gray-500">&copy; {{ date('Y') }} Astra Spaces. All rights reserved.</p>
        </div>
    </div>
</footer>
