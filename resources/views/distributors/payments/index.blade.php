<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Payment Management</h1>
            <a href="{{ route('distributors.payments.history') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                <i class="mr-1 fas fa-history"></i> Payment History
            </a>
        </div>

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
            <form id="batchActionForm" method="POST" action="{{ route('distributors.payments.batch-delete') }}">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-between mb-4">
                    <div>
                        @if(request('status') === 'paid')
                        <button type="button" id="selectAllBtn" class="px-3 py-1 mr-2 text-sm text-gray-600 bg-gray-100 rounded hover:bg-gray-200">
                            Select All
                        </button>
                        <button type="button" id="batchDeleteBtn" class="px-3 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600 disabled:opacity-50" disabled>
                            Delete Selected
                        </button>
                        @endif
                    </div>
                </div>
                
                <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                @if(request('status') === 'paid')
                                <th class="w-10 px-4 py-3 font-medium text-left text-gray-700">
                                    <input type="checkbox" id="selectAll" class="text-blue-600 border-gray-300 rounded">
                                </th>
                                @endif
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Order ID</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Retailer</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Amount</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Payment Status</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Delivery Status</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Paid At</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Note</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    @if(request('status') === 'paid')
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="selected_payments[]" value="{{ $payment->id }}" class="text-blue-600 border-gray-300 rounded payment-checkbox">
                                    </td>
                                    @endif
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
                                        @if ($payment->order->delivery)
                                            <span
                                                class="px-2 py-1 text-sm rounded-full 
                                                @if ($payment->order->delivery->status === 'delivered') bg-green-100 text-green-800
                                                @elseif ($payment->order->delivery->status === 'out_for_delivery') bg-blue-100 text-blue-800
                                                @elseif ($payment->order->delivery->status === 'in_transit') bg-indigo-100 text-indigo-800
                                                @elseif ($payment->order->delivery->status === 'failed') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucwords(str_replace('_', ' ', $payment->order->delivery->status)) }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-sm text-gray-800 bg-gray-100 rounded-full">
                                                Not Assigned
                                            </span>
                                        @endif
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
                                            <div class="flex space-x-1">
                                                @php
                                                    $canMarkAsPaid = $payment->order->delivery && $payment->order->delivery->status === 'delivered';
                                                @endphp
                                                <button type="button" 
                                                    onclick="openUpdateModal('{{ $payment->id }}', 'paid')"
                                                    class="px-3 py-1 text-xs text-white {{ $canMarkAsPaid ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 cursor-not-allowed' }}"
                                                    {{ $canMarkAsPaid ? '' : 'disabled' }}
                                                    title="{{ $canMarkAsPaid ? 'Mark as Paid' : 'Order must be delivered first' }}"
                                                    >
                                                    Mark as Paid
                                                </button>
                                                <button type="button" onclick="openUpdateModal('{{ $payment->id }}', 'failed')"
                                                    class="px-3 py-1 text-xs text-white bg-red-500 rounded hover:bg-red-600">
                                                    Mark as Failed
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @endif
    </div>

    <div id="updatePaymentModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                <h2 class="mb-4 text-lg font-semibold">Update Payment Status</h2>
                <form id="updatePaymentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="payment_status" id="payment_status_input">
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium">Payment Status</label>
                        <div class="px-3 py-2 text-sm bg-gray-100 rounded" id="selected_status_display"></div>
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
            // Add tooltips to disabled buttons
            document.addEventListener('DOMContentLoaded', function() {
                const disabledButtons = document.querySelectorAll('button[disabled]');
                disabledButtons.forEach(button => {
                    if (button.title) {
                        button.addEventListener('mouseover', function(e) {
                            const tooltip = document.createElement('div');
                            tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-800 rounded';
                            tooltip.style.top = (e.pageY - 30) + 'px';
                            tooltip.style.left = e.pageX + 'px';
                            tooltip.innerText = this.title;
                            tooltip.id = 'tooltip-' + Math.random().toString(36).substr(2, 9);
                            document.body.appendChild(tooltip);
                            
                            this.dataset.tooltipId = tooltip.id;
                        });
                        
                        button.addEventListener('mouseout', function() {
                            if (this.dataset.tooltipId) {
                                const tooltip = document.getElementById(this.dataset.tooltipId);
                                if (tooltip) tooltip.remove();
                                delete this.dataset.tooltipId;
                            }
                        });
                    }
                });
            });

            function openUpdateModal(paymentId, status) {
                const modal = document.getElementById('updatePaymentModal');
                const form = document.getElementById('updatePaymentForm');
                const statusInput = document.getElementById('payment_status_input');
                const statusDisplay = document.getElementById('selected_status_display');
                
                // Set the form action
                form.action = `/payments/${paymentId}/update-status`;
                
                // Set the status
                statusInput.value = status;
                statusDisplay.innerText = status === 'paid' ? 'Mark as Paid' : 'Mark as Failed';
                statusDisplay.className = "py-2 px-3 rounded text-sm " + 
                    (status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                
                // Show the modal
                modal.classList.remove('hidden');
            }

            document.getElementById('updatePaymentForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('_method', 'PUT');

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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: new URLSearchParams(formData)
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

            // Batch Delete Functionality - Only shown on paid payments page
            @if(request('status') === 'paid')
            const selectAll = document.getElementById('selectAll');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const batchDeleteBtn = document.getElementById('batchDeleteBtn');
            const checkboxes = document.querySelectorAll('.payment-checkbox');

            // Toggle select all
            selectAll?.addEventListener('change', function() {
                const isChecked = this.checked;
                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateDeleteButtonState();
            });

            // Select All button
            selectAllBtn?.addEventListener('click', function() {
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                selectAll.checked = !allChecked;
                updateDeleteButtonState();
            });

            // Update delete button state
            function updateDeleteButtonState() {
                const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                batchDeleteBtn.disabled = !anyChecked;
            }

            // Individual checkbox change
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateDeleteButtonState();
                    selectAll.checked = Array.from(checkboxes).every(cb => cb.checked);
                });
            });

            // Batch delete confirmation
            batchDeleteBtn?.addEventListener('click', function() {
                const selectedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete ${selectedCount} payment record(s). This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('batchActionForm').submit();
                    }
                });
            });
            @endif
        </script>
    @endpush
</x-distributor-layout>