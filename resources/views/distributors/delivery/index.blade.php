<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <div class="flex flex-wrap justify-between">
                <h1 class="mb-6 text-2xl font-bold text-center text-gray-800 sm:text-3xl">Delivery Management</h1>
                <div class="flex items-center">
                    @if (request('status') === 'out_for_delivery')
                        <a href="{{ route('distributors.delivery.scan-qr-general', ['delivery' => 'general']) }}"
                            class="inline-flex items-center px-4 py-2 mr-3 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                </path>
                            </svg>
                            Scan QR
                        </a>
                    @endif
                    <a href="{{ route('distributors.trucks.index') }}"
                        class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                        <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                        </svg>
                        Trucks
                    </a>
                </div>
            </div>

            <!-- Delivery Status Tabs -->
            <div class="flex mb-4 border-b">
                <a href="?status=pending"
                    class="px-4 py-2 -mb-px font-semibold 
                          @if (request('status') === 'pending' || !request('status')) text-green-500 border-green-500 
                          @else text-gray-600 border-transparent @endif 
                          border-b-2">
                    Pending
                </a>
                <a href="?status=in_transit"
                    class="px-4 py-2 -mb-px font-semibold 
                          @if (request('status') === 'in_transit') text-green-500 border-green-500 
                          @else text-gray-600 border-transparent @endif  
                          border-b-2">
                    In Transit
                </a>
                <a href="?status=out_for_delivery"
                    class="px-4 py-2 -mb-px font-semibold 
                          @if (request('status') === 'out_for_delivery') text-green-500 border-green-500 
                          @else text-gray-600 border-transparent @endif  
                          border-b-2">
                    Out for Delivery
                </a>
                <a href="?status=delivered"
                    class="px-4 py-2 -mb-px font-semibold 
                          @if (request('status') === 'delivered') text-green-500 border-green-500 
                          @else text-gray-600 border-transparent @endif  
                          border-b-2">
                    Delivered
                </a>
            </div>

            @if ($deliveries->isEmpty())
                <div class="p-8 text-center bg-white rounded-lg shadow-sm">
                    <p class="text-gray-600 sm:text-lg">No deliveries found.</p>
                </div>
            @else
                <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Tracking Number</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Retailer</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Delivery Address</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($deliveries as $delivery)
                                <tr class="transition
                                    @if ($loop->even) bg-gray-50 hover:bg-gray-100 @endif
                                    hover:bg-gray-100 hover:shadow-md cursor-pointer"
                                    onclick="openDeliveryModal(this)" data-delivery-id="{{ $delivery->id }}"
                                    data-delivery-status="{{ $delivery->status }}"
                                    data-order-id="{{ $delivery->order->formatted_order_id }}"
                                    data-order-details='@json($delivery->order->orderDetails)'>
                                    <td class="px-4 py-3">{{ $delivery->tracking_number }}</td>
                                    <td class="px-4 py-3">
                                        {{ $delivery->order->user->first_name }} {{ $delivery->order->user->last_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($delivery->order->orderDetails->isNotEmpty())
                                            {{ $delivery->order->orderDetails->first()->delivery_address }}
                                        @else
                                            <span class="text-gray-400">No address provided</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 text-sm rounded-full 
                                            @if ($delivery->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($delivery->status === 'in_transit') bg-blue-100 text-blue-800
                                            @elseif($delivery->status === 'out_for_delivery') bg-purple-100 text-purple-800
                                            @elseif($delivery->status === 'delivered') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $deliveries->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>


    <!-- Delivery Details Modal -->
    <div id="deliveryModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-2/3">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
                <h2 class="text-xl font-bold text-gray-800" id="deliveryModalTitle">Delivery Details</h2>
                <button onclick="closeDeliveryModal()"
                    class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="deliveryModalContent" class="p-6">
                <!-- Content will be dynamically inserted -->
            </div>

            <!-- Modal Footer -->
            <div id="deliveryModalFooter" class="sticky bottom-0 flex justify-end gap-3 p-4 bg-white border-t">
                <!-- Buttons will be dynamically inserted -->
            </div>
        </div>
    </div>

    <!-- Update the assign truck modal with matching style -->
    <div id="assignTruckModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl md:w-1/3 sm:w-2/3">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Assign Truck to Delivery</h2>
                <button onclick="closeAssignTruckModal()"
                    class="p-1 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="assignTruckForm" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label for="truck_id" class="block mb-2 text-sm font-medium text-gray-700">Select Truck</label>
                    <select id="truck_id" name="truck_id"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @foreach ($availableTrucks as $truck)
                            <option value="{{ $truck->id }}">{{ $truck->plate_number }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Only available trucks are shown</p>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeAssignTruckModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Assign Truck
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update the change status modal with matching style -->
    <div id="changeStatusModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl md:w-1/3 sm:w-2/3">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Change Delivery Status</h2>
                <button onclick="closeChangeStatusModal()"
                    class="p-1 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="changeStatusForm" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-700">Update Status</label>
                    <select id="status" name="status"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="in_transit">In Transit</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeChangeStatusModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>



    <script>
        function openDeliveryModal(row) {
            let orderDetails;
            let orderId;
            let deliveryId = row.getAttribute('data-delivery-id');
            let deliveryStatus = row.getAttribute('data-delivery-status');

            try {
                orderDetails = JSON.parse(row.getAttribute('data-order-details')) || [];
                orderId = row.getAttribute('data-order-id');
            } catch (e) {
                console.error('Error parsing order details:', e);
                return;
            }

            // Set the modal title
            document.getElementById('deliveryModalTitle').innerText = 'Order' + orderId;

            // Build the main content
            let modalContent = '<div class="space-y-6">';

            // Order Status Card
            modalContent += `<div class="p-3 rounded-lg ${getStatusCardClass(deliveryStatus)}">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold ${getStatusTextClass(deliveryStatus)}">Delivery Status</h3>
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusBadgeClass(deliveryStatus)}">
                ${formatStatus(deliveryStatus)}
            </span>
        </div>
    </div>`;

            // Products Section
            modalContent += '<div class="overflow-hidden bg-white rounded-lg shadow">';
            modalContent +=
                '<div class="p-4 border-b bg-gray-50"><h3 class="text-lg font-semibold text-gray-800">Products Ordered</h3></div>';
            modalContent += '<div class="overflow-x-auto">';
            modalContent += '<table class="min-w-full divide-y divide-gray-200">';
            modalContent += '<thead class="bg-gray-50"><tr>';
            modalContent += '<th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Product</th>';
            modalContent += '<th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Quantity</th>';
            modalContent += '<th class="px-4 py-3 text-sm font-medium text-right text-gray-700">Subtotal</th>';
            modalContent += '</tr></thead><tbody class="divide-y divide-gray-200">';

            let total = 0;
            orderDetails.forEach(function(detail) {
                modalContent += '<tr class="hover:bg-gray-50">';
                modalContent += '<td class="px-4 py-3">';
                modalContent += '<div class="flex items-center gap-3">';

                if (detail.product && detail.product.image) {
                    // Extract just the filename without any path components
                    const filename = detail.product.image.split('/').pop();

                    modalContent += `<img src="/storage/products/${filename}" 
        alt="${detail.product.product_name}" 
        class="object-cover w-12 h-12 rounded-lg" 
        onerror="this.src='/img/default-product.jpg'"/>`;
                } else {
                    modalContent +=
                        '<div class="flex items-center justify-center w-12 h-12 text-gray-400 bg-gray-100 rounded-lg">' +
                        '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>' +
                        '</svg></div>';
                }

                modalContent += `<span class="font-medium text-gray-800">${detail.product.product_name}</span>`;
                modalContent += '</div></td>';
                modalContent += `<td class="px-4 py-3">${detail.quantity}</td>`;
                modalContent +=
                    `<td class="px-4 py-3 font-medium text-right text-gray-900">₱${parseFloat(detail.subtotal).toFixed(2)}</td>`;
                modalContent += '</tr>';
                total += parseFloat(detail.subtotal);
            });

            modalContent += '</tbody>';
            modalContent += '<tfoot class="bg-gray-50"><tr>';
            modalContent += '<td colspan="2" class="px-4 py-3 font-medium text-right text-gray-700">Total Amount:</td>';
            modalContent += `<td class="px-4 py-3 font-bold text-right text-blue-600">₱${total.toFixed(2)}</td>`;
            modalContent += '</tr></tfoot>';
            modalContent += '</table></div></div>';

            // Delivery address could be added here if available
            if (orderDetails.length > 0 && orderDetails[0].delivery_address) {
                modalContent += '<div class="p-4 mt-4 bg-white rounded-lg shadow">';
                modalContent += '<div class="flex items-center text-sm text-gray-600">';
                modalContent +=
                    '<svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                modalContent +=
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />';
                modalContent +=
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />';
                modalContent += '</svg>';
                modalContent += `Delivery Address: ${orderDetails[0].delivery_address}`;
                modalContent += '</div></div>';
            }

            modalContent += '</div>'; // Close the space-y-6 div

            // Update the modal content
            document.getElementById('deliveryModalContent').innerHTML = modalContent;

            // Setup the footer buttons
            let footerContent = '';

            // Add Assign Truck button for pending deliveries
            if (deliveryStatus === 'pending') {
                footerContent += `<button onclick="openAssignTruckModal(${deliveryId})" 
            class="px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
            Assign Truck
        </button>`;
            } else if (deliveryStatus === 'in_transit' || deliveryStatus === 'out_for_delivery') {
                footerContent += `<button onclick="openChangeStatusModal(${deliveryId}, '${deliveryStatus}')" 
            class="px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
            Update Status
        </button>`;
            }

            // Always add a close button
            footerContent += `<button onclick="closeDeliveryModal()" 
        class="px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-600 rounded-lg hover:bg-gray-700">
        Close
    </button>`;

            document.getElementById('deliveryModalFooter').innerHTML = footerContent;

            // Show the modal
            document.getElementById('deliveryModal').classList.remove('hidden');
        }

        // Helper functions for styling based on status
        function getStatusCardClass(status) {
            switch (status) {
                case 'delivered':
                    return 'bg-green-50 border border-green-200';
                case 'out_for_delivery':
                    return 'bg-purple-50 border border-purple-200';
                case 'in_transit':
                    return 'bg-blue-50 border border-blue-200';
                default:
                    return 'bg-yellow-50 border border-yellow-200';
            }
        }

        function getStatusTextClass(status) {
            switch (status) {
                case 'delivered':
                    return 'text-green-700';
                case 'out_for_delivery':
                    return 'text-purple-700';
                case 'in_transit':
                    return 'text-blue-700';
                default:
                    return 'text-yellow-700';
            }
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'delivered':
                    return 'bg-green-100 text-green-800';
                case 'out_for_delivery':
                    return 'bg-purple-100 text-purple-800';
                case 'in_transit':
                    return 'bg-blue-100 text-blue-800';
                default:
                    return 'bg-yellow-100 text-yellow-800';
            }
        }

        function formatStatus(status) {
            return status.replace(/_/g, ' ')
                .replace(/\b\w/g, letter => letter.toUpperCase());
        }

        function closeDeliveryModal() {
            document.getElementById('deliveryModal').classList.add('hidden');
        }

        function openAssignTruckModal(deliveryId) {
            // Close the delivery modal
            document.getElementById('deliveryModal').classList.add('hidden');

            // Set the form action URL
            const form = document.getElementById('assignTruckForm');
            form.action = `/delivery/${deliveryId}/assign-truck`;

            // Show the assign truck modal
            document.getElementById('assignTruckModal').classList.remove('hidden');
        }

        function closeAssignTruckModal() {
            document.getElementById('assignTruckModal').classList.add('hidden');
        }

        function openChangeStatusModal(deliveryId, currentStatus) {
            // Close the delivery modal
            document.getElementById('deliveryModal').classList.add('hidden');

            // Set the form action URL
            const form = document.getElementById('changeStatusForm');
            form.action = `/delivery/${deliveryId}/update-status`;

            // Pre-select the next logical status
            const statusSelect = document.getElementById('status');
            if (currentStatus === 'in_transit') {
                statusSelect.value = 'out_for_delivery';
            } else if (currentStatus === 'out_for_delivery') {
                statusSelect.value = 'delivered';
            }

            // Show the status change modal
            document.getElementById('changeStatusModal').classList.remove('hidden');
        }

        function closeChangeStatusModal() {
            document.getElementById('changeStatusModal').classList.add('hidden');
        }
    </script>
</x-distributor-layout>
