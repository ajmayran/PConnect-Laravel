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

    <!-- Main Order Modal -->
    <div id="orderModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-3/4">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
                <h2 class="text-xl font-bold text-gray-800" id="modalTitle">Order Details</h2>
                <button onclick="closeModal()"
                    class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="modalContent" class="p-6">
                <!-- Modal Content (Products and retailer profile) is generated dynamically -->
            </div>

            <!-- Modal Footer with Accept, Reject, and Close buttons -->
            <div class="sticky bottom-0 flex justify-end gap-4 p-4 bg-white border-t">
                <div id="actionButtons">
                    <button onclick="acceptOrder()"
                        class="px-4 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                        Accept
                    </button>
                    <button onclick="openRejectModal()"
                        class="px-4 py-2 font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                        Reject
                    </button>
                </div>
                <!-- Add QR Code Button -->
                <a id="qrCodeButton" href="#"
                    class="px-4 py-2 font-medium text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                    QR Code
                </a>
                <button onclick="openEditOrderModal()"
                    class="px-4 py-2 font-medium text-white transition-colors bg-yellow-500 rounded-lg hover:bg-yellow-600">
                    Edit Order
                </button>
                <button onclick="closeModal()"
                    class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Reason Modal -->
    <div id="rejectModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">Reject Order</h2>
            </div>
            <div class="p-4">
                <p class="mb-2 text-gray-700">Select a rejection reason:</p>
                <div>
                    <label class="flex items-center mb-2">
                        <input type="radio" name="reject_reason_option" value="Out of stock" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Out of stock
                    </label>
                    <label class="flex items-center mb-2">
                        <input type="radio" name="reject_reason_option" value="Price mismatch" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Price mismatch
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="reject_reason_option" value="Other" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Other
                    </label>
                </div>
                <textarea id="rejectOtherReason" class="hidden w-full p-2 mt-2 border rounded"
                    placeholder="Enter custom rejection reason..."></textarea>
            </div>
            <div class="flex justify-end gap-2 p-4 border-t">
                <button onclick="submitRejectOrder()"
                    class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                    Submit
                </button>
                <button onclick="closeRejectModal()"
                    class="px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <div id="batchQrModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-2/3">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
                <h2 class="text-xl font-bold text-gray-800">Generate Batch QR Codes</h2>
                <button onclick="closeBatchQrModal()"
                    class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6">
                <div class="mb-4">
                    <p class="mb-2 text-gray-700">Select the orders you want to generate QR codes for:</p>
                    <div class="overflow-y-auto max-h-[40vh] border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="sticky top-0 bg-gray-50">
                                <tr>
                                    <th class="w-10 px-4 py-3">
                                        <input type="checkbox" id="selectAll"
                                            class="border-gray-300 rounded cursor-pointer"
                                            onchange="toggleAllCheckboxes()">
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Order
                                        ID</th>
                                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                        Retailer</th>
                                    <th class="px-4 py-3 text-xs font-medium text-left text-gray-500 uppercase">Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="batchOrdersList">
                                <!-- Orders will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-between mt-6">
                    <p class="text-sm text-gray-600"><span id="selectedCount">0</span> orders selected</p>
                    <div class="space-x-2">
                        <button onclick="generateSelectedQrCodes()" id="generateQrButton" disabled
                            class="px-4 py-2 font-medium text-white transition-colors bg-blue-500 rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-blue-600">
                            Generate QR Codes
                        </button>
                        <button onclick="closeBatchQrModal()"
                            class="px-4 py-2 font-medium text-gray-700 transition-colors border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div id="editOrderModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-2xl bg-white rounded-lg shadow-xl">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold text-gray-800">Edit Order</h2>
            </div>
            <div class="p-4">
                <form id="editOrderForm">
                    <div id="editOrderItems" class="space-y-4">
                        <!-- Order items will be dynamically populated here -->
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="submitEditOrder()"
                            class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeEditOrderModal()"
                            class="px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

        function openModal(row) {
            var orderId = row.getAttribute('data-order-id');
            var formattedOrderId = row.querySelector('td:first-child').textContent.trim();
            var orderStatus = row.getAttribute('data-status');
            currentOrderId = orderId;
            var retailer = JSON.parse(row.getAttribute('data-retailer'));
            var details = JSON.parse(row.getAttribute('data-details'));
            var dateTime = row.getAttribute('data-created-at');
            var deliveryAddress = row.getAttribute('data-delivery-address');

            document.getElementById('modalTitle').innerText = 'Order ' + formattedOrderId;

            let modalHtml = `
        <div class="space-y-6">
            <!-- Products Section -->
            <div class="overflow-hidden bg-white rounded-lg shadow">
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
                        <tbody class="divide-y divide-gray-200">
    `;

            let totalAmount = 0;
            details.forEach(function(detail) {
                const originalPrice = parseFloat(detail.product.price).toFixed(2);
                const discountedPrice = detail.discount_amount > 0 ?
                    (detail.product.price - (detail.discount_amount / detail.quantity)).toFixed(2) :
                    originalPrice;
                modalHtml += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <img src="${detail.product.image ? storageBaseUrl + '/' + detail.product.image : 'img/default-product.jpg'}" 
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
                <td class="px-4 py-3">${detail.quantity}</td>
                <td class="px-4 py-3 font-medium text-blue-600">₱${parseFloat(detail.subtotal).toFixed(2)}</td>
            </tr>
        `;
                totalAmount += parseFloat(detail.subtotal);
            });

            modalHtml += `
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 font-medium text-right text-gray-700">Total Amount:</td>
                                <td colspan="3" class="px-4 py-3 font-bold text-blue-600">₱${totalAmount.toFixed(2)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Retailer Profile Section -->
       <div class="p-4 bg-white rounded-lg shadow">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                         <a href="/retailers/${retailer.id}">
                        <img src="${retailer.retailer_profile?.profile_picture ? storageBaseUrl + '/' + retailer.retailer_profile.profile_picture : 'img/default-avatar.jpg'}" 
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
                    <p class="text-sm text-gray-600">${deliveryAddress || 'No delivery address'}</p>
                </div>
            </div>
        </div>
                </div>
            `;

            document.getElementById('modalContent').innerHTML = modalHtml;
            document.getElementById('orderModal').classList.remove('hidden');

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
                qrCodeButton.href = `/orders/${orderId}/qrcode`;
                qrCodeButton.classList.remove('hidden');
            } else {
                qrCodeButton.classList.add('hidden');
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
