<x-app-layout>

    <x-dashboard-nav />

    <!-- Distributors Section -->
    <section class="py-8 mb-6 rounded-lg shadow-sm bg-gray-50">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="mr-4 text-2xl font-bold text-gray-800">Explore Distributors</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 py-10 md:grid-cols-5">
                <!-- Sample Distributor Cards -->
                <x-sample-distributor-card distributor_name="Jacob Distribution" imagepath="storage/distributors/jacob.png" route="{{route('distributor.show')}}"></x-sample-distributor-card>
                <x-sample-distributor-card distributor_name="Primus Distributor" imagepath="storage/distributors/primus.png" route="null"></x-sample-distributor-card>
                <x-sample-distributor-card distributor_name="Glenmark Trading" imagepath="storage/distributors/glenmark.png" route="null"></x-sample-distributor-card>
                
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5 bg-white">
        <div class="container px-4 mx-auto">
            <div class="flex items-center justify-between">
                <h2 class="mr-4 text-2xl font-bold">Popular Products</h2>
            </div>
            <div class="flex flex-wrap gap-4 py-10">
                <!-- Sample Product Cards -->
                <div
                    class="flex flex-col items-center p-6 bg-white rounded-lg shadow-md basis-1/5 w-[20] border border-gray-100">
                    <div class="flex justify-center mb-4">
                        <img class="object-cover w-24 h-24 rounded" src="{{ asset('img/products/rtc-chicken-bbq.png') }}" alt="Product 1">
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-bold">Chicken BBQ</h3>
                        <p class="text-[12px] text-gray-500">Jacob Distribution</p>
                        <p class="text-[12px] text-gray-500">Min purchase qty: 10</p>
                        <p class="text-[12px] text-gray-500">Stocks Remaining: 100</p>
                        <div class="flex flex-col items-center mt-4">
                            <span class="text-lg font-bold text-green-600">₱380.00</span>
                            <div class="flex items-center mt-2">
                                <input type="number" value="10" min="10"
                                    class="w-16 text-center border border-gray-300 rounded focus:ring focus:ring-green-200">
                                <button
                                    class="px-4 py-2 ml-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Duplicate product cards for demo -->
                <!-- Product 2 -->
                <div
                    class="flex flex-col items-center p-6 bg-white rounded-lg shadow-md basis-1/5 w-[20] border border-gray-100">
                    <div class="flex justify-center mb-4">
                        <img class="object-cover w-24 h-24 rounded" src="{{ asset('img/softdrinks/coke_bottle.jpg') }}" alt="Product 1">
                    </div>
                    <div class="text-left">
                        <h3 class="text-lg font-bold">Coke mismo case (12 pieces)</h3>
                        <p class="text-[12px] text-gray-500">Primus Distributor</p>
                        <p class="text-[12px] text-gray-500">Min purchase qty: 4</p>
                        <p class="text-[12px] text-gray-500">Stocks Remaining: 75</p>
                        <div class="flex flex-col items-center mt-4">
                            <span class="text-lg font-bold text-green-600">₱210.00</span>
                            <div class="flex items-center mt-2">
                                <input type="number" value="15" min="15"
                                    class="w-16 text-center border border-gray-300 rounded focus:ring focus:ring-green-200">
                                <button
                                    class="px-4 py-2 ml-2 font-bold text-white bg-green-500 rounded hover:bg-green-700">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <x-footer />
</x-app-layout>
