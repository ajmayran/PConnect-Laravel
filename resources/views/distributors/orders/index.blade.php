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
        /* Mobile responsive adjustments */
        @media (max-width: 640px) {
            .orders-table th, .orders-table td {
                font-size: 0.75rem;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            .mobile-small-text {
                font-size: 0.75rem;
            }
        }
        /* Fix for scroll issues on status tabs */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
        <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
    </span>

    <div class="container p-4 mx-auto">
        <div class="relative flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-left text-gray-800 sm:text-2xl md:text-3xl">Orders Management</h1>

            <!-- Order Acceptance Toggle -->
            <div class="flex items-center gap-1 sm:gap-3">
                <span class="text-xs sm:text-sm font-medium text-gray-700">Accept Orders:</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="orderToggle" class="sr-only peer"
                        {{ Auth::user()->distributor->accepting_orders ? 'checked' : '' }}>
                    <div
                        class="w-9 h-5 sm:w-11 sm:h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 
                               peer-focus:ring-blue-300 rounded-full peer 
                               peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full 
                               peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] 
                               after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full 
                               after:h-4 after:w-4 sm:after:h-5 sm:after:w-5 after:transition-all peer-checked:bg-green-600">
                    </div>
                </label>
                <div id="statusIndicator" class="text-xs sm:text-sm font-medium"></div>
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
                            class="w-full py-1.5 sm:py-2 pl-3 sm:pl-4 pr-8 sm:pr-10 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                        <button type="submit"
                            class="absolute inset-y-0 right-0 flex items-center px-2 sm:px-3 text-gray-500 hover:text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if (request('status') === 'processing')
            <div class="flex justify-end mb-2">
                <button onclick="openBatchQrModal()"
                    class="flex gap-1 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    <iconify-icon icon="mdi:qrcode" class="text-xl sm:text-2xl icon"></iconify-icon> Generate QR Codes
                </button>
            </div>
        @endif

        @if (request('search'))
            <div class="mb-3">
                <div class="flex items-center">
                    <p class="text-xs sm:text-sm text-gray-600">Search results for: <span
                            class="font-bold">"{{ request('search') }}"</span></p>
                    <a href="{{ route('distributors.orders.index', ['status' => request('status', 'pending')]) }}"
                        class="ml-2 text-xs text-red-500 hover:underline">
                        Clear
                    </a>
                </div>
            </div>
        @endif

        <!-- Order Status Tabs -->
        <div class="mb-4 overflow-x-auto border-b scrollbar-hide">
            <div class="flex min-w-max">
                <a href="?status=pending{{ request('search') ? '&search=' . request('search') : '' }}"
                    class="px-2 sm:px-4 py-1.5 sm:py-2 -mb-px text-xs sm:text-sm font-semibold 
                          @if (request('status') === 'pending' || !request('status')) text-green-500 border-green-500 
                          @else text-gray-600 border-transparent @endif 
                          border-b-2">
                    Pending
                </a>
                <a href="?status=processing{{ request('search') ? '&search=' . request('search') : '' }}"
                    class="px-2 sm:px-4 py-1.5 sm:py-2 -mb-px text-xs sm:text-sm font-semibold 
                          @if (request('status') === 'processing') text-green-500 border-green-500
                          @else text-gray-600 border-transparent @endif  
                          border-b-2">
                    Processing
                </a>
            </div>
        </div>

        @if ($orders->isEmpty())
            <div class="p-6 text-center bg-white rounded-lg shadow-sm">
                <p class="text-sm sm:text-base text-gray-600">No orders found.</p>
            </div>
        @else
            <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                <table class="min-w-full text-xs sm:text-sm divide-y divide-gray-200 orders-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700">Order ID</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700">Retailer</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700">Amount</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700">Date</th>
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
                                <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $order->formatted_order_id }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3">
                                    {{ $order->user->first_name }} {{ $order->user->last_name }}
                                </td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-blue-600">
                                    ₱{{ number_format(optional($order->orderDetails)->sum('subtotal') ?: 0, 2) }}
                                </td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:gap-2">
                                        <span class="font-medium text-gray-700 mobile-small-text">
                                            {{ $order->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="px-1.5 py-0.5 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                                            {{ $order->created_at->setTimezone('Asia/Manila')->format('h:i A') }}
                                        </span>

                                        @if (request('status') === 'processing' && $order->status_updated_at)
                                            <span class="px-1.5 py-0.5 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                                Accepted: {{ $order->status_updated_at->format('M d') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Main Order Modal -->
    <div id="orderModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-3/4">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-3 sm:p-4 bg-white border-b">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800" id="modalTitle">Order Details</h2>
                <button onclick="closeModal()"
                    class="p-1 sm:p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="modalContent" class="p-4 sm:p-6">
                <!-- Modal Content (Products and retailer profile) is generated dynamically -->
            </div>

            <!-- Modal Footer with Accept, Reject, and Close buttons -->
            <div class="sticky bottom-0 flex flex-wrap justify-end gap-2 sm:gap-4 p-3 sm:p-4 bg-white border-t">
                <div id="actionButtons" class="flex gap-2">
                    <button onclick="acceptOrder()"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                        Accept
                    </button>
                    <button onclick="openRejectModal()"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                        Reject
                    </button>
                </div>
                <!-- Add QR Code Button -->
                <a id="qrCodeButton" href="#"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors bg-blue-500 rounded-lg hover:bg-blue-600">
                    QR Code
                </a>
                <button onclick="openEditOrderModal()"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors bg-yellow-500 rounded-lg hover:bg-yellow-600">
                    Edit Order
                </button>
                <button onclick="closeModal()"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Reason Modal -->
    <div id="rejectModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl">
            <div class="p-3 sm:p-4 border-b">
                <h2 class="text-base sm:text-xl font-bold text-gray-800">Reject Order</h2>
            </div>
            <div class="p-3 sm:p-4">
                <p class="mb-2 text-xs sm:text-sm text-gray-700">Select a rejection reason:</p>
                <div>
                    <label class="flex items-center mb-2 text-xs sm:text-sm">
                        <input type="radio" name="reject_reason_option" value="Out of stock" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Out of stock
                    </label>
                    <label class="flex items-center mb-2 text-xs sm:text-sm">
                        <input type="radio" name="reject_reason_option" value="Price mismatch" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Price mismatch
                    </label>
                    <label class="flex items-center text-xs sm:text-sm">
                        <input type="radio" name="reject_reason_option" value="Other" class="mr-2"
                            onchange="checkRejectOther(this)">
                        Other
                    </label>
                </div>
                <textarea id="rejectOtherReason" class="hidden w-full p-2 mt-2 text-xs sm:text-sm border rounded"
                    placeholder="Enter custom rejection reason..."></textarea>
            </div>
            <div class="flex justify-end gap-2 p-3 sm:p-4 border-t">
                <button onclick="submitRejectOrder()"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-red-600 rounded hover:bg-red-700">
                    Submit
                </button>
                <button onclick="closeRejectModal()"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-gray-600 rounded hover:bg-gray-700">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Batch QR Modal -->
    <div id="batchQrModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-2/3">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-3 sm:p-4 bg-white border-b">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Generate Batch QR Codes</h2>
                <button onclick="closeBatchQrModal()"
                    class="p-1 sm:p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-3 sm:p-6">
                <div class="mb-3 sm:mb-4">
                    <p class="mb-2 text-xs sm:text-sm text-gray-700">Select the orders you want to generate QR codes for:</p>
                    <div class="overflow-y-auto max-h-[40vh] border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="sticky top-0 bg-gray-50">
                                <tr>
                                    <th class="w-8 sm:w-10 px-2 sm:px-4 py-2 sm:py-3">
                                        <input type="checkbox" id="selectAll"
                                            class="border-gray-300 rounded cursor-pointer"
                                            onchange="toggleAllCheckboxes()">
                                    </th>
                                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-left text-gray-500 uppercase">Order
                                        ID</th>
                                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-left text-gray-500 uppercase">
                                        Retailer</th>
                                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-left text-gray-500 uppercase">Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="batchOrdersList">
                                <!-- Orders will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-between mt-4 sm:mt-6">
                    <p class="text-xs sm:text-sm text-gray-600"><span id="selectedCount">0</span> orders selected</p>
                    <div class="space-x-2">
                        <button onclick="generateSelectedQrCodes()" id="generateQrButton" disabled
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors bg-blue-500 rounded-lg disabled:bg-gray-300 disabled:cursor-not-allowed hover:bg-blue-600">
                            Generate QR Codes
                        </button>
                        <button onclick="closeBatchQrModal()"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 transition-colors border border-gray-300 rounded-lg hover:bg-gray-50">
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
            <div class="p-3 sm:p-4 border-b">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Edit Order</h2>
            </div>
            <div class="p-3 sm:p-4">
                <form id="editOrderForm">
                    <div id="editOrderItems" class="space-y-3 sm:space-y-4">
                        <!-- Order items will be dynamically populated here -->
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="submitEditOrder()"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-green-600 rounded hover:bg-green-700">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeEditOrderModal()"
                            class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-gray-600 rounded hover:bg-gray-700">
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

            var modalHtml = '<div class="space-y-4 sm:space-y-6">';

            // Products Section
            modalHtml += '<div class="overflow-hidden bg-white rounded-lg shadow">';
            modalHtml +=
                '<div class="p-3 sm:p-4 border-b bg-gray-50"><h3 class="text-base sm:text-lg font-semibold text-gray-800">Products Ordered</h3></div>';
            modalHtml += '<div class="overflow-x-auto">';
            modalHtml += '<table class="min-w-full divide-y divide-gray-200">';
            modalHtml += '<thead class="bg-gray-50"><tr>';
            modalHtml += '<th class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-left text-gray-700">Product</th>';
            modalHtml += '<th class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-left text-gray-700">Price</th>';
            modalHtml += '<th class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-left text-gray-700">Qty</th>';
            modalHtml += '<th class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-left text-gray-700">Subtotal</th>';
            modalHtml += '</tr></thead><tbody class="divide-y divide-gray-200">';

            let totalAmount = 0;
            details.forEach(function(detail) {
                modalHtml += '<tr class="hover:bg-gray-50">';
                modalHtml += '<td class="px-2 sm:px-4 py-2 sm:py-3">';
                modalHtml += '<div class="flex items-center gap-2 sm:gap-3">';
                if (detail.product.image) {
                    modalHtml += '<img src="' + storageBaseUrl + '/' + detail.product.image + '" alt="' + detail
                        .product.product_name + '" class="object-cover w-10 h-10 sm:w-16 sm:h-16 rounded-lg" />';
                } else {
                    modalHtml += '<img src="img/default-product.jpg" class="object-cover w-10 h-10 sm:w-16 sm:h-16 rounded-lg" />';
                }
                modalHtml += '<span class="font-medium text-xs sm:text-sm text-gray-800">' + detail.product.product_name + '</span>';
                modalHtml += '</div></td>';
                modalHtml += '<td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm">₱' + parseFloat(detail.product.price).toFixed(2) + '</td>';
                modalHtml += '<td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm">' + detail.quantity + '</td>';
                modalHtml += '<td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-blue-600">₱' + parseFloat(detail.subtotal)
                    .toFixed(2) + '</td>';
                modalHtml += '</tr>';
                totalAmount += parseFloat(detail.subtotal);
            });

            modalHtml += '</tbody>';
            modalHtml += '<tfoot class="bg-gray-50"><tr>';
            modalHtml += '<td colspan="3" class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-right text-gray-700">Total Amount:</td>';
            modalHtml += '<td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-bold text-blue-600">₱' + totalAmount.toFixed(2) + '</td>';
            modalHtml += '</tr></tfoot>';
            modalHtml += '</table></div></div>';

            // Retailer Profile Card - Compact Design
            modalHtml += '<div class="p-3 sm:p-4 bg-white rounded-lg shadow">';
            modalHtml += '<div class="flex items-start space-x-3 sm:space-x-4">';
            // Profile picture and name section with clickable elements
            modalHtml += '<div class="flex items-center">';
            modalHtml += '<a href="' + '{{ route('distributors.retailers.show', ':id') }}'.replace(':id', retailer.id) +
                '" class="block">';
            if (retailer.retailer_profile && retailer.retailer_profile.profile_picture) {
                modalHtml += '<img src="' + storageBaseUrl + '/' + retailer.retailer_profile.profile_picture +
                    '" alt="Profile" class="object-cover w-10 h-10 sm:w-12 sm:h-12 rounded-full shadow hover:ring-2 hover:ring-green-500" />';
            } else {
                modalHtml +=
                    '<div class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-gray-200 rounded-full hover:ring-2 hover:ring-green-500">' +
                    '<span class="text-lg sm:text-xl font-medium text-gray-600">' + retailer.first_name.charAt(0) + '</span></div>';
            }
            modalHtml += '</a>';
            modalHtml += '</div>';
            // Retailer information container
            modalHtml += '<div class="flex-1">';
            modalHtml += '<div class="flex items-center mb-1 sm:mb-2">';
            modalHtml += '<a href="/retailers/' + retailer.id + '" class="hover:text-green-600">';
            modalHtml += '<h4 class="text-base sm:text-lg font-medium text-gray-800">' + retailer.first_name + ' ' + retailer.last_name +
                '</h4>';
            modalHtml += '</a>';
            modalHtml += '</div>';
            modalHtml += '<div class="grid grid-cols-1 gap-1 sm:gap-2 text-xs sm:text-sm">';
            if (retailer.email) {
                modalHtml +=
                    '<p class="flex items-center text-gray-600"><svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>' +
                    retailer.email + '</p>';
            }
            if (retailer.retailer_profile) {
                if (retailer.retailer_profile.phone) {
                    modalHtml +=
                        '<p class="flex items-center text-gray-600"><svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>' +
                        retailer.retailer_profile.phone + '</p>';
                }
                if (deliveryAddress) {
                    modalHtml +=
                        '<p class="flex items-center text-gray-600"><svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>' +
                        deliveryAddress + '</p>';
                }
            }
            modalHtml += '</div>';
            modalHtml += '</div>';
            modalHtml += '</div>'; // End of retailer card

            modalHtml += '</div>';

            document.getElementById('modalContent').innerHTML = modalHtml;
            document.getElementById('orderModal').classList.remove('hidden');

            if (orderStatus !== 'pending') {
                document.getElementById('actionButtons').classList.add('hidden');
            } else {
                document.getElementById('actionButtons').classList.remove('hidden');
            }

            // Show QR code button only for processing orders
            const qrCodeButton = document.getElementById('qrCodeButton');
            if (orderStatus === 'processing') {
                qrCodeButton.href = "/orders/" + orderId + "/qrcode";
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
