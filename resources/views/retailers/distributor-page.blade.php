<x-app-layout>
    <x-dashboard-nav />

    <!-- Back Button -->

    <div class="container px-4 py-6 mx-auto">
        <a href="{{ route('retailers.dashboard') }}" class="flex items-center text-green-600 hover:text-green-700">

            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Distributor Header -->
    <section class="container p-8 mx-auto mb-6 bg-white rounded-lg shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <img class="object-cover w-24 h-24 rounded-full shadow-lg"
                    src="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-distributor.jpg') }}">
                <div class="ml-6">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $distributor->company_name }}</h1>
                    <p class="text-gray-600">{{ $distributor->company_address }}</p>
                </div>
            </div>

            <div class="flex space-x-4">
                <x-modal-review :distributor="$distributor" :reviews="$distributor->reviews" />
                <button class="flex items-center px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Message
                </button>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <div class="container mx-auto mb-6 bg-white rounded-lg shadow-lg">
        <div class="flex overflow-x-auto">
            <a href="{{ route('retailers.distributor-page', ['id' => $distributor->id]) }}"
                class="px-6 py-3 {{ $selectedCategory === 'all' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-500' }}">
                All Products
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('retailers.distributor-page', ['id' => $distributor->id, 'category' => $category->id]) }}"
                    class="px-6 py-3 {{ $selectedCategory == $category->id ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-500' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Products Grid -->
    <div class="container mx-auto mb-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-5">
            @forelse($products as $product)
                <div class="p-6 transition-shadow bg-white border border-gray-200 rounded-lg shadow-lg hover:shadow-xl">
                    <div class="flex justify-center mb-4">
                        <img class="object-cover w-32 h-32 rounded-lg"
                            src="{{ $product->image ? Storage::url($product->image) : asset('img/default-product.jpg') }}"
                            alt="{{ $product->product_name }}"
                            onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-bold">{{ Str::limit($product->product_name, 15) }}</h3>
                        <p class="text-[12px] text-gray-500">Min purchase qty: {{ $product->minimum_purchase_qty }}
                        </p>
                        <p class="text-[12px] text-gray-500">Stocks: {{ $product->stock_quantity }}</p>
                        <div class="flex justify-between mt-4">
                            <span
                                class="text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}</span>
                            <a href="{{ route('retailers.products.show', $product->id) }}"
                                class="mt-1 text-sm hover:text-green-500">View
                                Details →   
                            </a>
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                        </div>
                    </div>
                </div>
            @empty <div class="col-span-5 py-8 text-center">
                    <p class="text-gray-500">No products found in this category.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
