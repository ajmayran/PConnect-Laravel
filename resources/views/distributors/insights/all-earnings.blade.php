<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <h1 class="mb-4 text-2xl font-bold text-gray-800">All Earnings</h1>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-medium text-left text-gray-500">Order ID</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-500">Retailer</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-500">Amount</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($earnings as $earning)
                        <tr>
                            <td class="px-6 py-4 text-blue-600">{{ $earning->payment->order->formatted_order_id }}</td>
                            <td class="px-6 py-4">{{ $earning->payment->order->user->first_name }} {{ $earning->payment->order->user->last_name }}</td>
                            <td class="px-6 py-4 text-green-600">₱{{ number_format($earning->amount, 2) }}</td>
                            <td class="px-6 py-4">{{ $earning->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No earnings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $earnings->links() }}
        </div>
    </div>
</x-distributor-layout>