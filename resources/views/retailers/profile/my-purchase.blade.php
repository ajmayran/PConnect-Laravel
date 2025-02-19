<x-app-layout>
    <x-dashboard-nav />
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('My Purchase History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <x-retailer-sidebar :user="Auth::user()" />

        <div class="flex-1 space-y-6 lg:px-8">
            <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-medium">Recent Orders</h3>
                    <a href="{{ route('retailers.orders.index') }}" class="text-blue-600 hover:text-blue-800">View All
                        Orders</a>
                </div>

                <div class="space-y-4">
                    @forelse($orders as $order)
                        <div class="p-4 transition-all border rounded-lg hover:shadow-md">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">{{ $order->formatted_order_id }}</span>
                                <span
                                    class="px-2 py-1 text-sm {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <div class="mb-2">
                                @php
                                    $orderDetails = $order->orderDetails;
                                    $firstOrderDetail = $orderDetails->first();
                                    $remainingCount = $orderDetails->count() - 1;
                                @endphp

                                <p class="text-gray-800">
                                    @if ($firstOrderDetail && $firstOrderDetail->product)
                                        {{ $firstOrderDetail->product->product_name }}
                                        @if ($remainingCount > 0)
                                            <span class="text-gray-600">and {{ $remainingCount }} other products</span>
                                        @endif
                                    @else
                                        <span class="text-gray-600">No product details available</span>
                                    @endif
                                </p>
                            </div>

                            <div class="flex justify-between mt-2">
                                <span class="font-medium">Total:
                                    ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</span>
                                <button onclick="openOrderModal({{ $order->id }})"
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                    View Details →
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500 border rounded-lg">
                            No orders found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="orderModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative max-w-4xl p-8 mx-auto mt-20 bg-white rounded-lg shadow-xl">
            <!-- Remove the X button and replace with content wrapper -->
            <div class="space-y-6">
                <div id="modalContent" class="mt-4">
                    <!-- Modal content will be loaded here -->
                </div>

                <!-- New close button at bottom -->
                <div class="flex justify-end pt-4 mt-6 border-t border-gray-200">
                    <button onclick="closeOrderModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openOrderModal(orderId) {
                fetch(`/retailers/profile/${orderId}/order-details`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        document.getElementById('modalContent').innerHTML = html;
                        document.getElementById('orderModal').classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error loading order details');
                    });
            }

            function closeOrderModal() {
                document.getElementById('orderModal').classList.add('hidden');
                document.getElementById('modalContent').innerHTML = '';
            }

            // Close modal when clicking outside
            document.getElementById('orderModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeOrderModal();
                }
            });
        </script>
    @endpush
</x-app-layout>
