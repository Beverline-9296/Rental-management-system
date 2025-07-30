<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Account Information -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 mb-6">{{ __('Account Information') }}</h2>
            
            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div class="mb-4">
                <x-input-label for="phone" :value="__('Phone Number')" />
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        +254
                    </span>
                    <x-text-input id="phone" class="block w-full rounded-l-none" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="700000000" />
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- ID/Passport Number -->
            <div class="mb-4">
                <x-input-label for="id_number" :value="__('ID/Passport Number')" />
                <x-text-input id="id_number" class="block mt-1 w-full" type="text" name="id_number" :value="old('id_number')" required />
                <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Unit Assignment -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 mb-6">{{ __('Unit Assignment') }}</h2>
            
            <!-- Property Selection -->
            <div class="mb-4">
                <x-input-label for="property_id" :value="__('Select Property')" />>
                <select id="property_id" name="property_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required onchange="loadAvailableUnits(this.value)">
                    <option value="">-- Select Property --</option>
                    @foreach(\App\Models\Property::where('landlord_id', 1)->get() as $property)
                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }} - {{ $property->address }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('property_id')" class="mt-2" />
            </div>

            <!-- Unit Selection -->
            <div class="mb-4">
                <x-input-label for="unit_id" :value="__('Select Unit')" />
                <select id="unit_id" name="unit_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">-- Select a property first --</option>
                    @if(old('property_id'))
                        @php
                            $units = \App\Models\Unit::where('property_id', old('property_id'))
                                ->where('status', 'vacant')
                                ->get();
                        @endphp
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->unit_number }} - {{ $unit->type }} (KSh {{ number_format($unit->rent_amount, 2) }})
                            </option>
                        @endforeach
                    @endif
                </select>
                <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                <p id="no-units-available" class="mt-2 text-sm text-red-600 hidden">
                    No available units found for this property. Please select another property.
                </p>
            </div>

            <!-- Move-in Date -->
            <div class="mb-4">
                <x-input-label for="move_in_date" :value="__('Move-in Date')" />
                <x-text-input id="move_in_date" class="block mt-1 w-full" type="date" name="move_in_date" :value="old('move_in_date', now()->format('Y-m-d'))" required />
                <x-input-error :messages="$errors->get('move_in_date')" class="mt-2" />
            </div>

            <!-- Lease Duration (months) -->
            <div class="mb-6">
                <x-input-label for="lease_duration" :value="__('Lease Duration (months)')" />
                <select id="lease_duration" name="lease_duration" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="6" {{ old('lease_duration') == '6' ? 'selected' : '' }}>6 Months</option>
                    <option value="12" {{ old('lease_duration', '12') == '12' ? 'selected' : '' }}>12 Months</option>
                    <option value="24" {{ old('lease_duration') == '24' ? 'selected' : '' }}>24 Months</option>
                </select>
                <x-input-error :messages="$errors->get('lease_duration')" class="mt-2" />
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-center">
            <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
            <label for="terms" class="ml-2 block text-sm text-gray-700">
                I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a> and <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-blue-600 hover:text-blue-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="px-6 py-3">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function loadAvailableUnits(propertyId) {
            const unitSelect = document.getElementById('unit_id');
            const noUnitsMessage = document.getElementById('no-units-available');
            
            if (!propertyId) {
                unitSelect.innerHTML = '<option value="">-- Select a property first --</option>';
                unitSelect.disabled = true;
                noUnitsMessage.classList.add('hidden');
                return;
            }
            
            // Show loading state
            unitSelect.innerHTML = '<option value="">Loading available units...</option>';
            unitSelect.disabled = true;
            
            // Fetch available units via AJAX
            fetch(`/api/properties/${propertyId}/available-units`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        let options = '<option value="">-- Select Unit --</option>';
                        data.forEach(unit => {
                            options += `<option value="${unit.id}">${unit.unit_number} - ${unit.type} (KSh ${parseFloat(unit.rent_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2)})</option>`;
                        });
                        unitSelect.innerHTML = options;
                        unitSelect.disabled = false;
                        noUnitsMessage.classList.add('hidden');
                    } else {
                        unitSelect.innerHTML = '<option value="">No available units</option>';
                        unitSelect.disabled = true;
                        noUnitsMessage.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading units:', error);
                    unitSelect.innerHTML = '<option value="">Error loading units. Please try again.</option>';
                    unitSelect.disabled = true;
                    noUnitsMessage.classList.add('hidden');
                });
        }
        
        // Load units if property is already selected (form validation failed)
        document.addEventListener('DOMContentLoaded', function() {
            const propertyId = document.getElementById('property_id').value;
            if (propertyId) {
                loadAvailableUnits(propertyId);
            }
        });
    </script>
</x-guest-layout>
