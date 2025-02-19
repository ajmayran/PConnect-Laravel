<x-app-layout>
    <x-dashboard-nav />

    <div class="py-12">
        <div class="container px-4 mx-auto">
            <!-- Search Form -->
            <form action="{{ route('retailers.search') }}" method="GET" class="max-w-2xl mx-auto mb-8">
                <div class="flex gap-0">
                    <div class="relative w-full">
                        <input type="search" id="search-dropdown" name="query"
                            class="z-20 block w-full p-3 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Search for products or distributors..." required
                            value="{{ request('query') }}" />
                        <button type="submit"
                            class="absolute top-0 h-full p-3 text-sm font-medium text-white bg-green-500 border border-green-500 rounded-r-lg end-0 hover:bg-green-600 focus:ring-2 focus:outline-none focus:ring-green-300">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Search Results -->
            <div class="mb-8">
                <h2 class="mb-4 text-xl font-semibold">Search Results for "{{ request('query') }}"</h2>

                <!-- Distributors Results -->
                @if ($distributors->isNotEmpty())
                    <div class="mb-8">
                        <h3 class="mb-4 text-lg font-medium">Distributors</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            @foreach ($distributors as $distributor)
                                <a href="{{ route('retailers.distributor-page', $distributor->id) }}"
                                    class="flex flex-col items-center p-6 transition-all duration-300 bg-white border border-gray-100 shadow-md hover:shadow-xl rounded-xl group">
                                    <img class="w-24 h-24 mb-4 transition-transform duration-300 rounded-full shadow-md group-hover:scale-110"
                                        src="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-distributor.jpg') }}"
                                        alt="{{ $distributor->company_name }}">
                                    <h3 class="text-lg font-bold text-gray-800">{{ $distributor->company_name }}</h3>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Products Results -->
                @if ($products->isNotEmpty())
                    <div>
                        <h3 class="mb-4 text-lg font-medium">Products</h3>
                        <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                            @foreach ($products as $product)
                                <a href="{{ route('retailers.products.show', $product->id) }}"
                                    class="relative flex flex-col overflow-hidden transition-all duration-300 bg-white rounded-lg shadow-md group hover:shadow-xl">
                                    <div class="relative w-full pt-[100%] overflow-hidden bg-gray-100">
                                        <img class="absolute inset-0 object-contain w-full h-full p-2 transition-transform duration-300 group-hover:scale-110"
                                            src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                            alt="{{ $product->product_name }}"
                                            onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                                    </div>
                                    <div class="flex flex-col flex-grow p-4">
                                        <h3 class="mb-2 text-lg font-bold text-gray-800 line-clamp-2">
                                            {{ $product->product_name }}</h3>
                                        <p class="mt-1 text-sm text-gray-500 line-clamp-1">
                                            {{ $product->distributor->company_name }}</p>
                                        <div class="flex items-center justify-between pt-4 mt-auto">
                                            <span
                                                class="text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($distributors->isEmpty() && $products->isEmpty())
                    <div class="p-8 text-center text-gray-500 rounded-lg bg-gray-50">
                        No results found for "{{ request('query') }}"
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-footer />
</x-app-layout>
