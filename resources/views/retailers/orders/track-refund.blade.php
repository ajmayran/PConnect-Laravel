<x-app-layout>
    <x-dashboard-nav />
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-4 bg-white shadow sm:rounded-lg">
                <h1 class="text-2xl font-semibold text-gray-800">Track Refund</h1>
                <div class="mt-4">
                    @if($refunds->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Order ID</th>
                                        <th scope="col" class="px-6 py-3">Refund Amount</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Scheduled Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($refunds as $refund)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4">{{ $refund->order->formatted_order_id }}</td>
                                            <td class="px-6 py-4">₱{{ number_format($refund->amount, 2) }}</td>
                                            <td class="px-6 py-4">{{ ucfirst($refund->status) }}</td>
                                            <td class="px-6 py-4">{{ $refund->scheduled_date ? $refund->scheduled_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No refunds found.</p>
                    @endif
                </div>
                <div class="mt-6">
                    {{ $refunds->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>