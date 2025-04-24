<x-app-layout>
    <x-dashboard-nav />
    <div class="py-8 bg-gray-50">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white rounded-lg shadow-md sm:p-8"> {{-- Added more padding and subtle shadow --}}
                <div class="pb-4 mb-6 border-b border-gray-200"> {{-- Header section with bottom border --}}
                    <h1 class="text-2xl font-semibold text-gray-800">Purchase History</h1>
                    <p class="mt-1 text-sm text-gray-600">View all your past orders, including their status and distributor details.</p>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('retailers.orders.purchase-history') }}" class="flex items-center gap-3">
                        <div class="relative flex-grow">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Search by Order ID or Distributor Name..." 
                                class="w-full px-4 py-2 text-sm border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500" {{-- Adjusted input style --}}
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"> {{-- Adjusted button style --}}
                            Search
                        </button>
                    </form>
                </div>

                <!-- Orders Table -->
                <div class="overflow-hidden border border-gray-200 rounded-lg"> {{-- Added border and rounded corners to table container --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200"> {{-- Use min-w-full for responsiveness --}}
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Order ID</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Distributor</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Amount</th> {{-- Align amount to right --}}
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Status</th> {{-- Center status --}}
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                    <tr class="transition-colors duration-150 hover:bg-gray-50"> {{-- Added hover effect --}}
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $order->formatted_order_id }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $order->distributor->company_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-800 whitespace-nowrap">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            <span @class([
                                                'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                'bg-green-100 text-green-800' => $order->status === 'completed',
                                                'bg-red-100 text-red-800' => $order->status === 'cancelled' || $order->status === 'rejected',
                                                'bg-yellow-100 text-yellow-800' => $order->status === 'processing',
                                                'bg-blue-100 text-blue-800' => $order->status === 'pending' || $order->status === 'out_for_delivery' || $order->status === 'in_transit',
                                                'bg-purple-100 text-purple-800' => $order->status === 'delivered',
                                                'bg-gray-100 text-gray-800' => !in_array($order->status, ['completed', 'cancelled', 'rejected', 'processing', 'pending', 'out_for_delivery', 'in_transit', 'delivered']),
                                            ])>
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }} 
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-sm text-center text-gray-500"> {{-- Increased padding for empty state --}}
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <p class="mt-3">No purchase history found.</p>
                                                @if(request('search'))
                                                    <p class="mt-1 text-xs">Try adjusting your search query.</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->links() }} 
            </div>
        </div>
    </div>
</x-app-layout>