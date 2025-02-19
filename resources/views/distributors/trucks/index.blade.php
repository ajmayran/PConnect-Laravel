<x-distributor-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                        <div class="relative p-8 mx-auto mt-20 bg-white rounded-lg w-96">
                            <div class="mb-6">
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

                                <div class="mb-4">
                                    <label for="delivery_location" class="block mb-2 text-sm font-bold text-gray-700">
                                        Delivery Location
                                    </label>
                                    <input type="text" name="delivery_location" id="delivery_location"
                                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                        value="{{ old('delivery_location') }}">
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
                        <div class="relative p-8 mx-auto mt-20 bg-white rounded-lg w-96">
                            <div class="mb-6">
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

                                <div class="mb-4">
                                    <label for="edit_delivery_location"
                                        class="block mb-2 text-sm font-bold text-gray-700">
                                        Delivery Location
                                    </label>
                                    <input type="text" name="delivery_location" id="edit_delivery_location"
                                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
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

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2">Plate Number</th>
                                    <th class="px-4 py-2">Location</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Active Deliveries</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trucks as $truck)
                                    <tr>
                                        <td class="px-4 py-2 border">{{ $truck->plate_number }}</td>
                                        <td class="px-4 py-2 border">
                                            {{ $truck->delivery_location ?? 'Not Yet Assigned' }}</td>
                                        <td class="px-4 py-2 border">
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
                                        <td class="px-4 py-2 text-center border">{{ $truck->deliveries_count }}</td>
                                        <td class="px-4 py-2 border">
                                            <div class="flex justify-around">
                                                <a href="{{ route('distributors.trucks.show', $truck) }}"
                                                    class="text-blue-600 hover:text-blue-900">View</a>
                                                <button onclick="openEditModal({{ $truck->id }})"
                                                    class="text-green-600 hover:text-green-900">Edit</button>
                                                <button type="button"
                                                    onclick="confirmDelete({{ $truck->id }}, '{{ csrf_token() }}')"
                                                    class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>  
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addTruckModal').classList.remove('hidden');
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
                    document.getElementById('edit_delivery_location').value = data.delivery_location;
                    document.getElementById('edit_status').value = data.status;
                    form.action = `/trucks/${truckId}`;
                    modal.classList.remove('hidden');
                });
        }

        function closeEditModal() {
            document.getElementById('editTruckModal').classList.add('hidden');
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
    </script>
</x-distributor-layout>
