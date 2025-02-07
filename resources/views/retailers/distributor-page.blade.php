<x-app-layout>
    <x-dashboard-nav />

    <!-- Back Button -->
    <div class="container mx-auto px-4 py-6">
        <a href="{{ url()->previous() }}" class="text-green-600 hover:text-green-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Distributor Header -->
    <section class="container mx-auto p-8 bg-white rounded-lg shadow-lg mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <img class="w-24 h-24 rounded-full shadow-lg object-cover" 
                     src="{{ asset('storage/distributors/jacob.png') }}" 
                     alt="Distributor Logo">
                <div class="ml-6">
                    <h1 class="text-2xl font-bold text-gray-800">Jacob Distribution</h1>
                    <p class="text-gray-600">Cabato Drive, Guiwan, Zamboanga City 7000</p>
                    <div class="flex items-center mt-2">
                        <span class="text-yellow-400">★★★★☆</span>
                        <span class="ml-2 text-sm text-gray-600">4.0 (125 reviews)</span>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-4">
                <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 
                             flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Message
                </button>
                <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 
                             flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Reviews
                </button>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <div class="container mx-auto bg-white rounded-lg shadow-lg mb-6">
        <div class="flex overflow-x-auto">
            <button class="px-6 py-3 text-green-600 border-b-2 border-green-500">All Products</button>
            <button class="px-6 py-3 text-gray-500 hover:text-gray-700">Ready to Drink</button>
            <button class="px-6 py-3 text-gray-500 hover:text-gray-700">Canned Goods</button>
            <button class="px-6 py-3 text-gray-500 hover:text-gray-700">Snacks</button>
            <button class="px-6 py-3 text-gray-500 hover:text-gray-700">Fresh Foods</button>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="container mx-auto mb-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            @for ($i = 1; $i <= 10; $i++)
            <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 hover:shadow-xl transition-shadow">
                <div class="flex justify-center mb-4">
                    <img class="w-32 h-32 object-cover rounded-lg" 
                         src="{{ asset('storage/products/rtc-chicken-bbq.png') }}" 
                         alt="Product Image">
                </div>
                <div class="text-left">
                    <h3 class="text-lg font-bold">Chicken BBQ</h3>
                    <p class="text-[12px] text-gray-500">Min purchase qty: 10</p>
                    <p class="text-[12px] text-gray-500">Stocks: 100</p>
                    <div class="flex flex-col items-center mt-4">
                        <span class="text-lg font-bold text-green-600">₱380.00</span>
                        <div class="flex items-center mt-2">
                            <input type="number" value="10" min="10" 
                                   class="w-16 text-center border border-gray-300 rounded 
                                          focus:ring focus:ring-green-200">
                            <button class="px-4 py-2 ml-2 font-bold text-white bg-green-500 
                                         rounded hover:bg-green-700">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</x-app-layout>