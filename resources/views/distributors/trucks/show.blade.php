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
                                class="flex flex-col items-center justify-end mt-6 space-y-4 md:flex-row md:justify-end md:space-y-0">

                                <!-- Pagination -->
                                <div class="mt-2 md:mt-0">
                                    {{ $deliveries->links() }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Out for Delivery Button -->
                    <div class="flex justify-end pt-4 mt-6 border-t border-gray-200">
                        @if (!$deliveries->isEmpty() && $truck->status === 'available')
                            <button type="button" onclick="openEstimatedDeliveryModal()"
                                class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Out for Delivery
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="estimatedDeliveryModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl md:w-1/3 sm:w-2/3">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Set Estimated Delivery Date</h2>
                <button onclick="closeEstimatedDeliveryModal()"
                    class="p-1 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('distributors.trucks.out-for-delivery', $truck) }}" method="POST">
                @csrf
                <div class="p-6">
                    <div class="mb-4">
                        <label for="estimated_delivery" class="block mb-2 text-sm font-medium text-gray-700">
                            Estimated Delivery Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="estimated_delivery" name="estimated_delivery"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            min="{{ date('Y-m-d') }}" required>
                        <p class="mt-1 text-sm text-gray-500">Select the expected delivery date</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-4 rounded-b-lg bg-gray-50">
                    <button type="button" onclick="closeEstimatedDeliveryModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Confirm
                    </button>
                </div>
            </form>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
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
                    @php
                        $availableTrucks = optional(Auth::user()->distributor)->trucks ?? collect();
                        $availableTrucksCount = 0;

                        // Check if $availableTrucks is a collection before using collection methods
                        if ($availableTrucks instanceof \Illuminate\Support\Collection) {
                            $availableTrucksCount = $availableTrucks
                                ->where('status', 'available')
                                ->where('id', '!=', $truck->id)
                                ->count();
                        }
                    @endphp

                    @if ($availableTrucksCount > 0)
                        <button id="moveDeliveryBtn" onclick="openMoveDeliveryModal()"
                            class="px-4 py-2 mr-2 font-medium text-white transition-colors bg-yellow-600 rounded-lg hover:bg-yellow-700">
                            Move to Another Truck
                        </button>
                    @endif
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

    <!-- Move Delivery Modal -->
    <div id="moveDeliveryModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl md:w-1/3 sm:w-2/3">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Move Delivery to Another Truck</h2>
                <button onclick="closeMoveDeliveryModal()"
                    class="p-1 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="moveDeliveryForm" action="{{ route('distributors.deliveries.move-to-truck') }}"
                method="POST">
                @csrf
                <input type="hidden" id="delivery_id_to_move" name="delivery_id">

                <div class="p-6">
                    <div class="mb-4">
                        <label for="new_truck_id" class="block mb-2 text-sm font-medium text-gray-700">
                            Select Truck <span class="text-red-500">*</span>
                        </label>
                        <select id="new_truck_id" name="truck_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">Select a truck...</option>
                            @foreach (Auth::user()->distributor->trucks->where('status', 'available')->where('id', '!=', $truck->id) as $availableTruck)
                                <option value="{{ $availableTruck->id }}">
                                    {{ $availableTruck->plate_number }}
                                    @if ($availableTruck->deliveries()->whereIn('status', ['pending', 'in_transit', 'out_for_delivery'])->count() > 0)
                                        ({{ $availableTruck->deliveries()->whereIn('status', ['pending', 'in_transit', 'out_for_delivery'])->count() }}
                                        deliveries)
                                    @else
                                        (No deliveries)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Only available trucks are shown</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-4 rounded-b-lg bg-gray-50">
                    <button type="button" onclick="closeMoveDeliveryModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Move Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Modal when marking as delivered -->
    <div id="paymentModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl md:w-1/3 sm:w-2/3">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Payment Information</h2>
                <button onclick="closePaymentModal()"
                    class="p-1 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="paymentForm" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <h3 class="mb-3 font-medium text-gray-800 text-md">Record payment details upon delivery</h3>

                        <label class="block mb-2 text-sm font-medium text-gray-700">Payment Status</label>
                        <div class="flex gap-4">
                            <label class="flex items-center px-3 py-2 border rounded-md hover:bg-gray-50">
                                <input type="radio" name="payment_status" value="paid"
                                    class="w-4 h-4 text-green-600" checked>
                                <span class="ml-2 font-medium text-green-700">Paid</span>
                            </label>
                            <label class="flex items-center px-3 py-2 border rounded-md hover:bg-gray-50">
                                <input type="radio" name="payment_status" value="unpaid"
                                    class="w-4 h-4 text-red-600">
                                <span class="ml-2 font-medium text-red-700">Unpaid</span>
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">This will update both delivery status and payment record
                        </p>
                    </div>

                    <div>
                        <label for="payment_note" class="block mb-2 text-sm font-medium text-gray-700">Payment Notes
                            (Optional)</label>
                        <textarea id="payment_note" name="payment_note" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Add notes about this payment (e.g., paid with cash, promised to pay next week, etc.)"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-4 rounded-b-lg bg-gray-50">
                    <button type="button" onclick="closePaymentModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Complete Delivery
                    </button>
                </div>
            </form>
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

                // Create order ID even if formatted_order_id is missing
                let orderId = 'N/A';
                if (order.formatted_order_id) {
                    orderId = order.formatted_order_id;
                } else if (order.id) {
                    // Create a simple formatted ID if the attribute is missing
                    orderId = 'ORD-' + String(order.id).padStart(6, '0');
                }

                document.getElementById('deliveryModalTitle').innerText = 'Order ID: ' + orderId;

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

            const moveBtn = document.getElementById('moveDeliveryBtn');
            if (moveBtn) {
                if (delivery.status === 'pending' || delivery.status === 'in_transit') {
                    moveBtn.classList.remove('hidden');
                } else {
                    moveBtn.classList.add('hidden');
                }
            }
        }

        function closeDeliveryModal() {
            document.getElementById('deliveryModal').classList.add('hidden');
        }

        function markAsDelivered() {
            if (!currentDeliveryId) return;

            // Show payment modal instead of direct confirmation
            document.getElementById('paymentModal').classList.remove('hidden');

            // Set the form action dynamically
            const paymentForm = document.getElementById('paymentForm');
            paymentForm.action = "{{ route('distributors.deliveries.mark-delivered', ['delivery' => ':id']) }}"
                .replace(':id', currentDeliveryId);
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        document.getElementById('paymentForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent immediate form submission

            const form = this;
            const paymentStatus = form.querySelector('input[name="payment_status"]:checked').value;

            Swal.fire({
                title: 'Confirm Delivery & Payment',
                html: `
            <p>Are you sure this order has been delivered?</p>
            <p class="mt-2">Payment will be marked as <strong>${paymentStatus === 'paid' ? 'PAID' : 'UNPAID'}</strong>.</p>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4CAF50',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Actually submit the form
                }
            });
        });

        function openEstimatedDeliveryModal() {
            // Set minimum date to today
            document.getElementById('estimated_delivery').min = new Date().toISOString().split('T')[0];

            // Set default value to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('estimated_delivery').value = tomorrow.toISOString().split('T')[0];

            // Show modal
            document.getElementById('estimatedDeliveryModal').classList.remove('hidden');
        }

        function closeEstimatedDeliveryModal() {
            document.getElementById('estimatedDeliveryModal').classList.add('hidden');
        }

        function openMoveDeliveryModal() {
            if (!currentDeliveryId) return;

            // Set the hidden delivery ID field
            document.getElementById('delivery_id_to_move').value = currentDeliveryId;

            // Show the modal
            document.getElementById('moveDeliveryModal').classList.remove('hidden');

            // Close the delivery details modal
            closeDeliveryModal();
        }

        function closeMoveDeliveryModal() {
            document.getElementById('moveDeliveryModal').classList.add('hidden');
        }
    </script>
</x-distributor-layout>
