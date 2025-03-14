<x-distributor-layout>
    @vite(['resources/js/dist_dashboard.js'])
    <div class="container px-4 mx-auto">
        <span class="absolute text-4xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <h1 class="p-4 text-2xl font-bold text-left text-gray-800 sm:text-3xl">Overview</h1>

        <!-- Stats Summary Cards -->
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="p-4 transition-all bg-white border-l-4 border-green-500 rounded-lg shadow-sm hover:shadow-md">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-500">Total Sales</p>
                        <p class="text-xl font-bold text-gray-700" id="total-sales-value">₱0.00</p>
                    </div>
                </div>
            </div>

            <div class="p-4 transition-all bg-white border-l-4 border-blue-500 rounded-lg shadow-sm hover:shadow-md">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-500">Total Orders</p>
                        <p class="text-xl font-bold text-gray-700" id="total-orders-value">0</p>
                    </div>
                </div>
            </div>

            <div class="p-4 transition-all bg-white border-l-4 border-purple-500 rounded-lg shadow-sm hover:shadow-md">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-500">Products</p>
                        <p class="text-xl font-bold text-gray-700" id="total-products-value">0</p>
                    </div>
                </div>
            </div>

            <div class="p-4 transition-all bg-white border-l-4 border-yellow-500 rounded-lg shadow-sm hover:shadow-md">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-yellow-500 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-500">Customers</p>
                        <p class="text-xl font-bold text-gray-700" id="total-customers-value">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Section -->
        <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
            <!-- Sales Overview -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        <iconify-icon icon="healthicons:money-bag"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>
                        Sales Overview
                    </h3>
                    <div class="flex items-center space-x-2">
                        <select id="sales-period-selector"
                            class="px-2 py-1 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="7">Last 7 days</option>
                            <option value="30" selected>Last 30 days</option>
                            <option value="90">Last 3 months</option>
                        </select>
                    </div>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="productSalesChart"></canvas>
                </div>
            </div>

            <!-- Order Statistics -->
            <div class="p-6 bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        <iconify-icon icon="lets-icons:order-fill"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>
                        Order Statistics
                    </h3>
                </div>
                <div class="grid grid-cols-1 gap-4 mb-4 sm:grid-cols-3">
                    <div class="p-3 text-center rounded-lg bg-green-50">
                        <p class="text-sm font-medium text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-green-600" id="completed-orders">0</p>
                    </div>
                    <div class="p-3 text-center rounded-lg bg-yellow-50">
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600" id="pending-orders">0</p>
                    </div>
                    <div class="p-3 text-center rounded-lg bg-red-50">
                        <p class="text-sm font-medium text-gray-600">Cancelled</p>
                        <p class="text-2xl font-bold text-red-600" id="cancelled-orders">0</p>
                    </div>
                </div>
                <div class="relative" style="height: 230px;">
                    <canvas id="chartOrders"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-3">
            <!-- Customer Engagement -->
            <div class="col-span-1 p-6 bg-white rounded-lg shadow-md lg:col-span-1">
                <h3 class="mb-4 text-lg font-semibold text-gray-700">
                    <iconify-icon icon="mdi:cart" class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>
                    Customer Engagement
                </h3>
                <div class="relative" style="height: 200px;">
                    <canvas id="addToCartChart"></canvas>
                </div>
            </div>

            <!-- Most Selling Products -->
            <div class="col-span-1 p-6 bg-white rounded-lg shadow-md lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">
                        <iconify-icon icon="mdi:fire"
                            class="pb-1 mr-1 text-xl text-green-500 align-middle"></iconify-icon>
                        Top Selling Products
                    </h3>
                    <a href="#" class="text-sm font-medium text-green-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full mt-2 text-sm table-auto">
                        <thead>
                            <tr class="text-left text-gray-600 border-b">
                                <th class="pb-3 pl-3">Product</th>
                                <th class="pb-3">Name</th>
                                <th class="pb-3">Category</th>
                                <th class="pb-3">Sold</th>
                                <th class="pb-3 pr-3 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody id="top-products-table">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 pl-3">
                                    <div class="w-12 h-12 bg-gray-200 rounded-md animate-pulse"></div>
                                </td>
                                <td class="py-3">
                                    <div class="h-5 bg-gray-200 rounded w-28 animate-pulse"></div>
                                </td>
                                <td class="py-3">
                                    <div class="w-24 h-5 bg-gray-200 rounded animate-pulse"></div>
                                </td>
                                <td class="py-3">
                                    <div class="w-12 h-5 bg-gray-200 rounded animate-pulse"></div>
                                </td>
                                <td class="py-3 pr-3 text-right">
                                    <div class="w-16 h-5 ml-auto bg-gray-200 rounded animate-pulse"></div>
                                </td>
                            </tr>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 pl-3">
                                    <div class="w-12 h-12 bg-gray-200 rounded-md animate-pulse"></div>
                                </td>
                                <td class="py-3">
                                    <div class="h-5 bg-gray-200 rounded w-28 animate-pulse"></div>
                                </td>
                                <td class="py-3">
                                    <div class="w-24 h-5 bg-gray-200 rounded animate-pulse"></div>
                                </td>
                                <td class="py-3">
                                    <div class="w-12 h-5 bg-gray-200 rounded animate-pulse"></div>
                                </td>
                                <td class="py-3 pr-3 text-right">
                                    <div class="w-16 h-5 ml-auto bg-gray-200 rounded animate-pulse"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Load chart.js from CDN with required components -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

    <!-- Pass data from backend to JavaScript -->
    <script>
        const dashboardData = @json($dashboardData ?? []);
    </script>

</x-distributor-layout>
