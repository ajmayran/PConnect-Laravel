<x-distributor2nd-layout>
    <div class="container p-4 mx-auto">
        <!-- Header with back button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('distributors.dashboard') }}"
                    class="p-2 text-gray-600 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Exchange Management</h1>
            </div>
        </div>

        <!-- Tabs and Search Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
            <!-- Tabs -->
            <div class="flex justify-between mb-4 border-b">
                <div class="flex space-x-4">
                    <a href="{{ route('distributors.exchanges.index', ['status' => 'all']) }}"
                        class="px-4 py-2 {{ $status === 'all' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        All Exchanges
                    </a>
                    <a href="{{ route('distributors.exchanges.index', ['status' => 'pending']) }}"
                        class="px-4 py-2 {{ $status === 'pending' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        Pending
                    </a>
                    <a href="{{ route('distributors.exchanges.index', ['status' => 'in_transit']) }}"
                        class="px-4 py-2 {{ $status === 'in_transit' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        In Transit
                    </a>
                    <a href="{{ route('distributors.exchanges.index', ['status' => 'out_for_delivery']) }}"
                        class="px-4 py-2 {{ $status === 'out_for_delivery' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        Out for Delivery
                    </a>
                    <a href="{{ route('distributors.exchanges.index', ['status' => 'delivered']) }}"
                        class="px-4 py-2 {{ $status === 'delivered' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        Delivered
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="flex items-center justify-start">
                <div class="relative">
                    <form action="{{ route('distributors.exchanges.index') }}" method="GET">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input type="search" name="search" placeholder="Search by order ID or customer name..."
                            value="{{ request('search') }}"
                            class="px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Exchanges Table -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Order ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Customer
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Product
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Created Date
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($exchanges as $exchange)
                        @php
                            $statusClass = match ($exchange->status) {
                                'pending' => 'text-yellow-800 bg-yellow-100',
                                'in_transit' => 'text-blue-800 bg-blue-100',
                                'out_for_delivery' => 'text-indigo-800 bg-indigo-100',
                                'delivered' => 'text-green-800 bg-green-100',
                                'failed' => 'text-red-800 bg-red-100',
                                default => 'text-gray-800 bg-gray-100',
                            };

                            $statusText = match ($exchange->status) {
                                'pending' => 'Pending',
                                'in_transit' => 'In Transit',
                                'out_for_delivery' => 'Out for Delivery',
                                'delivered' => 'Delivered',
                                'failed' => 'Failed',
                                default => 'Unknown',
                            };

                            $itemsCount = $exchange->returnRequest ? $exchange->returnRequest->items->count() : 0;
                        @endphp

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $exchange->order->formatted_order_id }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $exchange->tracking_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $exchange->order->user->first_name }} {{ $exchange->order->user->last_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if ($exchange->returnRequest && $exchange->returnRequest->items->count() > 0)
                                        @foreach ($exchange->returnRequest->items as $item)
                                            <div class="mb-1">
                                                <span
                                                    class="font-medium">{{ \Illuminate\Support\Str::limit($item->orderDetail->product->product_name ?? 'Unknown Product', 30) }}</span>
                                                <span class="text-gray-500">(x{{ $item->quantity }})</span>
                                            </div>
                                        @endforeach
                                    @else
                                        No products found
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $exchange->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="showExchangeDetails({{ $exchange->id }})"
                                    class="px-3 py-1 mr-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    Details
                                </button>

                                @if ($exchange->status === 'pending')
                                    <button onclick="assignTruck({{ $exchange->id }})"
                                        class="px-3 py-1 text-sm font-medium bg-green-600 rounded-md hover:bg-green-700">
                                        Assign Truck
                                    </button>
                                @elseif($exchange->status === 'in_transit')
                                    <button onclick="markOutForDelivery({{ $exchange->id }})"
                                        class="px-3 py-1 text-sm font-medium bg-purple-600 rounded-md hover:bg-purple-700">
                                        Out for Delivery
                                    </button>
                                @elseif($exchange->status === 'out_for_delivery')
                                    <button onclick="confirmMarkDelivered({{ $exchange->id }})"
                                        class="px-3 py-1 text-sm font-medium bg-green-600 rounded-md hover:bg-green-700">
                                        Mark Delivered
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500">
                                No exchange deliveries found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $exchanges->appends(request()->query())->links() }}
        </div>
    </div>
    </div>

    <!-- Exchange Details Modal -->
    <div id="exchangeDetailsModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden overflow-auto bg-black bg-opacity-50">
        <div class="w-full max-w-2xl p-6 mx-4 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Exchange Details</h2>
                <button onclick="closeExchangeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="exchangeDetailsContent" class="mt-4">
                <!-- Content will be loaded here -->
                <div class="flex items-center justify-center p-4">
                    <svg class="w-10 h-10 text-green-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Truck Modal -->
    <div id="assignTruckModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden overflow-auto bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Assign Truck for Exchange Delivery</h2>
                <button onclick="closeAssignTruckModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="assignTruckForm" action="" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="truck_id" class="block mb-2 text-sm font-medium text-gray-700">Select Truck</label>
                    <select id="truck_id" name="truck_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">Select a truck</option>
                        @foreach ($availableTrucks as $truck)
                            <option value="{{ $truck->id }}">
                                {{ $truck->plate_number }} {{ isset($truck->model) ? '- ' . $truck->model : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAssignTruckModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        Assign Truck
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Out for Delivery Modal -->
    <div id="outForDeliveryModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden overflow-auto bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 mx-4 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Set Estimated Delivery Date</h2>
                <button onclick="closeOutForDeliveryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="outForDeliveryForm" action="" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="estimated_delivery" class="block mb-2 text-sm font-medium text-gray-700">
                        Estimated Delivery Date
                    </label>
                    <input type="date" id="estimated_delivery" name="estimated_delivery" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500"
                        min="{{ date('Y-m-d') }}" required>
                    <p class="mt-1 text-xs text-gray-500">Select the expected delivery date</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeOutForDeliveryModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium bg-purple-600 rounded-md hover:bg-purple-700">
                        Mark Out for Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden form for Mark as Delivered -->
    <form id="markDeliveredForm" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function showExchangeDetails(id) {
            document.getElementById('exchangeDetailsModal').classList.remove('hidden');
            const contentDiv = document.getElementById('exchangeDetailsContent');

            // Show loading spinner
            contentDiv.innerHTML = `
                <div class="flex items-center justify-center p-4">
                    <svg class="w-10 h-10 text-green-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;

            // Fetch exchange details
            fetch(`{{ route('distributors.exchanges.details', ':id') }}`.replace(':id', id))
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        let html = `<div class="space-y-6">`;
                        const delivery = data.delivery;
                        const returnRequest = data.return;
                        const items = data.items;
                        const customer = data.customer;

                        // Status info
                        const statusClass = delivery.status === 'pending' ?
                            'text-yellow-700 bg-yellow-50 border-yellow-200' :
                            delivery.status === 'in_transit' ? 'text-blue-700 bg-blue-50 border-blue-200' :
                            delivery.status === 'out_for_delivery' ? 'text-indigo-700 bg-indigo-50 border-indigo-200' :
                            delivery.status === 'delivered' ? 'text-green-700 bg-green-50 border-green-200' :
                            'text-gray-700 bg-gray-50 border-gray-200';

                        const statusBadgeClass = delivery.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                            delivery.status === 'in_transit' ? 'bg-blue-100 text-blue-800' :
                            delivery.status === 'out_for_delivery' ? 'bg-indigo-100 text-indigo-800' :
                            delivery.status === 'delivered' ? 'bg-green-100 text-green-800' :
                            'bg-gray-100 text-gray-800';

                        const statusText = delivery.status === 'pending' ? 'Pending' :
                            delivery.status === 'in_transit' ? 'In Transit' :
                            delivery.status === 'out_for_delivery' ? 'Out for Delivery' :
                            delivery.status === 'delivered' ? 'Delivered' :
                            delivery.status.charAt(0).toUpperCase() + delivery.status.slice(1);

                        html += `
                            <div class="p-3 border rounded-lg ${statusClass}">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold">Exchange Status</h3>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusBadgeClass}">${statusText}</span>
                                </div>
                                <div class="mt-1 text-sm">
                                    <p>Tracking Number: ${delivery.tracking_number}</p>
                                    <p>Created Date: ${new Date(delivery.created_at).toLocaleDateString()}</p>
                                    ${delivery.estimated_delivery ? `<p>Estimated Delivery: ${new Date(delivery.estimated_delivery).toLocaleDateString()}</p>` : ''}
                                    ${delivery.delivered_at ? `<p>Delivered Date: ${new Date(delivery.delivered_at).toLocaleDateString()}</p>` : ''}
                                </div>
                            </div>`;

                        // Customer details
                        html += `
                            <div class="p-4 border rounded-lg">
                                <h3 class="mb-2 text-lg font-semibold text-gray-800">Customer Information</h3>
                                <p class="text-sm text-gray-600">Name: ${customer.first_name} ${customer.last_name}</p>
                                <p class="text-sm text-gray-600">Email: ${customer.email}</p>
                            </div>`;

                        // Items
                        html += `
                            <div class="p-4 border rounded-lg">
                                <h3 class="mb-2 text-lg font-semibold text-gray-800">Exchange Items</h3>
                                <div class="space-y-2">`;

                        items.forEach(item => {
                            const product = item.order_detail?.product || {};
                            html += `
                                <div class="flex items-center justify-between p-2 border-b">
                                    <div>
                                        <p class="font-medium">${product.product_name || 'Unknown Product'}</p>
                                        <p class="text-sm text-gray-500">Quantity: ${item.quantity}</p>
                                    </div>
                                </div>`;
                        });

                        html += `
                                </div>
                            </div>
                        </div>`;

                        contentDiv.innerHTML = html;
                    } else {
                        contentDiv.innerHTML =
                            `<div class="p-4 text-red-500">Failed to load exchange details: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching exchange details:', error);
                    contentDiv.innerHTML =
                        `<div class="p-4 text-red-500">An error occurred while fetching exchange details: ${error.message}</div>`;
                });
        }

        function closeExchangeModal() {
            document.getElementById('exchangeDetailsModal').classList.add('hidden');
        }

        function assignTruck(id) {
            const form = document.getElementById('assignTruckForm');
            
            // Fix: use named route parameter correctly
            form.action = "{{ route('distributors.exchanges.assign-truck', ':id') }}".replace(':id', id);
            
            // Show the modal
            document.getElementById('assignTruckModal').classList.remove('hidden');
            
            // Validate form before submission
            form.onsubmit = function(e) {
                e.preventDefault();
                
                const truckId = document.getElementById('truck_id').value;
                if (!truckId) {
                    alert('Please select a truck');
                    return false;
                }
                
                // Show confirmation dialog
                Swal.fire({
                    title: 'Assign Truck?',
                    text: "Are you sure you want to assign this truck for the exchange delivery?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, assign it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            };
        }

        function closeAssignTruckModal() {
            document.getElementById('assignTruckModal').classList.add('hidden');
        }
        
        function markOutForDelivery(id) {
            const form = document.getElementById('outForDeliveryForm');
            
            // Fix: use named route parameter correctly
            form.action = "{{ route('distributors.exchanges.out-for-delivery', ':id') }}".replace(':id', id);
            
            // Set default date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('estimated_delivery').value = tomorrow.toISOString().split('T')[0];
            
            // Show the modal
            document.getElementById('outForDeliveryModal').classList.remove('hidden');
            
            // Validate form before submission
            form.onsubmit = function(e) {
                e.preventDefault();
                
                const estimatedDelivery = document.getElementById('estimated_delivery').value;
                if (!estimatedDelivery) {
                    alert('Please select an estimated delivery date');
                    return false;
                }
                
                // Show confirmation dialog
                Swal.fire({
                    title: 'Mark as Out for Delivery?',
                    text: "Are you sure this exchange is out for delivery?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#8B5CF6',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, confirm!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            };
        }
        
        function closeOutForDeliveryModal() {
            document.getElementById('outForDeliveryModal').classList.add('hidden');
        }
        
        function confirmMarkDelivered(id) {
            Swal.fire({
                title: 'Mark as Delivered?',
                text: "Are you sure this exchange has been delivered to the customer?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delivered!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('markDeliveredForm');
                    form.action = "{{ route('distributors.exchanges.delivered', ':id') }}".replace(':id', id);
                    form.submit();
                }
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const exchangeModal = document.getElementById('exchangeDetailsModal');
            const assignTruckModal = document.getElementById('assignTruckModal');
            const outForDeliveryModal = document.getElementById('outForDeliveryModal');

            if (event.target === exchangeModal) {
                closeExchangeModal();
            }

            if (event.target === assignTruckModal) {
                closeAssignTruckModal();
            }
            
            if (event.target === outForDeliveryModal) {
                closeOutForDeliveryModal();
            }
        });
        
        // Add SweetAlert for form submission confirmations
        document.addEventListener('DOMContentLoaded', function() {
            // Check for success/error messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
            
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "{{ session('error') }}"
                });
            @endif
        });
    </script>
</x-distributor2nd-layout>