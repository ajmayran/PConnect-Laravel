<x-app-layout>
    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Order Details #' . $order->id) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <x-retailer-sidebar :user="Auth::user()" />

        <div class="flex-1 space-y-6 lg:px-8">
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <!-- Order Status -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Order Status</h3>
                        <span class="px-4 py-2 text-sm {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="mt-6">
                    <h3 class="mb-4 text-lg font-medium">Order Items</h3>
                    <div class="overflow-x-auto">
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
                                    <tr>
                                        <td class="px-6 py-4">
                                            {{ $detail->product->product_name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $detail->quantity }}
                                        </td>
                                        <td class="px-6 py-4">
                                            ₱{{ number_format($detail->product->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            ₱{{ number_format($detail->subtotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mt-6 text-right">
                    <div class="p-4 rounded-lg bg-gray-50">
                        <span class="text-lg font-medium">Total Amount: ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>