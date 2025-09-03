@extends('landlord.layouts.app')

@section('header')
    Settings
@endsection

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Profile Information -->
        <div class="glass-card rounded-xl shadow-lg mb-8 animate-fadeInUp">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Profile Information
                </h3>
                <p class="text-sm text-gray-600 mt-1">Update your account's profile information and email address.</p>
            </div>
            
            <div class="p-6">
                <form method="post" action="{{ route('landlord.settings.update') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   required autofocus autocomplete="name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   required autocomplete="username">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone_number) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   autocomplete="tel">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Your email address is unverified.
                                        <button form="send-verification" class="underline text-sm text-yellow-600 hover:text-yellow-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            Click here to re-send the verification email.
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 shadow-lg">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Theme Preferences -->
        <div class="glass-card rounded-xl shadow-lg mb-8 animate-fadeInUp" style="animation-delay: 0.05s;">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-palette text-blue-600 mr-2"></i>
                    Theme Preferences
                </h3>
            </div>
            
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-medium text-gray-900">Dark Mode</h4>
                        <p class="text-sm text-gray-600">Switch between light and dark theme</p>
                    </div>
                    <form method="post" action="{{ route('landlord.settings.theme') }}" id="themeForm">
                        @csrf
                        <input type="hidden" name="theme" id="themeInput" value="{{ $theme === 'dark' ? 'light' : 'dark' }}">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   {{ $theme === 'dark' ? 'checked' : '' }}
                                   class="sr-only peer"
                                   onchange="toggleTheme()">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Update -->
        <div class="glass-card rounded-xl shadow-lg mb-8 animate-fadeInUp" style="animation-delay: 0.1s;">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-lock text-blue-600 mr-2"></i>
                    Update Password
                </h3>
                <p class="text-sm text-gray-600 mt-1">Ensure your account is using a long, random password to stay secure.</p>
            </div>
            
            <div class="p-6">
                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" id="update_password_current_password" name="current_password" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" id="update_password_password" name="password" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" id="update_password_password_confirmation" name="password_confirmation" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                   autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-6 py-2 rounded-lg font-medium transition duration-200 shadow-lg">
                            <i class="fas fa-key mr-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Business Settings -->
        <div class="glass-card rounded-xl shadow-lg mb-8 animate-fadeInUp" style="animation-delay: 0.2s;">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-building text-blue-600 mr-2"></i>
                    Business Settings
                </h3>
                <p class="text-sm text-gray-600 mt-1">Configure your business preferences and default settings.</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                        <input type="text" id="business_name" name="business_name" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Your Property Management Company">
                    </div>

                    <div>
                        <label for="default_rent_due_day" class="block text-sm font-medium text-gray-700 mb-2">Default Rent Due Day</label>
                        <select id="default_rent_due_day" name="default_rent_due_day" 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @for($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>{{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }} of the month</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="late_fee_amount" class="block text-sm font-medium text-gray-700 mb-2">Default Late Fee (KES)</label>
                        <input type="number" id="late_fee_amount" name="late_fee_amount" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="500" min="0" step="50">
                    </div>

                    <div>
                        <label for="grace_period_days" class="block text-sm font-medium text-gray-700 mb-2">Grace Period (Days)</label>
                        <input type="number" id="grace_period_days" name="grace_period_days" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="3" min="0" max="30">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" 
                            class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-6 py-2 rounded-lg font-medium transition duration-200 shadow-lg">
                        <i class="fas fa-save mr-2"></i>Save Business Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Notification Preferences -->
        <div class="glass-card rounded-xl shadow-lg mb-8 animate-fadeInUp" style="animation-delay: 0.3s;">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-bell text-blue-600 mr-2"></i>
                    Notification Preferences
                </h3>
                <p class="text-sm text-gray-600 mt-1">Choose how you want to be notified about important events.</p>
            </div>
            
            <div class="p-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Email Notifications</h4>
                            <p class="text-sm text-gray-600">Receive notifications via email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">SMS Notifications</h4>
                            <p class="text-sm text-gray-600">Receive notifications via SMS</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Payment Notifications</h4>
                            <p class="text-sm text-gray-600">Get notified when tenants make payments</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Maintenance Requests</h4>
                            <p class="text-sm text-gray-600">Get notified about new maintenance requests</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">Rent Reminders</h4>
                            <p class="text-sm text-gray-600">Send automatic rent reminders to tenants</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" 
                            class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-6 py-2 rounded-lg font-medium transition duration-200 shadow-lg">
                        <i class="fas fa-bell mr-2"></i>Save Notification Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="glass-card rounded-xl shadow-lg animate-fadeInUp" style="animation-delay: 0.4s;">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-user-cog text-red-600 mr-2"></i>
                    Account Actions
                </h3>
                <p class="text-sm text-gray-600 mt-1">Manage your account and data.</p>
            </div>
            
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-red-800">Delete Account</h4>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Once your account is deleted, all of its resources and data will be permanently deleted. This includes all properties, tenants, payments, and other associated data. Before deleting your account, please download any data or information that you wish to retain.</p>
                            </div>
                            <div class="mt-4">
                                <button type="button" onclick="confirmDelete()" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                                    <i class="fas fa-trash mr-2"></i>Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden forms -->
<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<!-- Delete Account Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Are you sure you want to delete your account?</h3>
        <p class="text-sm text-gray-600 mb-6">Once your account is deleted, all of its resources and data will be permanently deleted. This includes all properties, tenants, and payment records. Please enter your password to confirm you would like to permanently delete your account.</p>
        
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" 
                       placeholder="Password">
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

@if(session('status') === 'profile-updated')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Profile updated successfully!',
                    confirmButtonColor: '#3085d6',
                });
            }
        });
    </script>
@endif

@if(session('status') === 'password-updated')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Password updated successfully!',
                    confirmButtonColor: '#3085d6',
                });
            }
        });
    </script>
@endif

<script>
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}

function toggleTheme() {
    const form = document.getElementById('themeForm');
    const input = document.getElementById('themeInput');
    const checkbox = form.querySelector('input[type="checkbox"]');
    
    // Update hidden input value based on checkbox state
    input.value = checkbox.checked ? 'dark' : 'light';
    
    // Submit the form
    form.submit();
}
</script>
@endsection
