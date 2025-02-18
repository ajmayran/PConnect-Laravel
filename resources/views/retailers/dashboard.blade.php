<x-app-layout>
    <x-dashboard-nav />

    <form class="max-w-2xl p-4 mx-auto">
        <div class="flex gap-0">
            <!-- Dropdown Button -->
            <button id="dropdown-button" data-dropdown-toggle="dropdown"
                class="inline-flex items-center flex-shrink-0 px-4 py-3 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 focus:ring-2 focus:ring-green-500 focus:z-10"
                type="button">
                All categories
                <svg class="w-2.5 h-2.5 ms-2.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-lg w-44">
                <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdown-button">
                    <li>
                        <button type="button"
                            class="inline-flex w-full px-4 py-2 hover:bg-gray-100 hover:text-green-500">
                            Distributor
                        </button>
                    </li>
                    <li>
                        <button type="button"
                            class="inline-flex w-full px-4 py-2 hover:bg-gray-100 hover:text-green-500">
                            Product
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Search Input -->
            <div class="relative w-full">
                <input type="search" id="search-dropdown"
                    class="z-20 block w-full p-3 text-sm text-gray-900 bg-white border border-gray-300 border-s-0 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Search Products, Distributors..." required />
                <button type="submit"
                    class="absolute top-0 h-full p-3 text-sm font-medium text-white bg-green-500 border border-green-500 rounded-r-lg end-0 hover:bg-green-600 focus:ring-2 focus:outline-none focus:ring-green-300">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                    <span class="sr-only">Search</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Distributors Section -->
    <section class="py-8 bg-gray-50">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Explore Distributors</h2>
            </div>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @forelse ($distributors as $distributor)
                    <a href="{{ route('retailers.distributor-page', $distributor->id) }}"
                        class="flex flex-col items-center p-6 transition-all duration-300 bg-white border border-gray-100 shadow-md hover:shadow-xl rounded-xl group">
                        <img class="w-24 h-24 mb-4 transition-transform duration-300 rounded-full shadow-md group-hover:scale-110"
                            src="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-distributor.jpg') }}"
                            alt="Distributor {{ $distributor->user?->name ?? 'Unknown' }}">
                        <h3 class="text-lg font-bold text-gray-800">{{ $distributor->company_name }}</h3>
                    </a>
                @empty
                    <div class="text-center text-gray-500 col-span-full">
                        No distributors found.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-8 bg-white">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Popular Products</h2>
            </div>
            <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @forelse ($products as $product)
                    <a href="{{ route('retailers.products.show', $product->id) }}"
                        class="relative flex flex-col overflow-hidden transition-all duration-300 bg-white rounded-lg shadow-md group hover:shadow-xl">
                        <div class="relative w-full pt-[100%] overflow-hidden bg-gray-100">
                            <img class="absolute inset-0 object-contain w-full h-full p-2 transition-transform duration-300 group-hover:scale-110"
                                src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                alt="{{ $product->product_name }}"
                                onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                        </div>
                        <div class="flex flex-col flex-grow p-4">
                            <h3 class="mb-2 text-lg font-bold text-gray-800 line-clamp-2">{{ $product->product_name }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 line-clamp-1">{{ $product->distributor->company_name }}
                            </p>
                            <div class="flex items-center justify-between pt-4 mt-auto">
                                <span
                                    class="text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-gray-500">View Details →</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-gray-500 col-span-full">
                        No products found.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

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
    </div>

    <x-footer />
</x-app-layout>
