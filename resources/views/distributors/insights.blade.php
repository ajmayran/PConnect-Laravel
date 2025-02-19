<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <h1 class="mb-4 text-xl font-semibold text-center sm:text-2xl">Insights</h1>

            <!-- Tabs Navigation -->
            <div class="p-4 mb-6 bg-white rounded-lg shadow-sm">
                <div class="flex justify-between mb-4 border-b">
                    <div class="flex space-x-4">
                        <button id="tab-overview" class="px-4 py-2 text-gray-500 hover:text-green-600 tab-button">
                            Overview
                        </button>
                        <button id="tab-products" class="px-4 py-2 text-gray-500 hover:text-green-600 tab-button">
                            Products
                        </button>
                        <button id="tab-sales" class="px-4 py-2 text-gray-500 hover:text-green-600 tab-button">
                            Sales
                        </button>
                    </div>
                </div>
            </div>

            <!-- Overview Tab Content -->
            <div id="tab-content-overview">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Orders Card -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Orders</h3>
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <div class="flex items-center">
                            <span class="text-3xl font-bold text-gray-900">150</span>
                            <span class="flex items-center ml-4 text-sm text-green-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                +12%
                            </span>
                        </div>
                    </div>

                    <!-- Revenue Card -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Revenue</h3>
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex items-center">
                            <span class="text-3xl font-bold text-gray-900">₱45,000</span>
                            <span class="flex items-center ml-4 text-sm text-green-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                +8%
                            </span>
                        </div>
                    </div>

                    <!-- Visitors Card -->
                    <div class="p-4 bg-white rounded-lg shadow sm:col-span-2 lg:col-span-1">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Visitors</h3>
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex items-center">
                            <span class="text-3xl font-bold text-gray-900">215</span>
                            <span class="flex items-center ml-4 text-sm text-red-500">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                -32%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Trend Chart -->
                <div class="p-4 mb-6 bg-white rounded-lg shadow">
                    <h3 class="mb-4 text-lg font-semibold text-gray-700">Trend Overview</h3>
                    <div class="relative w-full h-64">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Products Tab Content -->
            <div id="tab-content-products" class="hidden">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Product List -->
                    <div class="p-4 bg-white rounded-lg shadow lg:col-span-2">
                        <h3 class="mb-4 text-lg font-semibold">
                            <svg class="inline-block w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Product Ranking
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full mt-4 text-sm table-auto">
                                <thead>
                                    <tr class="border-b">
                                        <th class="px-1 py-2 font-normal text-left">Rank</th>
                                        <th class="px-1 py-2 font-normal text-left">Preview</th>
                                        <th class="px-1 py-2 font-normal text-left">Product</th>
                                        <th class="px-1 py-2 font-normal text-left">Category</th>
                                        <th class="px-1 py-2 font-normal text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $index => $product)
                                        <tr class="border-b border-gray-200">
                                            <td class="py-2 text-lg font-semibold text-center text-green-500">
                                                {{ $index + 1 }}</td>
                                            <td class="py-2">
                                                <img src="{{ $product->image && Storage::disk('public')->exists($product->image)
                                                    ? asset('storage/' . $product->image)
                                                    : asset('img/default-product.jpg') }}"
                                                    alt="{{ $product->product_name }}" class="w-16 h-16 rounded">
                                            </td>
                                            <td class="py-2 text-[12px] font-light">{{ $product->product_name }}</td>
                                            <td class="py-2 text-[12px] font-light">
                                                {{ $categories->firstWhere('id', $product->category_id)->name ?? 'N/A' }}
                                            </td>
                                            <td class="py-2 text-right text-[12px] font-light">
                                                ₱{{ number_format($product->price, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-gray-500">
                                                No products found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Category Chart -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h3 class="mb-4 text-lg font-semibold text-gray-700">Category Distribution</h3>
                        <div class="relative w-full h-64">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Tab Content -->
            <div id="tab-content-sales" class="hidden">
                <!-- Sales Chart -->
                <div class="p-4 mb-6 bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Sales Overview</h3>
                        <div class="space-x-2">
                            <button id="weeklyBtn"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">Weekly</button>
                            <button id="monthlyBtn"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Monthly</button>
                        </div>
                    </div>
                    <div class="relative w-full h-64">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Sales Performance Table -->
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="mb-4 text-lg font-semibold text-gray-700">Sales Performance</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left bg-gray-50">
                                    <th class="p-4 text-sm font-medium text-gray-600">Product</th>
                                    <th class="p-4 text-sm font-medium text-gray-600">Total Sales</th>
                                    <th class="p-4 text-sm font-medium text-gray-600">Orders</th>
                                    <th class="p-4 text-sm font-medium text-gray-600">Trend</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach (range(1, 5) as $index)
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-4">Product {{ $index }}</td>
                                        <td class="p-4">₱{{ number_format($index * 1000, 2) }}</td>
                                        <td class="p-4">{{ $index * 5 }}</td>
                                        <td class="p-4">
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-sm text-green-600 bg-green-100 rounded-full">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $index * 2 }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Tab switching
            const tabs = {
                overview: document.getElementById('tab-overview'),
                products: document.getElementById('tab-products'),
                sales: document.getElementById('tab-sales')
            };

            const contents = {
                overview: document.getElementById('tab-content-overview'),
                products: document.getElementById('tab-content-products'),
                sales: document.getElementById('tab-content-sales')
            };

            function switchTab(tabName) {
                // Remove active state from all tabs
                Object.keys(tabs).forEach(key => {
                    tabs[key].classList.remove('text-green-600', 'border-b-2', 'border-green-500');
                    tabs[key].classList.add('text-gray-500');
                    contents[key].classList.add('hidden');
                });

                // Add active state to selected tab
                tabs[tabName].classList.remove('text-gray-500');
                tabs[tabName].classList.add('text-green-600', 'border-b-2', 'border-green-500');
                contents[tabName].classList.remove('hidden');
            }

            // Add click events to tabs
            Object.keys(tabs).forEach(key => {
                tabs[key].addEventListener('click', () => switchTab(key));
            });

            // Initialize charts
            function initializeCharts() {
                // Trend Chart
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Sales',
                            data: [12, 19, 3, 5, 2, 3],
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // Category Chart
                const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($categoryLabels),
                        datasets: [{
                            data: @json($categoryData),
                            backgroundColor: @json($categoryColors)
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });

                // Sales Chart
                const salesCtx = document.getElementById('salesChart').getContext('2d');
                new Chart(salesCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Sales',
                            data: [12, 19, 3, 5, 2, 3, 7],
                            backgroundColor: '#10B981'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Initialize everything when the page loads
            document.addEventListener('DOMContentLoaded', function() {
                switchTab('overview');
                initializeCharts();
            });
        </script>
    @endpush
</x-distributor-layout>
