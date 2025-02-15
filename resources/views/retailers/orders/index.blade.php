<x-app-layout>

    <x-dashboard-nav />
    <div class="container py-8 mx-auto">
        <h1 class="mb-6 text-2xl font-bold">My Orders</h1>

        @if ($orders->isEmpty())
            <p class="p-4 text-gray-600">No orders found.</p>
        @else
            <div class="space-y-6">
                @foreach ($orders as $order)
                    <div class="p-6 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-xl font-semibold">Order #{{ $order->id }}</h2>
                                <p class="text-gray-600">
                                    Distributor: {{ $order->distributor->company_name }}
                                </p>
                                <p class="text-gray-600">
                                    Status: <span class="font-medium">{{ ucfirst($order->status) }}</span>
                                </p>
                            </div>
                            <p class="text-gray-600">
                                {{ $order->created_at->format('M d, Y') }}
                            </p>
                        </div>

                        <div class="mt-4">
                            <table class="w-full">
                                <thead class="text-sm text-gray-600 border-b">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Product</th>
                                        <th class="px-4 py-2 text-center">Quantity</th>
                                        <th class="px-4 py-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderDetails as $detail)
                                        <tr class="border-b">
                                            <td class="px-4 py-3">{{ $detail->product->product_name }}</td>
                                            <td class="px-4 py-3 text-center">{{ $detail->quantity }}</td>
                                            <td class="px-4 py-3 text-right">
                                                ₱{{ number_format($detail->subtotal, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 font-semibold text-right">Total Amount:</td>
                                        <td class="px-4 py-3 font-semibold text-right">
                                            ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4 text-sm text-gray-600">
                            Delivery Address: {{ optional($order->orderDetails->first())->delivery_address }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
