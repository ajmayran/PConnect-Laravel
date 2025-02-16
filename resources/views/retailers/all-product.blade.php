<x-app-layout>
    <x-retailer-topnav />

    <section class="py-10" id="category">
        <div class="container px-4 mx-auto">
            <h2 class="mb-10 text-2xl font-bold text-gray-800">Categories</h2>
            <div class="flex mb-8 space-x-4 overflow-x-auto">
                <a href="{{ route('retailers.all-product') }}"
                    class="px-6 py-3 whitespace-nowrap {{ $selectedCategory === 'all' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700' }}">
                    All Products
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('retailers.all-product', ['category' => $category->id]) }}"
                        class="px-6 py-3 whitespace-nowrap {{ $selectedCategory == $category->id ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <div class="container p-4 mx-auto">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            @forelse ($products as $product)
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <a href="{{ route('retailers.products.show', $product->id) }}">
                        <div class="aspect-w-1 aspect-h-1">
                            <img src="{{ $product->image ? Storage::url($product->image) : asset('img/default-product.jpg') }}"
                                alt="{{ $product->product_name }}"
                                onerror="this.src='{{ asset('img/default-product.jpg') }}'"
                                class="object-cover w-full h-full">
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-900">{{ $product->product_name }}</h3>
                            <p class="text-sm text-gray-500">{{ $product->distributor->company_name }}</p>
                            <p class="mt-2 text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}
                            </p>
                            <p class="text-sm text-gray-500">Stock: {{ $product->stock_quantity }}</p>
                        </div>
                    </a>
                </div>
            @empty
                <div class="py-8 text-center text-gray-500 col-span-full">
                    No products found.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>
    @include('components.footer')
</x-app-layout>
