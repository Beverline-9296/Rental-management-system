@extends('landlord.layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
                    <p class="text-gray-600 mt-1">Manage your account information and preferences</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Messages -->
        @if(session('status') === 'profile-updated')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                Profile updated successfully!
            </div>
        @endif

        @if(session('status') === 'photo-deleted')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                Profile photo deleted successfully!
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Photo Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Photo</h3>
                    
                    <div class="text-center">
                        <div class="mb-4">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                                     alt="Profile Photo" 
                                     class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-200">
                            @else
                                <div class="w-32 h-32 rounded-full mx-auto bg-gray-200 flex items-center justify-center border-4 border-gray-300">
                                    <i class="fas fa-user text-4xl text-gray-400"></i>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('landlord.profile.update') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                            @csrf
                            @method('patch')
                            
                            <!-- Include current user data to pass validation -->
                            <input type="hidden" name="name" value="{{ $user->name }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            
                            <div class="mb-4">
                                <input type="file" name="profile_photo" id="profile_photo" 
                                       class="hidden" accept="image/*" onchange="this.form.submit()">
                                <label for="profile_photo" 
                                       class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                    <i class="fas fa-camera mr-2"></i>
                                    {{ $user->profile_photo_path ? 'Change Photo' : 'Upload Photo' }}
                                </label>
                            </div>
                        </form>

                        @if($user->profile_photo_path)
                            <form action="{{ route('landlord.profile.delete-photo') }}" method="POST">
                                @csrf
                                @method('delete')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 text-sm font-medium"
                                        onclick="return confirm('Are you sure you want to delete your profile photo?')">
                                    <i class="fas fa-trash mr-1"></i>
                                    Remove Photo
                                </button>
                            </form>
                        @endif

                        @error('profile_photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Personal Information</h3>
                    
                    <form action="{{ route('landlord.profile.update') }}" method="POST">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" id="name" name="name" 
                                       value="{{ old('name', $user->name) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" id="email" name="email" 
                                       value="{{ old('email', $user->email) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" id="phone_number" name="phone_number" 
                                       value="{{ old('phone_number', $user->phone_number) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="+254 712345678">
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ID Number -->
                            <div>
                                <label for="id_number" class="block text-sm font-medium text-gray-700 mb-2">ID Number</label>
                                <input type="text" id="id_number" name="id_number" 
                                       value="{{ old('id_number', $user->id_number) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="National ID Number">
                                @error('id_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" 
                                       value="{{ old('date_of_birth', $user->date_of_birth) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Occupation -->
                            <div>
                                <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                                <input type="text" id="occupation" name="occupation" 
                                       value="{{ old('occupation', $user->occupation) }}"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Property Manager, Real Estate Agent, etc.">
                                @error('occupation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mt-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea id="address" name="address" rows="3"
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Your full address">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div class="mt-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                            <textarea id="bio" name="bio" rows="4"
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact Section -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Emergency Contact</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                                    <input type="text" id="emergency_contact" name="emergency_contact" 
                                           value="{{ old('emergency_contact', $user->emergency_contact) }}"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Emergency contact full name">
                                    @error('emergency_contact')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="emergency_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                                    <input type="tel" id="emergency_phone" name="emergency_phone" 
                                           value="{{ old('emergency_phone', $user->emergency_phone) }}"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="+254 712345678">
                                    @error('emergency_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium transition duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Change Section -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h3>
            
            @include('profile.partials.update-password-form')
        </div>

        <!-- Account Deletion Section -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 text-red-600">Danger Zone</h3>
            
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection
