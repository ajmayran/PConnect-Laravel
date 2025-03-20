<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <h1 class="mb-4 text-xl font-semibold text-center sm:text-2xl">Cancellations</h1>

            <!-- Tabs and Search Section -->
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <!-- Tabs -->
                <div class="flex justify-between mb-4 border-b">
                    <div class="flex space-x-4">
                        <button id="customerTab"
                            class="px-4 py-2 text-green-600 border-b-2 border-green-500 tab-button">
                            Retailers Cancellations
                        </button>
                        <button id="myTab" class="px-4 py-2 text-gray-500 hover:text-green-600 tab-button">
                            My Cancellations
                        </button>
                    </div>
                </div>

                <!-- Search and Export -->
                <div class="flex items-center justify-between">
                    <div class="relative">
                        <form action="{{ route('distributors.cancellations.index') }}" method="GET" class="flex">
                            <input type="search" name="search" placeholder="Search orders..." id="search-input"
                                class="px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                value="{{ request('search') }}">
                            <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                        @if(request('search'))
                            <a href="{{ route('distributors.cancellations.index') }}" class="ml-2 text-sm text-red-500">Clear search</a>
                        @endif
                    </div>
                    <button id="batchDeleteBtn" class="hidden px-4 py-2 text-white bg-red-500 rounded-lg hover:bg-red-600">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="p-4">
                        <h2 class="mb-4 text-sm text-gray-600">Retailer Cancels: {{ count($retailerCancellations ?? []) }}</h2>
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="w-10 px-6 py-3">
                                            <input type="checkbox" id="selectAllCustomer" class="border-gray-300 rounded cursor-pointer">
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Order ID</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Amount</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Retailer</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Date</th>
                    
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Reason</th>
                                        <th class="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($retailerCancellations ?? [] as $order)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <input type="checkbox" name="selected_orders[]" value="{{ $order->id }}" class="border-gray-300 rounded cursor-pointer order-checkbox">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                ₱{{ number_format($order->total_amount, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->customer_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $order->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->cancel_reason ?? 'No reason provided' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-3">
                                                    <button type="button" onclick="showOrderDetails({{ $order->id }}, 'cancelled')"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </button>
                                                    <button type="button" onclick="deleteOrder({{ $order->id }})"
                                                        class="text-red-600 hover:text-red-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                                No retailer cancellations found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

                <!-- My Cancellations Table -->
                <div id="myCancellations" class="hidden tab-content">
                    <div class="p-4">
                        <h2 class="mb-4 text-sm text-gray-600">My Cancels: {{ count($myCancellations ?? []) }}</h2>
                        <form id="deleteFormReject" method="POST">
                            @csrf
                            @method('DELETE')
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="w-10 px-6 py-3">
                                            <input type="checkbox" id="selectAllMy" class="border-gray-300 rounded cursor-pointer">
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Order ID</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Amount</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Retailer</th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Date</th>
                       
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Rejection Reason</th>
                                        <th class="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($myCancellations ?? [] as $order)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <input type="checkbox" name="selected_orders[]" value="{{ $order->id }}" class="border-gray-300 rounded cursor-pointer order-checkbox-my">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                ₱{{ number_format($order->total_amount, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->customer_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $order->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->reject_reason ?? 'No reason provided' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-3">
                                                    <button type="button" onclick="showOrderDetails({{ $order->id }}, 'rejected')"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </button>
                                                    <button type="button" onclick="deleteOrder({{ $order->id }})"
                                                        class="text-red-600 hover:text-red-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                                No rejected orders found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <!-- Modal content will be dynamically populated -->
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">
                        Close
                    </button>
                </div>
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
            const modalContent = document.querySelector('#orderDetailsModal .px-4.pt-5.pb-4');
            modalContent.innerHTML = '<div class="text-center"><p>Loading order details...</p></div>';
            
            // Show the modal
            document.getElementById('orderDetailsModal').classList.remove('hidden');
            
            // Fetch order details from the server - FIX: Updated URL path
            fetch(`/cancellations/${orderId}/details`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let reasonText = type === 'cancelled' ? 
                        `<strong>Cancellation Reason:</strong> ${data.reason || 'No reason provided'}` : 
                        `<strong>Rejection Reason:</strong> ${data.reason || 'No reason provided'}`;
                        
                    let html = `
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Order ${data.formatted_id} Details</h3>
                        <dl class="grid grid-cols-1 mb-4 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Retailer</dt>
                                <dd class="text-sm text-gray-900">${data.customer}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                                <dd class="text-sm text-gray-900">${new Date(data.order.created_at).toLocaleDateString()}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="text-sm text-gray-900">${type === 'cancelled' ? 'Cancelled by retailer' : 'Rejected by you'}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Reason</dt>
                                <dd class="text-sm text-gray-900">${data.reason || 'No reason provided'}</dd>
                            </div>
                        </dl>
                        
                        <h4 class="mb-2 font-medium text-gray-900">Order Items</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-xs font-medium text-left text-gray-500 uppercase">Product</th>
                                        <th class="px-4 py-2 text-xs font-medium text-right text-gray-500 uppercase">Price</th>
                                        <th class="px-4 py-2 text-xs font-medium text-right text-gray-500 uppercase">Qty</th>
                                        <th class="px-4 py-2 text-xs font-medium text-right text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">`;
                                
                    data.items.forEach(item => {
                        html += `
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 whitespace-nowrap">${item.product.product_name}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 whitespace-nowrap">₱${parseFloat(item.price).toFixed(2)}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 whitespace-nowrap">${item.quantity}</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 whitespace-nowrap">₱${parseFloat(item.subtotal).toFixed(2)}</td>
                            </tr>`;
                    });
                    
                    html += `
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm font-medium text-right text-gray-900">Total:</td>
                                        <td class="px-4 py-2 text-sm font-medium text-right text-gray-900">₱${parseFloat(data.total).toFixed(2)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    `;
                    
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