<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <!-- Back button -->
        <div class="mb-6">
            <a href="{{ url()->previous() }}" class="flex items-center text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </a>
        </div>

        <!-- Retailer Profile Header -->
        <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if ($retailer->retailerProfile && $retailer->retailerProfile->profile_picture)
                            <img src="{{ asset('storage/' . $retailer->retailerProfile->profile_picture) }}"
                                alt="{{ $retailer->first_name }}" class="object-cover w-24 h-24 rounded-full">
                        @else
                            <div class="flex items-center justify-center w-24 h-24 bg-gray-200 rounded-full">
                                <span
                                    class="text-2xl font-medium text-gray-700">{{ $retailer->first_name[0] }}{{ $retailer->last_name[0] }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-800">{{ $retailer->first_name }}
                            {{ $retailer->last_name }}</h1>
                        @if ($retailer->retailerProfile && $retailer->retailerProfile->business_name)
                            <p class="text-lg text-gray-600">{{ $retailer->retailerProfile->business_name }}</p>
                        @endif
                        <div class="mt-2 space-y-1 text-sm text-gray-600">
                            <p class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $retailer->email }}
                            </p>
                            @if ($retailer->retailerProfile && $retailer->retailerProfile->phone)
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $retailer->retailerProfile->phone }}
                                </p>
                            @endif
                            @if ($retailer->retailerProfile && ($retailer->retailerProfile->barangay || $retailer->retailerProfile->street))
                                <p class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    @if ($retailer->retailerProfile->barangay_name)
                                        {{ $retailer->retailerProfile->barangay_name }}
                                    @endif
                                    @if ($retailer->retailerProfile->street)
                                        {{ $retailer->retailerProfile->barangay_name ? ', ' : '' }}{{ $retailer->retailerProfile->street }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-shrink-0 space-x-2">
                        <a href="{{ route('distributors.messages.show', $retailer->id) }}"
                            class="flex items-center px-4 py-2 text-sm font-medium text-white transition-colors bg-green-600 rounded-md hover:bg-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Message
                        </a>

                        <!-- More Actions Dropdown -->
                        <div x-data="{ open: false, top: 0, left: 0 }" @click.away="open = false" class="relative">
                            <button
                                @click="
                                open = !open; 
                                if (open) {
                                    $nextTick(() => {
                                        const rect = $el.getBoundingClientRect();
                                        top = rect.bottom + window.scrollY;
                                        left = rect.left + window.scrollX - 120;
                                    });
                                }
                            "
                                type="button"
                                class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>

                            <template x-if="open">
                                <div class="fixed z-50 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    :style="`top: ${top}px; left: ${left}px; min-width: 12rem; max-width: 16rem;`">
                                    <div class="py-1">
                                        <button @click="$dispatch('open-modal', 'report-retailer-modal'); open = false"
                                            class="flex w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-red-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Report Retailer
                                        </button>
                                        <form action="{{ route('distributors.retailers.block', $retailer->id) }}"
                                            id="blockRetailerForm" method="POST" class="w-full">
                                            @csrf
                                            <button type="button" onclick="confirmBlock()"
                                                class="flex w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5 mr-2 text-gray-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                {{ $retailer->is_blocked ? 'Unblock Retailer' : 'Block Retailer' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['total'] }}</h3>
                    </div>
                    <div class="p-3 text-white bg-blue-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Completed Orders</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['completed'] }}</h3>
                    </div>
                    <div class="p-3 text-white bg-green-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Processing Orders</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $orderStats['processing'] }}</h3>
                    </div>
                    <div class="p-3 text-white bg-yellow-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Spent</p>
                        <h3 class="text-2xl font-bold text-gray-900">
                            ₱{{ number_format($orderStats['totalSpent'], 2) }}</h3>
                    </div>
                    <div class="p-3 text-white bg-purple-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="mb-6 overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Recent Orders</h2>
                <a href="#" onclick="openOrdersModal()"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800">View All Orders</a>
            </div>
            @if ($recentOrders->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-gray-500">No orders found for this retailer.</p>
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
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Distributor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($recentOrders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->formatted_order_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-blue-600">
                                            ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $order->distributor->company_name }}
                                        </div>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- All Orders Modal -->
    <div id="allOrdersModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
                <h2 class="text-xl font-bold text-gray-800">All Orders for {{ $retailer->first_name }}
                    {{ $retailer->last_name }}</h2>
                <button onclick="closeOrdersModal()"
                    class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Distributor</th>
                            </tr>
                        </thead>
                        <tbody id="allOrdersTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Table body will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end p-4 border-t">
                <button onclick="closeOrdersModal()"
                    class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Report Retailer Modal -->
    <div id="report-retailer-modal" x-data="{ show: false }"
        x-on:open-modal.window="$event.detail == 'report-retailer-modal' ? show = true : null"
        x-on:keydown.escape.window="show = false" x-show="show"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50" x-cloak>
        <div class="w-11/12 max-w-md bg-white rounded-lg shadow-xl md:w-1/2 sm:w-2/3" @click.away="show = false">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Report Retailer</h2>
                <button @click="show = false"
                    class="p-1 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('distributors.retailers.report', $retailer->id) }}" method="POST"
                id="reportRetailerForm">
                @csrf
                <div class="p-4 space-y-4">
                    <div>
                        <label for="report_reason" class="block mb-2 text-sm font-medium text-gray-700">Reason for
                            reporting</label>
                        <select id="report_reason" name="reason"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a reason</option>
                            <option value="inappropriate_behavior">Inappropriate Behavior</option>
                            <option value="fraud">Fraud or Scam</option>
                            <option value="fake_profile">Fake Profile</option>
                            <option value="payment_issues">Payment Issues</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="report_details" class="block mb-2 text-sm font-medium text-gray-700">Additional
                            Details</label>
                        <textarea id="report_details" name="details" rows="4"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Please provide more details about your report"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-4 border-t rounded-b-lg bg-gray-50">
                    <button type="button" @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" onclick="confirmReport()"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Orders data from controller
        const retailerId = {{ $retailer->id }};
        const allOrders = @json($recentOrders);

        function openOrdersModal() {
            // Fetch all orders for this retailer from the server
            fetch(`/retailers/${retailerId}/orders`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('allOrdersTableBody');
                    tableBody.innerHTML = '';

                    if (data.orders && data.orders.length > 0) {
                        data.orders.forEach(order => {

                            const row = `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">${formatDate(order.created_at)}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-blue-600">₱${formatNumber(order.total_amount || calculateTotal(order))}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500">${order.distributor.company_name}</div>
                                    </tr>
                                `;
                            tableBody.innerHTML += row;
                        });
                    } else {
                        tableBody.innerHTML = `
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No orders found for this retailer.
                                    </td>
                                </tr>
                            `;
                    }

                    // Show the modal
                    document.getElementById('allOrdersModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);

                    // Show modal with error message
                    const tableBody = document.getElementById('allOrdersTableBody');
                    tableBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-red-500">
                                    Failed to load orders. Please try again.
                                </td>
                            </tr>
                        `;

                    document.getElementById('allOrdersModal').classList.remove('hidden');
                });
        }

        function closeOrdersModal() {
            document.getElementById('allOrdersModal').classList.add('hidden');
        }

        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });
        }

        function formatNumber(number) {
            return parseFloat(number).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        function calculateTotal(order) {
            if (order.order_details) {
                return order.order_details.reduce((total, detail) => total + parseFloat(detail.subtotal), 0);
            }
            return 0;
        }

        function capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1).replace(/_/g, ' ');
        }

        function confirmBlock() {
            const actionText = "{{ $retailer->is_blocked ? 'unblock' : 'block' }}";
            const retailerName = "{{ $retailer->first_name }} {{ $retailer->last_name }}";

            Swal.fire({
                title: `Confirm ${actionText}`,
                html: `Are you sure you want to ${actionText} <strong>${retailerName}</strong>?<br><br>
               ${actionText === 'block' ? 'This retailer will no longer be able to place orders with you.' : 
               'This will allow the retailer to place orders with you again.'}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: actionText === 'block' ? '#d33' : '#3085d6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: `Yes, ${actionText} retailer`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('blockRetailerForm').submit();
                }
            });
        }

        function confirmReport() {
            const reason = document.getElementById('report_reason').value;
            const details = document.getElementById('report_details').value;

            if (!reason) {
                Swal.fire({
                    icon: 'error',
                    title: 'Required Field Missing',
                    text: 'Please select a reason for reporting this retailer',
                });
                return;
            }

            Swal.fire({
                title: 'Report Retailer',
                html: `Are you sure you want to report <strong>{{ $retailer->first_name }} {{ $retailer->last_name }}</strong>?<br><br>
               This report will be reviewed by our team.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, submit report',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reportRetailerForm').submit();
                }
            });
        }
    </script>
</x-distributor-layout>
