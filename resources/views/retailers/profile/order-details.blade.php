<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between pb-4 border-b">
        <div>
            <h3 class="text-xl font-medium text-gray-900">Order Details {{ $order->formatted_order_id }}</h3>
            <p class="mt-1 text-sm text-gray-500">Ordered on {{ $order->created_at->format('F d, Y') }}</p>
        </div>
        <span class="px-3 py-1 text-sm rounded-full {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    <!-- Order Items Table -->
    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($order->orderDetails as $detail)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $detail->product->product_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $detail->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">₱{{ number_format($detail->product->price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">₱{{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Order Summary -->
    <div class="p-6 rounded-lg bg-gray-50">
        <div class="flex items-center justify-between mb-4">
            <span class="text-base font-medium text-gray-900">Order Summary</span>
        </div>
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Subtotal</span>
                <span class="text-sm font-medium text-gray-900">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</span>
            </div>
            <div class="flex justify-between pt-4 border-t border-gray-200">
                <span class="text-base font-medium text-gray-900">Total Amount</span>
                <span class="text-lg font-bold text-gray-900">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Delivery Information -->
    @if($order->orderDetails->first()->delivery_address)
    <div class="p-6 border border-gray-200 rounded-lg">
        <h4 class="mb-4 text-base font-medium text-gray-900">Delivery Information</h4>
        <div class="space-y-2">
            <div class="flex items-start">
                <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="ml-3 text-sm text-gray-600">{{ $order->orderDetails->first()->delivery_address }}</p>
            </div>
        </div>
    </div>
    @endif
</div>