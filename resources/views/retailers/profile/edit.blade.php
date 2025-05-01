<x-app-layout>
    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="flex py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <x-retailer-sidebar :user="Auth::user()" /> <!-- Retailer Sidebar -->

        <div class="flex-1 space-y-6 lg:pl-8">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Profile</h1>
                <div>
                    <span class="text-sm text-gray-500">Edit your profile</span>
                </div>
            </div>
            
            <!-- Profile Completion Status -->
            @php
                $profileComplete = true;
                $missingFields = [];
                
                if (!isset($user->retailerProfile) || !$user->retailerProfile) {
                    $profileComplete = false;
                    $missingFields = ['business name', 'phone number'];
                } else {
                    if (empty($user->retailerProfile->business_name)) {
                        $profileComplete = false;
                        $missingFields[] = 'business name';
                    }
                    if (empty($user->retailerProfile->phone)) {
                        $profileComplete = false;
                        $missingFields[] = 'phone number';
                    }
                }
            @endphp

            @if (!$profileComplete)
                <div class="p-4 mb-4 border-l-4 rounded-md text-amber-700 bg-amber-100 border-amber-500">
                    <p class="font-medium">Your profile is incomplete!</p>
                    <p>Please complete the following information to enable full functionality: {{ implode(', ', $missingFields) }}.</p>
                </div>
            @endif

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">
                        {{ __('Retailer Profile Information') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-800">
                        {{ __("Update your account's retailer profile information.") }}
                    </p>
                </header>

                <form method="POST" action="{{ route('retailers.profile.update.retailer') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-col items-center gap-4 p-4 sm:p-8 md:flex-col">
                        <!-- Right Side: Image Preview & File Input -->
                        <div id="imageHolder"
                            class="flex flex-col items-center justify-center h-64 max-w-xs gap-4 p-2 overflow-hidden border rounded-md cursor-pointer md:w-1/2">
                            <img id="image_preview"
                                src="{{ $user->retailerProfile && $user->retailerProfile->profile_picture ? asset('storage/' . $user->retailerProfile->profile_picture) : asset('images/default-placeholder.png') }}"
                                alt="Image Preview" class="h-auto max-w-full">
                            <input id="profile_picture" name="profile_picture" type="file" accept="image/*"
                                class="hidden">
                        </div>
                        <p class="text-sm text-gray-700">Click image to change</p>
                        <!-- Left Side: Form Fields -->
                        <div class="self-start w-full md:w-1/2">

                            <div class="mb-4">
                                <label for="business_name" class="block text-sm font-medium text-gray-700">
                                    Business Name
                                </label>
                                <input id="business_name" name="business_name" type="text" required autofocus
                                    autocomplete="business_name"
                                    value="{{ old('business_name', $user->retailerProfile->business_name ?? '') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-300 dark:bg-white dark:text-gray-900 focus:border-gray-500 dark:focus:border-green-500 focus:ring-green-400 dark:focus:ring-green-600">
                                @error('business_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Phone
                                </label>
                                <input id="phone" name="phone" type="text" required autofocus pattern="[0-9]{11}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, ''); validatePhoneNumber(this);" 
                                    autocomplete="phone"
                                    value="{{ old('phone', $user->retailerProfile->phone ?? '') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-300 dark:bg-white dark:text-gray-900 focus:border-gray-500 dark:focus:border-green-500 focus:ring-green-400 dark:focus:ring-green-600">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p id="phone_error" class="hidden mt-1 text-sm text-red-600">Phone number must be exactly 11 digits</p>
                            </div>

                            <x-primary-button type="submit">{{ __('Save') }}</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg ">
                <div class="max-w-xl">
                    @include('retailers.profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageHolder = document.getElementById('imageHolder');
            const fileInput = document.getElementById('profile_picture');
            const imagePreview = document.getElementById('image_preview');

            // Clicking anywhere in the imageHolder triggers the file input
            imageHolder.addEventListener('click', function() {
                fileInput.click();
            });

            // Update the preview when a file is selected
            fileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        function validatePhoneNumber(input) {
            const phoneError = document.getElementById('phone_error');
            if (input.value.length > 0 && input.value.length !== 11) {
                phoneError.classList.remove('hidden');
            } else {
                phoneError.classList.add('hidden');
            }
        }

        // Add this to your DOMContentLoaded event
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                validatePhoneNumber(this);
            });
            
            // Also validate on page load
            validatePhoneNumber(phoneInput);
        }
    </script>
</x-app-layout>
<x-footer />