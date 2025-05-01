<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between pb-4 border-b">
        <div>
            <p class="mt-1 text-sm text-gray-500">Ordered on {{ $order->created_at->format('F d, Y') }}</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            <span
                class="px-3 py-1 text-sm rounded-full {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'returned' ? 'bg-blue-100 text-blue-800' : ($order->status === 'refunded' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')) }}">
                Order Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
            @if (isset($order->payment) && $order->payment->payment_status)
                <span
                    class="px-3 py-1 text-sm rounded-full {{ $order->payment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($order->payment->payment_status === 'pending' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                    Payment Status: {{ ucfirst($order->payment->payment_status) }}
                </span>
            @endif
            
            <!-- Delivery Status Badge - only for regular orders -->
            @if (!$order->is_multi_address && isset($order->delivery))
                <span
                    class="px-3 py-1 text-sm rounded-full {{ in_array($order->delivery->status, ['delivered', 'completed']) ? 'bg-green-100 text-green-800' : ($order->delivery->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                    Delivery Status: {{ ucfirst(str_replace('_', ' ', $order->delivery->status)) }}
                </span>
            @endif

            <!-- Multi-address badge -->
            @if ($order->is_multi_address)
                <span class="px-3 py-1 text-sm font-medium text-white bg-purple-600 rounded-full">
                    Multiple Delivery Addresses
                </span>
            @endif
        </div>
    </div>

    <!-- Group order details by distributor -->
    @php
        $orderDetailsByDistributor = $order->orderDetails->groupBy(function ($detail) {
            return $detail->product->distributor_id ?? 'unknown';
        });
    @endphp

    @foreach ($orderDetailsByDistributor as $distributorId => $details)
        <!-- Order Items Table -->
        <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Image
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Product</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Quantity</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($details as $detail)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-6 py-4">
                                @if ($detail->product->image)
                                    <img src="{{ asset('storage/products/' . basename($detail->product->image)) }}"
                                        alt="{{ $detail->product->product_name }}" class="w-12 h-12 rounded">
                                @else
                                    <span class="text-gray-500">No Image</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $detail->product->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $detail->quantity }}
                                @if ($detail->free_items > 0)
                                    <span class="ml-1 text-xs text-green-600">(+{{ $detail->free_items }} free)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($detail->discount_amount > 0)
                                    <span
                                        class="text-xs text-gray-500 line-through">₱{{ number_format($detail->price, 2) }}</span>
                                    <br>
                                    <span
                                        class="text-green-600">₱{{ number_format($detail->price - $detail->discount_amount / $detail->quantity, 2) }}</span>
                                @else
                                    ₱{{ number_format($detail->price, 2) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <!-- Order Summary -->
    <div class="p-6 mt-6 rounded-lg bg-gray-50">
        <div class="flex items-center justify-between mb-4">
            <span class="text-base font-medium text-gray-900">Order Summary</span>
        </div>
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Subtotal</span>
                <span
                    class="text-sm font-medium text-gray-900">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</span>
            </div>
            <div class="flex justify-between pt-4 border-t border-gray-200">
                <span class="text-base font-medium text-gray-900">Total Amount</span>
                <span
                    class="text-lg font-bold text-gray-900">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Delivery Information -->
    @if ($order->is_multi_address)
        <div class="p-6 border border-gray-200 rounded-lg">
            <h4 class="mb-4 text-base font-medium text-gray-900">Delivery Information</h4>

            <!-- Show badge for multi-address -->
            <div class="px-4 py-3 mb-4 border border-purple-200 rounded-lg bg-purple-50">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="font-medium text-purple-800">Multiple Delivery Addresses</span>
                </div>
                <p class="mt-1 text-sm text-purple-700 ml-7">
                    This order is being delivered to multiple addresses as specified below.
                </p>
            </div>

            <!-- Multi-address details -->
            <div class="mt-4 space-y-4">
                @php
                    // First try to get deliveries if they exist (for accepted orders)
                    $deliveries = \App\Models\Delivery::where('order_id', $order->id)->get();

                    // If no deliveries yet (for pending orders), get item deliveries directly
                    $showItemDeliveries = $deliveries->isEmpty() && $order->status == 'pending';
                    if ($showItemDeliveries) {
                        $orderItemDeliveries = \App\Models\OrderItemDelivery::whereHas('orderDetail', function (
                            $query,
                        ) use ($order) {
                            $query->where('order_id', $order->id);
                        })
                            ->with(['address', 'orderDetail.product'])
                            ->get();

                        // Group by address
                        $groupedItemDeliveries = $orderItemDeliveries->groupBy('address_id');
                    }
                @endphp

                @if ($deliveries->isNotEmpty())
                    @foreach ($deliveries as $index => $delivery)
                        <div
                            class="p-4 border border-gray-200 rounded-lg {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <h5 class="mb-2 font-medium text-gray-900">Delivery Location {{ $index + 1 }}</h5>

                            <!-- Delivery address -->
                            <div class="flex items-start mb-3">
                                <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="ml-3 text-sm text-gray-600">
                                    @if ($delivery->address)
                                        {{ $delivery->address->barangay_name }},
                                        {{ $delivery->address->street ?? 'No street specified' }}
                                    @else
                                        Address information not available
                                    @endif
                                </p>
                            </div>

                            <!-- Delivery status -->
                            <div class="flex items-start mb-3">
                                <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-600">Status:
                                        <span
                                            class="font-medium {{ $delivery->status === 'delivered' ? 'text-green-600' : 'text-blue-600' }}">
                                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Items for this delivery -->
                            <div class="mt-3">
                                <p class="mb-2 text-sm font-medium text-gray-700">Items in this delivery:</p>
                                <ul class="pl-5 mt-1 space-y-1 text-sm list-disc">
                                    @php
                                        $itemDeliveries = \App\Models\OrderItemDelivery::where(
                                            'delivery_id',
                                            $delivery->id,
                                        )->get();
                                    @endphp

                                    @foreach ($itemDeliveries as $itemDelivery)
                                        <li class="text-gray-600">
                                            @if ($itemDelivery->orderDetail && $itemDelivery->orderDetail->product)
                                                {{ $itemDelivery->orderDetail->product->product_name }}
                                                <span class="text-gray-500">(Qty: {{ $itemDelivery->quantity }})</span>
                                            @else
                                                Product information not available
                                            @endif
                                        </li>
                                    @endforeach

                                    @if ($itemDeliveries->isEmpty())
                                        <li class="text-gray-500">No items found for this delivery</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @elseif ($showItemDeliveries && $groupedItemDeliveries->count() > 0)
                    @foreach ($groupedItemDeliveries as $addressId => $items)
                        @php
                            $address = $items->first()->address;
                            $index = $loop->index;
                        @endphp
                        <div
                            class="p-4 border border-gray-200 rounded-lg {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                            <h5 class="mb-2 font-medium text-gray-900">Delivery Location {{ $index + 1 }}</h5>

                            <!-- Delivery address -->
                            <div class="flex items-start mb-3">
                                <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="ml-3 text-sm text-gray-600">
                                    @if ($address)
                                        {{ $address->barangay_name }}, {{ $address->street ?? 'No street specified' }}
                                    @else
                                        Address information not available
                                    @endif
                                </p>
                            </div>

                            <!-- Pending status for all items -->
                            <div class="flex items-start mb-3">
                                <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-600">Status:
                                        <span class="font-medium text-blue-600">
                                            Pending
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Items for this address -->
                            <div class="mt-3">
                                <p class="mb-2 text-sm font-medium text-gray-700">Items for this location:</p>
                                <ul class="pl-5 mt-1 space-y-1 text-sm list-disc">
                                    @foreach ($items as $item)
                                        <li class="text-gray-600">
                                            @if ($item->orderDetail && $item->orderDetail->product)
                                                {{ $item->orderDetail->product->product_name }}
                                                <span class="text-gray-500">(Qty: {{ $item->quantity }})</span>
                                            @else
                                                Product information not available
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">No delivery information available yet.</p>
                @endif
            </div>
        </div>
    @elseif ($order->orderDetails->first()->delivery_address)
        <div class="p-6 border border-gray-200 rounded-lg">
            <h4 class="mb-4 text-base font-medium text-gray-900">Delivery Information</h4>
            <div class="space-y-2">
                <div class="flex items-start">
                    <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="ml-3 text-sm text-gray-600">{{ $order->orderDetails->first()->delivery_address }}</p>
                </div>

                @if ($order->delivery)
                    <div class="flex items-start mt-3">
                        <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Status:
                                <span
                                    class="font-medium {{ $order->delivery->status === 'delivered' ? 'text-green-600' : 'text-blue-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->delivery->status)) }}
                                </span>
                            </p>

                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Return/Refund Information (if applicable) -->
    @if ($order->status === 'returned' || $order->status === 'refunded')
        @php
            $returnRequest = $order->returnRequests()->latest()->first();
            $refund = $order->refunds()->latest()->first();
        @endphp

        @if ($returnRequest)
            <div
                class="p-6 border border-gray-200 rounded-lg {{ $order->status === 'refunded' ? 'bg-purple-50' : 'bg-blue-50' }}">
                <h4
                    class="mb-4 text-base font-medium {{ $order->status === 'refunded' ? 'text-purple-800' : 'text-blue-800' }}">
                    {{ $order->status === 'refunded' ? 'Refund Information' : 'Return Information' }}
                </h4>
                <div class="space-y-3">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Return Date</p>
                            <p class="text-sm">{{ $returnRequest->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Return Type</p>
                            <p class="text-sm">{{ ucfirst($returnRequest->preferred_solution) }}</p>
                        </div>

                        @if ($refund && $order->status === 'refunded')
                            <div>
                                <p class="text-sm font-medium text-gray-600">Refund Amount</p>
                                <p class="text-sm font-semibold text-green-600">
                                    ₱{{ number_format($refund->amount, 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Refund Date</p>
                                <p class="text-sm">{{ $refund->created_at->format('M d, Y') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 mt-2 border-t border-gray-200">
                        <p class="text-sm font-medium text-gray-600">Return Reason</p>
                        <p class="p-3 mt-1 text-sm bg-white rounded-md">{{ $returnRequest->reason }}</p>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
