<x-distributor2nd-layout>
    <div class="container p-4 mx-auto">
        <!-- Header with back button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('distributors.dashboard') }}"
                    class="p-2 text-gray-600 transition-colors rounded-full hover:bg-gray-100 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800 sm:text-3xl">Refund Management</h1>
            </div>
        </div>

        <!-- Tabs and Search Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
            <!-- Tabs -->
            <div class="flex justify-between mb-4 border-b">
                <div class="flex space-x-4">
                    <a href="{{ route('distributors.refunds.index', ['status' => 'all']) }}"
                        class="px-4 py-2 {{ $status === 'all' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        All Refunds
                    </a>
                    <a href="{{ route('distributors.refunds.index', ['status' => 'processing']) }}"
                        class="px-4 py-2 {{ $status === 'processing' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        Pending
                    </a>
                    <a href="{{ route('distributors.refunds.index', ['status' => 'pending_delivery']) }}"
                        class="px-4 py-2 {{ $status === 'pending_delivery' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        Scheduled
                    </a>
                    <a href="{{ route('distributors.refunds.index', ['status' => 'completed']) }}"
                        class="px-4 py-2 {{ $status === 'completed' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-600' }}">
                        Completed
                    </a>
                </div>
            </div>

            <!-- Search and Export -->
            <div class="flex items-center justify-start">
                <div class="relative">
                    <form action="{{ route('distributors.refunds.index') }}" method="GET">
                        <input type="hidden" name="status" value="{{ $status }}">
                        <input type="search" name="search" placeholder="Search by order ID or retailer name..."
                            value="{{ request('search') }}"
                            class="px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Refunds Table -->
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
                            Retailer
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Amount
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Schedule
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($refunds as $refund)
                        @php
                            $statusClass = match ($refund->status) {
                                'processing' => 'text-yellow-800 bg-yellow-100',
                                'pending_delivery' => 'text-blue-800 bg-blue-100',
                                'completed' => 'text-green-800 bg-green-100',
                                'failed' => 'text-red-800 bg-red-100',
                                default => 'text-gray-800 bg-gray-100',
                            };

                            $statusText = match ($refund->status) {
                                'processing' => 'Pending',
                                'pending_delivery' => 'Scheduled',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                default => 'Unknown',
                            };
                        @endphp

                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $refund->order->formatted_order_id }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $refund->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $refund->order->user->first_name }} {{ $refund->order->user->last_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    ₱{{ number_format($refund->amount, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if ($refund->scheduled_date)
                                        {{ $refund->scheduled_date->format('M d, Y') }}
                                    @elseif($refund->status === 'processing')
                                        Not yet scheduled
                                    @elseif($refund->status === 'completed')
                                        Delivered on {{ $refund->completed_at->format('M d, Y') }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($refund->status === 'processing')
                                    <button type="button" onclick="openScheduleModal({{ $refund->id }})"
                                        class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                        Schedule Refund
                                    </button>
                                @elseif($refund->status === 'pending_delivery')
                                    <form action="{{ route('distributors.refunds.complete', $refund->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                            Mark as Delivered
                                        </button>
                                    </form>
                                @elseif($refund->status === 'completed')
                                    <span class="text-sm text-gray-500">Refund completed</span>
                                @else
                                    <span class="text-sm text-red-500">Failed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500">
                                No refunds found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $refunds->appends(request()->query())->links() }}
        </div>
    </div>
    </div>

    <!-- Schedule Refund Modal -->
    <div id="scheduleModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50">
        <div class="w-full max-w-md p-6 mx-auto mt-20 bg-white rounded-lg shadow-lg">
            <h2 class="mb-4 text-lg font-bold text-gray-800">Schedule Refund</h2>
            <form id="scheduleForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="scheduled_date" class="block mb-2 text-sm font-medium text-gray-700">Select Date</label>
                    <input type="date" id="scheduled_date" name="scheduled_date" min="{{ now()->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeScheduleModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                        Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openScheduleModal(refundId) {
            const modal = document.getElementById('scheduleModal');
            const form = document.getElementById('scheduleForm');
            form.action = `{{ route('distributors.refunds.process', ':id') }}`.replace(':id', refundId);
            modal.classList.remove('hidden');
        }

        function closeScheduleModal() {
            const modal = document.getElementById('scheduleModal');
            modal.classList.add('hidden');
        }
    </script>

</x-distributor2nd-layout>
