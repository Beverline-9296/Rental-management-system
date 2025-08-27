@extends('tenant.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">
                Contact Landlord
            </h1>
            <p class="text-gray-600 mt-1">Get in touch with your landlord for any inquiries or concerns</p>
        </div>

        <!-- Landlord Information Card -->
        <div class="glass-card rounded-xl shadow-lg mb-8 animate-fadeInUp">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-user-tie text-blue-600 mr-2"></i>
                    Landlord Information
                </h3>
                
                @if(auth()->user()->tenantAssignment && auth()->user()->tenantAssignment->unit && auth()->user()->tenantAssignment->unit->property && auth()->user()->tenantAssignment->unit->property->landlord)
                    @php $landlord = auth()->user()->tenantAssignment->unit->property->landlord; @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-100 to-blue-200 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-blue-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $landlord->name }}</p>
                                    <p class="text-sm text-gray-600">Property Owner</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            @if($landlord->email)
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700">{{ $landlord->email }}</span>
                                </div>
                            @endif
                            
                            @if($landlord->phone)
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-400 w-5 mr-3"></i>
                                    <span class="text-gray-700">{{ $landlord->phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                        <p class="text-gray-600">Landlord information not available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Contact Options -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Send Message -->
            <div class="glass-card rounded-xl shadow-lg p-6 animate-fadeInUp" style="animation-delay: 0.1s;">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Send Message</h3>
                    <p class="text-gray-600 text-sm mb-4">Send a message through the system</p>
                    <a href="{{ route('tenant.messages.create') }}" 
                       class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 shadow-lg">
                        Send Message
                    </a>
                </div>
            </div>

            <!-- Call Landlord -->
            <div class="glass-card rounded-xl shadow-lg p-6 animate-fadeInUp" style="animation-delay: 0.2s;">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-100 to-green-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Call Direct</h3>
                    <p class="text-gray-600 text-sm mb-4">For urgent matters</p>
                    @if(auth()->user()->tenantAssignment && auth()->user()->tenantAssignment->unit && auth()->user()->tenantAssignment->unit->property && auth()->user()->tenantAssignment->unit->property->landlord && auth()->user()->tenantAssignment->unit->property->landlord->phone)
                        <a href="tel:{{ auth()->user()->tenantAssignment->unit->property->landlord->phone }}" 
                           class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 shadow-lg">
                            Call Now
                        </a>
                    @else
                        <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                            No Phone Available
                        </button>
                    @endif
                </div>
            </div>

            <!-- Email Direct -->
            <div class="glass-card rounded-xl shadow-lg p-6 animate-fadeInUp" style="animation-delay: 0.3s;">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-100 to-purple-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-at text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Email Direct</h3>
                    <p class="text-gray-600 text-sm mb-4">Send email directly</p>
                    @if(auth()->user()->tenantAssignment && auth()->user()->tenantAssignment->unit && auth()->user()->tenantAssignment->unit->property && auth()->user()->tenantAssignment->unit->property->landlord && auth()->user()->tenantAssignment->unit->property->landlord->email)
                        <a href="mailto:{{ auth()->user()->tenantAssignment->unit->property->landlord->email }}" 
                           class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 shadow-lg">
                            Send Email
                        </a>
                    @else
                        <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                            No Email Available
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Emergency Contact Information -->
        <div class="glass-card rounded-xl shadow-lg mb-8 animate-fadeInUp" style="animation-delay: 0.4s;">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    Emergency Contacts
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border-l-4 border-red-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-2">Emergency Maintenance</h4>
                        <p class="text-sm text-gray-600 mb-2">For urgent repairs (water leaks, electrical issues, etc.)</p>
                        <p class="text-red-600 font-medium">Available 24/7</p>
                    </div>
                    
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <h4 class="font-medium text-gray-900 mb-2">Property Management</h4>
                        <p class="text-sm text-gray-600 mb-2">For general inquiries and non-urgent matters</p>
                        <p class="text-yellow-600 font-medium">Business Hours: 9 AM - 5 PM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Messages -->
        <div class="glass-card rounded-xl shadow-lg animate-fadeInUp" style="animation-delay: 0.5s;">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Recent Messages</h3>
                    <a href="{{ route('tenant.messages.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Messages
                    </a>
                </div>
            </div>
            
            <div class="p-6">
                <div class="text-center py-8">
                    <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-600 mb-4">No recent messages</p>
                    <a href="{{ route('tenant.messages.create') }}" 
                       class="bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 shadow-lg">
                        <i class="fas fa-plus mr-2"></i>Start Conversation
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slideInRight">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.fixed.top-4').remove();
        }, 3000);
    </script>
@endif

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
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

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out forwards;
}

.animate-slideInRight {
    animation: slideInRight 0.5s ease-out forwards;
}
</style>
@endsection
