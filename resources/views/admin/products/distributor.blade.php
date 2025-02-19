<x-app-layout>
    @if (session('success'))
        <div class="relative px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    <div class="container px-4 py-8 mx-auto">
        <div class="overflow-hidden bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 bg-gray-800">
                <h1 class="text-2xl font-bold text-white">{{ $distributor->company_name }} Products</h1>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    User ID
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Company Name
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Product ID
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Product Name
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Product Price
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Product Quantity
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->distributor->user->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->distributor->company_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->product_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->price }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $product->stock_quantity }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        <form action="{{ route('admin.removeProduct', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="text" name="reason" placeholder="Reason for removal" required class="border rounded p-1">
                                            <button type="submit" class="font-medium text-red-600 hover:text-red-900">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>