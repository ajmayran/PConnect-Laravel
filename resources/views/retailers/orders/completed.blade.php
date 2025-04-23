<x-app-layout>
    <x-dashboard-nav />
    <div class="container max-w-full px-4 py-8 mx-auto">
        <div class="flex items-center justify-between mb-8 ml-4">
            <h1 class="text-3xl font-bold text-gray-900">Completed Orders</h1>
            <a href="{{ route('retailers.orders.unpaid') }}"
                class="px-4 py-2 font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Unpaid Orders
            </a>
        </div>

        <x-retailer-orderstatus-tabs />

        @if ($orders->isEmpty())
            <div class="flex items-center justify-center p-8 mt-4 bg-white rounded-lg">
                <p class="text-lg text-gray-500">No completed orders found</p>
            </div>
        @else
            <div class="overflow-hidden bg-white rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Order ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Distributor
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Order Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Total
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            @php
                                // Check if order was completed within the last 7 days
                                $orderDate = new \Carbon\Carbon($order->status_updated_at ?? $order->updated_at);
                                $isWithinReturnPeriod = $orderDate->diffInDays(now()) <= 7;

                                // Check if return request was rejected
                                $rejectedReturnRequest = $order
                                    ->returnRequests()
                                    ->where('status', 'rejected')
                                    ->exists();

                                // Check if order has any other return request
                                $pendingOrApprovedReturnRequest = $order
                                    ->returnRequests()
                                    ->whereIn('status', ['pending', 'approved'])
                                    ->exists();

                                $hasReturnRequest = $order->returnRequests()->exists();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->formatted_order_id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->distributor->company_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="openOrderModal({{ $order->id }})"
                                            class="text-blue-600 hover:text-blue-900">
                                            View Details
                                        </button>

                                        @if ($isWithinReturnPeriod && !$hasReturnRequest)
                                            <div class="w-px h-4 mx-2 bg-gray-300"></div>
                                            <a href="#" x-data
                                                @click.prevent="$dispatch('open-modal', 'request-return-{{ $order->id }}')"
                                                class="text-yellow-600 hover:text-yellow-800">
                                                Request Return
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="container flex justify-end px-2 py-4 mx-auto sm:px-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative max-w-4xl p-6 mx-auto mt-10 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Order Details</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="modalContent" class="mt-4">
                <div class="flex items-center justify-center p-8">
                    <svg class="w-12 h-12 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>

            <div class="flex justify-end pt-4 mt-6 border-t border-gray-200">
                <button onclick="closeOrderModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 transition bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Return Request Modals -->
    @foreach ($orders as $order)
        @php
            // Check if order was completed within the last 7 days
            $orderDate = new \Carbon\Carbon($order->status_updated_at ?? $order->updated_at);
            $isWithinReturnPeriod = $orderDate->diffInDays(now()) <= 7;

            // Check if order has any return request
            $hasReturnRequest = $order->returnRequests()->exists();
        @endphp

        @if ($isWithinReturnPeriod && !$hasReturnRequest)
            <!-- Modal for Return Request -->
            <x-modal name="request-return-{{ $order->id }}" focusable>
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        Request Return for Order #{{ $order->formatted_order_id }}
                    </h2>

                    <form method="POST" action="{{ route('retailers.orders.request-return', $order->id) }}"
                        class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-2">
                            <x-input-label for="reason" value="Reason for Return" />
                            <x-text-area-input id="reason" name="reason" class="block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <x-input-label for="receipt" value="Upload Receipt (Image or PDF)" />
                                <input type="file" id="receipt" name="receipt"
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                    accept=".jpg,.jpeg,.png,.pdf" required />
                                <p class="mt-1 text-xs text-gray-500">JPG, JPEG, PNG or PDF (max 5MB)</p>
                                <x-input-error class="mt-2" :messages="$errors->get('receipt')" />
                            </div>

                            <div class="space-y-2">
                                <x-input-label for="proof_image" value="Upload Proof Image (Optional)" />
                                <input type="file" id="proof_image" name="proof_image"
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                    accept=".jpg,.jpeg,.png" />
                                <p class="mt-1 text-xs text-gray-500">Additional photo of the returned item(s)</p>
                                <x-input-error class="mt-2" :messages="$errors->get('proof_image')" />
                            </div>
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-medium text-gray-700">Products to Return</h3>
                            <div class="overflow-y-auto border border-gray-200 rounded-md max-h-60">
                                @foreach ($order->orderDetails as $detail)
                                    <div class="flex items-center justify-between p-3 border-b border-gray-200">
                                        <div class="flex items-start">
                                            <input type="checkbox" name="products[{{ $detail->id }}][selected]"
                                                id="product-{{ $detail->id }}"
                                                class="w-4 h-4 mt-1 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <label for="product-{{ $detail->id }}"
                                                class="ml-3 text-sm text-gray-700">
                                                {{ $detail->product->product_name }} (Qty: {{ $detail->quantity }})
                                            </label>
                                        </div>
                                        <div class="w-20">
                                            <x-input-label for="quantity-{{ $detail->id }}" value="Qty"
                                                class="sr-only" />
                                            <x-text-input id="quantity-{{ $detail->id }}"
                                                name="products[{{ $detail->id }}][quantity]" type="number"
                                                class="block w-full text-xs" min="1"
                                                max="{{ $detail->quantity }}" value="1" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-x-6">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                Cancel
                            </x-secondary-button>
                            <x-primary-button>
                                Submit Return Request
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endif
    @endforeach

    <script>
        function openOrderModal(orderId) {
            // Show the modal first with loading indicator
            document.getElementById('orderModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling

            // Fetch the order details
            fetch(`/retailers/profile/${orderId}/order-details`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('modalContent').innerHTML = data.html;
                    document.getElementById('modalTitle').innerText = `Order ${data.order_id}`;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalContent').innerHTML =
                        '<p class="text-center text-red-500">Error loading order details. Please try again.</p>';
                });
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
            document.body.style.overflow = ''; // Restore background scrolling
            document.getElementById('modalContent').innerHTML =
                '<div class="flex items-center justify-center p-8"><svg class="w-12 h-12 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>';
        }

        // Close modal when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeOrderModal();
            }
        });

        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeOrderModal();
            }
        });
    </script>
</x-app-layout>
