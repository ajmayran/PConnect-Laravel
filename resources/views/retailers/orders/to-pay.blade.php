<x-app-layout>
    <x-dashboard-nav />
    <div class="container px-4 py-8 mx-auto max-w-7xl">
        <div class="flex flex-col items-start justify-between mb-8 space-y-4 sm:flex-row sm:items-center sm:space-y-0">
            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Orders To Pay</h1>

            <!-- Add Track Order Form -->
            <div class="relative w-full sm:w-auto">
                <form action="{{ route('retailers.orders.track') }}" method="GET" class="flex flex-col sm:flex-row">
                    <input type="text" name="tracking_number" placeholder="Enter tracking number"
                        class="px-4 py-2 mb-2 border border-gray-300 rounded-md sm:rounded-l-md sm:mb-0 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <button type="submit"
                        class="px-4 py-2 text-white bg-green-600 rounded-md sm:rounded-l-none sm:rounded-r-md hover:bg-green-700">
                        Track Order
                    </button>
                </form>
            </div>
        </div>

        <x-retailer-orderstatus-tabs />

        @if ($orders->isEmpty())
            <div class="flex items-center justify-center p-8 mt-4 bg-white rounded-lg">
                <p class="text-lg text-gray-500">No orders to pay</p>
            </div>
        @else
            <div class="mt-6 space-y-6">
                @foreach ($orders as $order)
                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="p-6">
                            <div
                                class="flex flex-col items-start justify-between mb-6 space-y-4 sm:flex-row sm:items-center sm:space-y-0">
                                <div class="space-y-1">
                                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">
                                        {{ $order->formatted_order_id }}
                                    </h2>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Distributor:</span>
                                        {{ $order->distributor->company_name }}
                                    </p>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Status:</span>
                                        <span
                                            class="px-3 py-1 text-sm font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                            Processing
                                        </span>
                                    </p>
                                    @if ($order->delivery && $order->delivery->tracking_number)
                                        <p class="text-gray-600">
                                            <span class="font-medium">Tracking Number:</span>
                                            <span class="font-mono">{{ $order->delivery->tracking_number }}</span>
                                        </p>
                                    @endif
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="text-sm font-medium text-gray-500">Order Date</p>
                                    <p class="text-gray-900">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <div class="mt-6 -mx-6 overflow-x-auto">
                                <table class="w-full min-w-max">
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
                                                Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($order->orderDetails as $detail)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900">
                                                    {{ $detail->product->product_name }}</td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                    {{ $detail->quantity }}</td>
                                                <td class="px-6 py-4 text-sm font-medium text-right text-gray-900">
                                                    ₱{{ number_format($detail->subtotal, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <td colspan="2"
                                                class="px-6 py-4 text-sm font-bold text-right text-gray-900">
                                                Total Amount:</td>
                                            <td class="px-6 py-4 text-sm font-bold text-right text-gray-900">
                                                ₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-200">
                                <div class="flex items-start text-sm text-gray-600 sm:items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Delivery Address: {{ optional($order->orderDetails->first())->delivery_address }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end p-4 bg-gray-50">
                            <button
                                onclick="document.getElementById('cancelModal{{ $order->id }}').style.display='block'"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                Cancel Order
                            </button>
                        </div>
                    </div>

                    <!-- Cancel Modal -->
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
            <div class="container flex justify-center px-2 pb-8 mx-auto sm:justify-end sm:px-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const textarea = this.closest('form').querySelector(
                        'textarea[name="custom_reason"]');
                    textarea.style.display = this.value === 'other' ? 'block' : 'none';
                });
            });

            @foreach ($orders as $order)
                const confirmBtn{{ $order->id }} = document.getElementById(
                    'confirmCancelBtn{{ $order->id }}');
                const modal{{ $order->id }} = document.getElementById('cancelModal{{ $order->id }}');
                const form{{ $order->id }} = modal{{ $order->id }}.querySelector('form');

                if (confirmBtn{{ $order->id }} && form{{ $order->id }}) {
                    confirmBtn{{ $order->id }}.addEventListener('click', function() {
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
                                Swal.fire({
                                    title: 'Processing...',
                                    text: 'Cancelling your order',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                        form{{ $order->id }}.submit();
                                    }
                                });
                            }
                        });
                    });
                }

                modal{{ $order->id }}.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            @endforeach

            @if (session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

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
