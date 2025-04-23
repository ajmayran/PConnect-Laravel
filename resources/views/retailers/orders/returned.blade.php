<x-app-layout>
    <x-dashboard-nav />
    <div class="container px-4 py-8 mx-auto max-w-7xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Returned Orders</h1>
        </div>

        <x-retailer-orderstatus-tabs />

        @if ($orders->isEmpty())
            <div class="flex items-center justify-center p-8 mt-4 bg-white rounded-lg">
                <p class="text-lg text-gray-500">No returned orders</p>
            </div>
        @else
            <div class="mt-6 space-y-6">
                @foreach ($orders as $order)
                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="p-6">
                            <!-- Order header info remains unchanged -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="space-y-1">
                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ $order->formatted_order_id }}
                                    </h2>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Distributor:</span>
                                        {{ $order->distributor->company_name }}
                                    </p>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Status:</span>
                                        <span
                                            class="px-3 py-1 text-sm font-medium text-indigo-800 bg-indigo-100 rounded-full">
                                            Returned
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-500">Order Date</p>
                                    <p class="text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Updated table with discount display -->
                            <div class="mt-6 -mx-6">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-sm font-medium tracking-wider text-left text-gray-500">
                                                Product
                                            </th>
                                            <th class="px-6 py-3 text-sm font-medium tracking-wider text-center text-gray-500">
                                                Quantity
                                            </th>
                                            <th class="px-6 py-3 text-sm font-medium tracking-wider text-right text-gray-500">
                                                Price
                                            </th>
                                            <th class="px-6 py-3 text-sm font-medium tracking-wider text-right text-gray-500">
                                                Total
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($order->orderDetails as $detail)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $detail->product->product_name }}
                                                    @if($detail->applied_discount)
                                                        <p class="text-xs text-green-600">{{ $detail->applied_discount }}</p>
                                                    @endif
                                                    @if($detail->free_items > 0)
                                                        <p class="text-xs text-green-600">+{{ $detail->free_items }} free item(s)</p>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                    {{ $detail->quantity }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-right text-gray-900">
                                                    @if($detail->discount_amount > 0)
                                                        <span class="text-xs text-gray-500 line-through">₱{{ number_format($detail->price, 2) }}</span>
                                                        <br>
                                                        <span class="text-green-600">₱{{ number_format($detail->price - ($detail->discount_amount / $detail->quantity), 2) }}</span>
                                                    @else
                                                        ₱{{ number_format($detail->price, 2) }}
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">
                                                    @if($detail->discount_amount > 0)
                                                        <span class="text-xs text-gray-500 line-through">₱{{ number_format($detail->price * $detail->quantity, 2) }}</span>
                                                        <br>
                                                    @endif
                                                    ₱{{ number_format($detail->subtotal, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-sm font-bold text-right text-gray-900">
                                                Total Amount:
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-right text-gray-900">
                                                ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-200">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Delivery Address: {{ optional($order->orderDetails->first())->delivery_address }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Pagination -->
            <div class="container flex justify-end px-2 pb-8 mx-auto sm:px-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
