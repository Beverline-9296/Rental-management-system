@extends('landlord.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Messages</h1>
                    <p class="text-gray-600 mt-1">Communicate with your tenants</p>
                </div>
                <a href="{{ route('landlord.messages.create') }}" 
                   class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>New Message
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.1s;">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-r from-purple-100 to-blue-100 rounded-xl">
                        <i class="fas fa-envelope text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Messages</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.2s;">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-r from-red-100 to-pink-100 rounded-xl">
                        <i class="fas fa-envelope-open text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Unread</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['unread'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.3s;">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-r from-green-100 to-emerald-100 rounded-xl">
                        <i class="fas fa-paper-plane text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sent</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['sent'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-6 rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 animate-fadeInUp" style="animation-delay: 0.4s;">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-xl">
                        <i class="fas fa-inbox text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Received</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['received'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('landlord.messages.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message Type</label>
                        <select name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>General</option>
                            <option value="maintenance" {{ request('type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="lease" {{ request('type') === 'lease' ? 'selected' : '' }}>Lease</option>
                            <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select name="priority" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Priorities</option>
                            <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Messages</option>
                            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="{{ route('landlord.messages.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Messages List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Messages</h3>
            </div>
            
            @if($messages->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($messages as $message)
                        <div class="p-6 hover:bg-gray-50 transition duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <h4 class="text-lg font-medium text-gray-900 mr-3">
                                            <a href="{{ route('landlord.messages.show', $message) }}" class="hover:text-blue-600">
                                                {{ $message->subject }}
                                            </a>
                                        </h4>
                                        
                                        <!-- Priority Badge -->
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $message->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                                               ($message->priority === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ ucfirst($message->priority) }}
                                        </span>
                                        
                                        <!-- Type Badge -->
                                        <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $message->message_type === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($message->message_type === 'lease' ? 'bg-green-100 text-green-800' : 
                                               ($message->message_type === 'payment' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($message->message_type) }}
                                        </span>
                                        
                                        <!-- Unread Badge -->
                                        @if($message->receiver_id === auth()->id() && !$message->is_read)
                                            <span class="ml-2 px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Unread
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <span class="mr-4">
                                            <i class="fas fa-user mr-1"></i>
                                            @if($message->sender_id === auth()->id())
                                                To: {{ $message->receiver->name }}
                                            @else
                                                From: {{ $message->sender->name }}
                                            @endif
                                        </span>
                                        
                                        @if($message->property)
                                            <span class="mr-4">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $message->property->name }}
                                            </span>
                                        @endif
                                        
                                        <span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $message->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-700 line-clamp-2">{{ Str::limit($message->message, 150) }}</p>
                                </div>
                                
                                <div class="ml-4 flex items-center space-x-2">
                                    <a href="{{ route('landlord.messages.show', $message) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View
                                    </a>
                                    
                                    @if($message->sender_id === auth()->id())
                                        <form method="POST" action="{{ route('landlord.messages.destroy', $message) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete this message?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $messages->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-envelope text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No messages found</h3>
                    <p class="text-gray-600 mb-6">Start communicating with your tenants by sending your first message.</p>
                    <a href="{{ route('landlord.messages.create') }}" 
                       class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>Send Message
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.glass-card {
    background: rgba(255, 255, 255, 0.95);
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

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out forwards;
}
</style>

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.querySelector('.fixed.top-4').remove();
        }, 3000);
    </script>
@endif
@endsection
