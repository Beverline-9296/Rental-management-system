@extends('landlord.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('landlord.messages.index') }}" 
                       class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $message->subject }}</h1>
                        <p class="text-gray-600 mt-1">
                            @if($message->sender_id === auth()->id())
                                To: {{ $message->receiver->name }}
                            @else
                                From: {{ $message->sender->name }}
                            @endif
                            • {{ $message->created_at->format('M j, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <!-- Reply Button -->
                    @if($message->sender_id !== auth()->id())
                        <a href="{{ route('landlord.messages.create', ['to' => $message->sender_id]) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            <i class="fas fa-reply mr-2"></i>Reply
                        </a>
                    @endif
                    
                    <!-- Mark as Read Button -->
                    @if($message->receiver_id === auth()->id() && !$message->is_read)
                        <form method="POST" action="{{ route('landlord.messages.mark-read', $message) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                <i class="fas fa-check mr-2"></i>Mark as Read
                            </button>
                        </form>
                    @endif
                    
                    <!-- Delete Button -->
                    @if($message->sender_id === auth()->id())
                        <form method="POST" action="{{ route('landlord.messages.destroy', $message) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this message?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Message Details -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Priority Badge -->
                    <span class="px-3 py-1 text-sm font-medium rounded-full 
                        {{ $message->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                           ($message->priority === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }}">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ ucfirst($message->priority) }} Priority
                    </span>
                    
                    <!-- Type Badge -->
                    <span class="px-3 py-1 text-sm font-medium rounded-full 
                        {{ $message->message_type === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 
                           ($message->message_type === 'lease' ? 'bg-green-100 text-green-800' : 
                           ($message->message_type === 'payment' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800')) }}">
                        <i class="fas fa-tag mr-1"></i>
                        {{ ucfirst($message->message_type) }}
                    </span>
                    
                    <!-- Read Status -->
                    @if($message->receiver_id === auth()->id())
                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                            {{ $message->is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas {{ $message->is_read ? 'fa-check-circle' : 'fa-circle' }} mr-1"></i>
                            {{ $message->is_read ? 'Read' : 'Unread' }}
                            @if($message->is_read && $message->read_at)
                                • {{ $message->read_at->format('M j, Y \a\t g:i A') }}
                            @endif
                        </span>
                    @endif
                    
                    <!-- Property Info -->
                    @if($message->property)
                        <span class="px-3 py-1 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">
                            <i class="fas fa-building mr-1"></i>
                            {{ $message->property->name }}
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Message Content -->
            <div class="p-6">
                <div class="prose max-w-none">
                    {!! nl2br(e($message->message)) !!}
                </div>
            </div>
            
            <!-- Attachments -->
            @if($message->attachments && count($message->attachments) > 0)
                <div class="px-6 py-4 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Attachments</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($message->attachments as $attachment)
                            <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex-shrink-0 mr-3">
                                    @if(str_contains($attachment['type'], 'image'))
                                        <i class="fas fa-image text-green-600 text-xl"></i>
                                    @elseif(str_contains($attachment['type'], 'pdf'))
                                        <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                                    @elseif(str_contains($attachment['type'], 'word') || str_contains($attachment['type'], 'document'))
                                        <i class="fas fa-file-word text-blue-600 text-xl"></i>
                                    @else
                                        <i class="fas fa-file text-gray-600 text-xl"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                </div>
                                <div class="flex-shrink-0 ml-3">
                                    <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sender/Receiver Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Sender Info -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sender Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center">
                        @if($message->sender->profile_photo_path)
                            <img src="{{ asset('storage/' . $message->sender->profile_photo_path) }}" 
                                 alt="{{ $message->sender->name }}" 
                                 class="w-12 h-12 rounded-full object-cover mr-4">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">{{ $message->sender->name }}</h4>
                            <p class="text-gray-600">{{ $message->sender->email }}</p>
                            @if($message->sender->phone_number)
                                <p class="text-gray-600">{{ $message->sender->phone_number }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Receiver Info -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recipient Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center">
                        @if($message->receiver->profile_photo_path)
                            <img src="{{ asset('storage/' . $message->receiver->profile_photo_path) }}" 
                                 alt="{{ $message->receiver->name }}" 
                                 class="w-12 h-12 rounded-full object-cover mr-4">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">{{ $message->receiver->name }}</h4>
                            <p class="text-gray-600">{{ $message->receiver->email }}</p>
                            @if($message->receiver->phone_number)
                                <p class="text-gray-600">{{ $message->receiver->phone_number }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
