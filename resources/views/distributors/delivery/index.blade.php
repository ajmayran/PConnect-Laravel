<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <div class="flex flex-wrap justify-between">
                <h1 class="mb-6 text-2xl font-bold text-center text-gray-800 sm:text-3xl">Delivery Management</h1>
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
            @endif
        </div>
    </div>

    <div id="deliveryModal" class="fixed inset-0 z-50 hidden overflow-auto bg-gray-600 bg-opacity-50">
        <div class="relative max-w-2xl p-8 mx-auto mt-20 bg-white rounded-lg shadow-xl">
            <!-- Changed width and padding -->
            <div id="deliveryModalContent" class="overflow-x-auto"></div> <!-- Added overflow-x-auto -->
        </div>
    </div>

    <div id="assignTruckModal" class="fixed inset-0 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
        <div class="relative max-w-md p-8 mx-auto mt-20 bg-white rounded-lg shadow-xl">
            <!-- Changed width and padding -->
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Assign Truck to Delivery</h3>
                <form id="assignTruckForm" method="POST" class="mt-2">
                    @csrf
                    <select name="truck_id" class="block w-full mt-2 border-gray-300 rounded-md shadow-sm">
                        @foreach ($availableTrucks as $truck)
                            <option value="{{ $truck->id }}">{{ $truck->plate_number }}</option>
                        @endforeach
                    </select>
                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md">Assign</button>
                        <button type="button" onclick="closeAssignTruckModal()"
                            class="px-4 py-2 text-white bg-gray-500 rounded-md">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="changeStatusModal" class="fixed inset-0 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
        <div class="relative max-w-md p-8 mx-auto mt-20 bg-white rounded-lg shadow-xl">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Change Delivery Status</h3>
                <form id="changeStatusForm" method="POST" class="mt-2">
                    @csrf
                    <label class="block mb-2 font-medium">Update Status:</label>
                    <select name="status" class="block w-full mt-2 border-gray-300 rounded-md shadow-sm">
                        <option value="in_transit">In Transit</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                    </select>
                    <div class="items-center px-4 py-3 mt-4">
                        <button type="submit" class="px-4 py-2 text-white bg-green-500 rounded-md">Update</button>
                        <button type="button" onclick="closeChangeStatusModal()"
                            class="px-4 py-2 ml-2 text-white bg-gray-500 rounded-md">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        // Order Details Modal
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

            let modalContent = '<div class="p-4">';
            modalContent += '<h2 class="mb-4 text-xl font-bold">Order Details</h2>';
            modalContent += '<p class="mb-4">Order ID: ' + orderId + '</p>';
            modalContent += '<table class="min-w-full text-sm">';
            modalContent += '<thead><tr>';
            modalContent += '<th class="px-4 py-2 text-left">Product</th>';
            modalContent += '<th class="px-4 py-2 text-left">Quantity</th>';
            modalContent += '<th class="px-4 py-2 text-left">Subtotal</th>';
            modalContent += '</tr></thead><tbody>';

            let total = 0;
            orderDetails.forEach(function(detail) {
                modalContent += '<tr>';
                modalContent += '<td class="px-4 py-2">' + detail.product.product_name + '</td>';
                modalContent += '<td class="px-4 py-2">' + detail.quantity + '</td>';
                modalContent += '<td class="px-4 py-2">₱' + parseFloat(detail.subtotal).toFixed(2) + '</td>';
                modalContent += '</tr>';
                total += parseFloat(detail.subtotal);
            });

            modalContent += '</tbody></table>';
            modalContent += '<div class="mt-4 font-bold text-right">Total Amount: ₱' + total.toFixed(2) + '</div>';

            // Add Assign Truck button for pending deliveries
            if (deliveryStatus === 'pending') {
                modalContent += '<div class="flex justify-end pt-4 mt-6 border-t">';
                modalContent += '<button onclick="openAssignTruckModal(' + deliveryId +
                    ')" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">Assign Truck</button>';
                modalContent += '</div>';
            }
            // For in_transit or out_for_delivery, allow manual status changes
            else if (deliveryStatus === 'in_transit' || deliveryStatus === 'out_for_delivery') {
                modalContent += '<div class="flex justify-end pt-4 mt-6 border-t">';
                modalContent += '<button onclick="openChangeStatusModal(' + deliveryId + ', \'' + deliveryStatus +
                    '\')" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">Change Status</button>';
                modalContent += '</div>';
            }


            modalContent += '<div class="mt-4 text-right">';
            modalContent +=
                '<button onclick="closeDeliveryModal()" class="px-4 py-2 text-white bg-gray-500 rounded hover:bg-gray-700">Close</button>';
            modalContent += '</div></div>';

            document.getElementById('deliveryModalContent').innerHTML = modalContent;
            document.getElementById('deliveryModal').classList.remove('hidden');
        }


        function closeDeliveryModal() {
            document.getElementById('deliveryModal').classList.add('hidden');
        }

        function openAssignTruckModal(deliveryId) {
            closeDeliveryModal(); // Close the delivery modal first
            const form = document.getElementById('assignTruckForm');
            form.action = `/delivery/${deliveryId}/assign-truck`;
            document.getElementById('assignTruckModal').classList.remove('hidden');
        }

        function closeAssignTruckModal() {
            document.getElementById('assignTruckModal').classList.add('hidden');
            document.getElementById('deliveryModal').classList.remove('hidden'); // Show delivery modal again
        }

        function openChangeStatusModal(deliveryId, currentStatus) {
            closeDeliveryModal(); // Close the delivery modal first
            const form = document.getElementById('changeStatusForm');
            form.action = `/delivery/${deliveryId}/update-status`;
            // Set current status as default selection
            form.querySelector('select[name="status"]').value = currentStatus;
            document.getElementById('changeStatusModal').classList.remove('hidden');
        }

        function closeChangeStatusModal() {
            document.getElementById('changeStatusModal').classList.add('hidden');
            document.getElementById('deliveryModal').classList.remove('hidden'); // Optionally show delivery modal again
        }
    </script>
</x-distributor-layout>
