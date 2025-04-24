<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-left text-gray-800 sm:text-3xl">Return and Refund</h1>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('distributors.exchanges.index') }}"
                        class="px-4 py-2 font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Manage Exchanges
                    </a>
                    <a href="{{ route('distributors.refunds.index') }}"
                        class="px-4 py-2 font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Manage Refunds
                    </a>
                </div>
            </div>

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
                <div class="flex items-center justify-start">
                    <div class="relative">
                        <form action="{{ route('distributors.returns.index') }}" method="GET">
                            <input type="search" name="search" placeholder="Search orders..."
                                value="{{ request('search') }}"
                                class="px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tables Container -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <!-- Return Orders Table -->
                <div id="returnOrders" class="tab-content">
                    <div class="p-4">
                        <h2 class="mb-4 text-sm text-gray-600">Return Orders: {{ $pendingExchanges->count() }}</h2>
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Order ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Products
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Retailer
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pendingExchanges as $returnRequest)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-semibold whitespace-nowrap">
                                            {{ $returnRequest->order->formatted_order_id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{ $returnRequest->items->count() }}
                                            {{ $returnRequest->items->count() > 1 ? 'Products' : 'Product' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{ $returnRequest->retailer->first_name }}
                                            {{ $returnRequest->retailer->last_name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{ $returnRequest->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                                {{ ucfirst($returnRequest->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            <button onclick="showOrderDetails('exchange', {{ $returnRequest->id }})"
                                                class="text-blue-600 hover:text-blue-900">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500">
                                            No pending exchange requests found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $pendingExchanges->links() }}
                        </div>
                    </div>
                </div>

                <!-- Refund Orders Table -->
                <div id="refundOrders" class="hidden tab-content">
                    <div class="p-4">
                        <h2 class="mb-4 text-sm text-gray-600">Refund Orders: {{ $pendingRefunds->count() }}</h2>
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Order ID
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Products
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Retailer
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pendingRefunds as $returnRequest)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-semibold whitespace-nowrap">
                                            {{ $returnRequest->order->formatted_order_id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{ $returnRequest->items->count() }} items
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{ $returnRequest->retailer->first_name }}
                                            {{ $returnRequest->retailer->last_name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            {{ $returnRequest->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                                {{ ucfirst($returnRequest->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            <button onclick="showOrderDetails('refund', {{ $returnRequest->id }})"
                                                class="text-blue-600 hover:text-blue-900">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500">
                                            No pending refund requests found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $pendingRefunds->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details Modal -->
            <div id="orderDetailsModal"
                class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
                <div
                    class="w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto bg-white rounded-lg shadow-xl md:w-3/4 sm:w-3/4">
                    <!-- Modal Header -->
                    <div class="sticky top-0 z-10 flex items-center justify-between p-4 bg-white border-b">
                        <h2 class="text-xl font-bold text-gray-800" id="modalTitle">Return Request Details</h2>
                        <button onclick="closeModal()"
                            class="p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="orderDetails" class="p-6">
                        <!-- Modal Content will be dynamically generated -->
                    </div>

                    <!-- Modal Footer -->
                    <div id="modalActions" class="sticky bottom-0 flex justify-end gap-4 p-4 bg-white border-t">
                        <!-- Buttons will be added dynamically -->
                    </div>
                </div>
            </div>

            <!-- Add hidden forms for approve/reject actions -->
            <form id="approveForm" action="" method="POST" style="display: none;">
                @csrf
            </form>
            <form id="rejectForm" action="" method="POST" style="display: none;">
                @csrf
            </form>

            <script>
                // Tab switching functionality
                document.getElementById('returnTab').addEventListener('click', function() {
                    switchTab('returnTab', 'returnOrders');
                });

                document.getElementById('refundTab').addEventListener('click', function() {
                    switchTab('refundTab', 'refundOrders');
                });

                // Helper function to switch tabs
                // Replace your switchTab function with this corrected version:
                function switchTab(activeTabId, activeContentId) {
                    // First get all tab buttons and deactivate them
                    document.querySelectorAll('.tab-button').forEach(button => {
                        button.classList.remove('text-green-600', 'border-b-2', 'border-green-500');
                        button.classList.add('text-gray-500', 'hover:text-green-600');
                    });

                    // Activate the selected tab button
                    const activeTab = document.getElementById(activeTabId);
                    if (activeTab) {
                        activeTab.classList.add('text-green-600', 'border-b-2', 'border-green-500');
                        activeTab.classList.remove('text-gray-500', 'hover:text-green-600');
                    }

                    // Hide all tab content sections
                    document.querySelectorAll('.tab-content').forEach(content => {
                        if (content) content.classList.add('hidden');
                    });

                    // Show the selected tab content
                    const activeContent = document.getElementById(activeContentId);
                    if (activeContent) {
                        activeContent.classList.remove('hidden');
                    } else {
                        console.error(`Tab content with ID "${activeContentId}" not found`);
                    }
                }

                // Function to show order details in modal
                function showOrderDetails(type, returnId) {
                    const modal = document.getElementById('orderDetailsModal');
                    const modalTitle = document.getElementById('modalTitle');
                    const orderDetails = document.getElementById('orderDetails');
                    const modalActions = document.getElementById('modalActions');

                    // Set title based on type
                    if (type === 'exchange') {
                        modalTitle.textContent = 'Exchange Request Details';
                    } else if (type === 'refund') {
                        modalTitle.textContent = 'Refund Request Details';
                    } else {
                        modalTitle.textContent = 'Return Request Details';
                    }

                    // Show loading indicator
                    orderDetails.innerHTML = `
                <div class="flex items-center justify-center p-8">
                    <svg class="w-12 h-12 text-green-500 animate-spin" xmlns="http://www.w3.org/2000/svg" 
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" 
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>`;
                    modal.classList.remove('hidden');

                    // Fetch return request details
                    fetch(`{{ route('distributors.returns.show', '') }}/${returnId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const returnRequest = data.return;

                                // Create a visually appealing layout for the return details
                                let detailsHtml = '<div class="space-y-6">';

                                // Status Card
                                const statusColorClass = returnRequest.status === 'pending' ?
                                    'bg-yellow-50 border-yellow-200 text-yellow-700' :
                                    (returnRequest.status === 'approved' ?
                                        'bg-green-50 border-green-200 text-green-700' :
                                        'bg-red-50 border-red-200 text-red-700');

                                const statusBadgeClass = returnRequest.status === 'pending' ?
                                    'bg-yellow-100 text-yellow-800' :
                                    (returnRequest.status === 'approved' ?
                                        'bg-green-100 text-green-800' :
                                        'bg-red-100 text-red-800');

                                detailsHtml += `
                                <div class="p-3 border rounded-lg ${statusColorClass}">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold">Return Status</h3>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusBadgeClass}">
                                            ${returnRequest.status.charAt(0).toUpperCase() + returnRequest.status.slice(1)}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-sm">
                                        Submitted on: ${new Date(returnRequest.created_at).toLocaleString()}
                                    </div>
                                </div>`;

                                // Order Information
                                detailsHtml += `
                                <div class="overflow-hidden bg-white rounded-lg shadow">
                                    <div class="p-4 border-b bg-gray-50">
                                        <h3 class="text-lg font-semibold text-gray-800">Order Information</h3>
                                    </div>
                                    <div class="p-4">
                                        <p class="mb-2"><strong>Order ID:</strong> ${returnRequest.order.formatted_order_id}</p>
                                        <p class="mb-2"><strong>Customer:</strong> ${returnRequest.retailer.first_name} ${returnRequest.retailer.last_name}</p>
                                        <p class="mb-2"><strong>Preferred Solution:</strong> ${returnRequest.preferred_solution.charAt(0).toUpperCase() + returnRequest.preferred_solution.slice(1)}</p>
                                        <p class="mb-4"><strong>Return Reason:</strong> ${returnRequest.reason}</p>
                                    </div>
                                </div>`;

                                // Proof Image
                                detailsHtml += `
                                <div class="overflow-hidden bg-white rounded-lg shadow">
                                    <div class="p-4 border-b bg-gray-50">
                                        <h3 class="text-lg font-semibold text-gray-800">Proof Image</h3>
                                    </div>
                                    <div class="p-4">
                                        <img src="/storage/${returnRequest.proof_image}" alt="Proof Image" class="w-full h-auto rounded-lg">
                                    </div>
                                </div>`;

                                // Products to Return - Different display based on return type
                                detailsHtml += `
                                <div class="overflow-hidden bg-white rounded-lg shadow">
                                    <div class="p-4 border-b bg-gray-50">
                                        <h3 class="text-lg font-semibold text-gray-800">Products to ${returnRequest.preferred_solution === 'exchange' ? 'Exchange' : 'Refund'}</h3>
                                    </div>
                                    <div class="p-4 space-y-4">`;

                                if (returnRequest.preferred_solution === 'exchange') {
                                    // For exchange, show only product name and quantity
                                    returnRequest.items.forEach(item => {
                                        const productName = item.order_detail?.product?.product_name ||
                                            'Unknown Product';
                                        const quantity = item.quantity || 0;

                                        detailsHtml += `
                                        <div class="flex items-center justify-between p-3 border-b border-gray-100">
                                            <div>
                                                <p class="font-medium text-gray-800">${productName}</p>
                                                <p class="text-sm text-gray-500">Quantity: ${quantity}</p>
                                            </div>
                                        </div>`;
                                    });
                                } else {
                                    // For refund, show product name, quantity, price and subtotal
                                    let totalRefundAmount = 0;

                                    returnRequest.items.forEach(item => {
                                        const productName = item.order_detail?.product?.product_name ||
                                            'Unknown Product';
                                        const quantity = item.quantity || 0;
                                        const price = parseFloat(item.order_detail?.price) || 0;
                                        const discountAmount = parseFloat(item.order_detail?.discount_amount) || 0;
                                        const discountedPrice = discountAmount > 0 ? price - (discountAmount / (item
                                            .order_detail?.quantity || 1)) : price;
                                        const subtotal = discountedPrice * quantity;
                                        totalRefundAmount += subtotal;

                                        detailsHtml += `
                                        <div class="flex items-center justify-between p-3 border-b border-gray-100">
                                            <div>
                                                <p class="font-medium text-gray-800">${productName}</p>
                                                <p class="text-sm text-gray-500">Quantity: ${quantity}</p>
                                                <p class="text-sm text-gray-500">
                                                    Price: 
                                                    ${discountAmount > 0 
                                                        ? `<span class="text-gray-400 line-through">₱${price.toFixed(2)}</span> 
                                                                                                        <span class="text-green-600">₱${discountedPrice.toFixed(2)}</span>` 
                                                        : `₱${price.toFixed(2)}`}
                                                </p>
                                            </div>
                                            <p class="text-sm font-medium text-gray-800">₱${subtotal.toFixed(2)}</p>
                                        </div>`;
                                    });

                                    // Add total refund amount for refund requests
                                    detailsHtml += `
                                    <div class="p-4 mt-4 rounded-lg bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-gray-700">Total Refund Amount:</span>
                                            <span class="text-lg font-bold text-green-600">₱${totalRefundAmount.toFixed(2)}</span>
                                        </div>
                                    </div>`;
                                }

                                detailsHtml += `
                                    </div>
                                </div>`;

                                // Add rejection reason if status is rejected
                                if (returnRequest.status === 'rejected' && returnRequest.reject_reason) {
                                    detailsHtml += `
                                    <div class="p-3 border border-red-200 rounded-lg bg-red-50">
                                        <h3 class="text-base font-semibold text-red-700">Rejection Reason</h3>
                                        <p class="mt-1 text-sm text-red-700">${returnRequest.reject_reason}</p>
                                    </div>`;
                                }

                                // Render the modal content
                                orderDetails.innerHTML = detailsHtml;

                                // Add action buttons for pending returns
                                if (returnRequest.status === 'pending') {
                                    modalActions.innerHTML = `
                                    <button type="button" onclick="confirmAction('approve', ${returnId})" 
                                            class="px-4 py-2 text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Approve ${returnRequest.preferred_solution === 'exchange' ? 'Exchange' : 'Refund'}
                                    </button>
                                    <button type="button" onclick="confirmAction('reject', ${returnId})" 
                                            class="px-4 py-2 mx-2 text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Reject
                                    </button>
                                    <button type="button" onclick="closeModal()" 
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Cancel
                                    </button>`;
                                } else {
                                    modalActions.innerHTML = `
                                    <button type="button" onclick="closeModal()" 
                                        class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                        Close
                                    </button>`;
                                }
                            } else {
                                orderDetails.innerHTML =
                                    '<div class="text-center text-red-500"><p>Failed to load return request details</p></div>';
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching return details:', error);
                            orderDetails.innerHTML =
                                '<div class="text-center text-red-500"><p>Error loading details. Please try again.</p></div>';
                        });
                }

                // Function to handle approval or rejection
                function confirmAction(action, returnId) {
                    // For approvals, use the existing confirmation flow
                    if (action === 'approve') {
                        // Properly construct the route URL
                        const form = document.getElementById('approveForm');
                        form.action = "{{ route('distributors.returns.approve', ':id') }}".replace(':id', returnId);

                        Swal.fire({
                            title: 'Approve Return?',
                            text: 'This will approve the return request and process the appropriate action.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#10B981',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Approve',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show loading state
                                Swal.fire({
                                    title: 'Processing...',
                                    text: 'Approving return request',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                        form.submit();
                                    }
                                });
                            }
                        });
                    }
                    // For rejections, first ask for rejection reason
                    else {
                        Swal.fire({
                            title: 'Reject Return?',
                            text: 'Please provide a reason for rejecting this return request.',
                            input: 'text',
                            inputPlaceholder: 'Enter rejection reason',
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'You need to provide a rejection reason!';
                                }
                            },
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Reject',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Get the rejection reason from the SweetAlert input
                                const rejectionReason = result.value;

                                // Prepare the form with absolute URL
                                const form = document.getElementById('rejectForm');
                                form.action = "{{ route('distributors.returns.reject', ':id') }}".replace(':id', returnId);

                                // Create and append the rejection reason field
                                let reasonField = document.getElementById('rejection_reason');
                                if (!reasonField) {
                                    reasonField = document.createElement('input');
                                    reasonField.type = 'hidden';
                                    reasonField.id = 'rejection_reason';
                                    reasonField.name = 'rejection_reason';
                                    form.appendChild(reasonField);
                                }
                                reasonField.value = rejectionReason;

                                // Show loading state
                                Swal.fire({
                                    title: 'Processing...',
                                    text: 'Rejecting return request',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                        form.submit();
                                    }
                                });
                            }
                        });
                    }
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
        </div>
</x-distributor-layout>
