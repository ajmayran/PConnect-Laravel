<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <h1 class="mb-4 text-2xl font-bold">Payment Management</h1>

        <!-- Payment Status Tabs -->
        <div class="flex mb-4 border-b">
            <a href="?status=all"
                class="px-4 py-2 -mb-px font-semibold 
                    @if (!request('status') || request('status') === 'all') text-green-500 border-green-500 
                    @else text-gray-600 border-transparent @endif 
                    border-b-2">
                All
            </a>
            <a href="?status=unpaid"
                class="px-4 py-2 -mb-px font-semibold 
                    @if (request('status') === 'unpaid') text-green-500 border-green-500 
                    @else text-gray-600 border-transparent @endif 
                    border-b-2">
                Unpaid
            </a>
            <a href="?status=paid"
                class="px-4 py-2 -mb-px font-semibold 
                    @if (request('status') === 'paid') text-green-500 border-green-500 
                    @else text-gray-600 border-transparent @endif 
                    border-b-2">
                Paid
            </a>
            <a href="?status=failed"
                class="px-4 py-2 -mb-px font-semibold 
                    @if (request('status') === 'failed') text-green-500 border-green-500 
                    @else text-gray-600 border-transparent @endif 
                    border-b-2">
                Failed
            </a>
        </div>

        @if ($payments->isEmpty())
            <div class="p-8 text-center bg-white rounded-lg shadow-sm">
                <p class="text-gray-600 sm:text-lg">No payments found.</p>
            </div>
        @else
            <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Order ID</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Retailer</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Amount</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Status</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Paid At</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Note</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $payment->order->formatted_order_id }}</td>
                                <td class="px-4 py-3">{{ $payment->order->user->first_name }}
                                    {{ $payment->order->user->last_name }}</td>
                                <td class="px-4 py-3">
                                    ₱{{ number_format($payment->order->orderDetails->sum('subtotal'), 2) }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 text-sm rounded-full 
                                        @if ($payment->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif ($payment->payment_status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($payment->paid_at)
                                        {{ \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $payment->payment_note ?? 'No note' }}</td>
                                <td class="px-4 py-3">
                                    @if ($payment->payment_status === 'unpaid')
                                        <button onclick="openUpdateModal('{{ $payment->id }}')"
                                            class="px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                            Update Status
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div id="updatePaymentModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                <h2 class="mb-4 text-lg font-semibold">Update Payment Status</h2>
                <form id="updatePaymentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium">Payment Status</label>
                        <select name="payment_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded">
                            <option value="paid">Mark as Paid</option>
                            <option value="failed">Mark as Failed</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium">Payment Note</label>
                        <textarea name="payment_note" class="w-full px-3 py-2 text-sm border border-gray-300 rounded"
                            placeholder="Add payment note"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeUpdateModal()"
                            class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                            Update Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            function openUpdateModal(paymentId) {
                const modal = document.getElementById('updatePaymentModal');
                const form = document.getElementById('updatePaymentForm');
                // Fix the route generation
                form.action = `/payments/${paymentId}/update-status`;
                modal.classList.remove('hidden');
            }

            document.getElementById('updatePaymentForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = {
                    payment_status: this.payment_status.value,
                    payment_note: this.payment_note.value,
                    _method: 'PUT'
                };

                Swal.fire({
                    title: 'Updating payment status...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to update payment status. Please try again.',
                        });
                    });
            });

            function closeUpdateModal() {
                const modal = document.getElementById('updatePaymentModal');
                modal.classList.add('hidden');
            }

            // Close modal when clicking outside
            document.getElementById('updatePaymentModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeUpdateModal();
                }
            });
        </script>
    @endpush
</x-distributor-layout>
