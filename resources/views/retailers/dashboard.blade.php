<x-app-layout>
    <x-dashboard-nav />

    <!-- Distributors Section -->
    <section class="py-8 mb-6 rounded-lg shadow-sm bg-gray-50">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="mr-4 text-2xl font-bold text-gray-800">Explore Distributors</h2>
            </div>

            @forelse ($distributors as $distributor)
                <a href="{{ route('retailers.distributor.show', $distributor->id) }}"
                    class="flex flex-col items-center p-6 transition-shadow duration-300 bg-white border border-gray-100 shadow-lg cursor-pointer rounded-xl hover:shadow-xl">
                    <img class="w-24 h-24 mb-4 rounded-full shadow-md" src="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-logo.png') }}"
                        alt="Distributor {{ $distributor->user?->name ?? 'Unknown' }}">
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ $distributor->company_name ?? $distributor->company_name }}</h3>
                </a>
            @empty
                <div class="col-span-5 text-center text-gray-500">
                    No distributors found.
                </div>
            @endforelse

        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5 bg-white">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between">
                <h2 class="mr-4 text-2xl font-bold">Popular Products</h2>
            </div>
            <div class="flex flex-wrap gap-4 py-10">

                @forelse ($products as $product)
                    <a href="{{ route('retailers.products.show', $product->id) }}"
                        class="flex flex-col items-center p-6 bg-white rounded-lg shadow-md basis-1/5 w-[20] border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex justify-center mb-4">
                            <img class="object-cover w-24 h-24 rounded"
                                src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/default-product.jpg') }}"
                                alt="{{ $product->name }}">
                        </div>
                        <div class="text-left">
                            <h3 class="text-lg font-bold">{{ $product->product_name }}</h3>
                            <p class="text-[12px] text-gray-500">{{ $product->distributor->company_name }}</p>
                            <p class="text-[12px] text-gray-500">Min purchase qty:
                                {{ $product->minimum_purchase_qty ?? 1 }}</p>
                            <p class="text-[12px] text-gray-500">Stocks Remaining: {{ $product->stock_quantity ?? 0 }}
                            </p>
                            <div class="flex flex-col items-center mt-4">
                                <span
                                    class="text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}</span>
                                <div class="flex items-center mt-2">
                                    <input type="number" value="{{ $product->minimum_purchase_qty }}"
                                        min="{{ $product->minimum_purchase_qty }}"
                                        class="w-16 text-center border border-gray-300 rounded focus:ring focus:ring-green-200">
                                    <button
                                        class="px-4 py-2 ml-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="w-full text-center text-gray-500">
                        No products found.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}

            </div>
        </div>
    </section>

    <x-footer />
</x-app-layout>
