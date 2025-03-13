<x-app-layout>
    <x-dashboard-nav />

    <!-- Back Button -->
    <div class="px-4 py-2 sm:px-6">
        <a href="{{ route('retailers.dashboard') }}" 
           class="inline-flex items-center text-sm sm:text-base text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Distributor Header -->

    <section class="container p-4 sm:p-8 mx-auto mb-4 sm:mb-6 bg-white rounded-lg shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-4 sm:gap-6">
                <img class="object-cover w-20 h-20 sm:w-24 sm:h-24 rounded-full shadow-lg"
                    src="{{ $distributor->company_profile_image ? asset('storage/' . $distributor->company_profile_image) : asset('img/default-distributor.jpg') }}"
                    alt="{{ $distributor->company_name }}">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $distributor->company_name }}</h1>
                    <p class="text-sm sm:text-base text-gray-600">{{ $distributor->barangay_name }},{{ $distributor->street }}</p>

                </div>
            </div>

            <div class="flex justify-center sm:justify-start gap-3 sm:gap-4">
                <x-modal-review :distributor="$distributor" :reviews="$distributor->reviews" />
                <button class="flex items-center px-3 py-2 sm:px-4 sm:py-2 text-sm sm:text-base text-white bg-green-500 rounded-lg hover:bg-green-600 active:bg-green-700 transition-colors duration-200 touch-manipulation">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    Message
                </button>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <div class="container px-2 sm:px-4 mx-auto mb-4 sm:mb-6 bg-white rounded-lg shadow-lg">
        <div class="flex overflow-x-auto scrollbar-hide">
            <a href="{{ route('retailers.distributor-page', ['id' => $distributor->id]) }}"
                class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap transition-colors duration-200 touch-manipulation
                {{ $selectedCategory === 'all' ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-500 active:text-green-600' }}">
                All Products
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('retailers.distributor-page', ['id' => $distributor->id, 'category' => $category->id]) }}"
                    class="px-4 sm:px-6 py-2 sm:py-3 whitespace-nowrap transition-colors duration-200 touch-manipulation
                    {{ $selectedCategory == $category->id ? 'text-green-600 border-b-2 border-green-500' : 'text-gray-500 hover:text-green-500 active:text-green-600' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Products Grid -->
    <div class="container px-2 sm:px-4 mx-auto mb-8">
        <div class="grid grid-cols-2 gap-3 sm:gap-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($products as $product)
                <a href="{{ route('retailers.products.show', $product->id) }}" 
                   class="block p-3 sm:p-6 transition-all duration-300 bg-white border border-gray-200 rounded-lg shadow-md 
                        hover:shadow-xl active:shadow-inner transform hover:scale-105 active:scale-95 touch-manipulation">
                    <div class="flex justify-center mb-2 sm:mb-4">
                        <img class="object-cover w-24 h-24 sm:w-32 sm:h-32 rounded-lg transition-transform duration-300 hover:scale-110"
                            src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/default-product.jpg') }}"
                            alt="{{ $product->name }}">
                    </div>
                    <h3 class="mb-1 sm:mb-2 text-sm sm:text-base font-semibold text-gray-800 line-clamp-2">
                        {{ $product->name }}
                    </h3>
                    <p class="mb-2 text-xs sm:text-sm text-gray-600">
                        {{ Str::limit($product->description, 50) }}
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm sm:text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}</span>
                        <span class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-white bg-green-500 
                            rounded-lg hover:bg-green-600 active:bg-green-700 transition-colors duration-200">
                            View Details
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    No products found in this category.
                </div>
            @endforelse
        </div>
    </div>

    <style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    </style>

    <x-footer />
</x-app-layout>
