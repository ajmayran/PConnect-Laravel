<x-app-layout>
    <div class="min-h-screen py-12 bg-gray-50">
        <div class="max-w-2xl px-6 mx-auto">
            <div class="p-8 bg-white rounded-lg shadow-md">
                <!-- Header -->
                <div class="pb-6 mb-6 border-b border-gray-200">
                    <h1 class="text-2xl font-semibold text-gray-800">Company Profile Setup</h1>
                    <p class="mt-2 text-sm text-gray-600">Please complete your company profile information.</p>
                </div>


                <!-- Form -->
                <form id="setupForm" action="{{ route('profile.updateSetup') }}" method="POST"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label for="company_profile_image" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Logo
                        </label>
                        <div class="flex items-center space-x-4">
                            <div id="imagePreviewContainer"
                                class="hidden w-24 h-24 overflow-hidden border border-gray-300 rounded-lg">
                                <img id="imagePreview" src="#" alt="Logo Preview"
                                    class="object-cover w-full h-full">
                            </div>
                            <div class="flex-1">
                                <input type="file" id="company_profile_image" name="company_profile_image"
                                    accept="image/*"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('company_profile_image') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500">Recommended size: 300x300px (Max 2MB)</p>
                                @error('company_profile_image')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Name
                        </label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                            required pattern="^[a-zA-Z0-9 ,.'\-]+$"
                            oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ,.'\-]/g, '')"
                            title="Only letters, numbers, spaces, commas, periods, apostrophes, and hyphens are allowed"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('company_name') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Only letters, numbers, spaces and basic punctuation
                            allowed</p>

                    </div>

                    <!-- Company Email -->
                    <div>
                        <label for="company_email" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Email
                        </label>
                        <input type="email" id="company_email" name="company_email" value="{{ old('company_email') }}"
                            required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('company_email') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Format: example@company.com</p>

                    </div>

                    <!-- Company Address -->
                    <div>
                        <label for="company_address" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Address
                        </label>
                        <input type="hidden" id="region" name="region" value="09">
                        <input type="hidden" id="province" name="province" value="097300">
                        <input type="hidden" id="city" name="city" value="093170">

                        <!-- Barangay Selection -->
                        <div class="mb-4">
                            <label for="barangay" class="block text-sm font-medium text-gray-700">Barangay</label>
                            <select id="barangay" name="barangay" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('barangay') border-red-500 @enderror">
                                <option value="">Select Barangay</option>
                                <!-- Options will be loaded via JavaScript -->
                            </select>
                            <div id="barangayLoadingIndicator" class="mt-2 text-sm text-gray-500">
                                <span
                                    class="inline-block w-4 h-4 mr-2 border-2 border-gray-300 rounded-full border-t-green-500 animate-spin"></span>
                                Loading barangays...
                            </div>
                            <div id="barangayError" class="hidden mt-2 text-sm text-red-500"></div>

                        </div>

                        <!-- Street Address -->
                        <div class="mb-4">
                            <label for="street" class="block text-sm font-medium text-gray-700">Street Address</label>
                            <textarea id="street" name="street" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('street') border-red-500 @enderror"
                                rows="3">{{ old('street') }}</textarea>

                        </div>
                    </div>

                    <!-- Company Phone Number -->
                    <div>
                        <label for="company_phone_number" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Phone Number
                        </label>
                        <input type="tel" id="company_phone_number" name="company_phone_number"
                            value="{{ old('company_phone_number') }}" required pattern="[0-9]+"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('company_phone_number') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Please enter 11 digit numbers</p>
                        <p id="phone_error" class="hidden mt-1 text-xs text-red-600">Phone number must be exactly 11
                            digits</p>

                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" id="submit-button"
                            class="w-full px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Complete Setup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const barangaySelect = document.getElementById('barangay');
            const barangayLoadingIndicator = document.getElementById('barangayLoadingIndicator');
            const barangayError = document.getElementById('barangayError');
            const companyEmailInput = document.getElementById('company_email');
            const phoneInput = document.getElementById('company_phone_number');
            const phoneError = document.getElementById('phone_error');
            const form = document.getElementById('setupForm');
            const imageInput = document.getElementById('company_profile_image');
            const imagePreview = document.getElementById('imagePreview');
            const previewContainer = document.getElementById('imagePreviewContainer');

            imageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    // Show the preview container
                    previewContainer.classList.remove('hidden');

                    // Create a file reader to read and display the image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                } else {
                    // Hide the preview if no file is selected
                    previewContainer.classList.add('hidden');
                }
            });

            // Store old barangay value to select after loading
            const oldBarangayValue = "{{ old('barangay') }}";

            // Email validation regex
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

            // Load barangays for Zamboanga City automatically
            const cityCode = '093170';
            fetchBarangays(cityCode);

            // Fetch barangays function
            function fetchBarangays(cityCode) {
                barangayLoadingIndicator.classList.remove('hidden');
                barangayError.classList.add('hidden');

                const url = `/barangays/${cityCode}`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        barangayLoadingIndicator.classList.add('hidden');

                        if (data.error) {
                            showBarangayError(data.message);
                            return;
                        }

                        // Clear existing options except the default one
                        while (barangaySelect.options.length > 1) {
                            barangaySelect.remove(1);
                        }

                        if (data.length === 0) {
                            // Try alternative code format as fallback
                            if (cityCode === '093170') {
                                fetchBarangays('09317');
                                return;
                            }

                            showBarangayError('No barangays found for this city');
                            return;
                        }

                        // Sort barangays alphabetically
                        data.sort((a, b) => a.name.localeCompare(b.name));

                        // Add barangay options
                        data.forEach(barangay => {
                            const option = document.createElement('option');
                            option.value = barangay.code;
                            option.textContent = barangay.name;

                            // If this is the previously selected barangay, select it again
                            if (barangay.code === oldBarangayValue) {
                                option.selected = true;
                            }

                            barangaySelect.appendChild(option);
                        });

                        // If there was a previously selected barangay but it's not in the data
                        // (which is unlikely but possible), create and select it
                        if (oldBarangayValue && !Array.from(barangaySelect.options).some(opt => opt.value ===
                                oldBarangayValue)) {
                            const option = document.createElement('option');
                            option.value = oldBarangayValue;
                            option.textContent = "Previously selected barangay";
                            option.selected = true;
                            barangaySelect.appendChild(option);
                        }
                    })
                    .catch(error => {
                        barangayLoadingIndicator.classList.add('hidden');
                        showBarangayError('Failed to load barangays. Please try again later.');
                        console.error('Error fetching barangays:', error);
                    });
            }

            function showBarangayError(message) {
                barangayError.textContent = message;
                barangayError.classList.remove('hidden');
            }

            // Phone number validation
            phoneInput.addEventListener('input', function() {
                validatePhoneNumber();
            });

            // Email validation
            companyEmailInput.addEventListener('blur', function() {
                validateEmail();
            });

            function validatePhoneNumber() {
                if (phoneInput.value.length > 0 && phoneInput.value.length !== 11) {
                    phoneError.classList.remove('hidden');
                    return false;
                } else {
                    phoneError.classList.add('hidden');
                    return true;
                }
            }

            function validateEmail() {
                if (companyEmailInput.value && !emailRegex.test(companyEmailInput.value)) {
                    return false;
                }
                return true;
            }

            // Form validation using SweetAlert
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let errors = [];

                // Validate phone number
                if (!validatePhoneNumber()) {
                    errors.push('Phone number must be exactly 11 digits');
                }

                // Validate email format
                if (!validateEmail()) {
                    errors.push('Please enter a valid email address format (example@company.com)');
                }

                // Validate barangay selection
                if (!barangaySelect.value) {
                    errors.push('Please select a barangay');
                }

                // If there are errors, show them with SweetAlert
                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cant Complete Setup',
                        html: errors.map(error => `• ${error}`).join('<br>'),
                        confirmButtonColor: '#10B981'
                    });
                } else {
                    // Show loading state
                    Swal.fire({
                        title: 'Submitting...',
                        text: 'Please wait while we save your information',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit the form
                    this.submit();
                }
            });

            // Check for success message in session
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#10B981'
                });
            @endif
        });
    </script>
</x-app-layout>
