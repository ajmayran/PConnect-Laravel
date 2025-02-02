<x-app-layout>
    <x-dashboard-nav />
    
    <x-slot name="header">
        <h2 class="text-2xl font-bold">Dashboard</h2>
    </x-slot>

    <!-- Distributors Section -->
    <section class="py-8 bg-gray-50 shadow-sm mb-6 rounded-lg">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="mr-4 text-2xl font-bold text-gray-800">Explore Distributors</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 py-10 md:grid-cols-5">
                <!-- Distributor Cards with Enhanced Styling -->
                <div class="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg cursor-pointer hover:shadow-xl transition-shadow duration-300 border border-gray-200 hover:border-green-200 hover:bg-gray-50">
                    <img class="w-24 h-24 mb-4 rounded-full shadow-md" src="{{ asset('storage/distributors/jacob.png') }}" alt="Distributor Jacob">
                    <h3 class="text-lg font-bold text-gray-800">Jacob Distribution</h3>
                </div>
                <div class="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg cursor-pointer hover:shadow-xl transition-shadow duration-300 border border-gray-200 hover:border-green-200 hover:bg-gray-50">
                    <img class="w-24 h-24 mb-4 rounded-full shadow-md" src="{{ asset('storage/distributors/primus.png') }}" alt="Distributor Primus">
                    <h3 class="text-lg font-bold text-gray-800">Primus Distributor</h3>
                </div>
                <div class="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg cursor-pointer hover:shadow-xl transition-shadow duration-300 border border-gray-200 hover:border-green-200 hover:bg-gray-50">
                    <img class="w-24 h-24 mb-4 rounded-full shadow-md" src="{{ asset('storage/distributors/glenmark.png') }}" alt="Distributor Glenmark">
                    <h3 class="text-lg font-bold text-gray-800">Glenmark Trading</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5 bg-gray-50 shadow-sm rounded-lg">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between">
                <h2 class="mr-4 mt-6 text-2xl font-bold text-gray-800">Popular Products</h2>
            </div>
            <div class="flex flex-wrap gap-6 py-6">
                <!-- Product Cards with Enhanced Styling -->
                <div class="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-green-200 hover:bg-gray-50 basis-1/5 w-[20]">
                    <div class="flex justify-center mb-4">
                        <img class="w-24 h-24 object-cover rounded-lg shadow-md" src="{{ asset('storage/products/rtc-chicken-bbq.png') }}" alt="Product 1">
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-bold">Chicken BBQ</h3>
                        <p class="text-[12px] text-gray-500">Jacob Distribution</p>
                        <p class="text-[12px] text-gray-500">Min purchase qty: 10</p>
                        <p class="text-[12px] text-gray-500">Stocks Remaining: 100</p>
                        <div class="flex flex-col items-center mt-4">
                            <span class="text-lg font-bold text-green-600">₱380.00</span>
                            <div class="flex items-center mt-2">
                                <input type="number" value="10" min="10" class="w-16 text-center border border-gray-300 rounded focus:ring focus:ring-green-200">
                                <button class="px-4 py-2 ml-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Duplicate product cards for demo -->
                <!-- Product 2 -->
                <div class="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 border border-gray-200 hover:border-green-200 hover:bg-gray-50 basis-1/5 w-[20]">
                    <div class="flex justify-center mb-4">
                        <img class="w-24 h-24 object-cover rounded" src="{{ asset('storage/products/coke_bottle.jpg') }}" alt="Product 1">
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-bold">Coke mismo case (12 pieces)</h3>
                        <p class="text-[12px] text-gray-500">Primus Distributor</p>
                        <p class="text-[12px] text-gray-500">Min purchase qty: 4</p>
                        <p class="text-[12px] text-gray-500">Stocks Remaining: 75</p>
                        <div class="flex flex-col items-center mt-4">
                            <span class="text-lg font-bold text-green-600">₱210.00</span>
                            <div class="flex items-center mt-2">
                                <input type="number" value="15" min="15" class="w-16 text-center border border-gray-300 rounded focus:ring focus:ring-green-200">
                                <button class="px-4 py-2 ml-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        setTimeout(() => {
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                successAlert.style.display = 'none';
            }
        }, 3000);
    </script>
    @endpush
</x-app-layout>