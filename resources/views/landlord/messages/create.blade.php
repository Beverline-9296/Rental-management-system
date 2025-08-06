@extends('landlord.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('landlord.messages.index') }}" 
                   class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">New Message</h1>
                    <p class="text-gray-600 mt-1">Send a message to your tenant</p>
                </div>
            </div>
        </div>

        <!-- Message Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Compose Message</h3>
            </div>
            
            <form method="POST" action="{{ route('landlord.messages.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <!-- Recipient Selection -->
                <div class="mb-6">
                    <label for="receiver_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Send To <span class="text-red-500">*</span>
                    </label>
                    <select name="receiver_id" id="receiver_id" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('receiver_id') border-red-500 @enderror">
                        <option value="">Select a tenant...</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}" {{ (old('receiver_id') == $tenant->id || (isset($recipient) && $recipient->id == $tenant->id)) ? 'selected' : '' }}>
                                {{ $tenant->name }} ({{ $tenant->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('receiver_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject -->
                <div class="mb-6">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('subject') border-red-500 @enderror"
                           placeholder="Enter message subject">
                    @error('subject')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message Type and Priority -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="message_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Message Type <span class="text-red-500">*</span>
                        </label>
                        <select name="message_type" id="message_type" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('message_type') border-red-500 @enderror">
                            <option value="general" {{ old('message_type') === 'general' ? 'selected' : '' }}>General</option>
                            <option value="maintenance" {{ old('message_type') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="lease" {{ old('message_type') === 'lease' ? 'selected' : '' }}>Lease</option>
                            <option value="payment" {{ old('message_type') === 'payment' ? 'selected' : '' }}>Payment</option>
                        </select>
                        @error('message_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                            Priority <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('priority') border-red-500 @enderror">
                            <option value="normal" {{ old('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Property Selection -->
                <div class="mb-6">
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Related Property (Optional)
                    </label>
                    <select name="property_id" id="property_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('property_id') border-red-500 @enderror">
                        <option value="">Select a property...</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }} - {{ $property->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message Content -->
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="message" rows="8" required
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-500 @enderror"
                              placeholder="Type your message here...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Attachments -->
                <div class="mb-6">
                    <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                        Attachments (Optional)
                    </label>
                    <input type="file" name="attachments[]" id="attachments" multiple
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('attachments.*') border-red-500 @enderror"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                    <p class="text-sm text-gray-500 mt-1">
                        You can upload multiple files. Maximum 10MB per file. Supported formats: PDF, DOC, DOCX, JPG, PNG, GIF
                    </p>
                    @error('attachments.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority Guidelines -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Priority Guidelines:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><strong>Normal:</strong> General inquiries, routine communications</li>
                        <li><strong>High:</strong> Important matters requiring prompt attention</li>
                        <li><strong>Urgent:</strong> Emergency situations requiring immediate response</li>
                    </ul>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('landlord.messages.index') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md text-sm font-medium transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-fill subject based on message type
document.getElementById('message_type').addEventListener('change', function() {
    const subjectField = document.getElementById('subject');
    const currentSubject = subjectField.value;
    
    if (!currentSubject) {
        const type = this.value;
        const subjects = {
            'general': 'General Inquiry',
            'maintenance': 'Maintenance Request Follow-up',
            'lease': 'Lease Information',
            'payment': 'Payment Notice'
        };
        
        if (subjects[type]) {
            subjectField.value = subjects[type];
        }
    }
});
</script>
@endsection
