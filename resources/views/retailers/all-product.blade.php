<x-app-layout>
    <x-retailer-topnav />

    <!-- Responsive Search Bar -->
    <form action="{{ route('retailers.search') }}" method="GET" class="max-w-2xl p-2 sm:p-4 mx-auto">
        <div class="relative">
            <input type="search" 
                id="search-dropdown" 
                name="query"
                class="block w-full p-2 sm:p-3 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="Search products..." 
                required 
                value="{{ request('query') }}" />
            <button type="submit"
                class="absolute top-0 h-full p-2 sm:p-3 text-sm font-medium text-white bg-green-500 border border-green-500 rounded-r-lg end-0 hover:bg-green-600 active:bg-green-700 focus:ring-2 focus:outline-none focus:ring-green-300">
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
                <span class="sr-only">Search</span>
            </button>
        </div>
    </form>
  
    <!-- Improved Category Section -->
    <section class="py-4 sm:py-10" id="category">
        <div class="container px-2 sm:px-4 mx-auto">
            <h2 class="mb-4 sm:mb-10 text-xl sm:text-2xl font-bold text-gray-800">Categories</h2>
            <div class="relative">
                <div class="flex pb-2 mx-auto space-x-2 sm:space-x-4 overflow-x-auto scrollbar-hide bg-white rounded-lg shadow-lg">
                    <a href="{{ route('retailers.all-product') }}"
                        class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap transition-colors duration-200 touch-manipulation
                        {{ $selectedCategory === 'all' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700 active:text-green-600' }}">
                        All Products
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('retailers.all-product', ['category' => $category->id]) }}"
                            class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap transition-colors duration-200 touch-manipulation
                            {{ $selectedCategory == $category->id ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-gray-700 active:text-green-600' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid -->
    <section class="py-4 sm:py-8">
        <div class="container px-2 sm:px-4 mx-auto">
            <div class="grid grid-cols-2 gap-3 sm:gap-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @forelse ($products as $product)
                    <a href="{{ route('retailers.products.show', $product->id) }}"
                        class="relative flex flex-col overflow-hidden transition-all duration-300 bg-white rounded-lg shadow-md group hover:shadow-xl">
                        <div class="relative w-full pt-[100%] overflow-hidden bg-gray-100">
                            <img class="absolute inset-0 object-contain w-full h-full p-2 transition-transform duration-300 group-hover:scale-110"
                                src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                alt="{{ $product->product_name }}"
                                onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                        </div>
                        <div class="flex flex-col flex-grow p-2 sm:p-4">
                            <h3 class="mb-1 sm:mb-2 text-sm sm:text-lg font-bold text-gray-800 line-clamp-2">
                                {{ $product->product_name }}
                            </h3>
                            <p class="mt-1 text-xs sm:text-sm text-gray-500 line-clamp-1">
                                {{ $product->distributor->company_name }}
                            </p>
                            <div class="flex items-center justify-between pt-2 sm:pt-4 mt-auto">
                                <span class="text-sm sm:text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}</span>
                                <span class="text-xs sm:text-sm text-gray-500">View Details →</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-500">
                        No products found.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Pagination -->
    <div class="container px-2 sm:px-4 mx-auto pb-8">
        {{ $products->links() }}
    </div>
    <x-footer />
</x-app-layout>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
