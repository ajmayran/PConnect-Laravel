<x-app-layout>
    <x-dashboard-nav />
    
    <div class="container max-w-full px-4 py-6 mx-auto sm:px-6 lg:px-8">
        <!-- Back button and page title -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <a href="{{ route('retailers.orders.returned') }}" class="p-2 mr-3 text-gray-600 transition-colors duration-200 bg-gray-100 rounded-full hover:bg-gray-200 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Return & Refund History</h1>
            </div>

        </div>

        <!-- Search bar -->
        <div class="mb-6">
            <form method="GET" action="{{ route('retailers.orders.returned-history') }}" class="flex items-center space-x-2">
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search by Order ID or Distributor..."
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Search
                </button>
            </form>
        </div>

        <!-- Card container with shadow -->
        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <!-- Filter Tabs -->
            <div class="flex border-b">
                <a href="{{ route('retailers.orders.returned-history', ['filter' => 'all']) }}"
                    class="px-6 py-3 font-medium transition-colors {{ !request('filter') || request('filter') === 'all' ? 'text-blue-600 border-blue-500 border-b-2' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300 hover:border-b' }}">
                    All
                </a>
                <a href="{{ route('retailers.orders.returned-history', ['filter' => 'returned']) }}"
                    class="px-6 py-3 font-medium transition-colors {{ request('filter') === 'returned' ? 'text-green-600 border-green-500 border-b-2' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300 hover:border-b' }}">
                    Returned
                </a>
                <a href="{{ route('retailers.orders.returned-history', ['filter' => 'refunded']) }}"
                    class="px-6 py-3 font-medium transition-colors {{ request('filter') === 'refunded' ? 'text-purple-600 border-purple-500 border-b-2' : 'text-gray-600 border-transparent hover:text-gray-900 hover:border-gray-300 hover:border-b' }}">
                    Refunded
                </a>
            </div>

            @if ($orders->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 mt-2">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-lg text-gray-500">
                        @if (request('filter') === 'returned')
                            No returned orders found
                        @elseif(request('filter') === 'refunded')
                            No refunded orders found
                        @else
                            No returned or refunded orders found
                        @endif
                    </p>
                    @if(request('search'))
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search query</p>
                    @endif
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
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Amount
                                </th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($orders as $order)
                                @php
                                    $statusClass = match($order->status) {
                                        'returned' => 'text-blue-800 bg-blue-100',
                                        'refunded' => 'text-purple-800 bg-purple-100',
                                        default => 'text-gray-800 bg-gray-100',
                                    };
                                    
                                    $refund = $order->refunds()->latest()->first();
                                    $returnRequest = $order->returnRequests()->latest()->first();
                                    $refundAmount = $refund ? $refund->amount : 0;
                                @endphp

                                <tr class="transition-colors hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->formatted_order_id }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $order->distributor->company_name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $order->status_updated_at ? $order->status_updated_at->format('M d, Y') : $order->updated_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($refundAmount > 0)
                                            <div class="text-sm font-medium text-green-600">₱{{ number_format($refundAmount, 2) }}</div>
                                        @elseif ($returnRequest && $returnRequest->preferred_solution === 'exchange')
                                            <div class="text-sm text-gray-500">Exchange</div>
                                        @else
                                            <div class="text-sm text-gray-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button onclick="viewOrderDetails({{ $order->id }})" 
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

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="relative max-w-4xl p-6 mx-auto mt-10 mb-10 bg-white rounded-lg shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Order Details</h3>
                <button onclick="closeModal()" class="p-2 text-gray-400 rounded-full hover:bg-gray-100 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="modalContent" class="mt-4 space-y-6 overflow-y-auto max-h-[70vh]">
                <!-- Order details will be loaded here -->
                <div class="flex items-center justify-center py-8">
                    <div class="inline-block p-4 text-blue-500">
                        <svg class="w-10 h-10 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 mt-6 border-t border-gray-200">
                <button onclick="closeModal()"
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
        function viewOrderDetails(orderId) {
            // Show modal
            document.getElementById('orderDetailsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            
            // Update modal title
            document.getElementById('modalTitle').textContent = 'Loading Order Details...';
            
            // Show loading indicator
            document.getElementById('modalContent').innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="inline-block p-4 text-blue-500">
                        <svg class="w-10 h-10 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            `;
            
            // Fetch order details
            fetch(`/retailers/profile/${orderId}/order-details`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Update modal title
                    document.getElementById('modalTitle').textContent = `Order Details (#${data.order_id})`;
                    
                    // Update modal content
                    document.getElementById('modalContent').innerHTML = data.html;
                    
                    // Add return/refund information if available
                    if (data.return_info) {
                        addReturnInfo(data.return_info);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalContent').innerHTML = `
                        <div class="p-4 text-center text-red-500">
                            <p>Error loading order details. Please try again.</p>
                        </div>
                    `;
                });
        }
        
        function addReturnInfo(returnInfo) {
            // This function will add return/refund specific information to the modal
            const returnInfoHtml = `
                <div class="p-4 mt-4 border rounded-lg ${returnInfo.status === 'refunded' ? 'bg-purple-50 border-purple-200' : 'bg-blue-50 border-blue-200'}">
                    <h3 class="flex items-center mb-2 text-lg font-semibold ${returnInfo.status === 'refunded' ? 'text-purple-700' : 'text-blue-700'}">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        ${returnInfo.status === 'refunded' ? 'Refund Information' : 'Return Information'}
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-4 mt-3 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="text-base font-semibold">${returnInfo.status.charAt(0).toUpperCase() + returnInfo.status.slice(1)}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date</p>
                            <p class="text-base font-semibold">${returnInfo.date}</p>
                        </div>
                        
                        ${returnInfo.amount ? `
                        <div>
                            <p class="text-sm font-medium text-gray-500">Refund Amount</p>
                            <p class="text-base font-semibold text-green-600">₱${returnInfo.amount}</p>
                        </div>
                        ` : ''}
                        
                        ${returnInfo.solution ? `
                        <div>
                            <p class="text-sm font-medium text-gray-500">Solution</p>
                            <p class="text-base font-semibold">${returnInfo.solution.charAt(0).toUpperCase() + returnInfo.solution.slice(1)}</p>
                        </div>
                        ` : ''}
                    </div>
                    
                    ${returnInfo.reason ? `
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Return Reason</p>
                        <p class="p-2 mt-1 text-sm bg-white rounded">${returnInfo.reason}</p>
                    </div>
                    ` : ''}
                </div>
            `;
            
            document.getElementById('modalContent').insertAdjacentHTML('beforeend', returnInfoHtml);
        }

        function closeModal() {
            document.getElementById('orderDetailsModal').classList.add('hidden');
            document.body.style.overflow = ''; // Restore background scrolling
        }
        
        // Close modal when clicking outside
        document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Close modal with escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</x-app-layout>