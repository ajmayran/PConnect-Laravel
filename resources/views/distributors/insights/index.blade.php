<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="flex flex-col items-start justify-between mb-6 sm:flex-row sm:items-center">
            <h1 class="text-2xl font-bold text-gray-800">Business Insights</h1>
            <div class="mt-3 sm:mt-0">
                <select id="period-selector"
                    class="px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="30">Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365" selected>This Year</option>
                    <option value="all">All Time</option>
                </select>
            </div>
        </div>

        <!-- Dashboard Stats -->
        <div class="grid gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Earnings -->
            <div class="p-5 bg-white border-l-4 border-green-500 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-500">Total Earnings</p>
                        <p class="text-xl font-bold text-gray-700">₱{{ number_format($totalEarnings, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- This Month -->
            <div class="p-5 bg-white border-l-4 border-blue-500 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-500">This Month</p>
                        <p class="text-xl font-bold text-gray-700">
                            ₱{{ number_format($monthlyEarnings->where('month', date('n'))->first()?->total ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Average Monthly -->
            <div class="p-5 bg-white border-l-4 border-purple-500 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-500">Average Monthly</p>
                        <p class="text-xl font-bold text-gray-700">
                            ₱{{ number_format($monthlyEarnings->avg('total') ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Growth Rate -->
            <div class="p-5 bg-white border-l-4 rounded-lg shadow-sm border-amber-500">
                <div class="flex items-center">
                    <div class="p-3 mr-4 rounded-full text-amber-500 bg-amber-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-gray-500">Growth Rate</p>
                        <p class="text-xl font-bold text-gray-700" id="growth-rate">
                            {{ $growthRate ?? '0%' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabbed Charts Section -->
        <div class="mb-8 bg-white rounded-lg shadow-sm">
            <div class="px-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="insightsTabs" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 text-green-600 border-b-2 border-green-600 rounded-t-lg active"
                            id="revenue-tab" data-tabs-target="#revenue" type="button" role="tab"
                            aria-controls="revenue" aria-selected="true">Revenue</button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                            id="products-tab" data-tabs-target="#products" type="button" role="tab"
                            aria-controls="products" aria-selected="false">Product Sales</button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                            id="trends-tab" data-tabs-target="#trends" type="button" role="tab"
                            aria-controls="trends" aria-selected="false">Sales Trends</button>
                    </li>
                </ul>
            </div>

            <div id="insightsTabContent" class="p-6">
                <!-- Revenue Tab Content -->
                <div class="block" id="revenue" role="tabpanel" aria-labelledby="revenue-tab">
                    <h2 class="mb-4 text-xl font-semibold text-gray-700">Monthly Revenue ({{ date('Y') }})</h2>
                    <div style="height: 350px;">
                        <canvas id="earningsChart"></canvas>
                    </div>
                </div>

                <!-- Product Sales Tab Content -->
                <div class="hidden" id="products" role="tabpanel" aria-labelledby="products-tab">
                    <h2 class="mb-4 text-xl font-semibold text-gray-700">Top Selling Products</h2>
                    <div style="height: 350px;">
                        <canvas id="productSalesChart"></canvas>
                    </div>
                </div>

                <!-- Sales Trends Tab Content -->
                <div class="hidden" id="trends" role="tabpanel" aria-labelledby="trends-tab">
                    <h2 class="mb-4 text-xl font-semibold text-gray-700">Weekly Sales Trend</h2>
                    <div style="height: 350px;">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Earnings Table -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700">Recent Earnings</h2>
                <p class="mt-1 text-sm text-gray-500">Latest revenue generated from completed orders</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 font-medium text-left text-gray-500">Order ID</th>
                            <th class="px-6 py-3 font-medium text-left text-gray-500">Retailer</th>
                            <th class="px-6 py-3 font-medium text-left text-gray-500">Order Items</th>
                            <th class="px-6 py-3 font-medium text-left text-gray-500">Amount</th>
                            <th class="px-6 py-3 font-medium text-left text-gray-500">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentEarnings as $earning)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-blue-600">
                                    {{ $earning->payment->order->formatted_order_id }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $earning->payment->order->user->first_name }}
                                    {{ $earning->payment->order->user->last_name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $earning->payment->order->orderDetails->count() }} items
                                </td>
                                <td class="px-6 py-4 font-medium text-green-600">
                                    ₱{{ number_format($earning->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $earning->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mb-1 text-lg font-medium">No earnings recorded yet</p>
                                        <p class="text-sm text-gray-500">Completed orders will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-200">
                <a href="#" class="inline-flex items-center text-sm font-medium text-green-600 hover:underline">
                    View all earnings
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Tab initialization
                const tabs = document.querySelectorAll('[data-tabs-target]');
                const tabContents = document.querySelectorAll('[role="tabpanel"]');

                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        const target = document.querySelector(tab.dataset.tabsTarget);

                        tabContents.forEach(tabContent => {
                            tabContent.classList.add('hidden');
                        });

                        tabs.forEach(t => {
                            t.classList.remove('text-green-600', 'border-green-600');
                            t.classList.add('hover:text-gray-600', 'hover:border-gray-300',
                                'border-transparent');
                            t.setAttribute('aria-selected', false);
                        });

                        tab.classList.add('text-green-600', 'border-green-600');
                        tab.classList.remove('hover:text-gray-600', 'hover:border-gray-300',
                            'border-transparent');
                        tab.setAttribute('aria-selected', true);

                        target.classList.remove('hidden');
                    });
                });

                // Chart.js default settings
                Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
                Chart.defaults.color = '#6B7280';
                Chart.defaults.scale.grid.color = 'rgba(243, 244, 246, 0.6)';

                // Monthly Earnings Chart
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const monthlyData = @json($monthlyEarnings);

                const earningsCtx = document.getElementById('earningsChart').getContext('2d');
                const gradient = earningsCtx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(34, 197, 94, 0.25)');
                gradient.addColorStop(1, 'rgba(34, 197, 94, 0.02)');

                const earningsChart = new Chart(earningsCtx, {
                    type: 'line',
                    data: {
                        labels: monthNames,
                        datasets: [{
                            label: 'Monthly Revenue',
                            data: monthNames.map((_, index) => {
                                const monthData = monthlyData.find(d => d.month === index + 1);
                                return monthData ? monthData.total : 0;
                            }),
                            backgroundColor: gradient,
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(34, 197, 94)',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#111827',
                                bodyColor: '#4B5563',
                                borderColor: '#E5E7EB',
                                borderWidth: 1,
                                padding: 10,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return '₱' + context.raw.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => '₱' + value.toLocaleString()
                                }
                            }
                        }
                    }
                });

                // Product Sales Chart
                const productCtx = document.getElementById('productSalesChart').getContext('2d');
                const topProductsData = @json($topProducts);
                const productSalesChart = new Chart(productCtx, {
                    type: 'bar',
                    data: {
                        labels: topProductsData.map(p => p.name),
                        datasets: [{
                            label: 'Units Sold',
                            data: topProductsData.map(p => p.units_sold),
                            backgroundColor: [
                                'rgba(34, 197, 94, 0.7)',
                                'rgba(59, 130, 246, 0.7)',
                                'rgba(139, 92, 246, 0.7)',
                                'rgba(245, 158, 11, 0.7)',
                                'rgba(239, 68, 68, 0.7)'
                            ],
                            borderColor: [
                                'rgb(34, 197, 94)',
                                'rgb(59, 130, 246)',
                                'rgb(139, 92, 246)',
                                'rgb(245, 158, 11)',
                                'rgb(239, 68, 68)'
                            ],
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#111827',
                                bodyColor: '#4B5563',
                                borderColor: '#E5E7EB',
                                borderWidth: 1,
                                padding: 10,
                                displayColors: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    display: true
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Sales Trend Chart
                const trendCtx = document.getElementById('salesTrendChart').getContext('2d');
                const trendGradient = trendCtx.createLinearGradient(0, 0, 0, 400);
                trendGradient.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
                trendGradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

                const weeklySalesTrendData = @json($weeklySalesTrend);
                const salesTrendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: weeklySalesTrendData.map(item => item.week),
                        datasets: [{
                            label: 'Weekly Sales',
                            data: weeklySalesTrendData.map(item => item.earnings),
                            backgroundColor: trendGradient,
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#111827',
                                bodyColor: '#4B5563',
                                borderColor: '#E5E7EB',
                                borderWidth: 1,
                                padding: 10,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return '₱' + context.raw.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => '₱' + value.toLocaleString()
                                }
                            }
                        }
                    }
                });

                // Period selector event listener
                document.getElementById('period-selector').addEventListener('change', function() {
                    const value = this.value;

                    // Add loading state to charts
                    document.getElementById('earningsChart').parentElement.classList.add('opacity-60');
                    document.getElementById('productSalesChart').parentElement.classList.add('opacity-60');
                    document.getElementById('salesTrendChart').parentElement.classList.add('opacity-60');

                    // Make AJAX request to get new data
                    fetch(`{{ route('distributors.insights.data') }}?period=${value}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Update total earnings card
                            document.querySelector('.border-green-500 .text-xl').textContent =
                                '₱' + parseFloat(data.totalEarnings).toLocaleString('en-PH', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                            // Update current month card
                            document.querySelector('.border-blue-500 .text-xl').textContent =
                                '₱' + parseFloat(data.currentMonthEarnings).toLocaleString('en-PH', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                            // Update average monthly card
                            document.querySelector('.border-purple-500 .text-xl').textContent =
                                '₱' + parseFloat(data.averageMonthlyEarnings).toLocaleString('en-PH', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });

                            // Update growth rate card
                            document.getElementById('growth-rate').textContent = data.growthRate;

                            // Update earnings chart
                            updateEarningsChart(earningsChart, data.monthlyEarnings);

                            // Update product sales chart
                            updateProductSalesChart(productSalesChart, data.topProducts);

                            // Update sales trend chart
                            updateSalesTrendChart(salesTrendChart, data.weeklySalesTrend);

                            // Reset loading state
                            document.getElementById('earningsChart').parentElement.classList.remove(
                                'opacity-60');
                            document.getElementById('productSalesChart').parentElement.classList.remove(
                                'opacity-60');
                            document.getElementById('salesTrendChart').parentElement.classList.remove(
                                'opacity-60');
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                            // Reset loading state and show error
                            document.getElementById('earningsChart').parentElement.classList.remove(
                                'opacity-60');
                            document.getElementById('productSalesChart').parentElement.classList.remove(
                                'opacity-60');
                            document.getElementById('salesTrendChart').parentElement.classList.remove(
                                'opacity-60');

                            // Show error toast
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to load insights data. Please try again later.',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        });
                });

                // Add these helper functions to update charts
                function updateEarningsChart(chart, monthlyData) {
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ];

                    // Group data by year-month
                    const groupedData = {};
                    monthlyData.forEach(item => {
                        const key = `${item.year}-${item.month}`;
                        groupedData[key] = item.total;
                    });

                    // Create dataset
                    const labels = [];
                    const values = [];

                    // For the last 12 months
                    for (let i = 11; i >= 0; i--) {
                        const date = new Date();
                        date.setMonth(date.getMonth() - i);
                        const year = date.getFullYear();
                        const month = date.getMonth() + 1;
                        const key = `${year}-${month}`;

                        labels.push(monthNames[month - 1]);
                        values.push(groupedData[key] || 0);
                    }

                    chart.data.labels = labels;
                    chart.data.datasets[0].data = values;
                    chart.update();
                }

                function updateProductSalesChart(chart, productsData) {
                    chart.data.labels = productsData.map(p => p.name);
                    chart.data.datasets[0].data = productsData.map(p => p.units_sold);
                    chart.update();
                }

                function updateSalesTrendChart(chart, trendData) {
                    chart.data.labels = trendData.map(item => item.week);
                    chart.data.datasets[0].data = trendData.map(item => item.earnings);
                    chart.update();
                }
            });
        </script>
    @endpush
</x-distributor-layout>
