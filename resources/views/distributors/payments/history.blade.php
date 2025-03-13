<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Payment History</h1>
            <a href="{{ route('distributors.payments.index') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600">
                <i class="mr-1 fas fa-arrow-left"></i> Back to Payments
            </a>
        </div>

        <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
            <div class="flex flex-wrap gap-4 mb-6">
                <!-- Date Filter -->
                <div class="flex-1 min-w-[250px]">
                    <h3 class="mb-2 text-sm font-medium text-gray-700">Filter by Date</h3>
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">From</label>
                            <input type="date" id="date_from" class="w-full px-3 py-2 text-sm border border-gray-300 rounded" value="{{ request('date_from', now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">To</label>
                            <input type="date" id="date_to" class="w-full px-3 py-2 text-sm border border-gray-300 rounded" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                        </div>
                        <button id="applyDateFilter" class="px-3 py-2 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                            Apply
                        </button>
                    </div>
                </div>
                
                <!-- Payment Summary -->
                <div class="flex-1 min-w-[250px]">
                    <h3 class="mb-2 text-sm font-medium text-gray-700">Payment Summary</h3>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        <div class="p-3 border border-green-100 rounded bg-green-50">
                            <p class="text-xs text-green-600">Total Paid</p>
                            <p class="text-lg font-semibold text-green-700">₱{{ number_format($totalPaid, 2) }}</p>
                        </div>
                        <div class="p-3 border border-yellow-100 rounded bg-yellow-50">
                            <p class="text-xs text-yellow-600">Total Pending</p>
                            <p class="text-lg font-semibold text-yellow-700">₱{{ number_format($totalPending, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Statistics -->
            <div class="p-4 mb-6 rounded-lg bg-gray-50">
                <h3 class="mb-4 text-sm font-medium text-gray-700">Payment Statistics</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4">
                    <div class="p-4 text-center bg-white border rounded-lg">
                        <p class="text-xs text-gray-500">Total Payments</p>
                        <p class="text-xl font-bold text-gray-700">{{ $stats['total'] }}</p>
                    </div>
                    <div class="p-4 text-center bg-white border rounded-lg">
                        <p class="text-xs text-gray-500">Paid</p>
                        <p class="text-xl font-bold text-green-600">{{ $stats['paid'] }}</p>
                    </div>
                    <div class="p-4 text-center bg-white border rounded-lg">
                        <p class="text-xs text-gray-500">Unpaid</p>
                        <p class="text-xl font-bold text-yellow-600">{{ $stats['unpaid'] }}</p>
                    </div>
                    <div class="p-4 text-center bg-white border rounded-lg">
                        <p class="text-xs text-gray-500">Failed</p>
                        <p class="text-xl font-bold text-red-600">{{ $stats['failed'] }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Payment History Table -->
            <h3 class="mb-2 text-sm font-medium text-gray-700">Recent Payment History</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Order ID</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Retailer</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Amount</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Status</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Date</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $payment->order->formatted_order_id }}</td>
                                <td class="px-4 py-3">{{ $payment->order->user->first_name }}
                                    {{ $payment->order->user->last_name }}</td>
                                <td class="px-4 py-3">
                                    ₱{{ number_format($payment->order->orderDetails->sum('subtotal'), 2) }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full 
                                        @if ($payment->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif ($payment->payment_status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($payment->paid_at)
                                        {{ \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') }}
                                    @elseif ($payment->updated_at)
                                        {{ $payment->updated_at->format('Y-m-d H:i') }}
                                    @else
                                        {{ $payment->created_at->format('Y-m-d H:i') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $payment->payment_note ?? 'No note' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
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
            document.getElementById('applyDateFilter').addEventListener('click', function() {
                const dateFrom = document.getElementById('date_from').value;
                const dateTo = document.getElementById('date_to').value;
                
                window.location.href = '{{ route("distributors.payments.history") }}' + 
                    `?date_from=${dateFrom}&date_to=${dateTo}`;
            });
        </script>
    @endpush
</x-distributor-layout> 