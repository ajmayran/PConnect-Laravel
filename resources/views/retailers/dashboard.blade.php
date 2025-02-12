<x-app-layout>
    <x-dashboard-nav />

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
                            src="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-logo.png') }}"
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
                            <img class="absolute inset-0 object-cover w-full h-full transition-transform duration-300 group-hover:scale-110"
                                src="{{ $product->image ? Storage::url($product->image) : asset('img/default-product.jpg') }}"
                                alt="{{ $product->product_name }}"
                                onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                        </div>
                        <div class="flex flex-col flex-grow p-4">
                            <h3 class="text-lg font-bold text-gray-800 line-clamp-2">{{ $product->product_name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $product->distributor->company_name }}</p>
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

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </section>

    <x-footer />
</x-app-layout>
