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

    <div class="container mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-3 sm:p-4 mx-auto">
            <h1 class="mb-4 text-lg sm:text-xl md:text-3xl font-bold">Return and Refund</h1>

            <!-- Tabs and Search Section -->
            <div class="p-3 sm:p-4 mb-4 sm:mb-6 bg-white rounded-lg shadow-sm">
                <!-- Tabs -->
                <div class="flex justify-between mb-3 sm:mb-4 border-b overflow-x-auto scrollbar-hide">
                    <div class="flex space-x-2 sm:space-x-4 min-w-max">
                        <button id="returnTab" class="px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-green-600 border-b-2 border-green-500 tab-button">
                            Return Orders
                        </button>
                        <button id="refundTab" class="px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-gray-500 hover:text-green-600 tab-button">
                            Refunded Orders
                        </button>
                    </div>
                </div>

                <!-- Search and Export -->
                <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-4">
                    <div class="relative w-full sm:w-auto">
                        <form action="{{ route('distributors.returns.index') }}" method="GET">
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
                    </div>
                    <a href="{{ route('distributors.returns.export') }}"
                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-green-500 rounded-lg hover:bg-green-600">
                        <svg class="inline-block w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export Reports
                    </a>
                </div>
            </div>

            <!-- Tables Container -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <!-- Return Orders Table -->
                <div id="returnOrders" class="tab-content">
                    <div class="p-3 sm:p-4">
                        <h2 class="mb-3 sm:mb-4 text-xs sm:text-sm text-gray-600">Return Orders: {{ $pendingReturns->count() }}</h2>
                        <div class="table-container">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Order ID
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Products
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase mobile-hidden">
                                            Customer
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Date
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Status
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($pendingReturns as $returnRequest)
                                        <tr>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm font-semibold whitespace-nowrap">
                                                {{ $returnRequest->order->formatted_order_id }}
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                {{ $returnRequest->items->count() }} items
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm whitespace-nowrap mobile-hidden">
                                                {{ $returnRequest->retailer->first_name }}
                                                {{ $returnRequest->retailer->last_name }}
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                {{ $returnRequest->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                <span
                                                    class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                                    {{ ucfirst($returnRequest->status) }}
                                                </span>
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                <button onclick="showOrderDetails('return', {{ $returnRequest->id }})"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-center text-gray-500">
                                                No pending return requests found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $pendingReturns->links() }}
                        </div>
                    </div>
                </div>

                <!-- Refund Orders Table -->
                <div id="refundOrders" class="hidden tab-content">
                    <div class="p-3 sm:p-4">
                        <h2 class="mb-3 sm:mb-4 text-xs sm:text-sm text-gray-600">Refunded Orders: {{ $completedReturns->count() }}</h2>
                        <div class="table-container">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Order ID
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Products
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase mobile-hidden">
                                            Customer
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Date
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Status
                                        </th>
                                        <th class="px-2 sm:px-6 py-2 sm:py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($completedReturns as $returnRequest)
                                        <tr>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm font-semibold whitespace-nowrap">
                                                {{ $returnRequest->order->formatted_order_id }}
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                {{ $returnRequest->items->count() }} items
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm whitespace-nowrap mobile-hidden">
                                                {{ $returnRequest->retailer->first_name }}
                                                {{ $returnRequest->retailer->last_name }}
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                {{ $returnRequest->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                <span
                                                    class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                                    {{ ucfirst($returnRequest->status) }}
                                                </span>
                                            </td>
                                            <td class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm whitespace-nowrap">
                                                <button onclick="showOrderDetails('refund', {{ $returnRequest->id }})"
                                                    class="text-blue-600 hover:text-blue-900">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-2 sm:px-6 py-2 sm:py-4 text-xs sm:text-sm text-center text-gray-500">
                                                No completed return requests found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $completedReturns->links() }}
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
                    <h2 class="text-base sm:text-lg md:text-xl font-bold text-gray-800" id="modalTitle">Return Request Details</h2>
                    <button onclick="closeModal()"
                        class="p-1 sm:p-2 text-gray-400 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-600">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div id="orderDetails" class="p-3 sm:p-6">
                    <!-- Modal Content will be dynamically generated -->
                </div>

                <!-- Modal Footer -->
                <div id="modalActions" class="sticky bottom-0 flex flex-wrap justify-end gap-2 sm:gap-4 p-3 sm:p-4 bg-white border-t">
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
                this.classList.add('text-green-600', 'border-b-2', 'border-green-500');
                document.getElementById('refundTab').classList.remove('text-green-600', 'border-b-2',
                    'border-green-500');
                document.getElementById('returnOrders').classList.remove('hidden');
                document.getElementById('refundOrders').classList.add('hidden');
            });

            document.getElementById('refundTab').addEventListener('click', function() {
                this.classList.add('text-green-600', 'border-b-2', 'border-green-500');
                document.getElementById('returnTab').classList.remove('text-green-600', 'border-b-2',
                    'border-green-500');
                document.getElementById('refundOrders').classList.remove('hidden');
                document.getElementById('returnOrders').classList.add('hidden');
            });

            // Modal functionality
            function showOrderDetails(type, returnId) {
                const modal = document.getElementById('orderDetailsModal');
                const modalTitle = document.getElementById('modalTitle');
                const orderDetails = document.getElementById('orderDetails');
                const modalActions = document.getElementById('modalActions');

                // Set title based on type
                modalTitle.textContent = type === 'return' ? 'Return Request Details' : 'Refunded Order Details';

                // Show loading indicator
                orderDetails.innerHTML = `
                <div class="flex items-center justify-center p-8">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-green-500 animate-spin" xmlns="http://www.w3.org/2000/svg" 
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
                            // Create a more visually appealing layout for the return details
                            let detailsHtml = '<div class="space-y-4 sm:space-y-6">';

                            // Status Card
                            const statusColorClass = data.return.status === 'pending' ?
                                'bg-yellow-50 border-yellow-200 text-yellow-700' :
                                (data.return.status === 'approved' ? 'bg-green-50 border-green-200 text-green-700' :
                                    'bg-red-50 border-red-200 text-red-700');

                            const statusBadgeClass = data.return.status === 'pending' ?
                                'bg-yellow-100 text-yellow-800' :
                                (data.return.status === 'approved' ? 'bg-green-100 text-green-800' :
                                    'bg-red-100 text-red-800');

                            detailsHtml += `
                            <div class="p-3 border rounded-lg ${statusColorClass}">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base sm:text-lg font-semibold">Return Status</h3>
                                    <span class="inline-flex px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-semibold rounded-full ${statusBadgeClass}">
                                        ${data.return.status.charAt(0).toUpperCase() + data.return.status.slice(1)}
                                    </span>
                                </div>
                                <div class="mt-1 text-xs sm:text-sm">
                                    Submitted on: ${new Date(data.return.created_at).toLocaleString()}
                                </div>
                            </div>`;

                            // Order Information
                            detailsHtml += `
                            <div class="overflow-hidden bg-white rounded-lg shadow">
                                <div class="p-3 sm:p-4 border-b bg-gray-50">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Order Information</h3>
                                </div>
                                <div class="p-3 sm:p-4">
                                    <p class="mb-2 text-xs sm:text-sm"><strong>Order ID:</strong> ${data.return.order.formatted_order_id}</p>
                                    <p class="mb-2 text-xs sm:text-sm"><strong>Customer:</strong> ${data.return.retailer.first_name} ${data.return.retailer.last_name}</p>
                                    <p class="mb-2 sm:mb-4 text-xs sm:text-sm"><strong>Return Reason:</strong> ${data.return.reason}</p>
                                </div>
                            </div>`;

                            // Items Section
                            detailsHtml += `
                            <div class="overflow-hidden bg-white rounded-lg shadow">
                                <div class="p-3 sm:p-4 border-b bg-gray-50">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Items to Return</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-left text-gray-700">Product</th>
                                                <th class="px-2 sm:px-4 py-2 sm:py-3 text-xs font-medium text-left text-gray-700">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">`;

                            data.return.items.forEach(item => {
                                detailsHtml += `
                                <tr class="hover:bg-gray-50">
                                    <td class="px-2 sm:px-4 py-2 sm:py-3">
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            ${item.product.image ? 
                                                `<img src="{{ asset('storage') }}/${item.product.image}" alt="${item.product.product_name}" class="object-cover w-10 h-10 sm:w-16 sm:h-16 rounded-lg" />` :
                                                `<div class="flex items-center justify-center w-10 h-10 sm:w-16 sm:h-16 text-gray-400 bg-gray-100 rounded-lg">No image</div>`
                                            }
                                            <span class="font-medium text-xs sm:text-sm text-gray-800">${item.product.product_name}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm">${item.quantity}</td>
                                </tr>`;
                            });

                            detailsHtml += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>`;

                            // Receipt Section
                            detailsHtml += `
                            <div class="overflow-hidden bg-white rounded-lg shadow">
                                <div class="p-3 sm:p-4 border-b bg-gray-50">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Receipt</h3>
                                </div>
                                <div class="p-3 sm:p-4">
                                    <a href="{{ asset('storage') }}/${data.return.receipt_path}" target="_blank" class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Receipt
                                    </a>
                                </div>
                            </div>`;

                            detailsHtml += '</div>';
                            orderDetails.innerHTML = detailsHtml;

                            // Add action buttons for pending returns
                            if (type === 'return' && data.return.status === 'pending') {
                                // Update modal actions with approve/reject buttons
                                modalActions.innerHTML = `
                                <button type="button" onclick="confirmAction('approve', ${returnId})" 
                                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Approve Return
                                </button>
                                <button type="button" onclick="confirmAction('reject', ${returnId})" 
                                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Reject
                                </button>
                                <button type="button" onclick="closeModal()" 
                                        class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>`;
                            } else {
                                // Just show close button for approved/rejected returns
                                modalActions.innerHTML = `
                                <button type="button" onclick="closeModal()" 
                                    class="px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
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

            // New function that replaces processReturn
            function confirmAction(action, returnId) {
                // For approvals, use the existing confirmation flow
                if (action === 'approve') {
                    // Properly construct the route URL
                    const form = document.getElementById('approveForm');
                    form.action = "{{ route('distributors.returns.approve', ':id') }}".replace(':id', returnId);

                    // Adjust the SweetAlert for mobile sizing
                    const width = window.innerWidth < 640 ? '90%' : '500px';

                    Swal.fire({
                        title: 'Approve Return?',
                        text: 'This will approve the return request and process a refund.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Approve',
                        cancelButtonText: 'Cancel',
                        width: width
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Approving return request',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                width: width,
                                didOpen: () => {
                                    Swal.showLoading();
                                    // Submit after a brief delay to ensure SweetAlert is shown
                                    setTimeout(() => {
                                        form.submit();
                                    }, 100);
                                }
                            });
                        }
                    });
                }
                // For rejections, first ask for rejection reason
                else {
                    // Adjust the SweetAlert for mobile sizing
                    const width = window.innerWidth < 640 ? '90%' : '500px';
                    
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
                        cancelButtonText: 'Cancel',
                        width: width
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
                                width: width,
                                didOpen: () => {
                                    Swal.showLoading();
                                    // Submit after a brief delay to ensure SweetAlert is shown
                                    setTimeout(() => {
                                        form.submit();
                                    }, 100);
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
