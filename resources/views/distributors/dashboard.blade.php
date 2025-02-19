<x-distributor-layout>
    @vite(['resources/js/dist_dashboard.js'])
    <div class="container mx-auto">
        <span class="absolute text-4xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <h1 class="mb-4 text-2xl font-semibold text-center">Overview</h1>

        <!-- Overview Section -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <!-- Product Sales -->
            <div class="p-4 bg-white rounded-lg shadow-md">
                <h3 class="text-lg font-semibold"><iconify-icon icon="healthicons:money-bag"
                        class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Product Sales</h3>
                <canvas id="productSalesChart" class="mt-4"></canvas>
            </div>

            <!-- Add to Cart -->
            <div class="p-4 bg-white rounded-lg shadow-md">
                <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:cart"
                        class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Add to Cart</h3>
                <canvas id="addToCartChart" class="mt-4"></canvas>
            </div>

            <!-- Checkout -->
            <div class="p-4 bg-white rounded-lg shadow-md">
                <h3 class="text-lg font-semibold"><iconify-icon icon="material-symbols:shopping-cart-checkout"
                        class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Checkout</h3>
                <canvas id="checkoutChart" class="mt-4"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mt-6 lg:grid-cols-3">
            <!-- Most Selling Products -->
            <div class="col-span-1 p-4 bg-white rounded-lg shadow-md lg:col-span-2">
                <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:fire"
                        class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Most Selling
                    Products
                </h3>
                <table class="w-full mt-4 text-sm table-auto">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2 text-left">Product</th>
                            <th class="py-2 text-left"></th>
                            <th class="py-2 text-left">Category</th>
                            <th class="py-2 text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2"><img src="{{ asset('img/Products/rtc-fried-chicken.png') }}"
                                    alt="" class="w-16 h-16 rounded"></td>
                            <td class="py-2 text-[12px] font-light">Magnolia Ready to Cook Fried Chicken</td>
                            <td class="py-2 text-[12px] font-light">Frozen Products</td>
                            <td class="py-2 text-right text-[12px] font-light">₱200</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2"><img src="{{ asset('img/Products/Chicken-lumpia-shanghai-mix.png') }}"
                                    alt="" class="ml-1 rounded h-14 w-14"></td>
                            <td class="py-2 text-[12px] font-light">Magnolia Ready to Cook Chicken Lumpia
                                Shanghai
                                Mix
                            </td>
                            <td class="py-2 text-[12px] font-light">Frozen Products</td>
                            <td class="py-2 text-right text-[12px] font-light">₱200</td>
                        </tr>
                        <!-- Rows -->
                    </tbody>
                </table>
            </div>

            <!-- Chart Orders -->
            <div class="p-4 bg-white rounded-lg shadow-md">
                <h3 class="text-lg font-semibold"><iconify-icon icon="lets-icons:order-fill"
                        class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Orders</h3>
                <canvas id="chartOrders"></canvas>
            </div>
        </div>

        <!-- Income Section -->
        <div class="grid grid-cols-1 gap-4 mt-6 lg:grid-cols-3">
            <div class="p-4 bg-white rounded-lg shadow-md">
                <h3 class="text-lg font-semibold"><iconify-icon icon="tdesign:money"
                        class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Income</h3>
                <canvas id="incomeChart"></canvas>
            </div>

            <!-- Trending Products -->
            <div class="col-span-1 p-4 bg-white rounded-lg shadow-md lg:col-span-2">
                <h3 class="text-lg font-semibold"><iconify-icon icon="mdi:fire"
                        class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>Trending Products
                </h3>
                <table class="w-full mt-4 text-sm table-auto">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">#</th>
                            <th class="py-2 text-left">Product</th>
                            <th class="py-2 text-left"></th>
                            <th class="py-2 text-left">Category</th>
                            <th class="py-2 text-right">Likes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="px-2 py-2">1</td>
                            <td class="py-2"><img src="{{ asset('img/Products/rtc-chicken-siomai.png') }}"
                                    alt="" class="w-16 h-16 rounded"></td>
                            <td class="py-2 text-[12px] font-light">Ready to Cook Chicken Siomai</td>
                            <td class="py-2 text-[12px] font-light">Frozen Products</td>
                            <td class="py-2 text-right text-[12px] font-light">314</td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-2">2</td>
                            <td class="py-2"><img src="{{ asset('img/Products/rtc-chicken-lumpia.png') }}"
                                    alt="" class="w-16 h-16 rounded"></td>
                            <td class="py-2 text-[12px] font-light">Ready to Cook Chicken Lumpia</td>
                            <td class="py-2 text-[12px] font-light">Frozen Products</td>
                            <td class="py-2 text-right text-[12px] font-light">290</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</x-distributor-layout>
