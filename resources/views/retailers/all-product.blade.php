<x-app-layout>
    <x-retailer-topnav />

    <form class="max-w-2xl p-4 mx-auto">
        <div class="relative">
            <input type="search" id="search" name="search"
                class="block w-full p-3 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="Search products by name..." required />
            <button type="submit"
                class="absolute top-0 h-full p-3 text-sm font-medium text-white bg-green-500 border border-green-500 rounded-r-lg end-0 hover:bg-green-600 focus:ring-2 focus:outline-none focus:ring-green-300">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
                <span class="sr-only">Search</span>
            </button>
        </div>
    </form>

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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse ($products as $product)
                <div
                    class="flex flex-col h-full overflow-hidden transition-transform duration-300 bg-white rounded-lg shadow-lg hover:scale-[1.02]">
                    <a href="{{ route('retailers.products.show', $product->id) }}" class="flex flex-col h-full">
                        <div class="relative w-full h-48 sm:h-40">
                            <img src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                alt="{{ $product->product_name }}"
                                onerror="this.src='{{ asset('img/default-product.jpg') }}'"
                                class="absolute inset-0 object-contain w-full h-full p-3">
                        </div>
                        <div class="flex flex-col flex-grow p-4">
                            <h3 class="mb-2 text-sm font-bold text-gray-900 line-clamp-2">{{ $product->product_name }}
                            </h3>
                            <p class="text-xs text-gray-500">{{ $product->distributor->company_name }}</p>
                            <div class="flex-grow"></div>
                            <div class="mt-4">
                                <p class="text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}
                                </p>
                                <p class="text-xs text-gray-500">Stock: {{ $product->stock_quantity }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="py-8 text-center text-gray-500 col-span-full">
                    No products found.
                </div>
            @endforelse
        </div>
    </div>

    <div class="container p-4 mx-auto">
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <!-- Pagination Results Info -->
                <div class="text-sm text-gray-600">
                    {!! __('Showing :first to :last of :total results', [
                        'first' => $products->firstItem() ?? 0,
                        'last' => $products->lastItem() ?? 0,
                        'total' => $products->total(),
                    ]) !!}
                </div>
                <!-- Pagination Links -->
                <div>
                    {{ $products->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
    @include('components.footer')
</x-app-layout>
