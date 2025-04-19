<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Product History</h1>
            <a href="{{ route('distributors.products.index') }}" 
               class="flex items-center gap-2 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Products
            </a>
        </div>

        <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
            <form action="{{ route('distributors.products.history') }}" method="GET">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label for="product" class="block mb-2 text-sm font-medium text-gray-700">Product</label>
                        <select name="product" id="product"
                            class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-200">
                            <option value="">All Products</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ request('product') == $product->id ? 'selected' : '' }}>
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="action" class="block mb-2 text-sm font-medium text-gray-700">Action Type</label>
                        <select name="action" id="action"
                            class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-200">
                            <option value="">All Actions</option>
                            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                    <div>
                        <label for="date" class="block mb-2 text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="w-full px-3 py-2 border rounded-md focus:ring focus:ring-blue-200">
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('distributors.products.history') }}"
                        class="px-4 py-2 mr-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Clear Filters
                    </a>
                    <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Product</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Action</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            User</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Changes</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($productHistories as $history)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10">
                                        @if ($history->product && $history->product->image)
                                            <img src="{{ asset('storage/' . $history->product->image) }}"
                                                alt="{{ $history->product->product_name }}"
                                                class="object-cover w-10 h-10 rounded-full">
                                        @else
                                            <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $history->product ? $history->product->product_name : 'Product Deleted' }}
                                            @if ($history->product && $history->product->deleted_at)
                                                <span class="text-xs text-red-600">(deleted)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 {{ 
                                    $history->action_type === 'created' ? 'text-green-800 bg-green-100' : 
                                    ($history->action_type === 'updated' ? 'text-blue-800 bg-blue-100' : 
                                    'text-red-800 bg-red-100') }} rounded-full">
                                    {{ ucfirst($history->action_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $history->user->first_name ?? 'Unknown User' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($history->action_type === 'updated')
                                    @foreach($history->new_values as $field => $value)
                                        <div class="text-xs text-gray-900">
                                            <span class="font-medium">{{ ucfirst($field) }}</span>: 
                                            <span class="text-gray-600">
                                                {{ is_array($value) ? json_encode($value) : $value }}
                                            </span>
                                        </div>
                                    @endforeach
                                @elseif($history->action_type === 'created')
                                    <div class="text-xs text-gray-600">New product created</div>
                                @else
                                    <div class="text-xs text-gray-600">Product deleted</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $history->created_at->format('M d, Y H:i') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="mt-2">No product history found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="flex justify-end p-6">
                {{ $productHistories->links() }}
            </div>
        </div>
    </div>
</x-distributor-layout>