<x-distributor-layout>
    <style>
        /* Mobile responsive adjustments */
        @media (max-width: 640px) {
            .table-container {
                overflow-x: auto;
            }
            .mobile-text-sm {
                font-size: 0.75rem;
            }
            .mobile-padding {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            .mobile-hidden {
                display: none;
            }
        }
        /* Fix for scroll issues on status tabs */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-3 sm:p-4 mx-auto">
            <h1 class="mb-4 text-lg sm:text-xl md:text-2xl font-semibold text-center">Cancellations</h1>

            <!-- Tabs and Search Section -->
            <div class="p-3 sm:p-4 mb-4 sm:mb-6 bg-white rounded-lg shadow-sm">
                <!-- Tabs -->
                <div class="flex justify-between mb-3 sm:mb-4 border-b overflow-x-auto scrollbar-hide">
                    <div class="flex space-x-2 sm:space-x-4 min-w-max">
                        <button id="customerTab"
                            class="px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-green-600 border-b-2 border-green-500 tab-button">
                            Retailers Cancellations
                        </button>
                        <button id="myTab" class="px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-500 hover:text-green-600 tab-button">
                            My Cancellations
                        </button>
                    </div>
                </div>

                <!-- Search and Export -->
                <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-4">
                    <div class="relative w-full sm:w-auto">
                        <form action="{{ route('distributors.cancellations.index') }}" method="GET">
                            <input type="search" name="search" placeholder="Search orders..."
                                value="{{ request('search') }}"
                                class="w-full px-3 sm:px-4 py-1.5 sm:py-2 pr-8 text-xs sm:text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center px-2 sm:px-3 text-gray-500">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                        @if(request('search'))
                            <a href="{{ route('distributors.cancellations.index') }}" class="ml-2 text-xs sm:text-sm text-red-500">Clear search</a>
                        @endif
                    </div>
                    <button id="batchDeleteBtn" class="hidden px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-red-500 rounded-lg hover:bg-red-600">
                        <svg class="inline-block w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Batch Delete
                    </button>
                </div>
            </div>
            
            <!-- Tables Container -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <!-- Customer Cancellations Table -->
                <div id="customerCancellations" class="tab-content">
                    <div class="p-3 sm:p-4">
                        <h2 class="mb-3 sm:mb-4 text-xs sm:text-sm text-gray-600">Retailer Cancels: {{ count($retailerCancellations ?? []) }}</h2>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="table-container">
                                <table class="w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="w-8 sm:w-10 px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                <input type="checkbox" id="selectAllCustomer" class="border-gray-300 rounded cursor-pointer">
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Order ID
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Amount
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Retailer
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Date
                                            </th>
                        
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Reason
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($retailerCancellations ?? [] as $order)
                                            <tr>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4">
                                                    <input type="checkbox" name="selected_orders[]" value="{{ $order->id }}" class="border-gray-300 rounded cursor-pointer order-checkbox">
                                                </td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-semibold whitespace-nowrap">{{ $order->id }}</td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                    ₱{{ number_format($order->total_amount, 2) }}</td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap">{{ $order->customer_name }}</td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                    {{ $order->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap mobile-hidden">
                                                    {{ Str::limit($order->cancel_reason ?? 'No reason provided', 20) }}
                                                </td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                    <div class="flex items-center space-x-2 sm:space-x-3">
                                                        <button type="button" onclick="showOrderDetails({{ $order->id }}, 'cancelled')"
                                                            class="text-blue-600 hover:text-blue-900">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </button>
                                                        <button type="button" onclick="deleteOrder({{ $order->id }})"
                                                            class="text-red-600 hover:text-red-900">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-center text-gray-500">
                                                    No retailer cancellations found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- My Cancellations Table -->
                <div id="myCancellations" class="hidden tab-content">
                    <div class="p-3 sm:p-4">
                        <h2 class="mb-3 sm:mb-4 text-xs sm:text-sm text-gray-600">My Cancels: {{ count($myCancellations ?? []) }}</h2>
                        <form id="deleteFormReject" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="table-container">
                                <table class="w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="w-8 sm:w-10 px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                <input type="checkbox" id="selectAllMy" class="border-gray-300 rounded cursor-pointer">
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Order ID
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Amount
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Retailer
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                                Date
                                            </th>
                           
                                            <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase mobile-hidden">
                                                Rejection Reason
                                            </th>
                                            <th class="px-2 sm:px-6 py-2 sm:py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($myCancellations ?? [] as $order)
                                            <tr>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4">
                                                    <input type="checkbox" name="selected_orders[]" value="{{ $order->id }}" class="border-gray-300 rounded cursor-pointer order-checkbox-my">
                                                </td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm font-semibold whitespace-nowrap">{{ $order->id }}</td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                    ₱{{ number_format($order->total_amount, 2) }}</td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap">{{ $order->customer_name }}</td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                    {{ $order->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap mobile-hidden">
                                                    {{ Str::limit($order->reject_reason ?? 'No reason provided', 20) }}
                                                </td>
                                                <td class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                    <div class="flex items-center space-x-2 sm:space-x-3">
                                                        <button type="button" onclick="showOrderDetails({{ $order->id }}, 'rejected')"
                                                            class="text-blue-600 hover:text-blue-900">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </button>
                                                        <button type="button" onclick="deleteOrder({{ $order->id }})"
                                                            class="text-red-600 hover:text-red-900">
                                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-center text-gray-500">
                                                    No rejected orders found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" 
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-3/4">
            <!-- Modal Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between p-3 sm:p-4 bg-white border-b">
                <h2 class="text-lg sm:text-xl font-bold text-gray-800">Order Details</h2>
                <button onclick="closeModal()"
                    class="p-1 sm:p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-3 sm:p-6">
                <!-- Modal content will be dynamically populated -->
            </div>

            <div class="sticky bottom-0 flex justify-end gap-2 sm:gap-4 p-3 sm:p-4 bg-white border-t">
                <button type="button" onclick="closeModal()"
                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Remove active state from all tabs
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('text-green-600', 'border-b-2', 'border-green-500');
                    btn.classList.add('text-gray-500');
                });

                // Add active state to clicked tab
                button.classList.add('text-green-600', 'border-b-2', 'border-green-500');
                button.classList.remove('text-gray-500');

                // Show corresponding content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                if (button.id === 'customerTab') {
                    document.getElementById('customerCancellations').classList.remove('hidden');
                } else {
                    document.getElementById('myCancellations').classList.remove('hidden');
                }
                
                // Update batch delete button visibility
                updateBatchDeleteButton();
            });
        });
        
        // Checkbox functionality for batch delete
        document.getElementById('selectAllCustomer').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.order-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBatchDeleteButton();
        });
        
        document.getElementById('selectAllMy').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.order-checkbox-my').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBatchDeleteButton();
        });
        
        // Update batch delete button visibility based on selections
        function updateBatchDeleteButton() {
            const activeTab = document.querySelector('.tab-button.text-green-600').id;
            const checkboxSelector = activeTab === 'customerTab' ? '.order-checkbox' : '.order-checkbox-my';
            const anyChecked = Array.from(document.querySelectorAll(checkboxSelector)).some(cb => cb.checked);
            
            const batchDeleteBtn = document.getElementById('batchDeleteBtn');
            if (anyChecked) {
                batchDeleteBtn.classList.remove('hidden');
            } else {
                batchDeleteBtn.classList.add('hidden');
            }
        }
        
        // Add event listeners to all checkboxes
        document.querySelectorAll('.order-checkbox, .order-checkbox-my').forEach(checkbox => {
            checkbox.addEventListener('change', updateBatchDeleteButton);
        });
        
        // Batch delete functionality
        document.getElementById('batchDeleteBtn').addEventListener('click', function() {
            const activeTab = document.querySelector('.tab-button.text-green-600').id;
            const formId = activeTab === 'customerTab' ? 'deleteForm' : 'deleteFormReject';
            const checkboxSelector = activeTab === 'customerTab' ? '.order-checkbox:checked' : '.order-checkbox-my:checked';
            
            const selectedOrders = Array.from(document.querySelectorAll(checkboxSelector)).map(cb => cb.value);
            
            if (selectedOrders.length === 0) return;
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete ${selectedOrders.length} selected order(s)?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete them!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(formId);
                    form.action = '/cancellations/batch-delete';
                    form.submit();
                }
            });
        });

        // Delete single order
        function deleteOrder(orderId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to delete this order record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/cancellations/${orderId}`;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Modal functionality
        function showOrderDetails(orderId, type) {
            // Show loading state
            const modalContent = document.querySelector('#orderDetailsModal .p-3.sm\\:p-6');
            modalContent.innerHTML = `
                <div class="flex items-center justify-center p-8">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-green-500 animate-spin" xmlns="http://www.w3.org/2000/svg" 
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" 
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>`;
            
            // Show the modal
            document.getElementById('orderDetailsModal').classList.remove('hidden');
            
            // Fetch order details from the server
            fetch(`/cancellations/${orderId}/details`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let html = `<div class="space-y-4 sm:space-y-6">`;
                    
                    // Status Card
                    const statusColor = type === 'cancelled' ? 
                        'bg-yellow-50 border-yellow-200 text-yellow-800' : 
                        'bg-red-50 border-red-200 text-red-800';
                        
                    html += `
                        <div class="p-3 border rounded-lg ${statusColor}">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base sm:text-lg font-semibold">Order ${data.formatted_id}</h3>
                                <span class="inline-flex px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-semibold rounded-full ${type === 'cancelled' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                                    ${type === 'cancelled' ? 'Cancelled by retailer' : 'Rejected by you'}
                                </span>
                            </div>
                            <p class="mt-1 text-xs sm:text-sm">
                                ${type === 'cancelled' ? 'Cancellation' : 'Rejection'} Reason: 
                                ${data.reason || 'No reason provided'}
                            </p>
                        </div>`;
                        
                    // Order Information
                    html += `
                        <div class="overflow-hidden bg-white rounded-lg shadow">
                            <div class="p-3 sm:p-4 border-b bg-gray-50">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Order Information</h3>
                            </div>
                            <div class="p-3 sm:p-4 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm">
                                <div>
                                    <p class="font-medium text-gray-500">Retailer</p>
                                    <p class="text-gray-900">${data.customer}</p>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-500">Order Date</p>
                                    <p class="text-gray-900">${new Date(data.order.created_at).toLocaleDateString()}</p>
                                </div>
                            </div>
                        </div>`;

                    // Order Items
                    html += `
                        <div class="overflow-hidden bg-white rounded-lg shadow">
                            <div class="p-3 sm:p-4 border-b bg-gray-50">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Order Items</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-left text-gray-700">Product</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-right text-gray-700">Price</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-right text-gray-700">Qty</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-right text-gray-700">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">`;
                                
                    data.items.forEach(item => {
                        html += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm">${item.product.product_name}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-right">₱${parseFloat(item.price).toFixed(2)}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-right">${item.quantity}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-blue-600 text-right">₱${parseFloat(item.subtotal).toFixed(2)}</td>
                            </tr>`;
                    });
                    
                    html += `
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium text-right text-gray-700">Total Amount:</td>
                                        <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-bold text-blue-600 text-right">₱${parseFloat(data.total).toFixed(2)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>`;
                    
                    html += `</div>`;
                    
                    modalContent.innerHTML = html;
                })
                .catch(error => {
                    modalContent.innerHTML = '<div class="text-center text-red-500"><p>Error loading order details: ' + error.message + '</p></div>';
                    console.error('Error fetching order details:', error);
                });
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
        
        // Initialize batch delete button visibility
        updateBatchDeleteButton();
    </script>
</x-distributor-layout>