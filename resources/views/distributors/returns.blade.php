<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <h1 class="mb-4 text-xl font-semibold text-center sm:text-2xl">Return and Refund</h1>

            <!-- Tabs and Search Section -->
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <!-- Tabs -->
                <div class="flex justify-between mb-4 border-b">
                    <div class="flex space-x-4">
                        <button id="returnTab" class="px-4 py-2 text-green-600 border-b-2 border-green-500 tab-button">
                            Return Orders
                        </button>
                        <button id="refundTab" class="px-4 py-2 text-gray-500 hover:text-green-600 tab-button">
                            Refund Orders
                        </button>
                    </div>
                </div>

                <!-- Search and Export -->
                <div class="flex items-center justify-between">
                    <div class="relative">
                        <input type="search" placeholder="Search orders..."
                            class="px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                    <button class="px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Reports
                    </button>
                </div>
            </div>

            <!-- Tables Container -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <!-- Return Orders Table -->
                <div id="returnOrders" class="tab-content">
                    <div class="p-4">
                        <h2 class="mb-4 text-sm text-gray-600">Return Orders: </h2>
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Order ID</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Amount</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Delivery</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Customer</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Return Orders Data -->
                                @foreach ($returnOrders ?? [] as $order)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-semibold whitespace-nowrap">
                                            {{ $order->id }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            ₱{{ number_format($order->amount, 2) }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $order->delivery_type }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $order->customer_name }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            <button onclick="showOrderDetails('return', {{ $order->id }})"
                                                class="text-blue-600 hover:text-blue-900">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Refund Orders Table -->
                <div id="refundOrders" class="hidden tab-content">
                    <div class="p-4">
                        <h2 class="mb-4 text-sm text-gray-600">Refund Orders: </h2>
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Order ID</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Amount</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Delivery</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Customer</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Refund Orders Data -->
                                @foreach ($refundOrders ?? [] as $order)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-semibold whitespace-nowrap">
                                            {{ $order->id }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            ₱{{ number_format($order->amount, 2) }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $order->delivery_type }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $order->customer_name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            <button onclick="showOrderDetails('refund', {{ $order->id }})"
                                                class="text-blue-600 hover:text-blue-900">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="relative inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <!-- Modal content -->
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modalTitle"></h3>
                            <div class="mt-2">
                                <div id="orderDetails"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" 
                            onclick="closeModal()" 
                            class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.getElementById('returnTab').addEventListener('click', function() {
            this.classList.add('text-green-600', 'border-b-2', 'border-green-500');
            document.getElementById('refundTab').classList.remove('text-green-600', 'border-b-2', 'border-green-500');
            document.getElementById('returnOrders').classList.remove('hidden');
            document.getElementById('refundOrders').classList.add('hidden');
        });

        document.getElementById('refundTab').addEventListener('click', function() {
            this.classList.add('text-green-600', 'border-b-2', 'border-green-500');
            document.getElementById('returnTab').classList.remove('text-green-600', 'border-b-2', 'border-green-500');
            document.getElementById('refundOrders').classList.remove('hidden');
            document.getElementById('returnOrders').classList.add('hidden');
        });

        // Modal functionality
        function showOrderDetails(type, orderId) {
            const modal = document.getElementById('orderDetailsModal');
            modal.classList.remove('hidden');
            // Add logic to populate modal with order details
        }

        function closeModal() {
            document.getElementById('orderDetailsModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('orderDetailsModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });

        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</x-distributor-layout>
