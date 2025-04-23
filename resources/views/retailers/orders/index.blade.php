<x-app-layout>
    <x-dashboard-nav />
    <div class="container max-w-full px-4 py-8 mx-auto">
        <div class="flex items-center justify-between mb-8 ml-4">
            <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
            <a href="{{ route('retailers.orders.unpaid') }}"
                class="px-4 py-2 font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Unpaid Orders
            </a>
        </div>

        <x-retailer-orderstatus-tabs />

        @if ($orders->isEmpty())
            <div class="flex items-center justify-center p-8 mt-4 bg-white rounded-lg">
                <p class="text-lg text-gray-500">No orders found</p>
            </div>
        @else
            <div class="overflow-hidden bg-white rounded-lg shadow">
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
                                Order Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Total
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->formatted_order_id }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $order->distributor->company_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-center whitespace-nowrap">
                                    <button onclick="openOrderModal({{ $order->id }})" class="text-blue-600 hover:text-blue-900">
                                        View Details
                                    </button>
                                    @if ($order->status === 'pending')
                                        <button onclick="document.getElementById('cancelModal{{ $order->id }}').style.display='block'" 
                                            class="ml-2 text-red-600 hover:text-red-900">
                                            Cancel
                                        </button>
                                    @endif
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

            <!-- Cancel Modals (one for each order) -->
            @foreach ($orders as $order)
                @if ($order->status === 'pending')
                    <div id="cancelModal{{ $order->id }}"
                        class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50">
                        <div class="relative max-w-md p-8 mx-auto mt-20 bg-white rounded-lg">
                            <h3 class="mb-4 text-lg font-bold">Cancel Order #{{ $order->formatted_order_id }}</h3>

                            <form action="{{ route('retailers.orders.cancel', $order) }}" method="POST">
                                @csrf
                                <p class="mb-4 text-gray-600">Please select a reason for cancellation:</p>

                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="cancel_reason" value="Changed my mind" required
                                            class="mr-2">
                                        Changed my mind
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="cancel_reason" value="Ordered by mistake" class="mr-2">
                                        Ordered by mistake
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="cancel_reason" value="Found better price" class="mr-2">
                                        Found better price elsewhere
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="cancel_reason" value="other" class="mr-2">
                                        Other reason
                                    </label>
                                </div>

                                <textarea name="custom_reason" class="w-full p-2 mt-4 border rounded" placeholder="Specify other reason..."
                                    style="display: none" rows="3"></textarea>

                                <div class="flex justify-end gap-2 mt-6">
                                    <button type="button"
                                        onclick="document.getElementById('cancelModal{{ $order->id }}').style.display='none'"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                        Close
                                    </button>
                                    <button type="button" id="confirmCancelBtn{{ $order->id }}"
                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                        Confirm Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide custom reason textarea
            document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const textarea = this.closest('form').querySelector(
                        'textarea[name="custom_reason"]');
                    textarea.style.display = this.value === 'other' ? 'block' : 'none';
                });
            });

            // Handle all cancel buttons directly
            @foreach ($orders as $order)
                // Only setup handlers for pending orders that can be cancelled
                @if ($order->status === 'pending')
                    // Get direct references to elements
                    const confirmBtn{{ $order->id }} = document.getElementById(
                        'confirmCancelBtn{{ $order->id }}');
                    const modal{{ $order->id }} = document.getElementById('cancelModal{{ $order->id }}');
                    const form{{ $order->id }} = modal{{ $order->id }}.querySelector('form');

                    if (confirmBtn{{ $order->id }} && form{{ $order->id }}) {
                        confirmBtn{{ $order->id }}.addEventListener('click', function() {
                            // Check if a reason is selected
                            const selectedReason = form{{ $order->id }}.querySelector(
                                'input[name="cancel_reason"]:checked');
                            if (!selectedReason) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Please select a cancellation reason',
                                    icon: 'error'
                                });
                                return;
                            }

                            // If "other" is selected, verify text is entered
                            if (selectedReason.value === 'other') {
                                const customReason = form{{ $order->id }}.querySelector(
                                    'textarea[name="custom_reason"]').value.trim();
                                if (!customReason) {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Please provide your reason for cancellation',
                                        icon: 'error'
                                    });
                                    return;
                                }
                            }

                            // Show confirmation alert
                            Swal.fire({
                                title: 'Cancel Order?',
                                text: 'Are you sure you want to cancel this order #{{ $order->formatted_order_id }}?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Yes, cancel it',
                                cancelButtonText: 'Keep my order'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Show loading state
                                    Swal.fire({
                                        title: 'Processing...',
                                        text: 'Cancelling your order',
                                        allowOutsideClick: false,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                            // Submit the form
                                            form{{ $order->id }}.submit();
                                        }
                                    });
                                }
                            });
                        });
                    }

                    // Close modal when clicking outside
                    modal{{ $order->id }}.addEventListener('click', function(e) {
                        if (e.target === this) {
                            this.style.display = 'none';
                        }
                    });
                @endif
            @endforeach
            
            // Close modal with escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeOrderModal();
                    
                    // Also close any open cancel modals
                    document.querySelectorAll('[id^="cancelModal"]').forEach(modal => {
                        modal.style.display = 'none';
                    });
                }
            });

            // Show success alert if present in session
            @if (session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            // Show error alert if present in session
            @if (session('error'))
                Swal.fire({
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    icon: 'error'
                });
            @endif
        });
    </script>
</x-app-layout>