<x-app-layout>
    <x-dashboard-nav />
    
    <div class="container max-w-full px-4 py-6 mx-auto sm:px-6 lg:px-8">
        <!-- Back button and page title -->
        <div class="flex flex-wrap items-center justify-between mb-6 gap-y-4">
            <div class="flex items-center">
                <a href="{{ route('retailers.profile.my-purchase') }}" class="p-2 mr-3 text-gray-600 transition-colors duration-200 bg-gray-100 rounded-full hover:bg-gray-200 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Return Requests</h1>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('retailers.orders.returned-history') }}" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors duration-200 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Return History
                </a>
                <a href="{{ route('retailers.orders.refund-track') }}" class="flex items-center px-4 py-2 text-sm font-medium text-white transition-colors duration-200 bg-yellow-500 rounded-md shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Track Refund
                </a>
            </div>
        </div>

        <!-- Card container with shadow -->
        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <!-- Return Status Filter Tabs -->
            <div class="flex border-b">
                <a href="{{ route('retailers.orders.returned', ['status' => 'all']) }}"
                    class="px-6 py-3 font-medium transition-colors {{ !request('status') || request('status') === 'all' ? 'text-green-600 border-green-500 border-b-2' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300 hover:border-b' }}">
                    All Returns
                </a>
                <a href="{{ route('retailers.orders.returned', ['status' => 'pending']) }}"
                    class="px-6 py-3 font-medium transition-colors {{ request('status') === 'pending' ? 'text-yellow-600 border-yellow-500 border-b-2' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300 hover:border-b' }}">
                    Pending
                </a>
                <a href="{{ route('retailers.orders.returned', ['status' => 'rejected']) }}"
                    class="px-6 py-3 font-medium transition-colors {{ request('status') === 'rejected' ? 'text-red-600 border-red-500 border-b-2' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300 hover:border-b' }}">
                    Rejected
                </a>
            </div>

            @if ($orders->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 mt-2">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-lg text-gray-500">
                        @if (request('status') === 'pending')
                            No pending return requests found
                        @elseif(request('status') === 'approved')
                            No approved return requests found
                        @elseif(request('status') === 'rejected')
                            No rejected return requests found
                        @else
                            No return requests found
                        @endif
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Order ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Distributor
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Return Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Return Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($orders as $order)
                                @php
                                    $returnRequest = $order->returnRequests()->latest()->first();
                                    $status = $returnRequest ? $returnRequest->status : 'processing';
                                    $solution = $returnRequest ? $returnRequest->preferred_solution : '';

                                    $statusClass = match ($status) {
                                        'pending' => 'text-yellow-800 bg-yellow-100',
                                        'approved' => 'text-green-800 bg-green-100',
                                        'rejected' => 'text-red-800 bg-red-100',
                                        default => 'text-indigo-800 bg-indigo-100',
                                    };
                                    
                                    $createdAtFormatted =
                                        $returnRequest && $returnRequest->created_at instanceof \Illuminate\Support\Carbon
                                            ? $returnRequest->created_at->format('M d, Y H:i')
                                            : $returnRequest->created_at;
                                    $processedAtFormatted =
                                        $returnRequest && $returnRequest->processed_at instanceof \Illuminate\Support\Carbon
                                            ? $returnRequest->processed_at->format('M d, Y H:i')
                                            : $returnRequest->processed_at;
                                @endphp

                                <tr class="transition-colors hover:bg-gray-50" 
                                    data-return-id="{{ $returnRequest ? $returnRequest->id : '' }}"
                                    data-order-id="{{ $order->id }}" 
                                    data-status="{{ $status }}"
                                    data-solution="{{ $solution }}"
                                    data-reject-reason="{{ $returnRequest && $returnRequest->reject_reason ? $returnRequest->reject_reason : '' }}"
                                    data-reason="{{ $returnRequest ? $returnRequest->reason : '' }}"
                                    data-created="{{ $createdAtFormatted }}" 
                                    data-processed="{{ $processedAtFormatted }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->formatted_order_id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $order->distributor->company_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $returnRequest ? $returnRequest->created_at->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($returnRequest)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                                {{ ucfirst($returnRequest->preferred_solution) }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button onclick="showReturnDetails(this)" 
                                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        
        <!-- Pagination -->
        @if (!$orders->isEmpty())
            <div class="mt-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Return Details Modal -->
    <div id="returnModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative max-w-4xl p-6 mx-auto mt-10 mb-10 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Return Request Details</h3>
                <button onclick="closeReturnModal()" class="p-2 text-gray-400 rounded-full hover:bg-gray-100 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="returnModalContent" class="mt-4 space-y-6 overflow-y-auto max-h-[70vh]">
                <!-- Return request details will be loaded here -->
            </div>

            <div class="flex justify-end pt-4 mt-6 border-t border-gray-200">
                <button onclick="closeReturnModal()"
                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function showReturnDetails(button) {
            const row = button.closest('tr');
            const returnId = row.getAttribute('data-return-id');
            const orderId = row.getAttribute('data-order-id');
            const status = row.getAttribute('data-status');
            const solution = row.getAttribute('data-solution');
            const rejectReason = row.getAttribute('data-reject-reason');
            const returnReason = row.getAttribute('data-reason');
            const createdAt = row.getAttribute('data-created');
            const processedAt = row.getAttribute('data-processed');

            // Update modal title
            document.getElementById('modalTitle').textContent = `Return Request Details (Order #${orderId})`;

            // Create modal content based on return status
            let statusColorClass, statusBadgeClass;

            switch (status) {
                case 'pending':
                    statusColorClass = 'bg-yellow-50 border-yellow-200 text-yellow-700';
                    statusBadgeClass = 'bg-yellow-100 text-yellow-800';
                    break;  
                case 'approved':
                    statusColorClass = 'bg-green-50 border-green-200 text-green-700';
                    statusBadgeClass = 'bg-green-100 text-green-800';
                    break;
                case 'rejected':
                    statusColorClass = 'bg-red-50 border-red-200 text-red-700';
                    statusBadgeClass = 'bg-red-100 text-red-800';
                    break;
                default:
                    statusColorClass = 'bg-blue-50 border-blue-200 text-blue-700';
                    statusBadgeClass = 'bg-blue-100 text-blue-800';
            }

            let contentHtml = `
                <!-- Status Card -->
                <div class="p-4 border rounded-lg ${statusColorClass}">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Return Status</h3>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full ${statusBadgeClass}">
                            ${status.charAt(0).toUpperCase() + status.slice(1)}
                        </span>
                    </div>
                    <div class="mt-2 text-sm">
                        <p class="flex items-center mb-1">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Submitted on: ${createdAt}
                        </p>
                        ${processedAt ? `
                        <p class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Processed on: ${processedAt}
                        </p>` : ''}
                    </div>
                </div>
                
                <!-- Return Details -->
                <div class="overflow-hidden bg-white border rounded-lg shadow-sm">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Return Information</h3>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <span class="font-medium text-gray-600">Preferred Solution:</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium ${
                                solution === 'refund' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                            }">
                                ${solution === 'refund' ? 
                                    '<svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' : 
                                    '<svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m-8 4H4m0 0l4 4m-4-4l4-4" /></svg>'
                                }
                                ${solution.charAt(0).toUpperCase() + solution.slice(1)}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Return Reason:</span>
                            <p class="px-3 py-2 mt-1 text-gray-800 rounded-md bg-gray-50">${returnReason}</p>
                        </div>
                    </div>
                </div>`;

            // Add rejection reason section if status is rejected
            if (status === 'rejected' && rejectReason) {
                contentHtml += `
                    <!-- Rejection Reason -->
                    <div class="p-4 border border-red-300 rounded-lg bg-red-50">
                        <h3 class="flex items-center mb-2 text-lg font-semibold text-red-700">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Rejection Reason
                        </h3>
                        <p class="p-3 text-red-700 bg-white border border-red-200 rounded">${rejectReason}</p>
                    </div>`;
            }

            // Add proof image section
            contentHtml += `
                <!-- Proof Images -->
                <div class="overflow-hidden bg-white border rounded-lg shadow-sm">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="flex items-center text-lg font-semibold text-gray-800">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Proof Images
                        </h3>
                    </div>
                    <div id="proofImagesSection" class="p-4">
                        <div class="flex items-center justify-center">
                            <div class="inline-block p-4 text-blue-500">
                                <svg class="w-10 h-10 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>`;

            // Load related items
            contentHtml += `
                <!-- Products -->
                <div class="overflow-hidden bg-white border rounded-lg shadow-sm">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="flex items-center text-lg font-semibold text-gray-800">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Products ${status === 'approved' ? 'Returned' : 'to Return'}
                        </h3>
                    </div>
                    <div class="p-4" id="productsSection">
                        <div class="flex items-center justify-center">
                            <div class="inline-block p-4 text-blue-500">
                                <svg class="w-10 h-10 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>`;

            // Update modal content
            document.getElementById('returnModalContent').innerHTML = contentHtml;

            // If we have a return ID, fetch the items and proof images
            if (returnId) {
                fetchReturnItems(returnId, solution);
                fetchProofImages(returnId);
            }

            // Show modal
            document.getElementById('returnModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function fetchReturnItems(returnId, solution) {
            fetch(`/retailers/return-items/${returnId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Create items list
                        let itemsHtml = '';

                        if (solution === 'exchange') {
                            // For exchange, only product name and quantity
                            data.items.forEach(item => {
                                const productName = item.order_detail?.product?.product_name || 'Unknown Product';
                                const quantity = item.quantity || 0;

                                itemsHtml += `
                                <div class="flex items-center justify-between p-4 border-b border-gray-100 hover:bg-gray-50">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">${productName}</p>
                                        <div class="flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                            </svg>
                                            <p class="text-sm text-gray-600">Quantity: ${quantity}</p>
                                        </div>
                                    </div>
                                </div>`;
                            });
                        } else {
                            // For refund, show product name, quantity, and price
                            let totalRefund = 0;

                            data.items.forEach(item => {
                                const productName = item.order_detail?.product?.product_name || 'Unknown Product';
                                const quantity = item.quantity || 0;
                                const price = parseFloat(item.order_detail?.price) || 0;
                                const discountAmount = parseFloat(item.order_detail?.discount_amount) || 0;
                                const discountedPrice = discountAmount > 0 ? price - (discountAmount / (item.order_detail?.quantity || 1)) : price;
                                const subtotal = discountedPrice * quantity;
                                totalRefund += subtotal;

                                itemsHtml += `
                                <div class="flex items-center justify-between p-4 border-b border-gray-100 hover:bg-gray-50">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800">${productName}</p>
                                        <div class="flex flex-wrap items-center mt-1 gap-x-4">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                                </svg>
                                                <p class="text-sm text-gray-600">Qty: ${quantity}</p>
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-sm text-gray-600">
                                                    ${discountAmount > 0 
                                                        ? `<span class="text-gray-400 line-through">₱${price.toFixed(2)}</span> 
                                                           <span class="ml-1 text-green-600">₱${discountedPrice.toFixed(2)}</span>` 
                                                        : `₱${price.toFixed(2)}`}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium text-gray-800">₱${subtotal.toFixed(2)}</p>
                                </div>`;
                            });

                            // Add total section for refunds
                            if (solution === 'refund') {
                                itemsHtml += `
                                <div class="p-4 mt-2 rounded-lg bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <span class="flex items-center font-semibold text-gray-700">
                                            <svg class="w-5 h-5 mr-1.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Total Refund Amount:
                                        </span>
                                        <span class="text-lg font-bold text-green-600">₱${totalRefund.toFixed(2)}</span>
                                    </div>
                                </div>`;
                            }
                        }

                        // Replace loading spinner with actual items
                        const productsSection = document.getElementById('productsSection');
                        productsSection.innerHTML = itemsHtml || '<p class="text-center text-gray-500">No items found</p>';
                    } else {
                        throw new Error(data.message || 'Failed to load return items');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const productsSection = document.getElementById('productsSection');
                    productsSection.innerHTML = `<p class="text-center text-red-500">Error loading return items: ${error.message}</p>`;
                });
        }

        function fetchProofImages(returnId) {
            fetch(`/retailers/return-proof-images/${returnId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        let proofHtml = '';

                        if (data.proofImages && data.proofImages.length > 0) {
                            proofHtml = '<div class="grid grid-cols-1 gap-4 md:grid-cols-2">';
                            data.proofImages.forEach((image, index) => {
                                proofHtml += `
                                <div class="overflow-hidden border rounded-lg">
                                    <a href="${image.url}" target="_blank" class="group">
                                        <div class="relative flex items-center justify-center h-40 p-4 transition-colors bg-gray-50 hover:bg-gray-100">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <div class="absolute inset-0 flex items-center justify-center transition-opacity bg-black bg-opacity-0 group-hover:bg-opacity-10">
                                                <span class="text-white opacity-0 group-hover:opacity-100 px-3 py-1.5 bg-gray-900 bg-opacity-75 rounded-md text-sm font-medium transition-opacity">View Image</span>
                                            </div>
                                        </div>
                                        <div class="p-2 border-t">
                                            <p class="text-sm font-medium text-blue-600 transition-colors hover:text-blue-800">
                                                Proof Image ${index + 1}
                                            </p>
                                        </div>
                                    </a>
                                </div>`;
                            });
                            proofHtml += '</div>';
                        } else {
                            proofHtml = '<p class="py-4 text-center text-gray-500">No proof images provided</p>';
                        }

                        const proofImagesSection = document.getElementById('proofImagesSection');
                        proofImagesSection.innerHTML = proofHtml;
                    } else {
                        throw new Error(data.message || 'Failed to load proof images');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const proofImagesSection = document.getElementById('proofImagesSection');
                    proofImagesSection.innerHTML = `<p class="text-center text-red-500">Error loading proof images: ${error.message}</p>`;
                });
        }

        function closeReturnModal() {
            // Hide the modal
            document.getElementById('returnModal').classList.add('hidden');

            // Restore background scrolling
            document.body.style.overflow = '';

            // Clear the modal content
            document.getElementById('returnModalContent').innerHTML = '';
        }
        
        // Close modal when clicking outside
        document.getElementById('returnModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReturnModal();
            }
        });
    </script>
</x-app-layout>