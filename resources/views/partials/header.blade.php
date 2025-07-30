<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-8">
                <div class="flex items-center">
                    <i class="fas fa-building text-3xl text-blue-600 mr-3"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Astra Spaces</h1>
                </div>
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ url('/') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">Home</a>
                    <a href="{{ url('about') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">About</a>
                    <a href="{{ url('contact') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">Contact</a>
                    <a href="{{ auth()->check() && auth()->user()->isLandlord() ? route('landlord.properties.index') : route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">Properties</a>
                </nav>
            </div>
            @auth
                <nav class="flex items-center space-x-4">
                    <a href="{{ route('landlord.dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        Dashboard
                    </a>
                </nav>
            @else
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">Log in</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        Sign up
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>
