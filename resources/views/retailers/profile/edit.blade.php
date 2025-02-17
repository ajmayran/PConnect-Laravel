<x-app-layout>

    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <x-retailer-sidebar :user="Auth::user()" /> <!-- Retailder Side bar -->

        <div class="flex-1 space-y-6 lg:px-8"> <!-- Retailder form edit -->
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
                    <div class="flex flex-col gap-4 p-4 sm:p-8 md:flex-col items-center">
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
                        <div class="w-full md:w-1/2 self-start">
                            <div class="mb-4">
                                <label for="business_name" class="block text-sm font-medium text-gray-700">
                                    Business Name
                                </label>
                                <input id="business_name" name="business_name" type="text" required autofocus autocomplete="business_name"
                                    value="{{ old('business_name', $user->retailerProfile->business_name ?? '') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md ">
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Phone
                                </label>
                                <input id="phone" name="phone" type="text" required autofocus autocomplete="phone"
                                    value="{{ old('phone', $user->retailerProfile->phone ?? '') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md">
                            </div>

                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700">
                                    Address
                                </label>
                                <textarea id="address" name="address" required autofocus autocomplete="address" class="block w-full mt-1 border-gray-300 rounded-md">{{ old('address', $user->retailerProfile->address ?? '') }}</textarea>
                            </div>

                            <x-primary-button type="submit">{{ __('Save') }}</x-primary-button>
                        </div>


                    </div>
                </form>
            </div>

            <div class="p-4 shadow sm:p-8 bg-white sm:rounded-lg ">
                <div class="max-w-xl">
                    @include('retailers.profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('retailers.profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('retailers.profile.partials.delete-user-form')
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
    </script>
</x-app-layout>
