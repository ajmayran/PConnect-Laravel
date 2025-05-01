<x-distributor-layout>
    <style>
        /* This ensures toggle stays within its container during scroll */
        #navbar {
            position: sticky;
            z-index: 40;
        }

        .container {
            position: relative;
            z-index: 30;
        }
    </style>

    <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
        <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
    </span>

    <div class="container p-4 mx-auto">
        <div class="relative flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-left text-gray-800 sm:text-3xl">Orders Management</h1>

            <!-- Order Acceptance Toggle -->
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-700">Accept New Orders:</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="orderToggle" class="sr-only peer"
                        {{ Auth::user()->distributor->accepting_orders ? 'checked' : '' }}>
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 
                               peer-focus:ring-blue-300 rounded-full peer 
                               peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full 
                               peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] 
                               after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full 
                               after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                    </div>
                </label>
                <div id="statusIndicator" class="text-sm font-medium"></div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="flex items-center justify-between mb-4">
            <div class="w-full md:w-1/2 lg:w-1/3">
                <form action="{{ route('distributors.orders.index') }}" method="GET">
                    <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                    <div class="relative flex">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search orders..."
                            class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <button type="submit"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            <!-- Order History Link -->
            <div class="flex justify-end flex-1">
                <a href="{{ route('distributors.orders.history') }}" 
                   class="px-4 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-clock-history"></i>
                        Order History
                    </span>
                </a>
            </div>
        </div>

        @if (request('status') === 'processing')
            <div class="flex justify-end">
                <button onclick="openBatchQrModal()"
                    class="flex gap-2 px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    <iconify-icon icon="mdi:qrcode" class="text-2xl icon"></iconify-icon> Generate QR Codes
                </button>
            </div>
        @endif

        @if (request('search'))
            <div class="mb-4">
                <div class="flex items-center">
                    <p class="text-gray-600">Search results for: <span
                            class="font-bold">"{{ request('search') }}"</span></p>
                    <a href="{{ route('distributors.orders.index', ['status' => request('status', 'pending')]) }}"
                        class="ml-3 text-sm text-red-500 hover:underline">
                        Clear search
                    </a>
                </div>
            </div>
        @endif

        <!-- Order Status Tabs -->
        <div class="flex mb-4 border-b">
            <a href="?status=pending{{ request('search') ? '&search=' . request('search') : '' }}"
                class="px-4 py-2 -mb-px font-semibold 
                      @if (request('status') === 'pending' || !request('status')) text-green-500 border-green-500 
                      @else text-gray-600 border-transparent @endif 
                      border-b-2">
                Pending
            </a>
            <a href="?status=processing{{ request('search') ? '&search=' . request('search') : '' }}"
                class="px-4 py-2 -mb-px font-semibold 
                      @if (request('status') === 'processing') text-green-500 border-green-500
                      @else text-gray-600 border-transparent @endif  
                      border-b-2">
                Processing
            </a>
        </div>

        @if ($orders->isEmpty())
            <div class="p-8 text-center bg-white rounded-lg shadow-sm">
                <p class="text-gray-600 sm:text-lg">No orders found.</p>
            </div>
        @else
            <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Order ID</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Retailer Name</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Total Amount</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Order Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr onclick="openModal(this)" data-order-id="{{ $order->id }}"
                                data-status="{{ $order->status }}" data-retailer='@json($order->user)'
                                data-details='@json($order->orderDetails)'
                                data-delivery-address="{{ $order->orderDetails->first()->delivery_address ?? '' }}"
                                data-created-at="{{ $order->created_at->setTimezone('Asia/Manila')->format('F d, Y h:i A') }}"
                                data-is-multi-address="{{ $order->is_multi_address ? '1' : '0' }}"
                                class="transition-colors cursor-pointer hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $order->formatted_order_id }}</td>
                                <td class="px-4 py-3">
                                    {{ $order->user->first_name }} {{ $order->user->last_name }}
                                </td>
                                <td class="px-4 py-3 font-medium text-blue-600">
                                    ₱{{ number_format(optional($order->orderDetails)->sum('subtotal') ?: 0, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                        <span
                                            class="font-medium text-gray-700">{{ $order->created_at->format('F d, Y') }}</span>
                                        <span
                                            class="px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                                            {{ $order->created_at->setTimezone('Asia/Manila')->format('h:i A') }}
                                        </span>

                                        @if (request('status') === 'processing' && $order->status_updated_at)
                                            <span
                                                class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                                Accepted: {{ $order->status_updated_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
    @include('distributors.orders.indexmodal')


    <!-- Hidden Forms for Accept and Reject -->
    <form id="acceptForm" method="POST" class="hidden">
        @csrf
    </form>

    <form id="rejectForm" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="reject_reason" id="rejectReasonInput">
    </form>
    </div>
    <script>
        var storageBaseUrl = "{{ asset('storage') }}";
        var currentOrderId = null;

  
        function buildProductsTable(details) {
            let html = `
                <div class="mb-6 overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Products Ordered</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Product</th>
                                    <th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Price</th>
                                    <th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Quantity</th>
                                    <th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">`;

            let totalAmount = 0;
            details.forEach(function(detail) {
                const originalPrice = parseFloat(detail.product.price).toFixed(2);
                const discountedPrice = detail.discount_amount > 0 ?
                    (detail.product.price - (detail.discount_amount / detail.quantity)).toFixed(2) :
                    originalPrice;
                    
                html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="${detail.product.image ? storageBaseUrl + '/' + detail.product.image : '/img/default-product.jpg'}" 
                                    alt="${detail.product.product_name}" 
                                    class="object-cover w-16 h-16 rounded-lg" />
                                <span class="font-medium text-gray-800">${detail.product.product_name}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            ${detail.discount_amount > 0
                                ? `<span class="text-xs text-gray-500 line-through">₱${originalPrice}</span><br>
                                <span class="text-green-600">₱${discountedPrice}</span>`
                                : `₱${originalPrice}`}
                        </td>
                        <td class="px-4 py-3">
                            ${detail.quantity}
                            ${detail.free_items > 0 ? `<span class="text-sm text-green-600">+${detail.free_items} free</span>` : ''}
                        </td>
                        <td class="px-4 py-3 font-medium text-blue-600">₱${parseFloat(detail.subtotal).toFixed(2)}</td>
                    </tr>
                `;
                totalAmount += parseFloat(detail.subtotal);
            });

            html += `
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 font-medium text-right text-gray-700">Total Amount:</td>
                                    <td colspan="1" class="px-4 py-3 font-bold text-blue-600">₱${totalAmount.toFixed(2)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;

            return html;
        }

        function openModal(row) {
            // Extract order data from row attributes
            const orderId = row.getAttribute('data-order-id');
            const formattedOrderId = row.querySelector('td:first-child').textContent.trim();
            const orderStatus = row.getAttribute('data-status');
            const retailer = JSON.parse(row.getAttribute('data-retailer'));
            const details = JSON.parse(row.getAttribute('data-details'));
            const dateTime = row.getAttribute('data-created-at');
            const deliveryAddress = row.getAttribute('data-delivery-address');
            
            // Store the current order ID for other functions
            currentOrderId = orderId;
            
            // Update modal title
            document.getElementById('modalTitle').innerText = 'Order ' + formattedOrderId;
            
            // Show loading state
            document.getElementById('modalContent').innerHTML = 
                '<div class="flex items-center justify-center p-12"><div class="w-12 h-12 border-t-2 border-b-2 border-green-500 rounded-full animate-spin"></div></div>';
            document.getElementById('orderModal').classList.remove('hidden');
            
            // Create the products table HTML
            const productsHtml = buildProductsTable(details);
            
            // Make sure we're correctly checking is_multi_address
            // Check directly on the row data attribute
            const isMultiAddress = row.hasAttribute('data-is-multi-address') ? 
                row.getAttribute('data-is-multi-address') === '1' : false;
            
            console.log('Order ID:', orderId, 'is multi-address:', isMultiAddress);
            
            // Always fetch delivery information for multi-address orders
            if (isMultiAddress || orderStatus !== 'pending') {
                fetchDeliveryAddresses(orderId, productsHtml, retailer, deliveryAddress, orderStatus);
            } else {
                // For pending regular orders, just render the basic information
                renderModalContent(productsHtml, null, retailer, deliveryAddress, orderStatus, false);
            }
        }
 
        function fetchDeliveryAddresses(orderId, productsHtml, retailer, deliveryAddress, orderStatus) {
        fetch(`/orders/${orderId}/deliveries`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Delivery data received:', data);

                if (data.success) {
                    const isMultiAddress = data.is_multi_address;
                    const deliveriesHtml = buildDeliveriesSection(data.deliveries, isMultiAddress);

                    // Render the modal with the fetched data
                    renderModalContent(productsHtml, deliveriesHtml, retailer, deliveryAddress, orderStatus, isMultiAddress);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to load delivery information.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching delivery addresses:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'An unexpected error occurred.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                });
            });
    }

        function buildDeliveriesSection(deliveries, isMultiAddress) {
            if (!deliveries || deliveries.length === 0) return '';

            let html = `
                <div class="mb-6 overflow-hidden bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between p-4 border-b bg-purple-50">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-purple-800">Delivery Information</h3>
                        </div>
                        ${isMultiAddress ? 
                            `<span class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-800 bg-purple-200 rounded-full">
                                Multiple Delivery Addresses
                            </span>` : ''}
                    </div>
                    <div class="p-4 space-y-4">`;

            deliveries.forEach((delivery, index) => {
                html += `
                    <div class="p-4 border rounded-lg ${index % 2 === 0 ? 'bg-gray-50' : 'bg-white'}">
                        ${isMultiAddress ? `<h4 class="mb-2 font-medium text-gray-900 text-md">Delivery Location ${index + 1}</h4>` : ''}
                        
                        <!-- Delivery Address -->
                        <div class="flex items-start mb-3">
                            <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="ml-3 text-sm text-gray-600">
                                ${delivery.address ? 
                                    `${delivery.address.barangay_name || delivery.address.barangay || ''}${delivery.address.street ? ', ' + delivery.address.street : ''}` : 
                                    'Address information not available'}
                            </p>
                        </div>
                        
                        <!-- Delivery Status -->
                        ${delivery.status ? `
                        <div class="flex items-start mb-3">
                            <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm text-gray-600">Status: 
                                    <span class="font-medium ${delivery.status === 'delivered' ? 'text-green-600' : 'text-blue-600'}">
                                        ${delivery.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                    </span>
                                </p>
                            </div>
                        </div>
                        ` : ''}

                        <!-- Products for this delivery -->
                        <div class="mt-3">
                            <p class="text-sm font-medium text-gray-700">Products for this location:</p>
                            <ul class="mt-1 ml-5 space-y-1 list-disc">`;
                            
                if (delivery.items && delivery.items.length > 0) {
                    delivery.items.forEach(item => {
                        html += `
                            <li class="text-sm text-gray-600">
                                ${item.product_name} <span class="text-gray-500">(Qty: ${item.quantity})</span>
                            </li>`;
                    });
                } else {
                    html += `<li class="text-sm text-gray-500">No products specified</li>`;
                }
                            
                html += `
                            </ul>
                        </div>
                    </div>`;
            });

            html += `
                    </div>
                </div>`;
                
            return html;
        }
        function renderModalContent(productsHtml, deliveriesHtml, retailer, deliveryAddress, orderStatus, isMultiAddress) {
            // Create retailer profile section
            const retailerHtml = `
                <div class="p-4 bg-white rounded-lg shadow">
                    <div class="flex flex-col md:flex-row">
                        <div class="flex items-start space-x-4 ${isMultiAddress ? 'md:w-1/2' : ''}">
                            <div class="flex-shrink-0">
                                <a href="/retailers/${retailer.id}">
                                    <img src="${retailer.retailer_profile?.profile_picture ? storageBaseUrl + '/' + retailer.retailer_profile.profile_picture : '/img/default-avatar.jpg'}" 
                                        alt="Profile" 
                                        class="object-cover w-16 h-16 rounded-full shadow hover:opacity-80" />
                                </a>
                            </div>
                            <div>
                                <a href="/retailers/${retailer.id}" class="hover:underline">
                                    <h4 class="text-lg font-medium text-gray-800">${retailer.first_name} ${retailer.last_name}</h4>
                                </a>
                                <p class="text-sm text-gray-600">${retailer.email}</p>
                                <p class="text-sm text-gray-600">${retailer.retailer_profile?.phone || 'No phone number'}</p>
                                
                                <!-- Address display logic based on order status and type -->
                                ${isMultiAddress ? 
                                `<div class="flex items-center mt-1 text-purple-700">
                                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="font-medium">Multiple Delivery Addresses</span>
                                </div>` : 
                                `<div class="mt-2">
                                    <p class="text-sm font-medium text-gray-600">Delivery Address:</p>
                                    <p class="text-sm text-gray-600">${deliveryAddress || 'Address not specified'}</p>
                                </div>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove the delivery info HTML for regular orders - only use for multi-address orders
            const finalHtml = `
                <div class="space-y-6">
                    ${productsHtml}
                    ${deliveriesHtml || ''}
                    ${retailerHtml}
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = finalHtml;
            
            // Show or hide action buttons based on order status
            const actionButtons = document.getElementById('actionButtons');
            if (orderStatus === 'pending') {
                actionButtons.classList.remove('hidden');
            } else {
                actionButtons.classList.add('hidden');
            }

            // Show QR code button only for processing orders
            const qrCodeButton = document.getElementById('qrCodeButton');
            if (orderStatus === 'processing') {
                qrCodeButton.href = `/orders/${currentOrderId}/qrcode`;
                qrCodeButton.classList.remove('hidden');
            } else {
                qrCodeButton.classList.add('hidden');
            }
            
            // Show edit order button only for processing orders
            const editOrderButton = document.getElementById('editOrderButton');
            if (orderStatus === 'processing') {
                editOrderButton.classList.remove('hidden');
            } else {
                editOrderButton.classList.add('hidden');
            }
        }

        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }

        function acceptOrder() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to accept this order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, accept it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/orders/${currentOrderId}/accept`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Order accepted successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                // Show validation error in alert
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message ||
                                        'An error occurred while accepting the order.',
                                    icon: 'error',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: error.message || 'An unexpected error occurred.',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        });
                }
            });
        }

        function openRejectModal() {
            document.getElementById('orderModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('orderModal').classList.remove('hidden');
            document.body.style.overflow = ''; // Restore background scrolling
        }

        function checkRejectOther(radio) {
            var otherReasonInput = document.getElementById('rejectOtherReason');
            if (radio.value === 'Other') {
                otherReasonInput.classList.remove('hidden');
            } else {
                otherReasonInput.classList.add('hidden');
                otherReasonInput.value = '';
            }
        }

        function openEditOrderModal() {
            const modal = document.getElementById('editOrderModal');
            const orderItemsContainer = document.getElementById('editOrderItems');
            orderItemsContainer.innerHTML = '';

            // Fetch order details and stock availability
            fetch(`/orders/${currentOrderId}/detail`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.orderDetails.forEach(detail => {
                            const stockLeft = detail.product.stockLeft;

                            orderItemsContainer.innerHTML += `
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">${detail.product.product_name}</p>
                                <p class="text-sm text-gray-600">Stock Left: ${stockLeft}</p>
                            </div>
                            <div>
                                <input type="number" name="order_details[${detail.id}][quantity]"
                                    value="${detail.quantity}" min="1" max="${stockLeft}"
                                    class="w-20 px-2 py-1 border rounded">
                            </div>
                        </div>
                    `;
                        });

                        modal.classList.remove('hidden');
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to load order details.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An unexpected error occurred.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                });
        }

        function submitEditOrder() {
            const form = document.getElementById('editOrderForm');
            const formData = new FormData(form);

            // Convert FormData to JSON object
            const orderDetails = [];
            formData.forEach((value, key) => {
                const match = key.match(
                    /^order_details\[(\d+)\]\[quantity\]$/); // Match keys like "order_details[1][quantity]"
                if (match) {
                    const detailId = match[1];
                    orderDetails.push({
                        id: detailId,
                        quantity: parseInt(value),
                    });
                }
            });

            // Ensure orderDetails is not empty
            if (orderDetails.length === 0) {
                Swal.fire({
                    title: 'Error!',
                    text: 'No order details found to update.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            fetch(`/orders/${currentOrderId}/edit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        order_details: orderDetails
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            location.reload(); // Refresh the page on success
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to update order.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'An unexpected error occurred.',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                });
        }

        function closeEditOrderModal() {
            document.getElementById('editOrderModal').classList.add('hidden');
        }

        function submitRejectOrder() {
            var selected = document.querySelector('input[name="reject_reason_option"]:checked');
            var reason = '';

            if (selected) {
                if (selected.value === 'Other') {
                    reason = document.getElementById('rejectOtherReason').value;
                    if (reason.trim() === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Please provide a custom rejection reason.'
                        });
                        return;
                    }
                } else {
                    reason = selected.value;
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select a rejection reason.'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to reject this order?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('rejectForm').action = "/orders/" + currentOrderId +
                        "/reject";
                    document.getElementById('rejectReasonInput').value = reason;
                    document.getElementById('rejectForm').submit();
                }
            });
        }

        function openBatchQrModal() {
            // Show loading state while fetching data
            const ordersList = document.getElementById('batchOrdersList');
            ordersList.innerHTML =
                '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">Loading orders...</td></tr>';

            document.getElementById('batchQrModal').classList.remove('hidden');

            // Fetch processing orders for QR code generation
            fetch('{{ route('distributors.orders.processing') }}')
                .then(response => response.json())
                .then(data => {
                    console.log("Response data:", data); // Debug log

                    if (data.orders && data.orders.length > 0) {
                        console.log("First order details:", data.orders[0]);
                        console.log("formatted_order_id exists:", data.orders[0].hasOwnProperty('formatted_order_id'));
                    }

                    ordersList.innerHTML = '';

                    if (!data.orders || data.orders.length === 0) {
                        ordersList.innerHTML =
                            '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">No processing orders found</td></tr>';
                        return;
                    }

                    data.orders.forEach(order => {


                        ordersList.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="border-gray-300 rounded cursor-pointer order-checkbox" 
                            value="${order.id}" onchange="updateSelectedCount()">
                    </td>
                    <td class="px-4 py-3">${order.formatted_order_id}</td>
                    <td class="px-4 py-3">${order.user.first_name} ${order.user.last_name}</td>
                    <td class="px-4 py-3">${formatDate(order.created_at)}</td>
                </tr>
                `;
                    });

                    updateSelectedCount();
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
                    ordersList.innerHTML =
                        '<tr><td colspan="4" class="px-4 py-3 text-center text-red-500">Error loading orders. Please try again.</td></tr>';
                });
        }

        function closeBatchQrModal() {
            document.getElementById('batchQrModal').classList.add('hidden');
        }

        function toggleAllCheckboxes() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.order-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });

            updateSelectedCount();
        }

        function updateSelectedCount() {
            const selectedOrders = document.querySelectorAll('.order-checkbox:checked');
            const selectedCount = document.getElementById('selectedCount');
            const generateButton = document.getElementById('generateQrButton');

            selectedCount.textContent = selectedOrders.length;
            generateButton.disabled = selectedOrders.length === 0;
        }

        function generateSelectedQrCodes() {
            const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked'))
                .map(checkbox => checkbox.value);

            if (selectedOrders.length === 0) {
                return;
            }

            // Create a form to submit the selected order IDs
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('distributors.orders.batch-qrcode') }}';
            form.style.display = 'none';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfToken);

            // Add selected order IDs
            selectedOrders.forEach(orderId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'order_ids[]';
                input.value = orderId;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';

            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch (e) {
                console.error('Error formatting date:', e);
                return 'Invalid date';
            }
        }

        // Add this to the existing script section at the bottom
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('orderToggle');
            const statusIndicator = document.getElementById('statusIndicator');

            // Set initial status text
            updateStatusIndicator();

            toggle.addEventListener('change', function() {
                // Show loading indicator
                statusIndicator.textContent = "Updating...";
                statusIndicator.className = "text-sm font-medium text-blue-500";

                // Send AJAX request to update status
                fetch('{{ route('distributors.toggle-order-acceptance') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            accepting_orders: toggle.checked
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateStatusIndicator();
                        } else {
                            // Revert toggle if there was an error
                            toggle.checked = !toggle.checked;
                            statusIndicator.textContent = "Update failed";
                            statusIndicator.className = "text-sm font-medium text-red-500";
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert toggle if there was an error
                        toggle.checked = !toggle.checked;
                        statusIndicator.textContent = "Update failed";
                        statusIndicator.className = "text-sm font-medium text-red-500";
                    });
            });

            function updateStatusIndicator() {
                if (toggle.checked) {
                    statusIndicator.textContent = "Accepting Orders";
                    statusIndicator.className = "text-sm font-medium text-green-500";
                } else {
                    statusIndicator.textContent = "Not Accepting Orders";
                    statusIndicator.className = "text-sm font-medium text-red-500";
                }
            }
        });
    </script>
</x-distributor-layout>
