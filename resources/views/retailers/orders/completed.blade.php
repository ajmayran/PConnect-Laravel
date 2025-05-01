<x-app-layout>
    <x-dashboard-nav />
    <div class="container max-w-full px-4 py-8 mx-auto">
        <div class="flex items-center justify-between mb-8 ml-4">
            <h1 class="text-3xl font-bold text-gray-900">Completed Orders</h1>
            <a href="{{ route('retailers.orders.unpaid') }}"
                class="px-4 py-2 font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Unpaid Orders
            </a>
        </div>

        <x-retailer-orderstatus-tabs />

        @if ($orders->isEmpty())
            <div class="flex items-center justify-center p-8 mt-4 bg-white rounded-lg">
                <p class="text-lg text-gray-500">No completed orders found</p>
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
                                Order Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Total
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
                                // Check if order was completed within the last 7 days
                                $orderDate = new \Carbon\Carbon($order->status_updated_at ?? $order->updated_at);
                                $isWithinReturnPeriod = $orderDate->diffInDays(now()) <= 7;

                                // Check if return request was rejected
                                $rejectedReturnRequest = $order
                                    ->returnRequests()
                                    ->where('status', 'rejected')
                                    ->exists();

                                // Check if order has any other return request
                                $pendingOrApprovedReturnRequest = $order
                                    ->returnRequests()
                                    ->whereIn('status', ['pending', 'approved'])
                                    ->exists();

                                $hasReturnRequest = $order->returnRequests()->exists();
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->formatted_order_id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->distributor->company_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="openOrderModal({{ $order->id }})"
                                            class="text-blue-600 hover:text-blue-900">
                                            View Details
                                        </button>

                                        <div class="w-px h-4 mx-2 bg-gray-300"></div>
                                        <button
                                            onclick="checkReturnEligibility({{ $order->id }}, {{ $isWithinReturnPeriod ? 'true' : 'false' }}, {{ $hasReturnRequest ? 'true' : 'false' }}, '{{ $order->formatted_order_id }}')"
                                            class="text-yellow-600 hover:text-yellow-800">
                                            Request Return
                                        </button>
                                    </div>
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
        @endif
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

    <!-- Return Request Modals -->
    @foreach ($orders as $order)
        @php
            // Check if order was completed within the last 7 days
            $orderDate = new \Carbon\Carbon($order->status_updated_at ?? $order->updated_at);
            $isWithinReturnPeriod = $orderDate->diffInDays(now()) <= 7;

            // Check if order has any return request
            $hasReturnRequest = $order->returnRequests()->exists();
        @endphp

        <!-- Create a modal for EVERY order, not just those without return requests -->
        <div id="request-return-{{ $order->id }}"
            class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
            <div class="relative p-6 mx-auto mt-10 bg-white rounded-lg shadow-xl max-w-7xl">
                <div class="flex items-center justify-between pb-3 border-b">
                    <h2 class="text-lg font-medium text-gray-900">
                        Request Return for Order #{{ $order->formatted_order_id }}
                    </h2>
                    <button onclick="closeReturnRequestModal({{ $order->id }})"
                        class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('retailers.orders.request-return', $order->id) }}"
                        class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-2">
                            <x-input-label for="reason-{{ $order->id }}" value="Reason for Return" />
                            <select id="reason-{{ $order->id }}" name="reason"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                onchange="toggleOtherReason(this, {{ $order->id }})" required>
                                <option value="">Select reason</option>
                                <option value="Damage">Damage</option>
                                <option value="Spoiled upon arrival">Spoiled upon arrival</option>
                                <option value="Wrong Item sent">Wrong Item sent</option>
                                <option value="other">Other reason</option>
                            </select>
                            <div id="other_reason_container-{{ $order->id }}" class="hidden mt-2">
                                <textarea id="other_reason-{{ $order->id }}" name="other_reason"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Please specify your reason..."></textarea>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                        </div>

                        <div class="space-y-2">
                            <x-input-label for="solution-{{ $order->id }}" value="Preferred Solution" />
                            <select id="solution-{{ $order->id }}" name="preferred_solution"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                required>
                                <option value="">Select preferred solution</option>
                                <option value="exchange">Return and Exchange</option>
                                <option value="refund">Refund only</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('solution')" />
                        </div>

                        <div>
                            <x-input-label for="proof_image-{{ $order->id }}"
                                value="Upload Proof Image (Required)" />
                            <input type="file" id="proof_image-{{ $order->id }}" name="proof_image"
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                accept=".jpg,.jpeg,.png" required />
                            <p class="mt-1 text-xs text-gray-500">Photo evidence of the item(s) condition</p>
                            <x-input-error class="mt-2" :messages="$errors->get('proof_image')" />
                        </div>

                        <div>
                            <h3 class="mb-2 text-sm font-medium text-gray-700">Products to Return</h3>
                            <div class="overflow-y-auto border border-gray-200 rounded-md max-h-60">
                                @foreach ($order->orderDetails as $detail)
                                    <div class="flex items-center justify-between p-3 border-b border-gray-200">
                                        <div class="flex items-start">
                                            <input type="checkbox" name="products[{{ $detail->id }}][selected]"
                                                id="product-{{ $detail->id }}-{{ $order->id }}"
                                                class="w-4 h-4 mt-1 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                onchange="toggleQuantityInput(this, {{ $detail->id }}, {{ $order->id }})">
                                            <label for="product-{{ $detail->id }}-{{ $order->id }}"
                                                class="ml-3 text-sm text-gray-700">
                                                {{ $detail->product->product_name }} (Qty: {{ $detail->quantity }})
                                            </label>
                                        </div>
                                        <div class="w-20">
                                            <label for="quantity-{{ $detail->id }}-{{ $order->id }}"
                                                class="sr-only">Qty</label>
                                            <input id="quantity-{{ $detail->id }}-{{ $order->id }}"
                                                name="products[{{ $detail->id }}][quantity]" type="number"
                                                class="block w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                min="1" max="{{ $detail->quantity }}" value="1"
                                                disabled />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-x-6">
                            <button type="button" onclick="closeReturnRequestModal({{ $order->id }})"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Submit Return Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure SweetAlert is loaded
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert is not loaded. Please include SweetAlert in your project.');
                return;
            }

            // Select all return request forms
            const returnForms = document.querySelectorAll('form[action*="request-return"]');

            if (returnForms.length === 0) {
                console.warn('No return request forms found on the page.');
                return;
            }

            // Attach event listeners to each form
            returnForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission

                    // Check if at least one product is selected
                    const selectedProducts = form.querySelectorAll(
                        'input[name^="products"][name$="[selected]"]:checked');
                    if (selectedProducts.length === 0) {
                        Swal.fire({
                            title: 'No Products Selected',
                            text: 'Please select at least one product to return.',
                            icon: 'warning',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Confirm Return Request',
                        text: 'Are you sure you want to submit this return request?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, submit request',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show processing message
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Submitting your return request.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                    form.submit(); // Submit the form
                                }
                            });
                        }
                    });
                });
            });

            console.log(`Attached SweetAlert confirmation to ${returnForms.length} return request forms.`);
        });

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

        // Close modal when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeOrderModal();
            }
        });

        // Close modal with escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !document.getElementById('orderModal').classList.contains('hidden')) {
                closeOrderModal();
            }
        });


        function checkReturnEligibility(orderId, isWithinReturnPeriod, hasReturnRequest, formattedOrderId) {
            if (!isWithinReturnPeriod) {
                Swal.fire({
                    title: 'Cannot Request Return',
                    text: 'This order is more than 7 days old and is no longer eligible for return.',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            } else if (hasReturnRequest) {
                // Check if there's a pending or approved return request
                fetch(`/retailers/check-return-request-status/${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.can_request_again) {
                            // If previous return request was completed (approved/rejected), allow new request
                            openReturnRequestModal(orderId);
                        } else {
                            Swal.fire({
                                title: 'Return Already Requested',
                                text: 'You have already submitted a return request for this order.',
                                icon: 'info',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Could not verify return request status. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    });
            } else {
                // If eligible, open the modal
                openReturnRequestModal(orderId);
            }
        }

        function openReturnRequestModal(orderId) {
            const modalElement = document.getElementById(`request-return-${orderId}`);
            if (modalElement) {
                modalElement.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            } else {
                console.error(`Modal with ID request-return-${orderId} not found.`);
            }
        }

        function closeReturnRequestModal(orderId) {
            const modalElement = document.getElementById(`request-return-${orderId}`);
            if (modalElement) {
                modalElement.classList.add('hidden');
                document.body.style.overflow = ''; // Restore background scrolling
            }
        }

        // Toggle other reason textarea
        function toggleOtherReason(selectElement, orderId) {
            const otherReasonContainer = document.getElementById(`other_reason_container-${orderId}`);
            const otherReasonInput = document.getElementById(`other_reason-${orderId}`);

            if (selectElement.value === 'other') {
                otherReasonContainer.classList.remove('hidden');
                otherReasonInput.setAttribute('required', 'required');
            } else {
                otherReasonContainer.classList.add('hidden');
                otherReasonInput.removeAttribute('required');
            }
        }

        // Toggle quantity input
        function toggleQuantityInput(checkbox, detailId, orderId) {
            const quantityInput = document.getElementById(`quantity-${detailId}-${orderId}`);
            if (checkbox.checked) {
                quantityInput.disabled = false;
                quantityInput.setAttribute('required', 'required');
            } else {
                quantityInput.disabled = true;
                quantityInput.removeAttribute('required');
            }
        }
    </script>
</x-app-layout>
