<x-app-layout>
    <x-dashboard-nav />
    <div class="container px-4 py-8 mx-auto max-w-7xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
        </div>

        <x-retailer-orderstatus-tabs />

        @if ($orders->isEmpty())
            <div class="flex items-center justify-center p-8 mt-4 bg-white rounded-lg">
                <p class="text-lg text-gray-500">No orders found</p>
            </div>
        @else
            <div class="mt-6 space-y-6">
                @foreach ($orders as $order)
                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="space-y-1">
                                    <h2 class="text-xl font-bold text-gray-900">
                                        {{ $order->formatted_order_id }}
                                    </h2>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Distributor:</span>
                                        {{ $order->distributor->company_name }}
                                    </p>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Status:</span>
                                        <span
                                            class="px-3 py-1 text-sm font-medium rounded-full 
                                            @if ($order->status === 'completed') bg-green-100 text-green-800
                                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-500">Order Date</p>
                                    <p class="text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <div class="mt-6 -mx-6">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-sm font-medium tracking-wider text-left text-gray-500">
                                                Product</th>
                                            <th
                                                class="px-6 py-3 text-sm font-medium tracking-wider text-center text-gray-500">
                                                Quantity</th>
                                            <th
                                                class="px-6 py-3 text-sm font-medium tracking-wider text-right text-gray-500">
                                                Price</th>
                                            <th
                                                class="px-6 py-3 text-sm font-medium tracking-wider text-right text-gray-500">
                                                Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($order->orderDetails as $detail)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $detail->product->product_name }}
                                                    @if ($detail->applied_discount)
                                                        <p class="text-xs text-green-600">
                                                            {{ $detail->applied_discount }}</p>
                                                    @endif
                                                    @if ($detail->free_items > 0)
                                                        <p class="text-xs text-green-600">+{{ $detail->free_items }}
                                                            free item(s)</p>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                    {{ $detail->quantity }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-right text-gray-900">
                                                    @if ($detail->discount_amount > 0)
                                                        <span
                                                            class="text-xs text-gray-500 line-through">₱{{ number_format($detail->price, 2) }}</span>
                                                        <br>
                                                        <span
                                                            class="text-green-600">₱{{ number_format($detail->price - $detail->discount_amount / $detail->quantity, 2) }}</span>
                                                    @else
                                                        ₱{{ number_format($detail->price, 2) }}
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">
                                                    @if ($detail->discount_amount > 0)
                                                        <span
                                                            class="text-xs text-gray-500 line-through">₱{{ number_format($detail->price * $detail->quantity, 2) }}</span>
                                                        <br>
                                                    @endif
                                                    ₱{{ number_format($detail->subtotal, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        @php
                                            $totalOriginal = 0;
                                            $totalDiscount = 0;
                                            $hasFreeItems = false;

                                            foreach ($order->orderDetails as $detail) {
                                                // Calculate original price (before discounts)
                                                if ($detail->discount_amount > 0) {
                                                    $totalOriginal += $detail->price * $detail->quantity;
                                                    $totalDiscount += $detail->discount_amount;
                                                } else {
                                                    $totalOriginal += $detail->subtotal;
                                                }

                                                // Check for free items
                                                if ($detail->free_items > 0) {
                                                    $hasFreeItems = true;
                                                    // Optionally, calculate the value of free items
                                                    $freeItemsValue = $detail->price * $detail->free_items;
                                                    $totalDiscount += $freeItemsValue;
                                                }
                                            }

                                            $finalTotal = $order->orderDetails->sum('subtotal');

                                            // Check for either monetary discounts OR free items
                                            $hasDiscounts = $totalDiscount > 0 || $hasFreeItems;
                                        @endphp


                                        <tr>
                                            <td colspan="3"
                                                class="px-6 py-4 text-sm font-bold text-right text-gray-900">
                                                @if ($hasDiscounts)
                                                    Original Total:
                                                @else
                                                    Total Amount:
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm font-bold text-right text-gray-900">
                                                @if ($hasDiscounts)
                                                    <span>₱{{ number_format($totalOriginal, 2) }}</span>
                                                @else
                                                    <span>₱{{ number_format($finalTotal, 2) }}</span>
                                                @endif
                                            </td>
                                        </tr>

                                        @if ($hasDiscounts)
                                            <tr>
                                                <td colspan="3"
                                                    class="px-6 py-4 text-sm font-bold text-right text-green-600">
                                                    Total Discount:
                                                </td>
                                                <td class="px-6 py-4 text-sm font-bold text-right text-green-600">
                                                    -₱{{ number_format($totalDiscount, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"
                                                    class="px-6 py-4 text-sm font-bold text-right text-gray-900 bg-gray-100">
                                                    Final Total:
                                                </td>
                                                <td
                                                    class="px-6 py-4 text-sm font-bold text-right text-gray-900 bg-gray-100">
                                                    ₱{{ number_format($finalTotal, 2) }}
                                                </td>
                                            </tr>
                                        @endif
                                    </tfoot>
                                </table>
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-200">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Delivery Address: {{ optional($order->orderDetails->first())->delivery_address }}
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end p-4 bg-gray-50">
                            @if ($order->status === 'pending')
                                <!-- Cancel Button -->
                                <button
                                    onclick="document.getElementById('cancelModal{{ $order->id }}').style.display='block'"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                    Cancel Order
                                </button>
                            @endif

                            @if ($order->status === 'completed')
                                <form action="{{ route('retailers.orders.return', $order) }}" method="POST"
                                    class="inline ml-2">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700">
                                        Return Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

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
                                        <input type="radio" name="cancel_reason" value="Ordered by mistake"
                                            class="mr-2">
                                        Ordered by mistake
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="cancel_reason" value="Found better price"
                                            class="mr-2">
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
                @endforeach
            </div>
            <!-- Pagination -->
            <div class="container flex justify-end px-2 pb-8 mx-auto sm:px-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
    <script>
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
                            console.log('Cancel button clicked for order {{ $order->id }}');

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
