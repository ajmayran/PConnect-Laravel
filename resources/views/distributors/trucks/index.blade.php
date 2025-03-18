<x-distributor-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <a href="{{ route('distributors.delivery.index') }}"
                class="inline-block px-4 py-2 mb-4 text-sm font-medium text-gray-700 hover:text-green-400">← Back to
                Deliveries</a>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold">Trucks Management</h2>
                        <button onclick="openAddModal()"
                            class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                            Add New Truck
                        </button>
                    </div>

                    <!-- Add Truck Modal -->
                    <div id="addTruckModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50">
                        <div class="relative p-8 mx-auto mt-20 bg-white rounded-lg w-96 md:w-[500px]">
                            <div class="relative mb-6">
                                <h3 class="text-xl font-bold">Add New Truck</h3>
                                <button onclick="closeAddModal()"
                                    class="absolute text-gray-600 top-4 right-4 hover:text-gray-800">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <form action="{{ route('distributors.trucks.store') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="plate_number" class="block mb-2 text-sm font-bold text-gray-700">
                                        Plate Number
                                    </label>
                                    <input type="text" name="plate_number" id="plate_number"
                                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline @error('plate_number') border-red-500 @enderror"
                                        value="{{ old('plate_number') }}" required>
                                    @error('plate_number')
                                        <p class="mt-1 text-xs italic text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Location Fields -->
                                <div class="mb-4">
                                    <label class="block mb-2 text-sm font-bold text-gray-700">Delivery Locations</label>

                                    <div id="locations-container">
                                        <div class="p-3 mb-3 border border-gray-300 rounded-md location-item">
                                            <!-- Hidden fields for Region, Province, and City (Zamboanga City) -->
                                            <input type="hidden" name="locations[0][region]" value="09">
                                            <input type="hidden" name="locations[0][province]" value="097300">
                                            <input type="hidden" name="locations[0][city]" value="093170">

                                            <!-- Barangay Selection -->
                                            <div class="mb-3">
                                                <label class="block mb-1 text-sm text-gray-700">Barangay</label>
                                                <select name="locations[0][barangay]" required
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow appearance-none focus:outline-none focus:shadow-outline location-barangay">
                                                    <option value="">Select Barangay</option>
                                                    <!-- Options will be loaded via JavaScript -->
                                                </select>
                                                <div class="mt-2 text-xs text-gray-500 location-loading-indicator">
                                                    <span
                                                        class="inline-block w-3 h-3 mr-1 border-2 border-gray-300 rounded-full border-t-blue-500 animate-spin"></span>
                                                    Loading barangays...
                                                </div>
                                            </div>

                                            <!-- Street Address -->
                                            <div>
                                                <label class="block mb-1 text-sm text-gray-700">Street Address</label>
                                                <textarea name="locations[0][street]" 
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow appearance-none focus:outline-none focus:shadow-outline location-street"
                                                    rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" id="add-location-btn"
                                        class="flex items-center px-3 py-2 mt-2 text-xs font-medium text-white bg-blue-500 rounded hover:bg-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Add Another Location
                                    </button>
                                </div>

                                <div class="flex items-center justify-end gap-4">
                                    <button type="button" onclick="closeAddModal()"
                                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
                                        Add Truck
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Edit Truck Modal -->
                    <div id="editTruckModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50">
                        <div class="relative p-8 mx-auto mt-20 bg-white rounded-lg w-96 md:w-[500px]">
                            <div class="relative mb-6">
                                <h3 class="text-xl font-bold">Edit Truck</h3>
                                <button onclick="closeEditModal()"
                                    class="absolute text-gray-600 top-4 right-4 hover:text-gray-800">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <form id="editTruckForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label for="edit_plate_number" class="block mb-2 text-sm font-bold text-gray-700">
                                        Plate Number
                                    </label>
                                    <input type="text" name="plate_number" id="edit_plate_number"
                                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                        required>
                                </div>

                                <!-- Location Fields -->
                                <div class="mb-4">
                                    <label class="block mb-2 text-sm font-bold text-gray-700">Delivery Locations</label>

                                    <div id="edit-locations-container">
                                        <!-- Locations will be loaded dynamically via JavaScript -->
                                    </div>

                                    <button type="button" id="edit-add-location-btn"
                                        class="flex items-center px-3 py-2 mt-2 text-xs font-medium text-white bg-blue-500 rounded hover:bg-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Add Another Location
                                    </button>
                                </div>

                                <div class="mb-4">
                                    <label for="edit_status" class="block mb-2 text-sm font-bold text-gray-700">
                                        Status
                                    </label>
                                    <select name="status" id="edit_status"
                                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                                        <option value="available">Available</option>
                                        <option value="on_delivery">On Delivery</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                </div>

                                <div class="flex items-center justify-end gap-4">
                                    <button type="button" onclick="closeEditModal()"
                                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
                                        Update Truck
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Trucks Table -->
                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2">Plate Number</th>
                                    <th class="px-4 py-2">Location</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Active Deliveries</th>
                                    <th class="w-10 px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trucks as $truck)
                                    <tr class="p-2 border-b border-gray-200 cursor-pointer hover:bg-gray-50"
                                        onclick="viewTruckDetails({{ $truck->id }}, event)">
                                        <td class="px-4 py-2 text-center">{{ $truck->plate_number }}</td>

                                        <td class="px-4 py-2">
                                            @if ($truck->deliveryLocations && $truck->deliveryLocations->count() > 0)
                                                @php $primaryLocation = $truck->deliveryLocations->first() @endphp

                                                <!-- Primary Location - Prominently Displayed -->
                                                <div class="font-medium">
                                                    {{ $primaryLocation->barangayName ?? 'Unknown Barangay' }}</div>
                                                @if ($primaryLocation->street)
                                                    <div class="text-xs text-gray-600">{{ $primaryLocation->street }}
                                                    </div>
                                                @endif

                                                <!-- Additional Locations Indicator -->
                                                @if ($truck->deliveryLocations->count() > 1)
                                                    <div class="flex items-center mt-2">
                                                        <span
                                                            class="inline-flex items-center justify-center w-5 h-5 mr-2 text-xs font-semibold text-white bg-blue-600 rounded-full">
                                                            {{ $truck->deliveryLocations->count() - 1 }}
                                                        </span>
                                                        <span
                                                            class="text-xs font-medium text-blue-600 cursor-pointer hover:text-blue-800"
                                                            onclick="viewAllLocations({{ $truck->id }})">
                                                            {{ $truck->deliveryLocations->count() - 1 == 1 ? 'more location' : 'more locations' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-gray-500">Not Yet Assigned</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-2 text-center">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $truck->status === 'available'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($truck->status === 'on_delivery'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $truck->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center">{{ $truck->deliveries_count }}</td>
                                        <td class="relative px-4 py-2 text-right">
                                            <div class="relative dropdown" onclick="event.stopPropagation()">
                                                <button class="p-1 rounded-full dropdown-toggle hover:bg-gray-200"
                                                    onclick="toggleDropdown({{ $truck->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="w-5 h-5 text-gray-500" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="dropdown-container">
                            @foreach ($trucks as $truck)
                                <div id="dropdown-menu-{{ $truck->id }}"
                                    class="fixed z-50 hidden w-48 mt-2 bg-white rounded-md shadow-lg dropdown-menu ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <button onclick="openEditModal({{ $truck->id }})"
                                            class="w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                            Edit
                                        </button>
                                        <button onclick="confirmDelete({{ $truck->id }}, '{{ csrf_token() }}')"
                                            class="w-full px-4 py-2 text-sm text-left text-red-600 hover:bg-gray-100">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown(truckId) {
            const dropdown = document.getElementById(`dropdown-menu-${truckId}`);

            // Toggle visibility
            const isHidden = dropdown.classList.contains('hidden');

            // Hide all dropdowns first
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });

            if (isHidden) {
                dropdown.classList.remove('hidden');

                // Position the dropdown relative to the button
                const button = document.querySelector(`[onclick="toggleDropdown(${truckId})"]`);
                const buttonRect = button.getBoundingClientRect();

                // Set position
                dropdown.style.top = `${buttonRect.bottom + window.scrollY}px`;
                dropdown.style.left = `${buttonRect.right - dropdown.offsetWidth + window.scrollX}px`;

                // Check if dropdown goes beyond viewport bottom
                const dropdownRect = dropdown.getBoundingClientRect();
                const viewportHeight = window.innerHeight;

                if (dropdownRect.bottom > viewportHeight) {
                    // Position above button instead
                    dropdown.style.top = `${buttonRect.top - dropdown.offsetHeight + window.scrollY}px`;
                }
            }
        }

        // Close dropdowns when clicking elsewhere
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown-toggle')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // Function to handle row clicks (view truck details)
        function viewTruckDetails(truckId, event) {
            // Don't navigate if clicked on dropdown or the "more locations" text
            if (event.target.closest('.dropdown') ||
                event.target.closest('.text-blue-600')) {
                return;
            }

            window.location.href = `{{ route('distributors.trucks.show', '') }}/${truckId}`;
        }

        function openAddModal() {
            document.getElementById('addTruckModal').classList.remove('hidden');
            loadBarangays('barangay', 'barangayLoadingIndicator');
        }

        function closeAddModal() {
            document.getElementById('addTruckModal').classList.add('hidden');
        }

        function openEditModal(truckId) {
            const modal = document.getElementById('editTruckModal');
            const form = document.getElementById('editTruckForm');

            // Fetch truck data and populate form
            fetch(`/trucks/${truckId}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_plate_number').value = data.plate_number;

                    // Load barangays first, then set the selected value
                    loadBarangays('edit_barangay', 'editBarangayLoadingIndicator').then(() => {
                        if (data.location) {
                            document.getElementById('edit_barangay').value = data.location.barangay || '';
                            document.getElementById('edit_street').value = data.location.street || '';
                        } else {
                            document.getElementById('edit_barangay').value = '';
                            document.getElementById('edit_street').value = '';
                        }
                    });

                    document.getElementById('edit_status').value = data.status;
                    form.action = `/trucks/${truckId}`;
                    modal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching truck data:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to load truck data. Please try again.',
                        icon: 'error'
                    });
                });
        }

        function closeEditModal() {
            document.getElementById('editTruckModal').classList.add('hidden');
        }

        // Function to load barangays
        function loadBarangays(selectId, loadingIndicatorId) {
            const barangaySelect = document.getElementById(selectId);
            const loadingIndicator = document.getElementById(loadingIndicatorId);

            loadingIndicator.classList.remove('hidden');

            // Keep the first option (placeholder)
            const firstOption = barangaySelect.options[0];
            barangaySelect.innerHTML = '';
            barangaySelect.appendChild(firstOption);

            return fetch('/barangays/093170') // Zamboanga City code
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    loadingIndicator.classList.add('hidden');

                    if (data.error) {
                        console.error('Error:', data.message);
                        return;
                    }

                    // Sort barangays alphabetically
                    data.sort((a, b) => a.name.localeCompare(b.name));

                    // Add options
                    data.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay.code;
                        option.textContent = barangay.name;
                        barangaySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    loadingIndicator.classList.add('hidden');
                    console.error('Error loading barangays:', error);
                });
        }

        // Close modals when clicking outside
        document.getElementById('addTruckModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });

        document.getElementById('editTruckModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Show validation errors in modal if they exist
        @if ($errors->any())
            window.addEventListener('load', function() {
                openAddModal();
            });
        @endif

        // Close modals on escape key press
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });

        function confirmDelete(truckId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form programmatically
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/trucks/${truckId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);

                    form.submit();
                }
            });
        }
        // Load barangays if there are validation errors
        @if ($errors->any())
            window.addEventListener('load', function() {
                openAddModal();
            });
        @endif

        let locationCounter = 1;
        let editLocationCounter = 1;

        // Add a new location field
        document.getElementById('add-location-btn').addEventListener('click', function() {
            const container = document.getElementById('locations-container');
            const newLocation = document.createElement('div');
            newLocation.className = 'p-3 mb-3 border border-gray-300 rounded-md location-item';

            newLocation.innerHTML = `
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium">Additional Location</span>
                <button type="button" class="text-red-600 remove-location-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <input type="hidden" name="locations[${locationCounter}][region]" value="09">
            <input type="hidden" name="locations[${locationCounter}][province]" value="097300">
            <input type="hidden" name="locations[${locationCounter}][city]" value="093170">
            
            <div class="mb-3">
                <label class="block mb-1 text-sm text-gray-700">Barangay</label>
                <select name="locations[${locationCounter}][barangay]" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow appearance-none focus:outline-none focus:shadow-outline location-barangay">
                    <option value="">Select Barangay</option>
                </select>
                <div class="mt-2 text-xs text-gray-500 location-loading-indicator">
                    <span class="inline-block w-3 h-3 mr-1 border-2 border-gray-300 rounded-full border-t-blue-500 animate-spin"></span>
                    Loading barangays...
                </div>
            </div>
            
            <div>
                <label class="block mb-1 text-sm text-gray-700">Street Address</label>
                <textarea name="locations[${locationCounter}][street]"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow appearance-none focus:outline-none focus:shadow-outline location-street"
                    rows="2"></textarea>
            </div>
        `;

            container.appendChild(newLocation);

            // Load barangays for this new location
            const newSelect = newLocation.querySelector('.location-barangay');
            const newLoadingIndicator = newLocation.querySelector('.location-loading-indicator');
            loadBarangaysForSelect(newSelect, newLoadingIndicator);

            // Add event listener for the remove button
            newLocation.querySelector('.remove-location-btn').addEventListener('click', function() {
                container.removeChild(newLocation);
            });

            locationCounter++;
        });

        // Add a new location field in edit form
        document.getElementById('edit-add-location-btn').addEventListener('click', function() {
            const container = document.getElementById('edit-locations-container');
            const newLocation = document.createElement('div');
            newLocation.className = 'p-3 mb-3 border border-gray-300 rounded-md location-item';

            newLocation.innerHTML = `
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium">Additional Location</span>
                <button type="button" class="text-red-600 remove-location-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <input type="hidden" name="locations[${editLocationCounter}][region]" value="09">
            <input type="hidden" name="locations[${editLocationCounter}][province]" value="097300">
            <input type="hidden" name="locations[${editLocationCounter}][city]" value="093170">
            
            <div class="mb-3">
                <label class="block mb-1 text-sm text-gray-700">Barangay</label>
                <select name="locations[${editLocationCounter}][barangay]" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow appearance-none focus:outline-none focus:shadow-outline location-barangay">
                    <option value="">Select Barangay</option>
                </select>
                <div class="mt-2 text-xs text-gray-500 location-loading-indicator">
                    <span class="inline-block w-3 h-3 mr-1 border-2 border-gray-300 rounded-full border-t-blue-500 animate-spin"></span>
                    Loading barangays...
                </div>
            </div>
            
            <div>
                <label class="block mb-1 text-sm text-gray-700">Street Address</label>
                <textarea name="locations[${editLocationCounter}][street]" 
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow appearance-none focus:outline-none focus:shadow-outline location-street"
                    rows="2"></textarea>
            </div>
        `;

            container.appendChild(newLocation);

            // Load barangays for this new location
            const newSelect = newLocation.querySelector('.location-barangay');
            const newLoadingIndicator = newLocation.querySelector('.location-loading-indicator');
            loadBarangaysForSelect(newSelect, newLoadingIndicator);

            // Add event listener for the remove button
            newLocation.querySelector('.remove-location-btn').addEventListener('click', function() {
                container.removeChild(newLocation);
            });

            editLocationCounter++;
        });

        // Function to load barangays for a specific select element
        function loadBarangaysForSelect(selectElement, loadingIndicator) {
            loadingIndicator.classList.remove('hidden');

            // Keep the first option (placeholder)
            const firstOption = selectElement.options[0];
            selectElement.innerHTML = '';
            selectElement.appendChild(firstOption);

            return fetch('/barangays/093170') // Zamboanga City code
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    loadingIndicator.classList.add('hidden');

                    if (data.error) {
                        console.error('Error:', data.message);
                        return;
                    }

                    // Sort barangays alphabetically
                    data.sort((a, b) => a.name.localeCompare(b.name));

                    // Add options
                    data.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay.code;
                        option.textContent = barangay.name;
                        selectElement.appendChild(option);
                    });
                })
                .catch(error => {
                    loadingIndicator.classList.add('hidden');
                    console.error('Error loading barangays:', error);
                });
        }

        // Load barangays for the first location when opening the add modal
        function openAddModal() {
            document.getElementById('addTruckModal').classList.remove('hidden');
            const firstSelect = document.querySelector('.location-barangay');
            const firstLoadingIndicator = document.querySelector('.location-loading-indicator');
            loadBarangaysForSelect(firstSelect, firstLoadingIndicator);
        }

        // Modify the openEditModal function to handle multiple locations
        function openEditModal(truckId) {
            const modal = document.getElementById('editTruckModal');
            const form = document.getElementById('editTruckForm');
            const locationsContainer = document.getElementById('edit-locations-container');

            // Clear existing locations
            locationsContainer.innerHTML = '';
            editLocationCounter = 0;

            // Fetch truck data with all locations
            fetch(`/trucks/${truckId}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_plate_number').value = data.plate_number;
                    document.getElementById('edit_status').value = data.status;

                    // Add locations
                    if (data.locations && data.locations.length > 0) {
                        data.locations.forEach((location, index) => {
                            addLocationToEditForm(location, index);
                        });
                    } else {
                        // Add at least one empty location field
                        addLocationToEditForm(null, 0);
                    }

                    form.action = `/trucks/${truckId}`;
                    modal.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching truck data:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to load truck data. Please try again.',
                        icon: 'error'
                    });
                });
        }

        // Helper function to add a location to the edit form
        function addLocationToEditForm(location, index) {
            const container = document.getElementById('edit-locations-container');
            const locationDiv = document.createElement('div');
            locationDiv.className = 'p-3 mb-3 border border-gray-300 rounded-md location-item';

            const isFirstLocation = index === 0;
            locationDiv.innerHTML = `
            <div class="flex justify-between mb-2">
                <span class="text-sm font-medium">${isFirstLocation ? 'Primary Location' : 'Additional Location'}</span>
                ${!isFirstLocation ? `
                                                <button type="button" class="text-red-600 remove-location-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                ` : ''}
            </div>
            <input type="hidden" name="locations[${index}][id]" value="${location ? location.id || '' : ''}">
            <input type="hidden" name="locations[${index}][region]" value="09">
            <input type="hidden" name="locations[${index}][province]" value="097300">
            <input type="hidden" name="locations[${index}][city]" value="093170">
            
            <div class="mb-3">
                <label class="block mb-1 text-sm text-gray-700">Barangay</label>
                <select name="locations[${index}][barangay]" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow appearance-none focus:outline-none focus:shadow-outline location-barangay-edit-${index}">
                    <option value="">Select Barangay</option>
                </select>
                <div class="mt-2 text-xs text-gray-500 location-loading-indicator-edit-${index}">
                    <span class="inline-block w-3 h-3 mr-1 border-2 border-gray-300 rounded-full border-t-blue-500 animate-spin"></span>
                    Loading barangays...
                </div>
            </div>
            
            <div>
                <label class="block mb-1 text-sm text-gray-700">Street Address</label>
                <textarea name="locations[${index}][street]" 
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                    rows="2">${location ? location.street || '' : ''}</textarea>
            </div>
        `;

            container.appendChild(locationDiv);

            // Add event listener for remove button
            if (!isFirstLocation) {
                locationDiv.querySelector('.remove-location-btn').addEventListener('click', function() {
                    container.removeChild(locationDiv);
                });
            }

            // Load barangays and set selected value
            const selectElement = locationDiv.querySelector(`.location-barangay-edit-${index}`);
            const loadingIndicator = locationDiv.querySelector(`.location-loading-indicator-edit-${index}`);

            loadBarangaysForSelect(selectElement, loadingIndicator).then(() => {
                if (location && location.barangay) {
                    selectElement.value = location.barangay;
                }
            });

            // Increment counter for next location
            editLocationCounter = index + 1;
        }

        // Function to show all locations for a truck in a modal
        function viewAllLocations(truckId) {
            fetch(`/trucks/${truckId}/locations`)
                .then(response => response.json())
                .then(data => {
                    let locationsHTML = '';

                    data.forEach(location => {
                        locationsHTML += `
                        <div class="p-3 mb-3 border border-gray-200 rounded-md">
                            <div class="font-medium">${location.barangay_name || 'Unknown Barangay'}</div>
                            <div class="text-sm text-gray-600">${location.street || ''}</div>
                        </div>
                    `;
                    });

                    Swal.fire({
                        title: 'All Delivery Locations',
                        html: `<div class="mt-4">${locationsHTML}</div>`,
                        width: 600,
                        confirmButtonText: 'Close'
                    });
                })
                .catch(error => {
                    console.error('Error fetching locations:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to load locations. Please try again.',
                        icon: 'error'
                    });
                });
        }
    </script>
</x-distributor-layout>
