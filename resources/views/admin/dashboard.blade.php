<x-app-layout>
    <main class="w-full md:w-[calc(100%-256px)] md:ml-64 bg-gray-50 min-h-screen transition-all main">
        <!-- Mobile menu button -->
        <div class="fixed z-50 top-4 left-4 md:hidden">
            <button id="toggle-sidebar" class="p-2 text-white bg-green-600 rounded-md shadow-md hover:bg-green-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>

        <x-admin-sidebar />

        <!-- Dashboard Content -->
        <div class="p-6">
            <!-- Welcome Banner -->
            <div class="p-6 mb-6 text-white rounded-lg shadow-md bg-gradient-to-r from-green-400 to-blue-500">
                <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->first_name }}!</h2>
                <p class="mt-1 text-white/80">Here's what's happening with your platform today.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Active Retailers -->
                <div class="p-6 transition-all bg-white border border-gray-100 rounded-lg shadow-md hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mb-1 text-2xl font-bold">{{ $activeRetailersCount }}</div>
                            <div class="text-sm font-medium text-gray-500">Active Retailers</div>
                        </div>
                        <div class="p-3 text-white bg-blue-500 rounded-full">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Orders -->
                <div class="p-6 transition-all bg-white border border-gray-100 rounded-lg shadow-md hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mb-1 text-2xl font-bold">{{ $activeOrdersCount }}</div>
                            <div class="text-sm font-medium text-gray-500">Active Orders</div>
                        </div>
                        <div class="p-3 text-white bg-green-500 rounded-full">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="p-6 transition-all bg-white border border-gray-100 rounded-lg shadow-md hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mb-1 text-2xl font-bold">{{ $completedOrdersCount }}</div>
                            <div class="text-sm font-medium text-gray-500">Completed Orders</div>
                        </div>
                        <div class="p-3 text-white bg-purple-500 rounded-full">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="p-6 transition-all bg-white border border-gray-100 rounded-lg shadow-md hover:shadow-lg">
                    <a href="{{ route('admin.users.index') }}" class="block">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="mb-1 text-2xl font-bold">{{ $totalUsersCount }}</div>
                                <div class="text-sm font-medium text-gray-500">Total Users</div>
                            </div>
                            <div class="p-3 text-white rounded-full bg-amber-500">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                <!-- Order Statistics Chart -->
                <div class="p-6 bg-white border border-gray-100 rounded-lg shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-700">Order Statistics</h3>
                        <select id="order-period"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="7">Last 7 days</option>
                            <option value="30" selected>Last 30 days</option>
                            <option value="90">Last 90 days</option>
                        </select>
                    </div>
                    <div class="relative" style="height: 320px;">
                        <canvas id="order-chart"></canvas>
                    </div>
                </div>

                <!-- User Activity -->
                <div class="p-6 bg-white border border-gray-100 rounded-lg shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-700">Recent Support Tickets</h3>
                        <a href="{{ route('admin.tickets.index') }}" class="text-sm text-blue-600 hover:underline">View
                            All</a>
                    </div>
                    <div class="overflow-hidden overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Subject</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        User</th>
                                    <th scope="col"
                                        class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentTickets ?? [] as $ticket)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ Str::limit($ticket->subject, 30) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $ticket->user->first_name }}
                                            {{ $ticket->user->last_name }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold {{ $ticket->status === 'open' ? 'text-green-800 bg-green-100' : ($ticket->status === 'closed' ? 'text-gray-800 bg-gray-100' : 'text-blue-800 bg-blue-100') }} rounded-full">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-sm text-center text-gray-500">No
                                            recent tickets found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reports Section -->
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div class="p-6 bg-white border border-gray-100 rounded-lg shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-700">Recent Reports</h3>
                        <div>
                            <button id="retailer-report-btn"
                                class="px-4 py-2 mr-2 text-white bg-green-500 rounded-md hover:bg-green-600">Retailer
                                Reports</button>
                            <button id="distributor-report-btn"
                                class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">Distributor
                                Reports</button>
                        </div>
                    </div>

                    <!-- Report Charts Container -->
                    <div class="relative" style="height: 300px;">
                        <canvas id="reports-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Admin Dashboard Scripts -->
    @vite(['resources/js/admin_dash.js'])


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle-sidebar');
            const closeSidebarBtn = document.getElementById('close-sidebar');
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', openSidebar);
            }

            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', closeSidebar);
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }

            // Close sidebar when resizing to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) { // 'md' breakpoint
                    backdrop.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });


            // Chart.js Configuration
            Chart.defaults.font.family = "'Figtree', system-ui, sans-serif";
            Chart.defaults.color = '#6B7280';

            // Order Statistics Chart
            const orderCtx = document.getElementById('order-chart').getContext('2d');
            const orderChart = new Chart(orderCtx, {
                type: 'line',
                data: {
                    labels: @json($chartDates),
                    datasets: [{
                            label: 'Active Orders',
                            data: @json($activeOrdersChartData),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Completed Orders',
                            data: @json($completedOrdersChartData),
                            borderColor: 'rgb(168, 85, 247)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Canceled Orders',
                            data: @json($canceledOrdersChartData),
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 6
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#111827',
                            bodyColor: '#4B5563',
                            borderColor: '#E5E7EB',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            usePointStyle: true
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 0,
                                maxTicksLimit: 7
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Reports Chart
            const reportsCtx = document.getElementById('reports-chart').getContext('2d');
            const reportsChart = new Chart(reportsCtx, {
                type: 'bar',
                data: {
                    labels: @json($retailerReportLabels),
                    datasets: [{
                        label: 'Retailer Reports',
                        data: @json($retailerReportData),
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1,
                        borderRadius: 4
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
                            padding: 10
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
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Toggle report data
            document.getElementById('retailer-report-btn').addEventListener('click', function() {
                reportsChart.data.datasets[0].label = 'Retailer Reports';
                reportsChart.data.datasets[0].backgroundColor = 'rgba(34, 197, 94, 0.7)';
                reportsChart.data.datasets[0].borderColor = 'rgb(34, 197, 94)';
                reportsChart.data.labels = @json($retailerReportLabels);
                reportsChart.data.datasets[0].data = @json($retailerReportData);
                reportsChart.update();

                this.classList.add('bg-green-600');
                this.classList.remove('bg-green-500');
                document.getElementById('distributor-report-btn').classList.remove('bg-blue-600');
                document.getElementById('distributor-report-btn').classList.add('bg-blue-500');
            });

            document.getElementById('distributor-report-btn').addEventListener('click', function() {
                reportsChart.data.datasets[0].label = 'Distributor Reports';
                reportsChart.data.datasets[0].backgroundColor = 'rgba(59, 130, 246, 0.7)';
                reportsChart.data.datasets[0].borderColor = 'rgb(59, 130, 246)';
                reportsChart.data.labels = @json($distributorReportLabels);
                reportsChart.data.datasets[0].data = @json($distributorReportData);
                reportsChart.update();

                this.classList.add('bg-blue-600');
                this.classList.remove('bg-blue-500');
                document.getElementById('retailer-report-btn').classList.remove('bg-green-600');
                document.getElementById('retailer-report-btn').classList.add('bg-green-500');
            });

            // Order period selector
            document.getElementById('order-period').addEventListener('change', function() {
                const days = parseInt(this.value);

                // Add loading animation
                const chartContainer = document.getElementById('order-chart').parentElement;
                chartContainer.classList.add('opacity-50');

                // Fetch new data from backend
                fetch(`{{ route('admin.dashboard.chart-data') }}?days=${days}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update chart with new data
                        orderChart.data.labels = data.dates;
                        orderChart.data.datasets[0].data = data.activeOrders;
                        orderChart.data.datasets[1].data = data.completedOrders;
                        orderChart.data.datasets[2].data = data.canceledOrders;
                        orderChart.update();

                        // Remove loading animation
                        chartContainer.classList.remove('opacity-50');
                    })
                    .catch(error => {
                        console.error('Error fetching chart data:', error);
                        chartContainer.classList.remove('opacity-50');
                    });
            });
        });
    </script>
</x-app-layout>
