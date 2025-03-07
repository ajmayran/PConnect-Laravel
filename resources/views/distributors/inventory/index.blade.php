<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <h1 class="mb-6 text-2xl font-bold">Inventory Management</h1>

        <!-- Search Bar -->
        <div class="flex items-center justify-between mb-6">
            <div class="w-full md:w-1/2 lg:w-1/3">
                <form action="{{ route('distributors.inventory.index') }}" method="GET">
                    <div class="relative flex">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search products..."
                            class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <button type="submit"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Results Info -->
        @if (request('search'))
            <div class="mb-4">
                <div class="flex items-center">
                    <p class="text-gray-600">Search results for: <span
                            class="font-bold">"{{ request('search') }}"</span></p>
                    <a href="{{ route('distributors.inventory.index') }}"
                        class="ml-3 text-sm text-blue-500 hover:underline">
                        Clear search
                    </a>
                </div>
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Product</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Current Stock</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Last
                            Updated</th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($products as $product)
                        <tr data-product-id="{{ $product->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex-shrink-0 w-20 h-20">
                                    @if ($product->image && Storage::disk('public')->exists($product->image))
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->product_name }}"
                                            class="object-cover w-full h-full rounded-lg">
                                    @else
                                        <img src="{{ asset('img/default-product.jpg') }}" alt="Default Product Image"
                                            class="object-cover w-full h-full rounded-lg">
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $product->product_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 stock-quantity">{{ $product->stock_quantity }}</div>
                            </td>
                            <!-- Update the last updated cell -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 last-updated">
                                    {{ $product->stock_updated_at ? Carbon\Carbon::parse($product->stock_updated_at)->format('M d, Y H:i') : 'Never' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    onclick="openUpdateStockModal({{ $product->id }}, '{{ $product->product_name }}', {{ $product->stock_quantity }})"
                                    class="px-4 py-2 text-sm text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                    Update Stock
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="flex justify-end px-6 py-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Update Stock Modal -->
    <div id="updateStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>

            <div class="relative w-full max-w-md p-6 bg-white rounded-lg">
                <h2 class="mb-4 text-xl font-bold">Update Stock</h2>
                <form id="updateStockForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-medium">Product Name</label>
                        <div id="productName" class="text-gray-700"></div>
                    </div>
                    <div class="mb-4">
                        <label for="stock_quantity" class="block mb-2 text-sm font-medium">Stock Quantity</label>
                        <input type="number" name="stock_quantity" id="stock_quantity"
                            class="w-full px-3 py-2 border rounded-md" required min="0">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeUpdateStockModal()"
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openUpdateStockModal(productId, productName, currentStock) {
                const modal = document.getElementById('updateStockModal');
                const form = document.getElementById('updateStockForm');
                const stockInput = document.getElementById('stock_quantity');

                form.action = `/inventory/${productId}/update-stock`;
                document.getElementById('productName').textContent = productName;
                stockInput.value = currentStock;

                modal.classList.remove('hidden');
            }

            function closeUpdateStockModal() {
                document.getElementById('updateStockModal').classList.add('hidden');
            }

            document.getElementById('updateStockForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const productId = this.action.split('/').slice(-2)[0];

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Find the specific row using data-product-id
                            const row = document.querySelector(`tr[data-product-id="${productId}"]`);

                            // Update stock quantity
                            const stockCell = row.querySelector('.stock-quantity');
                            if (stockCell) {
                                stockCell.textContent = formData.get('stock_quantity');
                            }

                            // Update last updated timestamp
                            const lastUpdatedCell = row.querySelector('.last-updated');
                            if (lastUpdatedCell) {
                                lastUpdatedCell.textContent = data.last_updated;
                            }

                            closeUpdateStockModal();

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            throw new Error(data.message || 'Failed to update stock');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Failed to update stock'
                        });
                    });
            });
        </script>
    @endpush
</x-distributor-layout>
