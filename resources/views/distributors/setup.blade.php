<x-app-layout>
    <div class="min-h-screen py-12 bg-gray-50">
        <div class="max-w-2xl px-6 mx-auto">
            <div class="p-8 bg-white rounded-lg shadow-md">
                <!-- Header -->
                <div class="pb-6 mb-6 border-b border-gray-200">
                    <h1 class="text-2xl font-semibold text-gray-800">Company Profile Setup</h1>
                    <p class="mt-2 text-sm text-gray-600">Please complete your company profile information.</p>
                </div>

                <!-- Alerts -->
                @if ($errors->any())
                    <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50">
                        <ul class="ml-4 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('profile.updateSetup') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <!-- Company Profile Image -->
                    <div>
                        <label for="company_profile_image" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Logo
                        </label>
                        <input type="file" id="company_profile_image" name="company_profile_image" accept="image/*"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Name
                        </label>
                        <input type="text" id="company_name" name="company_name" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Company Email -->
                    <div>
                        <label for="company_email" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Email
                        </label>
                        <input type="email" id="company_email" name="company_email" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
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
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
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
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Company Phone Number -->
                    <div>
                        <label for="company_phone_number" class="block mb-2 text-sm font-medium text-gray-700">
                            Company Phone Number
                        </label>
                        <input type="tel" id="company_phone_number" name="company_phone_number" required
                            pattern="[0-9]+" oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <p class="mt-1 text-xs text-gray-500">Please enter 11 digit numbers</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit"
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
                            barangaySelect.appendChild(option);
                        });
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
        });
    </script>
</x-app-layout>
