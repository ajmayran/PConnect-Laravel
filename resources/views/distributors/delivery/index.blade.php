<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <div class="flex flex-wrap justify-between">
                <h1 class="mb-6 text-2xl font-bold text-center text-gray-800 sm:text-3xl">
                    Delivery Management

                </h1>
                <div class="flex items-center">
                    @if (request('status') === 'out_for_delivery')
                        <a href="{{ route('distributors.delivery.scan-qr-general', ['delivery' => 'general']) }}"
                            class="inline-flex items-center px-4 py-2 mr-3 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
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
            <div class="mb-4 overflow-x-auto border-b scrollbar-hide">
                <div class="flex min-w-max">
                    <a href="?status=pending"
                        class="px-2 py-2 -mb-px text-xs sm:text-sm font-semibold
            @if (request('status') === 'pending' || !request('status')) text-green-500 border-green-500 
            @else text-gray-600 border-transparent @endif 
            border-b-2">
                        Pending
                    </a>
                    <a href="?status=in_transit"
                        class="px-2 py-2 -mb-px text-xs sm:text-sm font-semibold
            @if (request('status') === 'in_transit') text-green-500 border-green-500 
            @else text-gray-600 border-transparent @endif  
            border-b-2">
                        In Transit
                    </a>
                    <a href="?status=out_for_delivery"
                        class="px-2 py-2 -mb-px text-xs sm:text-sm font-semibold
            @if (request('status') === 'out_for_delivery') text-green-500 border-green-500 
            @else text-gray-600 border-transparent @endif  
            border-b-2">
                        Out for Delivery
                    </a>
                    <a href="?status=delivered"
                        class="px-2 py-2 -mb-px text-xs sm:text-sm font-semibold
            @if (request('status') === 'delivered') text-green-500 border-green-500 
            @else text-gray-600 border-transparent @endif  
            border-b-2">
                        Delivered
                    </a>
                    <a href="?view=exchanges"
                        class="px-2 py-2 -mb-px text-xs sm:text-sm font-semibold
            @if (request('view') === 'exchanges') text-purple-500 border-purple-500 
            @else text-gray-600 border-transparent @endif  
            border-b-2">
                        Exchanges
                    </a>
                </div>
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
                                    @if ($delivery->is_exchange_delivery || $delivery->exchange_for_return_id) border-l-4 border-purple-300 @endif
                                    hover:bg-gray-100 hover:shadow-md cursor-pointer"
                                    onclick="openDeliveryModal(this)" data-delivery-id="{{ $delivery->id }}"
                                    data-delivery-status="{{ $delivery->status }}"
                                    data-order-id="{{ $delivery->order->formatted_order_id }}"
                                    data-is-multi-address="{{ $delivery->order->is_multi_address ? 'true' : 'false' }}"
                                    data-is-exchange-delivery="{{ $delivery->is_exchange_delivery || $delivery->exchange_for_return_id ? 'true' : 'false' }}"
                                    data-exchange-for-return-id="{{ $delivery->exchange_for_return_id ?? '' }}"
                                    data-order-details='@json($delivery->order->orderDetails)'>
                                    <td class="px-4 py-3">
                                        {{ $delivery->tracking_number }}
                                        @if ($delivery->is_exchange_delivery || $delivery->exchange_for_return_id)
                                            <span class="ml-1 text-xs font-medium text-purple-600">(Exchange)</span>
                                        @endif
                                        @if ($delivery->order->is_multi_address)
                                            <span class="ml-1 text-xs font-medium text-blue-600">(Multi-Address)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $delivery->order->user->first_name }}
                                        {{ $delivery->order->user->last_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($delivery->address)
                                            {{ $delivery->address->barangay_name }}, {{ $delivery->address->street ?? 'No street specified' }}
                                        @elseif ($delivery->order->is_multi_address)
                                            @php
                                                // Get the address for this specific delivery from orderItemDeliveries
                                                $itemDelivery = App\Models\OrderItemDelivery::where('delivery_id', $delivery->id)
                                                    ->with('address')
                                                    ->first();
                                                $address = $itemDelivery ? $itemDelivery->address : null;
                                            @endphp
                                            @if ($address)
                                                {{ $address->barangay_name }}, {{ $address->street ?? 'No street specified' }}
                                                <span class="ml-1 text-xs font-medium text-blue-500">(Multiple order)</span>
                                            @else
                                                <span class="text-gray-400">Address not found</span>
                                            @endif
                                        @elseif ($delivery->order->orderDetails->isNotEmpty() && $delivery->order->orderDetails->first()->delivery_address)
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
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
                        <option value="{{ $truck->id }}">
                            {{ $truck->plate_number }} 
                            @if($truck->deliveryLocations->isNotEmpty())
                                - {{ $truck->deliveryLocations->first()->barangay_name }}
                            @else
                                - No location assigned
                            @endif
                        </option>
                    @endforeach
                    </select>
                    <p class="mt-2 text-xs text-gray-500">Select a truck that operates in the delivery area</p>
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

    <script>
        function openDeliveryModal(row) {
            let orderDetails;
            let orderId;
            let deliveryId = row.getAttribute('data-delivery-id');
            let deliveryStatus = row.getAttribute('data-delivery-status');
            let isExchange = row.hasAttribute('data-is-exchange-delivery') && row.getAttribute(
                'data-is-exchange-delivery') === 'true';
            let isMultiAddress = row.hasAttribute('data-is-multi-address') && row.getAttribute('data-is-multi-address') ===
                'true';
            let exchangeForReturnId = row.getAttribute('data-exchange-for-return-id');

            try {
                orderDetails = JSON.parse(row.getAttribute('data-order-details')) || [];
                orderId = row.getAttribute('data-order-id');
            } catch (e) {
                console.error('Error parsing order details:', e);
                return;
            }

            // Set the modal title
            document.getElementById('deliveryModalTitle').innerText = isExchange ?
                `Exchange for Order ${orderId}` :
                `Order ${orderId}`;

            // Build the main content
            let modalContent = '<div class="space-y-6">';

            // Order Status Card
            modalContent += `<div class="p-3 rounded-lg ${getStatusCardClass(deliveryStatus)}">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold ${getStatusTextClass(deliveryStatus)}">
                        ${isExchange ? 'Exchange Status' : 'Delivery Status'}
                    </h3>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusBadgeClass(deliveryStatus)}">
                        ${formatStatus(deliveryStatus)}
                    </span>
                </div>
            </div>`;

            // Show multi-address badge if this is a multi-address order
            if (isMultiAddress) {
                modalContent += `<div class="p-3 border-2 border-blue-200 rounded-lg bg-blue-50">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm font-bold text-blue-800">MULTIPLE DELIVERY ADDRESSES</span>
                    </div>
                    <p class="mt-1 text-sm text-blue-700">This order is split across multiple delivery addresses.</p>
                </div>`;
            }

            // Add special exchange banner if this is an exchange delivery
            if (isExchange) {
                modalContent += `<div class="p-3 border-2 border-purple-300 rounded-lg bg-purple-50">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 4H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span class="text-sm font-bold text-purple-800">EXCHANGE DELIVERY</span>
                </div>
                <p class="mt-1 text-sm text-purple-700">This is an exchange delivery for return request #${exchangeForReturnId}.</p>
                <div class="mt-2">
                    <a href="/distributors/returns/${exchangeForReturnId}" class="text-sm font-medium text-purple-600 hover:text-purple-800">
                        View Original Return Request
                    </a>
                </div>
            </div>`;
            }

            // If this is a multi-address order, fetch and display address-specific details
            if (isMultiAddress) {
                // Display loading state
                modalContent += `<div id="multi-address-content" class="p-4 bg-white rounded-lg shadow">
                    <div class="flex justify-center">
                        <svg class="w-8 h-8 text-blue-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-2">Loading address details...</span>
                    </div>
                </div>`;

                // Fetch the specific delivery details for multi-address orders
                setTimeout(() => {
                    fetch(`/delivery/${deliveryId}/details`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                let addressContent = `<div class="p-4 bg-white rounded-lg shadow">
                            <h3 class="mb-3 text-lg font-semibold text-gray-800">Delivery Address Details</h3>
                            <div class="space-y-4">`;

                                // Display each address with its products
                                data.items.forEach((item, index) => {
                                    addressContent += `
                            <div class="p-3 border ${index % 2 === 0 ? 'bg-gray-50' : ''} rounded-lg">
                                <div class="flex items-center mb-2">
                                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <h4 class="ml-2 font-medium text-gray-900">Address ${index + 1}: ${item.address.barangay_name}, ${item.address.street || 'No street provided'}</h4>
                                </div>
                                <div class="ml-7">
                                    <p class="mb-1 text-sm font-medium text-gray-700">Products for this address:</p>
                                    <ul class="pl-5 space-y-1 text-sm list-disc">`;

                                    item.products.forEach(product => {
                                        addressContent += `
                                        <li>
                                            ${product.name} <span class="text-gray-500">(Qty: ${product.quantity})</span>
                                        </li>`;
                                    });

                                    addressContent += `
                                    </ul>
                                </div>
                            </div>`;
                                });

                                addressContent += `</div></div>`;

                                // Replace the loading placeholder with the actual content
                                const multiAddressContent = document.getElementById('multi-address-content');
                                if (multiAddressContent) {
                                    multiAddressContent.outerHTML = addressContent;
                                }
                            } else {
                                // Show error message if data fetch fails
                                const multiAddressContent = document.getElementById('multi-address-content');
                                if (multiAddressContent) {
                                    multiAddressContent.innerHTML = `
                                <div class="p-4 text-red-600 border border-red-200 rounded-lg bg-red-50">
                                    <p>Unable to load address details. Please try again.</p>
                                </div>`;
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching delivery details:', error);
                            // Show error message
                            const multiAddressContent = document.getElementById('multi-address-content');
                            if (multiAddressContent) {
                                multiAddressContent.innerHTML = `
                            <div class="p-4 text-red-600 border border-red-200 rounded-lg bg-red-50">
                                <p>Error loading address details: ${error.message}</p>
                            </div>`;
                            }
                        });
                }, 300);
            }

            // Products Section - Different display for exchanges vs regular orders
            modalContent += '<div class="overflow-hidden bg-white rounded-lg shadow">';
            modalContent += `<div class="p-4 border-b bg-gray-50">
             <h3 class="text-lg font-semibold text-gray-800">
            ${isExchange ? 'Exchange Items' : 'Products Ordered'}
              </h3>
             </div>`;
            modalContent += '<div class="overflow-x-auto">';
            modalContent += '<table class="min-w-full divide-y divide-gray-200">';
            modalContent += '<thead class="bg-gray-50"><tr>';
            modalContent += '<th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Product</th>';
            modalContent += '<th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Quantity</th>';

            // Only show price columns for regular orders
            if (!isExchange) {
                modalContent += '<th class="px-4 py-3 text-sm font-medium text-right text-gray-700">Subtotal</th>';
            }

            modalContent += '</tr></thead><tbody class="divide-y divide-gray-200">';

            let total = 0;
            orderDetails.forEach(function(detail) {
                modalContent += '<tr class="hover:bg-gray-50">';
                modalContent += '<td class="px-4 py-3">';

                // Check if product exists and handle cases where it might be missing
                if (detail.product) {
                    modalContent += `<div class="flex items-center">`;

                    // Add product image if available
                    if (detail.product.image) {
                        modalContent += `<div class="flex-shrink-0 w-10 h-10 mr-3">`;
                        modalContent +=
                            `<img class="object-cover w-10 h-10 rounded-full" src="/storage/products/${detail.product.image.split('/').pop()}" alt="${detail.product.product_name}">`;
                        modalContent += `</div>`;
                    }

                    modalContent += `<div class="ml-2">`;
                    modalContent += `<div class="font-medium text-gray-900">${detail.product.product_name}</div>`;

                    // Only show price for regular orders, not for exchanges
                    if (!isExchange) {
                        modalContent +=
                            `<div class="text-xs text-gray-500">Unit Price: ₱${parseFloat(detail.price).toFixed(2)}</div>`;
                    }

                    modalContent += `</div>`;
                    modalContent += `</div>`;
                } else {
                    modalContent += `<div class="text-gray-500">Product information not available</div>`;
                }

                modalContent += '</td>';
                // For regular items, just show the quantity
                if (isExchange && exchangeForReturnId) {
                    modalContent += `<td class="px-4 py-3">
                <div class="flex flex-col">
                    <span>${detail.quantity}</span>
                    <span id="return-qty-${detail.product.id}" class="text-xs font-medium text-purple-600">
                        <svg class="inline-block w-4 h-4 mr-1 text-purple-500 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading return quantity...
                    </span>
                </div>
            </td>`;

                    // Fetch return quantities
                    setTimeout(() => {
                        fetch(`/returns/${exchangeForReturnId}/item/${detail.product.id}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const returnQtySpan = document.getElementById(
                                        `return-qty-${detail.product.id}`);
                                    if (returnQtySpan) {
                                        returnQtySpan.innerHTML = `(to return: ${data.quantity})`;
                                    }
                                } else {
                                    throw new Error('Failed to load return quantity');
                                }
                            })
                            .catch(error => {
                                const returnQtySpan = document.getElementById(
                                    `return-qty-${detail.product.id}`);
                                if (returnQtySpan) {
                                    returnQtySpan.innerHTML = `(to return: ${detail.quantity})`;
                                }
                            });
                    }, 300);
                } else {
                    modalContent += `<td class="px-4 py-3">${detail.quantity}</td>`;
                }

                // Only show subtotal for regular orders
                if (!isExchange) {
                    modalContent +=
                        `<td class="px-4 py-3 font-medium text-right text-gray-900">₱${parseFloat(detail.subtotal).toFixed(2)}</td>`;
                    total += parseFloat(detail.subtotal);
                }

                modalContent += '</tr>';
            });

            modalContent += '</tbody>';

            // Only show footer with total for regular orders
            if (!isExchange) {
                modalContent += '<tfoot class="bg-gray-50"><tr>';
                modalContent += '<td colspan="2" class="px-4 py-3 font-medium text-right text-gray-700">Total Amount:</td>';
                modalContent += `<td class="px-4 py-3 font-bold text-right text-blue-600">₱${total.toFixed(2)}</td>`;
                modalContent += '</tr></tfoot>';
            }

            modalContent += '</table></div></div>';

            // Delivery address section
            if (orderDetails.length > 0 && orderDetails[0].delivery_address) {
                modalContent += '<div class="flex items-center gap-2 p-3 rounded-lg bg-gray-50">';
                modalContent += '<svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
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
                case 'pending':
                    return 'bg-yellow-50';
                case 'in_transit':
                    return 'bg-blue-50';
                case 'out_for_delivery':
                    return 'bg-purple-50';
                case 'delivered':
                    return 'bg-green-50';
                case 'failed':
                    return 'bg-red-50';
                default:
                    return 'bg-gray-50';
            }
        }

        function getStatusTextClass(status) {
            switch (status) {
                case 'pending':
                    return 'text-yellow-800';
                case 'in_transit':
                    return 'text-blue-800';
                case 'out_for_delivery':
                    return 'text-purple-800';
                case 'delivered':
                    return 'text-green-800';
                case 'failed':
                    return 'text-red-800';
                default:
                    return 'text-gray-800';
            }
        }

        function getStatusBadgeClass(status) {
            switch (status) {
                case 'pending':
                    return 'bg-yellow-100 text-yellow-800';
                case 'in_transit':
                    return 'bg-blue-100 text-blue-800';
                case 'out_for_delivery':
                    return 'bg-purple-100 text-purple-800';
                case 'delivered':
                    return 'bg-green-100 text-green-800';
                case 'failed':
                    return 'bg-red-100 text-red-800';
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
