<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Order History</h1>

        <!-- Search Bar -->
        <div class="mb-4">
            <form action="{{ route('distributors.orders.history') }}" method="GET">
                <div class="relative flex">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search orders..."
                        class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <button type="submit"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        @if ($orders->isEmpty())
            <div class="p-8 text-center bg-white rounded-lg shadow-sm">
                <p class="text-gray-600 sm:text-lg">No orders found.</p>
            </div>
        @else
            <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Order ID</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Retailer Name</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Total Amount</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Order Date</th>
                            <th class="px-4 py-3 font-medium text-left text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $order->formatted_order_id }}</td>
                                <td class="px-4 py-3">
                                    {{ $order->user->first_name }} {{ $order->user->last_name }}
                                </td>
                                <td class="px-4 py-3 font-medium text-blue-600">
                                    ₱{{ number_format(optional($order->orderDetails)->sum('subtotal') ?: 0, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $order->created_at->format('F d, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-distributor-layout>