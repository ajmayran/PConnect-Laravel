<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-2 mx-auto">
            <h1 class="mb-6 text-3xl font-bold text-gray-800">My Products</h1>
            <div class="flex items-center justify-end mb-6 space-x-4">
                <div class="flex items-center space-x-4">
                    <button onclick="openModal('priceModal')"
                        class="px-4 py-2 font-bold text-white transition duration-200 bg-blue-500 rounded-lg hover:bg-blue-600">
                        Product Prices
                    </button>
                </div>
                <div class="space-x-2">
                    <a href="{{ route('distributors.products.create') }}"
                        class="px-4 py-2 font-bold text-white transition duration-200 bg-green-500 rounded-lg hover:bg-green-600">
                        Add New Product
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-4 alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filter Form -->
            <form method="GET" action="{{ route('distributors.products.index') }}" class="mb-6">
                <div class="flex items-center space-x-4">
                    <select name="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">All</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                    <button type="submit" class="px-4 py-2 font-bold text-white transition duration-200 bg-green-500 rounded-lg hover:bg-green-600">
                        Filter
                    </button>
                </div>
            </form>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($products as $product)
                    <div class="overflow-hidden bg-white rounded-lg shadow-md">
                        <div class="relative">
                            @if ($product->image && Storage::disk('public')->exists($product->image))
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}"
                                    class="object-cover w-full h-48">
                            @else
                                <img src="{{ asset('img/default-product.jpg') }}" alt="Default Product Image"
                                    class="object-cover w-full h-48">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $product->product_name }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $categories->firstWhere('id', $product->category_id)->name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500">Min: {{ $product->minimum_purchase_qty }}</p>
                            <p class="text-sm font-bold text-gray-800">Status: 
                                <span class="{{ $product->status == 'approved' ? 'text-green-500' : 'text-yellow-500' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </p>
                            <div class="flex mt-4 space-x-4">
                                <button onclick="openEditModal({{ $product->id }})"
                                    class="text-blue-500 hover:text-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.07a2.5 2.5 0 113.536 3.536L7 21H3v-4L16.732 3.464z" />
                                    </svg>
                                </button>
                                <form action="{{ route('distributors.products.destroy', $product->id) }}"
                                    method="POST" id="delete-form-{{ $product->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        onclick="confirmDelete('{{ $product->id }}', '{{ $product->product_name }}')"
                                        class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-distributor-layout>