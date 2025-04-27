<x-app-layout>
    <x-dashboard-nav />
    <div class="container max-w-full px-4 py-8 mx-auto">
        <div class="flex items-center justify-between mb-8 ml-4">
            <h1 class="text-3xl font-bold text-gray-900">Return Requests</h1>
            <a href="{{ route('retailers.orders.unpaid') }}"
                class="px-4 py-2 font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Unpaid Orders
            </a>
        </div>

        <x-retailer-orderstatus-tabs />

        <!-- Return Status Filter Tabs -->
        <div class="flex mb-6 border-b">
            <a href="{{ route('retailers.orders.returned', ['status' => 'all']) }}"
                class="px-4 py-2 -mb-px font-semibold {{ !request('status') || request('status') === 'all' ? 'text-green-600 border-green-500 border-b-2' : 'text-gray-600 border-transparent' }}">
                All Returns
            </a>
            <a href="{{ route('retailers.orders.returned', ['status' => 'pending']) }}"
                class="px-4 py-2 -mb-px font-semibold {{ request('status') === 'pending' ? 'text-yellow-600 border-yellow-500 border-b-2' : 'text-gray-600 border-transparent' }}">
                Pending
            </a>
            <a href="{{ route('retailers.orders.returned', ['status' => 'rejected']) }}"
                class="px-4 py-2 -mb-px font-semibold {{ request('status') === 'rejected' ? 'text-red-600 border-red-500 border-b-2' : 'text-gray-600 border-transparent' }}">
                Rejected
            </a>
        </div>

        @if ($orders->isEmpty())
            <div class="flex items-center justify-center p-8 mt-4 bg-white rounded-lg">
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
                                Return Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Return Type
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
                                $returnRequest = $order->returnRequests()->latest()->first();
                                $status = $returnRequest ? $returnRequest->status : 'processing';
                                $solution = $returnRequest ? $returnRequest->preferred_solution : '';

                                $statusClass = match ($status) {
                                    'pending' => 'text-yellow-800 bg-yellow-100',
                                    'approved' => 'text-green-800 bg-green-100',
                                    'rejected' => 'text-red-800 bg-red-100',
                                    default => 'text-indigo-800 bg-indigo-100',
                                };
                            @endphp


                            @php
                                $createdAtFormatted =
                                    $returnRequest && $returnRequest->created_at instanceof \Illuminate\Support\Carbon
                                        ? $returnRequest->created_at->format('M d, Y H:i')
                                        : $returnRequest->created_at;
                                $processedAtFormatted =
                                    $returnRequest && $returnRequest->processed_at instanceof \Illuminate\Support\Carbon
                                        ? $returnRequest->processed_at->format('M d, Y H:i')
                                        : $returnRequest->processed_at;
                            @endphp

                            <tr class="hover:bg-gray-50" data-return-id="{{ $returnRequest ? $returnRequest->id : '' }}"
                                data-order-id="{{ $order->id }}" data-status="{{ $status }}"
                                data-solution="{{ $solution }}"
                                data-reject-reason="{{ $returnRequest && $returnRequest->reject_reason ? $returnRequest->reject_reason : '' }}"
                                data-reason="{{ $returnRequest ? $returnRequest->reason : '' }}"
                                data-created="{{ $createdAtFormatted }}" data-processed="{{ $processedAtFormatted }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->formatted_order_id }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</div>
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
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($returnRequest)
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">
                                            {{ ucfirst($returnRequest->preferred_solution) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="showReturnDetails(this)" class="text-blue-600 hover:text-blue-900">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="container flex justify-end px-2 py-4 mx-auto sm:px-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Return Details Modal -->
    <div id="returnModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative max-w-4xl p-6 mx-auto mt-10 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Return Request Details</h3>
                <button onclick="closeReturnModal()" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="returnModalContent" class="mt-4 space-y-6">
                <!-- Return request details will be loaded here -->
            </div>

            <div class="flex justify-end pt-4 mt-6 border-t border-gray-200">
                <button onclick="closeReturnModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
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
                <div class="p-3 border rounded-lg ${statusColorClass}">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Return Status</h3>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusBadgeClass}">
                            ${status.charAt(0).toUpperCase() + status.slice(1)}
                        </span>
                    </div>
                    <div class="mt-1 text-sm">
                        <p>Submitted on: ${createdAt}</p>
                        ${processedAt ? `<p>Processed on: ${processedAt}</p>` : ''}
                    </div>
                </div>
                
                <!-- Return Details -->
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Return Information</h3>
                    </div>
                    <div class="p-4">
                        <p class="mb-2"><strong>Preferred Solution:</strong> ${solution.charAt(0).toUpperCase() + solution.slice(1)}</p>
                        <p class="mb-4"><strong>Return Reason:</strong> ${returnReason}</p>
                    </div>
                </div>`;

            // Add rejection reason section if status is rejected
            if (status === 'rejected' && rejectReason) {
                contentHtml += `
                    <!-- Rejection Reason -->
                    <div class="p-4 border border-red-300 rounded-lg bg-red-50">
                        <h3 class="mb-2 text-lg font-semibold text-red-700">Rejection Reason</h3>
                        <p class="text-red-700">${rejectReason}</p>
                    </div>`;
            }

            // Add proof image section
            contentHtml += `
                <!-- Proof Images -->
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Proof Images</h3>
                    </div>
                    <div id="proofImagesSection" class="p-4">
                        <div class="flex items-center justify-center">
                            <div class="inline-block p-4 text-blue-500">
                                <svg class="w-12 h-12 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                <div class="overflow-hidden bg-white rounded-lg shadow">
                    <div class="p-4 border-b bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800">Products ${status === 'approved' ? 'Returned' : 'to Return'}</h3>
                    </div>
                    <div class="p-4" id="productsSection">
                        <div class="flex items-center justify-center">
                            <div class="inline-block p-4 text-blue-500">
                                <svg class="w-12 h-12 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                                const productName = item.order_detail?.product?.product_name ||
                                    'Unknown Product';
                                const quantity = item.quantity || 0;

                                itemsHtml += `
                                <div class="flex items-center justify-between p-3 border-b border-gray-100">
                                    <div>
                                        <p class="font-medium text-gray-800">${productName}</p>
                                        <p class="text-sm text-gray-500">Quantity: ${quantity}</p>
                                    </div>
                                </div>`;
                            });
                        } else {
                            // For refund, show product name, quantity, and price
                            let totalRefund = 0;

                            data.items.forEach(item => {
                                const productName = item.order_detail?.product?.product_name ||
                                    'Unknown Product';
                                const quantity = item.quantity || 0;
                                const price = parseFloat(item.order_detail?.price) || 0;
                                const discountAmount = parseFloat(item.order_detail?.discount_amount) || 0;
                                const discountedPrice = discountAmount > 0 ? price - (discountAmount / (item
                                    .order_detail?.quantity || 1)) : price;
                                const subtotal = discountedPrice * quantity;
                                totalRefund += subtotal;

                                itemsHtml += `
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

                            // Add total section for refunds
                            if (solution === 'refund') {
                                itemsHtml += `
                                <div class="p-4 mt-4 rounded-lg bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold text-gray-700">Total Refund Amount:</span>
                                        <span class="text-lg font-bold text-green-600">₱${totalRefund.toFixed(2)}</span>
                                    </div>
                                </div>`;
                            }
                        }

                        // Replace loading spinner with actual items
                        const productsSection = document.getElementById('productsSection');
                        productsSection.innerHTML = itemsHtml ||
                            '<p class="text-center text-gray-500">No items found</p>';
                    } else {
                        throw new Error(data.message || 'Failed to load return items');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const productsSection = document.getElementById('productsSection');
                    productsSection.innerHTML =
                        `<p class="text-center text-red-500">Error loading return items: ${error.message}</p>`;
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
                            data.proofImages.forEach(image => {
                                proofHtml += `
                        <div class="mb-2">
                            <a href="${image.url}" target="_blank" class="text-blue-600 hover:underline">
                                View Proof Image
                            </a>
                        </div>`;
                            });
                        } else {
                            proofHtml = '<p class="text-center text-gray-500">No proof images provided</p>';
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
                    proofImagesSection.innerHTML =
                        `<p class="text-center text-red-500">Error loading proof images: ${error.message}</p>`;
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
    </script>
</x-app-layout>
