<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between pb-4 border-b">
        <div>
            <h3 class="text-xl font-medium text-gray-900">Order Details {{ $order->formatted_order_id }}</h3>
            <p class="mt-1 text-sm text-gray-500">Ordered on {{ $order->created_at->format('F d, Y') }}</p>
        </div>
        <div class="flex justify-end gap-2">
            <span
                class="px-3 py-1 text-sm rounded-full {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'returned' ? 'bg-blue-100 text-blue-800' : ($order->status === 'refunded' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')) }}">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
            @if (isset($order->payment) && $order->payment && $order->payment->payment_status === 'unpaid')
                <span class="px-3 py-1 ml-2 text-sm text-red-800 bg-red-100 rounded-full">
                    Unpaid
                </span>
            @endif
        </div>
    </div>

    <!-- Group order details by distributor -->
    @php
        $orderDetailsByDistributor = $order->orderDetails->groupBy(function($detail) {
            return $detail->product->distributor_id ?? 'unknown';
        });
    @endphp

    @foreach($orderDetailsByDistributor as $distributorId => $details)
        <!-- Distributor Header -->
        <div class="mt-6 mb-2">
            <h2 class="text-lg font-semibold text-gray-800">
                {{ $details->first()->product->distributor->company_name ?? 'Unknown Distributor' }}
            </h2>
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
                    @foreach ($details as $detail)
                        <tr class="transition-colors hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $detail->product->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $detail->quantity }}
                                @if($detail->free_items > 0)
                                    <span class="ml-1 text-xs text-green-600">(+{{ $detail->free_items }} free)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($detail->discount_amount > 0)
                                    <span class="text-xs text-gray-500 line-through">₱{{ number_format($detail->price, 2) }}</span>
                                    <br>
                                    <span class="text-green-600">₱{{ number_format($detail->price - $detail->discount_amount / $detail->quantity, 2) }}</span>
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
                <span class="text-sm font-medium text-gray-900">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</span>
            </div>
            <div class="flex justify-between pt-4 border-t border-gray-200">
                <span class="text-base font-medium text-gray-900">Total Amount</span>
                <span class="text-lg font-bold text-gray-900">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Delivery Information -->
    @if ($order->orderDetails->first()->delivery_address)
        <div class="p-6 border border-gray-200 rounded-lg">
            <h4 class="mb-4 text-base font-medium text-gray-900">Delivery Information</h4>
            <div class="space-y-2">
                <div class="flex items-start">
                    <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="ml-3 text-sm text-gray-600">{{ $order->orderDetails->first()->delivery_address }}</p>
                </div>
                @if($order->delivery)
                <div class="flex items-start mt-3">
                    <svg class="flex-shrink-0 w-5 h-5 mt-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600"><span class="font-medium">Tracking Number:</span> {{ $order->delivery->tracking_number }}</p>
                        @if($order->delivery->status)
                            <p class="mt-1 text-sm text-gray-600"><span class="font-medium">Delivery Status:</span> {{ ucfirst(str_replace('_', ' ', $order->delivery->status)) }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    @endif
    
    <!-- Return/Refund Information (if applicable) -->
    @if($order->status === 'returned' || $order->status === 'refunded')
        @php
            $returnRequest = $order->returnRequests()->latest()->first();
            $refund = $order->refunds()->latest()->first();
        @endphp
        
        @if($returnRequest)
        <div class="p-6 border border-gray-200 rounded-lg {{ $order->status === 'refunded' ? 'bg-purple-50' : 'bg-blue-50' }}">
            <h4 class="mb-4 text-base font-medium {{ $order->status === 'refunded' ? 'text-purple-800' : 'text-blue-800' }}">
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
                    
                    @if($refund && $order->status === 'refunded')
                    <div>
                        <p class="text-sm font-medium text-gray-600">Refund Amount</p>
                        <p class="text-sm font-semibold text-green-600">₱{{ number_format($refund->amount, 2) }}</p>
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