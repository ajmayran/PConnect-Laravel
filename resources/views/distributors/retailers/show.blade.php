<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <!-- Back button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="flex items-center text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>

        <!-- Retailer Profile Header -->
        <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if ($retailer->retailerProfile && $retailer->retailerProfile->profile_picture)
                            <img src="{{ asset('storage/' . $retailer->retailerProfile->profile_picture) }}"
                                alt="{{ $retailer->first_name }}" class="object-cover w-24 h-24 rounded-full">
                        @else
                            <div class="flex items-center justify-center w-24 h-24 bg-gray-200 rounded-full">
                                <span
                                    class="text-2xl font-medium text-gray-700">{{ $retailer->first_name[0] }}{{ $retailer->last_name[0] }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-800">{{ $retailer->first_name }}
                            {{ $retailer->last_name }}</h1>
                        @if ($retailer->retailerProfile && $retailer->retailerProfile->business_name)
                            <p class="text-lg text-gray-600">{{ $retailer->retailerProfile->business_name }}</p>
                        @endif
                        <div class="mt-2 space-y-1 text-sm text-gray-600">
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $retailer->email }}
                            </p>
                            @if ($retailer->retailerProfile && $retailer->retailerProfile->phone)
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $retailer->retailerProfile->phone }}
                                </p>
                            @endif
                            @if ($retailer->retailerProfile && ($retailer->retailerProfile->barangay || $retailer->retailerProfile->street))
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    @if ($retailer->retailerProfile->barangay_name)
                                        {{ $retailer->retailerProfile->barangay_name }}
                                    @endif
                                    @if ($retailer->retailerProfile->street)
                                        {{ $retailer->retailerProfile->barangay_name ? ', ' : '' }}{{ $retailer->retailerProfile->street }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('distributors.messages.index', ['retailer' => $retailer->id]) }}"
                            class="flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-md hover:bg-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Message
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['total'] }}</h3>
                    </div>
                    <div class="p-3 text-white bg-blue-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Completed Orders</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['completed'] }}</h3>
                    </div>
                    <div class="p-3 text-white bg-green-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Processing Orders</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['processing'] }}</h3>
                    </div>
                    <div class="p-3 text-white bg-yellow-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Spent</p>
                        <h3 class="text-2xl font-bold text-gray-900">
                            ₱{{ number_format($orderStats['totalSpent'], 2) }}</h3>
                    </div>
                    <div class="p-3 text-white bg-purple-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Recent Orders</h2>
                <a href="{{ ('distributors.orders.index', ['search' => $retailer->first_name . ' ' . $retailer->last_name]) }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800">View All Orders</a>
            </div>
            @if ($recentOrders->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-gray-500">No orders found for this retailer.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Order ID</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->formatted_order_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-blue-600">
                                            ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColor = match ($order->status) {
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'completed' => 'bg-green-100 text-green-800',
                                                'cancelled', 'rejected' => 'bg-red-100 text-red-800',
                                                'returned' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                        <a href="#" onclick="openOrderModal(this)"
                                            data-order-id="{{ $order->id }}" data-status="{{ $order->status }}"
                                            data-retailer='@json($order->user)'
                                            data-details='@json($order->orderDetails)'
                                            data-delivery-address="{{ $order->orderDetails->first()->delivery_address ?? '' }}"
                                            data-created-at="{{ $order->created_at->format('F d, Y h:i A') }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- filepath: c:\xampp\htdocs\PConnect-Laravel\resources\views\distributors\orders\_order_detail_modal.blade.php -->
    <!-- Order Details Modal -->
    <div id="orderModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-3/4">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
                <h2 class="text-xl font-bold text-gray-800" id="modalTitle">Order Details</h2>
                <button onclick="closeModal()"
                    class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="modalContent" class="p-6">
                <!-- Modal Content (Products and retailer profile) is generated dynamically -->
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 flex justify-end p-4 bg-white border-t">
                <button onclick="closeModal()"
                    class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        var storageBaseUrl = "{{ asset('storage') }}";
        var currentOrderId = null;

        function openModal(row) {
            var orderId = row.getAttribute('data-order-id');
            var formattedOrderId = row.closest('tr').querySelector('td:first-child').textContent.trim();
            var orderStatus = row.getAttribute('data-status');
            currentOrderId = orderId;
            var retailer = JSON.parse(row.getAttribute('data-retailer'));
            var details = JSON.parse(row.getAttribute('data-details'));
            var dateTime = row.getAttribute('data-created-at');
            var deliveryAddress = row.getAttribute('data-delivery-address');

            document.getElementById('modalTitle').innerText = 'Order ' + formattedOrderId;

            var modalHtml = '<div class="space-y-6">';

            // Products Section
            modalHtml += '<div class="overflow-hidden bg-white rounded-lg shadow">';
            modalHtml +=
                '<div class="p-4 border-b bg-gray-50"><h3 class="text-lg font-semibold text-gray-800">Products Ordered</h3></div>';
            modalHtml += '<div class="overflow-x-auto">';
            modalHtml += '<table class="min-w-full divide-y divide-gray-200">';
            modalHtml += '<thead class="bg-gray-50"><tr>';
            modalHtml += '<th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Product</th>';
            modalHtml += '<th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Price</th>';
            modalHtml += '<th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Quantity</th>';
            modalHtml += '<th class="px-4 py-3 text-sm font-medium text-left text-gray-700">Subtotal</th>';
            modalHtml += '</tr></thead><tbody class="divide-y divide-gray-200">';

            let totalAmount = 0;
            details.forEach(function(detail) {
                modalHtml += '<tr class="hover:bg-gray-50">';
                modalHtml += '<td class="px-4 py-3">';
                modalHtml += '<div class="flex items-center gap-3">';
                if (detail.product && detail.product.image) {
                    modalHtml += '<img src="' + storageBaseUrl + '/' + detail.product.image + '" alt="' + detail
                        .product.product_name + '" class="object-cover w-16 h-16 rounded-lg" />';
                } else {
                    modalHtml += '<img src="/img/default-product.jpg" class="object-cover w-16 h-16 rounded-lg" />';
                }
                modalHtml += '<span class="font-medium text-gray-800">' + (detail.product ? detail.product
                    .product_name : 'Unknown Product') + '</span>';
                modalHtml += '</div></td>';
                modalHtml += '<td class="px-4 py-3">₱' + parseFloat(detail.price).toFixed(2) + '</td>';
                modalHtml += '<td class="px-4 py-3">' + detail.quantity + '</td>';
                modalHtml += '<td class="px-4 py-3 font-medium text-blue-600">₱' + parseFloat(detail.subtotal)
                    .toFixed(2) + '</td>';
                modalHtml += '</tr>';
                totalAmount += parseFloat(detail.subtotal);
            });

            modalHtml += '</tbody>';
            modalHtml += '<tfoot class="bg-gray-50"><tr>';
            modalHtml += '<td colspan="3" class="px-4 py-3 font-medium text-right text-gray-700">Total Amount:</td>';
            modalHtml += '<td class="px-4 py-3 font-bold text-blue-600">₱' + totalAmount.toFixed(2) + '</td>';
            modalHtml += '</tr></tfoot>';
            modalHtml += '</table></div></div>';

            // Order Info Section
            modalHtml += '<div class="p-4 bg-white rounded-lg shadow">';
            modalHtml += '<h3 class="mb-3 text-lg font-semibold text-gray-800">Order Information</h3>';
            modalHtml += '<div class="grid grid-cols-1 gap-3 md:grid-cols-2">';

            // Order Date & Status
            modalHtml += '<div class="p-3 rounded-lg bg-gray-50">';
            modalHtml += '<p class="text-sm font-medium text-gray-500">Order Date</p>';
            modalHtml += '<p class="text-gray-800">' + dateTime + '</p>';
            modalHtml += '</div>';

            modalHtml += '<div class="p-3 rounded-lg bg-gray-50">';
            modalHtml += '<p class="text-sm font-medium text-gray-500">Status</p>';

            let statusClass = '';
            if (orderStatus === 'completed') statusClass = 'bg-green-100 text-green-800';
            else if (orderStatus === 'processing') statusClass = 'bg-blue-100 text-blue-800';
            else if (orderStatus === 'pending') statusClass = 'bg-yellow-100 text-yellow-800';
            else if (orderStatus === 'rejected' || orderStatus === 'cancelled') statusClass = 'bg-red-100 text-red-800';
            else if (orderStatus === 'returned') statusClass = 'bg-gray-100 text-gray-800';
            else statusClass = 'bg-gray-100 text-gray-800';

            modalHtml += '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' + statusClass + '">';
            modalHtml += orderStatus.charAt(0).toUpperCase() + orderStatus.slice(1);
            modalHtml += '</span>';
            modalHtml += '</div>';

            // Delivery Address
            if (deliveryAddress) {
                modalHtml += '<div class="col-span-1 p-3 rounded-lg bg-gray-50 md:col-span-2">';
                modalHtml += '<p class="text-sm font-medium text-gray-500">Delivery Address</p>';
                modalHtml += '<p class="text-gray-800">' + deliveryAddress + '</p>';
                modalHtml += '</div>';
            }

            modalHtml += '</div>';
            modalHtml += '</div>';

            modalHtml += '</div>';

            document.getElementById('modalContent').innerHTML = modalHtml;
            document.getElementById('orderModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }
    </script>
</x-distributor-layout>
