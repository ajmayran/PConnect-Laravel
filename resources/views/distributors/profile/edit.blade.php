<x-distributor-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="px-4 mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Company Profile</h1>
                <div>
                    <span class="text-sm text-gray-500">Edit your company profile</span>
                </div>
            </div>
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">
                        {{ __('Company Information') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-800">
                        {{ __("Update your company's profile information.") }}
                    </p>
                </header>

                <form method="POST" action="{{ route('distributors.profile.update.distributor') }}"
                    enctype="multipart/form-data" class="mt-6">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-col items-center gap-4 p-4 sm:p-8 md:flex-col">
                        <!-- Right Side: Image Preview & File Input -->
                        <div id="imageHolder"
                            class="flex flex-col items-center justify-center h-64 max-w-xs gap-4 p-2 overflow-hidden border rounded-md cursor-pointer md:w-1/2">
                            <img id="image_preview"
                                src="{{ Auth::user()->distributor->company_profile_image ? asset('storage/' . Auth::user()->distributor->company_profile_image) : asset('images/default-placeholder.png') }}"
                                alt="Image Preview" class="h-auto max-w-full">
                            <input id="company_profile_image" name="company_profile_image" type="file"
                                accept="image/*" class="hidden">
                        </div>
                        <p class="text-sm text-gray-700">Click image to change</p>
                        <!-- Left Side: Form Fields -->
                        <div class="self-start w-full md:w-1/2">
                            <div class="mb-4">
                                <label for="company_name" class="block text-sm font-medium text-gray-700">
                                    Company Name
                                </label>
                                <input id="company_name" name="company_name" type="text" required autofocus
                                    autocomplete="company_name"
                                    value="{{ old('company_name', Auth::user()->distributor->company_name ?? '') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-300 dark:bg-white dark:text-gray-900 focus:border-gray-500 dark:focus:border-green-500 focus:ring-green-400 dark:focus:ring-green-600">
                            </div>

                            <div class="mb-4">
                                <label for="company_email" class="block text-sm font-medium text-gray-700">
                                    Company Email
                                </label>
                                <input id="company_email" name="company_email" type="email" required autofocus
                                    autocomplete="company_email"
                                    value="{{ old('company_email', Auth::user()->distributor->company_email ?? '') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-300 dark:bg-white dark:text-gray-900 focus:border-gray-500 dark:focus:border-green-500 focus:ring-green-400 dark:focus:ring-green-600">
                            </div>

                            <div class="mb-4">
                                <label for="company_phone_number" class="block text-sm font-medium text-gray-700">
                                    Company Phone
                                </label>
                                <input id="company_phone_number" name="company_phone_number" type="tel" required
                                    autofocus autocomplete="company_phone_number" pattern="[0-9]+" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    value="{{ old('company_phone_number', Auth::user()->distributor->company_phone_number ?? '') }}"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-300 dark:bg-white dark:text-gray-900 focus:border-gray-500 dark:focus:border-green-500 focus:ring-green-400 dark:focus:ring-green-600">
                                <p class="mt-1 text-xs text-gray-500">Please enter 11 digit numbers</p>
                            </div>

                            <div class="mb-4">
                                <label for="address" class="block mb-2 text-sm font-medium text-gray-700">
                                    Address
                                </label>
                                <input type="hidden" id="region" name="region" value="09">
                                <input type="hidden" id="province" name="province" value="097300">
                                <input type="hidden" id="city" name="city" value="093170">

                                <div class="mb-4">
                                    <label for="barangay"
                                        class="block text-sm font-medium text-gray-700">Barangay</label>

                                    <!-- Current barangay display section -->
                                    <div id="barangayDisplaySection"
                                        class="flex items-center mt-1 {{ Auth::user()->distributor && Auth::user()->distributor->barangay ? '' : 'hidden' }}">
                                        <span id="currentBarangayDisplay"
                                            class="inline-block px-3 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md">
                                            {{ Auth::user()->distributor && Auth::user()->distributor->barangay ? Auth::user()->distributor->barangay : 'Loading...' }}
                                        </span>
                                        <button type="button" id="changeBarangayBtn"
                                            class="px-3 py-1 ml-3 text-sm text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            Change
                                        </button>
                                    </div>

                                    <!-- Select dropdown -->
                                    <div id="barangaySelectSection"
                                        class="{{ Auth::user()->distributor && Auth::user()->distributor->barangay ? 'hidden' : '' }}">
                                        <select id="barangay" name="barangay"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-300 dark:bg-white dark:text-gray-900 focus:border-gray-500 dark:focus:border-green-500 focus:ring-green-400 dark:focus:ring-green-600">
                                            <option value="">Select Barangay</option>
                                        </select>

                                        <!-- Cancel button (only shown when changing) -->
                                        <button type="button" id="cancelBarangayBtn"
                                            class="hidden px-3 py-1 mt-2 text-sm text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            Cancel
                                        </button>
                                    </div>

                                    <!-- Hidden input to store the actual barangay code -->
                                    <input type="hidden" id="barangayCode" name="barangay"
                                        value="{{ Auth::user()->distributor ? Auth::user()->distributor->barangay ?? '' : '' }}">
                                </div>

                                <div class="mb-4">
                                    <label for="street" class="block text-sm font-medium text-gray-700">Street
                                        Address</label>
                                    <textarea id="street" name="street" autocomplete="street"
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:border-gray-300 dark:bg-white dark:text-gray-900 focus:border-gray-500 dark:focus:border-green-500 focus:ring-green-400 dark:focus:ring-green-600">{{ old('street', Auth::user()->distributor->street ?? '') }}</textarea>
                                </div>
                            </div>

                            <x-primary-button type="submit">{{ __('Save') }}</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="p-4 mt-6 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-900">
                            {{ __('Personal Information') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-800">
                            {{ __("Update your account's personal information.") }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('distributors.profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <x-text-input id="first_name" name="first_name" type="text" class="block w-full mt-1"
                                :value="old('first_name', Auth::user()->first_name)" required autofocus autocomplete="first_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                        </div>

                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <x-text-input id="last_name" name="last_name" type="text" class="block w-full mt-1"
                                :value="old('last_name', Auth::user()->last_name)" required autofocus autocomplete="last_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>

                        <div>
                            <x-input-label for="middle_name" :value="__('Middle Name')" />
                            <x-text-input id="middle_name" name="middle_name" type="text"
                                class="block w-full mt-1" :value="old('middle_name', Auth::user()->middle_name)" required autofocus
                                autocomplete="middle_name" />
                            <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="block w-full mt-1"
                                :value="old('email', Auth::user()->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>

                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 mt-6 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('distributors.profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 mt-6 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="max-w-xl">
                    @include('distributors.profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageHolder = document.getElementById('imageHolder');
            const fileInput = document.getElementById('company_profile_image');
            const imagePreview = document.getElementById('image_preview');
            const barangaySelect = document.getElementById('barangay');
            const changeBarangayBtn = document.getElementById('changeBarangayBtn');
            const cancelBarangayBtn = document.getElementById('cancelBarangayBtn');
            const barangayDisplaySection = document.getElementById('barangayDisplaySection');
            const barangaySelectSection = document.getElementById('barangaySelectSection');
            const currentBarangayDisplay = document.getElementById('currentBarangayDisplay');
            const barangayCodeInput = document.getElementById('barangayCode');
            const savedBarangay =
                '{{ Auth::user()->distributor ? Auth::user()->distributor->barangay ?? '' : '' }}';

            changeBarangayBtn.addEventListener('click', function() {
                barangayDisplaySection.classList.add('hidden');
                barangaySelectSection.classList.remove('hidden');
                cancelBarangayBtn.classList.remove('hidden');

                // Pre-select the current barangay in the dropdown
                const currentBarangayCode = barangayCodeInput.value;
                if (currentBarangayCode) {
                    barangaySelect.value = currentBarangayCode;
                }
            });

            // Handle canceling the change
            cancelBarangayBtn.addEventListener('click', function() {
                barangayDisplaySection.classList.remove('hidden');
                barangaySelectSection.classList.add('hidden');
                cancelBarangayBtn.classList.add('hidden');
            });

            // Update both the display and hidden input when selecting a new barangay
            barangaySelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                if (this.value) {
                    barangayCodeInput.value = this.value;
                    currentBarangayDisplay.textContent = selectedOption.textContent;

                    // Automatically switch back to display view when a selection is made
                    barangayDisplaySection.classList.remove('hidden');
                    barangaySelectSection.classList.add('hidden');
                }
            });

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

            // Helper function to clear and add default option to dropdown
            function clearDropdown(dropdown, defaultText) {
                dropdown.innerHTML = '';
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = defaultText;
                dropdown.appendChild(defaultOption);
            }

            // Load barangays for Zamboanga City automatically
            // Updated city code to match what we used in our seeder
            const cityCode = '093170';
            console.log('Loading barangays for city code:', cityCode);
            fetchBarangays(cityCode);

            // Fetch barangays function
            function fetchBarangays(cityCode) {
                const url = `/barangays/${cityCode}`;
                console.log('Fetching barangays from:', url);

                return fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            console.error('Server returned error status:', response.status);
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Barangays data received:', data);

                        if (data.error) {
                            console.error('Error in response:', data.message);
                            // Display error in the UI
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'text-red-500 text-sm mt-1';
                            errorDiv.textContent = data.message;
                            barangaySelect.parentNode.appendChild(errorDiv);
                            return;
                        }

                        // Clear and populate dropdown
                        clearDropdown(barangaySelect, 'Select Barangay');

                        if (data.length === 0) {
                            console.log('No barangays found');
                            // Try alternative code format as fallback
                            if (cityCode === '093170') {
                                console.log('Trying alternative code format for Zamboanga City');
                                fetchBarangays('09317');
                                return;
                            }

                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No barangays available';
                            barangaySelect.appendChild(option);
                            return;
                        }

                        data.forEach(barangay => {
                            const option = document.createElement('option');
                            option.value = barangay.code;
                            option.textContent = barangay.name;
                            barangaySelect.appendChild(option);
                        });
                        console.log(`Added ${data.length} barangay options to dropdown`);

                        // If there's a previously saved value, select it
                        const savedBarangay =
                            '{{ old('barangay', Auth::user()->distributor->barangay ?? '') }}';
                        console.log('Saved barangay code:', savedBarangay);

                        if (savedBarangay) {
                            // Find the matching barangay name for the saved code
                            const selectedOption = Array.from(barangaySelect.options).find(opt => opt.value ===
                                savedBarangay);
                            if (selectedOption) {
                                currentBarangayDisplay.textContent = selectedOption.textContent;

                                // Make sure the display section is visible if we have a saved barangay
                                barangayDisplaySection.classList.remove('hidden');
                                barangaySelectSection.classList.add('hidden');
                            }
                        } else {
                            // If no saved barangay, show the selection section instead
                            barangayDisplaySection.classList.add('hidden');
                            barangaySelectSection.classList.remove('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching barangays:', error);
                        // Show error message in the UI
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'text-red-500 text-sm mt-1';
                        errorDiv.textContent = 'Failed to load barangays. Please try again later.';
                        barangaySelect.parentNode.appendChild(errorDiv);
                    });
            }
        });
    </script>
</x-distributor-layout>
