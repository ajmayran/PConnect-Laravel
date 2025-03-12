<x-distributor-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('distributors.trucks.show', $truck) }}"
                    class="inline-block px-4 py-2 mb-4 text-sm font-medium text-gray-700 hover:text-green-400">← Back to
                    Truck</a>

                <div class="text-right">
                    <h1 class="text-2xl font-semibold text-gray-900">Delivery History</h1>
                    <p class="text-gray-600">Truck: {{ $truck->plate_number }}</p>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filter/Sort options - Always show these regardless of results -->
                    <div class="flex flex-wrap items-center justify-between mb-6 gap-y-3">
                        <div class="flex flex-wrap items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">Filter by:</span>
                            <a href="{{ route('distributors.trucks.delivery-history', ['truck' => $truck, 'filter' => 'all', 'period' => request('period')]) }}"
                                class="px-3 py-1 text-sm {{ !request('filter') || request('filter') == 'all' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} rounded-full">
                                All
                            </a>
                            <a href="{{ route('distributors.trucks.delivery-history', ['truck' => $truck, 'filter' => 'delivered', 'period' => request('period')]) }}"
                                class="px-3 py-1 text-sm {{ request('filter') == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} rounded-full">
                                Delivered
                            </a>
                            <a href="{{ route('distributors.trucks.delivery-history', ['truck' => $truck, 'filter' => 'failed', 'period' => request('period')]) }}"
                                class="px-3 py-1 text-sm {{ request('filter') == 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }} rounded-full">
                                Failed
                            </a>
                        </div>


                        <!-- Date Range Picker (simplified) -->
                        <div class="flex flex-wrap items-center space-x-2">
                            <span class="text-sm font-medium text-gray-700">Period:</span>
                            <select onchange="window.location.href=this.value"
                                class="px-3 py-1 text-sm bg-gray-100 border-none rounded-full focus:ring-blue-500">
                                <option
                                    value="{{ route('distributors.trucks.delivery-history', ['truck' => $truck, 'period' => 'all', 'filter' => request('filter')]) }}"
                                    {{ !request('period') || request('period') == 'all' ? 'selected' : '' }}>
                                    All Time
                                </option>
                                <option
                                    value="{{ route('distributors.trucks.delivery-history', ['truck' => $truck, 'period' => 'today', 'filter' => request('filter')]) }}"
                                    {{ request('period') == 'today' ? 'selected' : '' }}>
                                    Today
                                </option>
                                <option
                                    value="{{ route('distributors.trucks.delivery-history', ['truck' => $truck, 'period' => 'week', 'filter' => request('filter')]) }}"
                                    {{ request('period') == 'week' ? 'selected' : '' }}>
                                    This Week
                                </option>
                                <option
                                    value="{{ route('distributors.trucks.delivery-history', ['truck' => $truck, 'period' => 'month', 'filter' => request('filter')]) }}"
                                    {{ request('period') == 'month' ? 'selected' : '' }}>
                                    This Month
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Applied filters summary -->
                    <div class="flex items-center mb-6 space-x-2">
                        <span class="text-sm text-gray-600">
                            Showing
                            <span class="font-medium">
                                {{ request('filter') == 'delivered' ? 'delivered' : (request('filter') == 'failed' ? 'failed' : 'all') }}
                            </span>
                            deliveries

                            @if (request('period') == 'today')
                                from today
                            @elseif(request('period') == 'week')
                                from this week
                            @elseif(request('period') == 'month')
                                from this month
                            @else
                                from all time
                            @endif
                        </span>

                        @if (request('filter') || request('period'))
                            <a href="{{ route('distributors.trucks.delivery-history', ['truck' => $truck]) }}"
                                class="inline-flex items-center px-2 py-1 text-xs text-gray-600 bg-gray-100 rounded hover:bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear filters
                            </a>
                        @endif
                    </div>

                    @if ($deliveryHistory->isEmpty())
                        <!-- No results message -->
                        <div class="p-8 text-center rounded-md bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-4 text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-medium text-gray-600">No delivery history found</p>
                            <p class="mt-1 text-gray-500">Try changing your filter options or check back later.</p>
                        </div>
                    @else
                        <!-- Delivery History Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Order ID</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Customer</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Delivery Address</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Payment</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Completed At</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($deliveryHistory as $delivery)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">{{ $delivery->order->formatted_order_id ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($delivery->order && $delivery->order->user)
                                                    {{ $delivery->order->user->first_name }}
                                                    {{ $delivery->order->user->last_name }}
                                                @else
                                                    <span class="text-gray-400">Unknown</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($delivery->order && $delivery->order->orderDetails && $delivery->order->orderDetails->isNotEmpty())
                                                    {{ $delivery->order->orderDetails->first()->delivery_address }}
                                                @else
                                                    <span class="text-gray-400">No address provided</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if ($delivery->order && $delivery->order->payment)
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                        {{ $delivery->order->payment->payment_status === 'paid'
                                                            ? 'bg-green-100 text-green-800'
                                                            : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ ucfirst($delivery->order->payment->payment_status) }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full">
                                                        No record
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $delivery->updated_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <button
                                                    onclick="viewDeliveryDetails({{ json_encode($delivery) }}, {{ json_encode($delivery->order) }}, {{ json_encode($delivery->order->orderDetails) }})"
                                                    class="px-3 py-1 text-xs text-white bg-blue-500 rounded hover:bg-blue-600">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $deliveryHistory->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Details Modal -->
    <div id="deliveryHistoryModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-3/4">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
                <h2 class="text-xl font-bold text-gray-800" id="deliveryHistoryModalTitle">Delivery Details</h2>
                <button onclick="closeDeliveryHistoryModal()"
                    class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="deliveryHistoryModalContent" class="p-6">
                <!-- Modal Content will be dynamically generated -->
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 flex justify-end p-4 bg-white border-t">
                <button onclick="closeDeliveryHistoryModal()"
                    class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        const storageBaseUrl = "{{ asset('storage') }}";

        function viewDeliveryDetails(delivery, order, orderDetails) {
            try {
                document.getElementById('deliveryHistoryModalTitle').innerText = 'Order #' + (order.formatted_order_id ||
                    'N/A');

                let modalHtml = '<div class="space-y-6">';

                // Order Status Card
                modalHtml += '<div class="p-3 rounded-lg ' +
                    (delivery.status === 'delivered' ? 'bg-green-50 border border-green-200' :
                        'bg-red-50 border border-red-200') + '">';
                modalHtml += '<div class="flex items-center justify-between">';
                modalHtml += '<h3 class="text-lg font-semibold ' +
                    (delivery.status === 'delivered' ? 'text-green-700' : 'text-red-700') + '">Delivery Status</h3>';
                modalHtml += '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' +
                    (delivery.status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + '">' +
                    (delivery.status || '').replace(/_/g, ' ').replace(/(^\w{1})|(\s+\w{1})/g, letter => letter
                        .toUpperCase()) + '</span>';
                modalHtml += '</div>';
                modalHtml += '<div class="mt-1 text-sm ' + (delivery.status === 'delivered' ? 'text-green-600' :
                    'text-red-600') + '">';
                modalHtml += 'Tracking #: ' + (delivery.tracking_number || 'N/A');
                modalHtml += '<br>Completed on: ' + new Date(delivery.updated_at).toLocaleString();
                modalHtml += '</div>';
                modalHtml += '</div>';

                if (order && order.payment) {
                    const paymentStatus = order.payment.payment_status === 'paid' ? {
                        bg: 'bg-green-50',
                        border: 'border-green-200',
                        text: 'text-green-700',
                        badge: 'bg-green-100 text-green-800'
                    } : {
                        bg: 'bg-yellow-50',
                        border: 'border-yellow-200',
                        text: 'text-yellow-700',
                        badge: 'bg-yellow-100 text-yellow-800'
                    };

                    modalHtml += `<div class="p-3 rounded-lg ${paymentStatus.bg} border ${paymentStatus.border}">`;
                    modalHtml += '<div class="flex items-center justify-between">';
                    modalHtml += `<h3 class="text-lg font-semibold ${paymentStatus.text}">Payment Information</h3>`;
                    modalHtml +=
                        `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${paymentStatus.badge}">`;
                    modalHtml += order.payment.payment_status.charAt(0).toUpperCase() + order.payment.payment_status.slice(
                        1);
                    modalHtml += '</span>';
                    modalHtml += '</div>';
                    modalHtml += `<div class="mt-1 text-sm ${paymentStatus.text}">`;

                    if (order.payment.paid_at) {
                        modalHtml += 'Paid on: ' + new Date(order.payment.paid_at).toLocaleString();
                    }

                    if (order.payment.payment_note) {
                        modalHtml += '<div class="p-2 mt-2 border-l-4 border-gray-300 bg-gray-50">';
                        modalHtml += '<p class="italic text-gray-700">' + order.payment.payment_note + '</p>';
                        modalHtml += '</div>';
                    }

                    modalHtml += '</div>';
                    modalHtml += '</div>';
                }

                // Products Section
                if (orderDetails && Array.isArray(orderDetails) && orderDetails.length > 0) {
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

                    orderDetails.forEach(function(detail) {
                        if (!detail) return;

                        modalHtml += '<tr class="hover:bg-gray-50">';
                        modalHtml += '<td class="px-4 py-3">';
                        modalHtml += '<div class="flex items-center gap-3">';

                        if (detail.product && detail.product.image) {
                            modalHtml += '<img src="' + storageBaseUrl + '/' + detail.product.image + '" alt="' +
                                detail.product.product_name + '" class="object-cover w-16 h-16 rounded-lg" />';
                        } else {
                            modalHtml +=
                                '<div class="flex items-center justify-center w-16 h-16 text-gray-400 bg-gray-100 rounded-lg">No image</div>';
                        }

                        modalHtml += '<span class="font-medium text-gray-800">' + (detail.product ? detail.product
                            .product_name : 'Unknown Product') + '</span>';
                        modalHtml += '</div></td>';
                        modalHtml += '<td class="px-4 py-3">₱' + parseFloat(detail.price || 0).toFixed(2) + '</td>';
                        modalHtml += '<td class="px-4 py-3">' + (detail.quantity || 0) + '</td>';
                        modalHtml += '<td class="px-4 py-3 font-medium text-blue-600">₱' + parseFloat(detail
                            .subtotal || 0).toFixed(2) + '</td>';
                        modalHtml += '</tr>';

                        totalAmount += parseFloat(detail.subtotal || 0);
                    });

                    modalHtml += '</tbody>';
                    modalHtml += '<tfoot class="bg-gray-50"><tr>';
                    modalHtml +=
                        '<td colspan="3" class="px-4 py-3 font-medium text-right text-gray-700">Total Amount:</td>';
                    modalHtml += '<td class="px-4 py-3 font-bold text-blue-600">₱' + totalAmount.toFixed(2) + '</td>';
                    modalHtml += '</tr></tfoot>';
                    modalHtml += '</table></div></div>';
                }

                // Delivery Information
                if (order && order.user) {
                    modalHtml += '<div class="p-4 mt-4 bg-white rounded-lg shadow">';
                    modalHtml += '<h3 class="mb-3 text-lg font-semibold text-gray-800">Delivery Information</h3>';
                    modalHtml += '<div class="grid grid-cols-1 gap-4 md:grid-cols-2">';

                    // Customer details
                    modalHtml += '<div class="p-3 border rounded-lg">';
                    modalHtml += '<h4 class="font-medium text-gray-700">Customer Details</h4>';
                    modalHtml += '<div class="mt-2 space-y-1 text-sm">';
                    modalHtml += '<p><span class="font-medium">Name:</span> ' + (order.user.first_name || '') + ' ' + (order
                        .user.last_name || '') + '</p>';

                    if (order.user.email) {
                        modalHtml += '<p><span class="font-medium">Email:</span> ' + order.user.email + '</p>';
                    }

                    if (order.user.retailer_profile && order.user.retailer_profile.phone) {
                        modalHtml += '<p><span class="font-medium">Phone:</span> ' + order.user.retailer_profile.phone +
                            '</p>';
                    }
                    modalHtml += '</div>';
                    modalHtml += '</div>';

                    // Delivery address
                    modalHtml += '<div class="p-3 border rounded-lg">';
                    modalHtml += '<h4 class="font-medium text-gray-700">Delivery Address</h4>';
                    modalHtml += '<div class="mt-2 text-sm">';

                    if (orderDetails && Array.isArray(orderDetails) && orderDetails.length > 0 && orderDetails[0]
                        .delivery_address) {
                        modalHtml += '<p>' + orderDetails[0].delivery_address + '</p>';
                    } else {
                        modalHtml += '<p class="text-gray-500">No address provided</p>';
                    }

                    modalHtml += '</div>';
                    modalHtml += '</div>';

                    modalHtml += '</div>';
                    modalHtml += '</div>';
                }

                modalHtml += '</div>';

                document.getElementById('deliveryHistoryModalContent').innerHTML = modalHtml;
                document.getElementById('deliveryHistoryModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error in viewDeliveryDetails:', error);
                alert('An error occurred while displaying delivery details: ' + error.message);
            }
        }

        function closeDeliveryHistoryModal() {
            document.getElementById('deliveryHistoryModal').classList.add('hidden');
        }
    </script>
</x-distributor-layout>
