<x-distributor-layout>
    <div class="container p-3 sm:p-4 mx-auto">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-2 sm:gap-0">
            <div class="flex items-center justify-between">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold">Payment History</h1>
                <a href="{{ route('distributors.payments.index') }}" 
                   class="ml-2 sm:hidden inline-flex items-center justify-center px-2 py-1 text-xs font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>
            <a href="{{ route('distributors.payments.index') }}" 
               class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Payments
            </a>
        </div>

        <div class="p-3 sm:p-6 mb-4 sm:mb-6 bg-white rounded-lg shadow-sm">
            <!-- Payment Summary -->
            <div class="mb-4 sm:mb-6">
                <h3 class="mb-2 text-xs sm:text-sm font-medium text-gray-700">Payment Summary</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="p-2 sm:p-3 border border-green-100 rounded bg-green-50">
                        <p class="text-xs text-green-600">Total Paid</p>
                        <p class="text-sm sm:text-lg font-semibold text-green-700">₱{{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <div class="p-2 sm:p-3 border border-yellow-100 rounded bg-yellow-50">
                        <p class="text-xs text-yellow-600">Total Pending</p>
                        <p class="text-sm sm:text-lg font-semibold text-yellow-700">₱{{ number_format($totalPending, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Statistics -->
            <div class="p-3 sm:p-4 mb-4 sm:mb-6 rounded-lg bg-gray-50">
                <h3 class="mb-3 sm:mb-4 text-xs sm:text-sm font-medium text-gray-700">Payment Statistics</h3>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-2 sm:gap-4">
                    <div class="p-2 sm:p-4 text-center bg-white border rounded-lg">
                        <p class="text-xs text-gray-500">Total Payments</p>
                        <p class="text-base sm:text-xl font-bold text-gray-700">{{ $stats['total'] }}</p>
                    </div>
                    <div class="p-2 sm:p-4 text-center bg-white border rounded-lg">
                        <p class="text-xs text-gray-500">Paid</p>
                        <p class="text-base sm:text-xl font-bold text-green-600">{{ $stats['paid'] }}</p>
                    </div>
                    <div class="p-2 sm:p-4 text-center bg-white border rounded-lg">
                        <p class="text-xs text-gray-500">Unpaid</p>
                        <p class="text-base sm:text-xl font-bold text-yellow-600">{{ $stats['unpaid'] }}</p>
                    </div>
                    <div class="p-2 sm:p-4 text-center bg-white border rounded-lg">
                        <p class="text-xs text-gray-500">Failed</p>
                        <p class="text-base sm:text-xl font-bold text-red-600">{{ $stats['failed'] }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Date Filter - Moved below statistics -->
            <div class="p-3 sm:p-4 mb-4 sm:mb-6 rounded-lg border border-gray-100 bg-white">
                <h3 class="mb-2 text-xs sm:text-sm font-medium text-gray-700">Filter by Date</h3>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                    <div class="w-full sm:flex-1">
                        <label class="text-xs text-gray-500">From</label>
                        <input type="date" id="date_from" 
                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded" 
                            value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
                    </div>
                    <div class="w-full sm:flex-1">
                        <label class="text-xs text-gray-500">To</label>
                        <input type="date" id="date_to" 
                            class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm border border-gray-300 rounded" 
                            value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                    <button id="applyDateFilter" 
                        class="mt-2 sm:mt-auto px-4 py-1.5 sm:py-2 text-xs sm:text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                        Apply
                    </button>
                </div>
            </div>
            
            <!-- Payment History Table -->
            <h3 class="mb-2 text-xs sm:text-sm font-medium text-gray-700">Recent Payment History</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs sm:text-sm divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700">Order ID</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700 hidden sm:table-cell">Retailer</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700">Amount</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700">Status</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700">Date</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 font-medium text-left text-gray-700 hidden sm:table-cell">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50" onclick="toggleDetails(this, {{ $payment->id }})">
                                <td class="px-2 sm:px-4 py-2 sm:py-3">{{ $payment->order->formatted_order_id }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 hidden sm:table-cell">
                                    {{ $payment->order->user->first_name }} {{ $payment->order->user->last_name }}
                                </td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3">
                                    ₱{{ number_format($payment->order->orderDetails->sum('subtotal'), 2) }}
                                </td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3">
                                    <span class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs rounded-full 
                                        @if ($payment->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif ($payment->payment_status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm whitespace-nowrap">
                                    @if ($payment->paid_at)
                                        {{ \Carbon\Carbon::parse($payment->paid_at)->format('m/d/Y') }}
                                    @elseif ($payment->updated_at)
                                        {{ $payment->updated_at->format('m/d/Y') }}
                                    @else
                                        {{ $payment->created_at->format('m/d/Y') }}
                                    @endif
                                </td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 hidden sm:table-cell">
                                    {{ Str::limit($payment->payment_note ?? 'No note', 20) }}
                                </td>
                            </tr>
                            <!-- Expandable mobile row -->
                            <tr class="mobile-payment-details hidden bg-gray-50" id="details-{{ $payment->id }}">
                                <td colspan="6" class="px-2 sm:px-4 py-2 sm:py-3">
                                    <div class="grid grid-cols-1 gap-2 text-xs">
                                        <div>
                                            <span class="font-semibold">Retailer:</span> 
                                            {{ $payment->order->user->first_name }} {{ $payment->order->user->last_name }}
                                        </div>
                                        @if($payment->paid_at)
                                        <div>
                                            <span class="font-semibold">Paid At:</span> 
                                            {{ \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') }}
                                        </div>
                                        @endif
                                        <div>
                                            <span class="font-semibold">Note:</span> 
                                            {{ $payment->payment_note ?? 'No note' }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-2 sm:px-4 py-4 sm:py-6 text-center text-gray-500">
                                    No payment history found for the selected date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Apply date filter
            document.getElementById('applyDateFilter').addEventListener('click', function() {
                const dateFrom = document.getElementById('date_from').value;
                const dateTo = document.getElementById('date_to').value;
                
                window.location.href = '{{ route("distributors.payments.history") }}' + 
                    `?date_from=${dateFrom}&date_to=${dateTo}`;
            });

            // Add enter key support for date inputs
            document.getElementById('date_from').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('applyDateFilter').click();
                }
            });

            document.getElementById('date_to').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('applyDateFilter').click();
                }
            });

            // Toggle mobile row details visibility
            function toggleDetails(row, paymentId) {
                // Only in mobile view
                if (window.innerWidth < 640) {
                    const detailsRow = document.getElementById('details-' + paymentId);
                    if (detailsRow) {
                        if (detailsRow.classList.contains('hidden')) {
                            // Hide all other details first
                            const allDetails = document.querySelectorAll('.mobile-payment-details');
                            allDetails.forEach(detail => detail.classList.add('hidden'));
                            
                            // Show this one
                            detailsRow.classList.remove('hidden');
                            row.classList.add('bg-gray-100');
                        } else {
                            detailsRow.classList.add('hidden');
                            row.classList.remove('bg-gray-100');
                        }
                    }
                }
            }

            // Improve touch responsiveness for mobile
            document.addEventListener('DOMContentLoaded', function() {
                const rows = document.querySelectorAll('tbody tr:not(.mobile-payment-details)');
                
                rows.forEach(row => {
                    row.addEventListener('touchstart', function() {
                        this.classList.add('bg-gray-100');
                    });
                    
                    row.addEventListener('touchend', function() {
                        setTimeout(() => {
                            // Only remove if not showing details
                            const paymentId = this.nextElementSibling?.id?.split('-')[1];
                            if (!paymentId || document.getElementById('details-' + paymentId).classList.contains('hidden')) {
                                this.classList.remove('bg-gray-100');
                            }
                        }, 100);
                    });
                });

                // Add responsive date picker on mobile
                const dateInputs = document.querySelectorAll('input[type="date"]');
                dateInputs.forEach(input => {
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        input.addEventListener('touchstart', function(e) {
                            e.preventDefault();
                            this.click();
                        });
                    }
                });
            });
        </script>
    @endpush
</x-distributor-layout>