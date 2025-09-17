<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Verify Your Account</h2>
        <p class="mt-2 text-sm text-gray-600">
            Please enter the verification code sent to your email and set a new password.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
            {{ session('info') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('account.verification.verify') }}">
        @csrf

        <!-- Verification Code -->
        <div class="mb-4">
            <label for="verification_code" class="block text-sm font-medium text-gray-700">
                Verification Code
            </label>
            <input id="verification_code" 
                   type="text" 
                   name="verification_code" 
                   value="{{ old('verification_code') }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('verification_code') border-red-500 @enderror"
                   placeholder="Enter 6-digit code"
                   maxlength="6"
                   required
                   autofocus>
            @error('verification_code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- New Password -->
        <div class="mb-4">
            <label for="new_password" class="block text-sm font-medium text-gray-700">
                New Password
            </label>
            <input id="new_password" 
                   type="password" 
                   name="new_password"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('new_password') border-red-500 @enderror"
                   required>
            @error('new_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">
                Confirm New Password
            </label>
            <input id="new_password_confirmation" 
                   type="password" 
                   name="new_password_confirmation"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                   required>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Verify Account
            </button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('account.verification.resend') }}" class="inline">
            @csrf
            <button type="submit" 
                    class="text-sm text-indigo-600 hover:text-indigo-500 underline">
                Didn't receive the code? Resend
            </button>
        </form>
    </div>

    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" 
                    class="text-sm text-gray-600 hover:text-gray-500 underline">
                Sign out
            </button>
        </form>
    </div>

    <script>
    // Auto-format verification code input
    document.getElementById('verification_code').addEventListener('input', function(e) {
        // Remove any non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Limit to 6 characters
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });

    // Auto-submit when 6 digits are entered (optional)
    document.getElementById('verification_code').addEventListener('input', function(e) {
        if (this.value.length === 6) {
            // Focus on password field
            document.getElementById('new_password').focus();
        }
    });
    </script>
</x-guest-layout>
