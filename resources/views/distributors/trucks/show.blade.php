<x-distributor-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <a href="{{ route('distributors.trucks.index') }}"
                class="inline-block px-4 py-2 mb-4 text-sm font-medium text-gray-700 hover:text-green-400">← Back to
                Trucks</a>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Truck Info Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold">Truck Details</h2>
                            <p class="mt-1 text-gray-600">Plate Number: {{ $truck->plate_number }}</p>
                            <div class="mt-4 mb-6">
                                <h3 class="mb-3 text-lg font-medium text-gray-700">Delivery Locations:</h3>

                                @if ($truck->deliveryLocations && $truck->deliveryLocations->count() > 0)
                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                                        @foreach ($truck->deliveryLocations as $index => $location)
                                            <div
                                                class="p-3 border {{ $index === 0 ? 'border-blue-300 bg-blue-50' : 'border-gray-200' }} rounded-md">
                                                <div class="flex justify-between">
                                                    <div
                                                        class="font-medium {{ $index === 0 ? 'text-blue-700' : 'text-gray-700' }}">
                                                        {{ $index === 0 ? 'Primary Location' : 'Location #' . ($index + 1) }}
                                                    </div>
                                                </div>
                                                <div class="mt-1 font-semibold">
                                                    {{ $location->barangayName ?? 'Unknown Barangay' }}</div>
                                                @if ($location->street)
                                                    <div class="text-sm text-gray-600">{{ $location->street }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-3 mt-2 border border-gray-200 rounded-md">
                                        <span class="text-gray-500">No locations assigned to this truck</span>
                                    </div>
                                @endif
                            </div>
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $truck->status === 'available'
                                    ? 'bg-green-100 text-green-800'
                                    : ($truck->status === 'on_delivery'
                                        ? 'bg-blue-100 text-blue-800'
                                        : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $truck->status)) }}
                            </span>
                        </div>
                        <div>
                            <a href="{{ route('distributors.trucks.delivery-history', $truck) }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Delivery History
                            </a>
                        </div>
                    </div>

                    <!-- Deliveries Table -->
                    <div class="mt-8">
                        <div class="mb-4">
                            <h3 class="text-xl font-semibold">Delivery</h3>
                        </div>

                        @if ($deliveries->isEmpty())
                            <div class="p-4 text-center bg-gray-50">
                                <p class="text-gray-600">No deliveries found for this truck.</p>
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
                                                Customer</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Delivery Address</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Started At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($deliveries as $delivery)
                                            <tr class="cursor-pointer hover:bg-gray-50"
                                                onclick="openDeliveryModal({{ json_encode($delivery) }}, {{ json_encode($delivery->order) }}, {{ json_encode($delivery->order->orderDetails) }})">
                                                <td class="px-6 py-4">{{ $delivery->order->formatted_order_id }}</td>
                                                <td class="px-6 py-4">
                                                    {{ $delivery->order->user->first_name }}
                                                    {{ $delivery->order->user->last_name }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if ($delivery->order->orderDetails->isNotEmpty())
                                                        {{ $delivery->order->orderDetails->first()->delivery_address }}
                                                    @else
                                                        <span class="text-gray-400">No address provided</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                        {{ $delivery->status === 'pending'
                                                            ? 'bg-yellow-100 text-yellow-800'
                                                            : ($delivery->status === 'in_transit'
                                                                ? 'bg-blue-100 text-blue-800'
                                                                : ($delivery->status === 'delivered'
                                                                    ? 'bg-green-100 text-green-800'
                                                                    : ($delivery->status === 'out_for_delivery'
                                                                        ? 'bg-purple-100 text-purple-800'
                                                                        : 'bg-red-100 text-red-800'))) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    @if ($delivery->pivot && $delivery->pivot->started_at)
                                                        {{ \Carbon\Carbon::parse($delivery->pivot->started_at)->format('M d, Y H:i') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div
                                class="flex flex-col items-center mt-6 space-y-4 md:flex-row md:justify-end md:space-y-0">
                                <!-- Out for Delivery Button -->
                                @if (!$deliveries->isEmpty() && $truck->status === 'available')
                                    <form action="{{ route('distributors.trucks.out-for-delivery', $truck) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                            Out for Delivery
                                        </button>
                                    </form>
                                @else
                                    <div></div>
                                @endif

                                <!-- Pagination -->
                                <div class="mt-2 md:mt-0">
                                    {{ $deliveries->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Details Modal -->
    <div id="deliveryModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-3/4">
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

            <div id="deliveryModalContent" class="p-6">
                <!-- Modal Content will be dynamically generated -->
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 flex justify-end gap-4 p-4 bg-white border-t">
                <div id="deliveryActionButtons">
                    <button id="markDeliveredBtn" onclick="markAsDelivered()"
                        class="px-4 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                        Mark as Delivered
                    </button>
                </div>
                <button onclick="closeDeliveryModal()"
                    class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Mark as Delivered -->
    <form id="markDeliveredForm" method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>

    <script>
        let currentDeliveryId = null;
        const storageBaseUrl = "{{ asset('storage') }}";

        function openDeliveryModal(delivery, order, orderDetails) {
            try {
                console.log('Delivery:', delivery);
                console.log('Order:', order);
                console.log('Order Details:', orderDetails);

                // Validate data is available
                if (!delivery) {
                    console.error('Delivery data is missing');
                    alert('Error: Delivery data is missing');
                    return;
                }

                if (!order) {
                    console.error('Order data is missing');
                    alert('Error: Order data is missing');
                    return;
                }

                currentDeliveryId = delivery.id;
                document.getElementById('deliveryModalTitle').innerText = 'Order #' + (order.formatted_order_id || 'N/A');

                let modalHtml = '<div class="space-y-6">';

                // Order Status Card
                modalHtml += '<div class="p-3 rounded-lg ' +
                    (delivery.status === 'delivered' ? 'bg-green-50 border border-green-200' :
                        (delivery.status === 'out_for_delivery' ? 'bg-purple-50 border border-purple-200' :
                            'bg-blue-50 border border-blue-200')) + '">';
                modalHtml += '<div class="flex items-center justify-between">';
                modalHtml += '<h3 class="text-lg font-semibold ' +
                    (delivery.status === 'delivered' ? 'text-green-700' :
                        (delivery.status === 'out_for_delivery' ? 'text-purple-700' :
                            'text-blue-700')) + '">Delivery Status</h3>';
                modalHtml += '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' +
                    (delivery.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                        (delivery.status === 'in_transit' ? 'bg-blue-100 text-blue-800' :
                            (delivery.status === 'delivered' ? 'bg-green-100 text-green-800' :
                                (delivery.status === 'out_for_delivery' ? 'bg-purple-100 text-purple-800' :
                                    'bg-red-100 text-red-800')))) + '">' +
                    (delivery.status || '').replace(/_/g, ' ').replace(/(^\w{1})|(\s+\w{1})/g, letter => letter
                        .toUpperCase()) + '</span>';
                modalHtml += '</div>';
                modalHtml += '<div class="mt-1 text-sm ' +
                    (delivery.status === 'delivered' ? 'text-green-600' :
                        (delivery.status === 'out_for_delivery' ? 'text-purple-600' :
                            'text-blue-600')) + '">';
                modalHtml += 'Tracking #: ' + (delivery.tracking_number || 'N/A');
                modalHtml += '</div>';
                modalHtml += '</div>';

                // Products Section - Check if orderDetails is available and not empty
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

                        // Check if product exists before accessing properties
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
                } else {
                    // Display a message if no order details available
                    modalHtml += '<div class="p-4 border rounded-lg bg-gray-50">';
                    modalHtml += '<p class="text-center text-gray-500">No product details available for this order</p>';
                    modalHtml += '</div>';
                }

                // Delivery Information - Only show if order has user information
                if (order && order.user) {
                    modalHtml += '<div class="p-4 mt-4 bg-white rounded-lg shadow">';
                    modalHtml += '<h3 class="mb-3 text-lg font-semibold text-gray-800">Delivery Information</h3>';
                    modalHtml += '<div class="grid grid-cols-1 gap-4 md:grid-cols-2">';

                    // Customer details
                    modalHtml += '<div class="p-3 border rounded-lg">';
                    modalHtml += '<h4 class="font-medium text-gray-700">Customer Details</h4>';
                    modalHtml += '<div class="mt-2 space-y-1 text-sm">';
                    modalHtml += '<p><span class="font-medium">Name:</span> ' +
                        (order.user.first_name || '') + ' ' + (order.user.last_name || '') + '</p>';

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

                    // Safely check if orderDetails has elements and if the first element has a delivery_address
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

                document.getElementById('deliveryModalContent').innerHTML = modalHtml;
                document.getElementById('deliveryModal').classList.remove('hidden');

                // Only show the Delivered button for deliveries that are out_for_delivery
                if (delivery.status === 'out_for_delivery') {
                    document.getElementById('markDeliveredBtn').classList.remove('hidden');
                } else {
                    document.getElementById('markDeliveredBtn').classList.add('hidden');
                }
            } catch (error) {
                console.error('Error in openDeliveryModal:', error);
                alert('An error occurred while displaying delivery details: ' + error.message);
            }
        }

        function closeDeliveryModal() {
            document.getElementById('deliveryModal').classList.add('hidden');
        }

        function markAsDelivered() {
            if (!currentDeliveryId) return;

            Swal.fire({
                title: 'Confirm Delivery',
                text: "Are you sure this order has been delivered?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, mark as delivered'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('markDeliveredForm');
                    // Fix: Generate URL correctly with ID as a parameter
                    form.action = "{{ route('distributors.deliveries.mark-delivered', ['delivery' => ':id']) }}"
                        .replace(':id', currentDeliveryId);
                    form.submit();
                }
            });
        }
    </script>
</x-distributor-layout>
