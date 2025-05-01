<x-distributor-layout>
    <div class="container px-4 mx-auto">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Product Activity Reports</h1>

        <!-- Filter and Export Controls -->
        <div class="flex flex-wrap items-center justify-between p-4 mb-6 bg-white rounded-lg shadow">
            <div class="mb-3 sm:mb-0">
                <form action="{{ route('distributors.reports.products') }}" method="GET"
                    class="flex items-center space-x-2">
                    <label for="period" class="text-sm font-medium text-gray-700">Period:</label>
                    <select id="period" name="period" onchange="this.form.submit()"
                        class="px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </form>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('distributors.reports.products', ['period' => $period, 'export' => 'pdf']) }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700">
                    <i class="mr-1 bi bi-file-pdf"></i> Export PDF
                </a>
                <button onclick="window.print()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                    <i class="mr-1 bi bi-printer"></i> Print
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 md:grid-cols-4">
            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Products Created</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $products->where('action_type', 'created')->count() }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Products Removed</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $products->where('action_type', 'deleted')->count() }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Units Sold</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">{{ $products->sum('sold') }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Active Products</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">{{ $products->where('deleted_at', null)->count() }}
                    </div>
                </div>
                <div class="text-xs text-gray-500">Currently active</div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Product Activity Timeline</h2>
            <div class="relative h-80">
                <canvas id="productsChart"></canvas>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Product Activity Details</h2>

            <!-- Tabs for filtering -->
            <div class="flex mb-4 border-b">
                <button id="allTab" class="px-4 py-2 text-gray-600 border-b-2 border-blue-500">All</button>
                <button id="createdTab" class="px-4 py-2 text-gray-600">Created</button>
                <button id="deletedTab" class="px-4 py-2 text-gray-600">Removed</button>
                <button id="activeTab" class="px-4 py-2 text-gray-600">Active</button>
            </div>

            <div class="overflow-x-auto">   
                <table id="productTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Product</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Price</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Current Stock</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Date Added</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Units Sold</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Status</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 product-row" data-action="{{ $product['action_type'] }}"
                                data-status="{{ $product['deleted_at'] ? 'deleted' : 'active' }}">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $product['product_name'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">₱{{ number_format($product['price'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $product['current_stock'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $product['created_at'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $product['sold'] }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($product['deleted_at'])
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Removed</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($product['action_type'] == 'created')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Added</span>
                                    @elseif ($product['action_type'] == 'deleted')
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Removed</span>
                                    @else
                                        <span
                                            class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">Updated</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-sm text-center text-gray-500">No products found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Time-based Product Activity -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Product Activity by Date</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Date</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Products Added</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Products Removed</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Units Sold</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activityByDate as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $data['date'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $data['added'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $data['removed'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $data['sold'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500">No activity
                                    found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- Include Flowbite and Chart.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Chart initialization
                const ctx = document.getElementById('productsChart').getContext('2d');

                // Create gradients for better visual appearance
                const addedGradient = ctx.createLinearGradient(0, 0, 0, 400);
                addedGradient.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
                addedGradient.addColorStop(1, 'rgba(16, 185, 129, 0.2)');

                const removedGradient = ctx.createLinearGradient(0, 0, 0, 400);
                removedGradient.addColorStop(0, 'rgba(239, 68, 68, 0.8)');
                removedGradient.addColorStop(1, 'rgba(239, 68, 68, 0.2)');

                const soldGradient = ctx.createLinearGradient(0, 0, 0, 400);
                soldGradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
                soldGradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)');

                // Get data from PHP
                const dates = @json($chartData['dates'] ?? []);
                const addedData = @json($chartData['added'] ?? []);
                const removedData = @json($chartData['removed'] ?? []);
                const soldData = @json($chartData['sold'] ?? []);

                // Chart configuration
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: dates,
                        datasets: [{
                                label: 'Products Added',
                                data: addedData,
                                backgroundColor: addedGradient,
                                borderColor: 'rgb(16, 185, 129)',
                                borderWidth: 1,
                                borderRadius: 6,
                                barThickness: 16,
                                maxBarThickness: 20
                            },
                            {
                                label: 'Products Removed',
                                data: removedData,
                                backgroundColor: removedGradient,
                                borderColor: 'rgb(239, 68, 68)',
                                borderWidth: 1,
                                borderRadius: 6,
                                barThickness: 16,
                                maxBarThickness: 20
                            },
                            {
                                label: 'Units Sold',
                                data: soldData,
                                backgroundColor: soldGradient,
                                borderColor: 'rgb(59, 130, 246)',
                                borderWidth: 1,
                                borderRadius: 6,
                                barThickness: 16,
                                maxBarThickness: 20
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        family: "'Figtree', 'Segoe UI', 'Arial'",
                                        size: 12
                                    },
                                    color: '#6B7280'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(243, 244, 246, 1)',
                                    borderDash: [4, 4]
                                },
                                ticks: {
                                    precision: 0,
                                    font: {
                                        family: "'Figtree', 'Segoe UI', 'Arial'",
                                        size: 12
                                    },
                                    color: '#6B7280'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    boxWidth: 15,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 20,
                                    font: {
                                        family: "'Figtree', 'Segoe UI', 'Arial'",
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#111827',
                                bodyColor: '#4B5563',
                                borderColor: '#E5E7EB',
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 6,
                                displayColors: true,
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                callbacks: {
                                    title: function(context) {
                                        return context[0].label;
                                    },
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y;
                                    }
                                }
                            }
                        }
                    }
                });

                // Table filtering functionality
                const allTab = document.getElementById('allTab');
                const createdTab = document.getElementById('createdTab');
                const deletedTab = document.getElementById('deletedTab');
                const activeTab = document.getElementById('activeTab');
                const productRows = document.querySelectorAll('.product-row');

                function setActiveTab(tab) {
                    // Remove active class from all tabs
                    [allTab, createdTab, deletedTab, activeTab].forEach(t => {
                        t.classList.remove('border-blue-500');
                        t.classList.add('text-gray-600');
                    });

                    // Add active class to selected tab
                    tab.classList.add('border-blue-500');
                    tab.classList.add('text-blue-600');
                }

                function filterTable(action = null, status = null) {
                    productRows.forEach(row => {
                        const rowAction = row.getAttribute('data-action');
                        const rowStatus = row.getAttribute('data-status');

                        if (
                            (action === null || rowAction === action) &&
                            (status === null || rowStatus === status)
                        ) {
                            row.classList.remove('hidden');
                        } else {
                            row.classList.add('hidden');
                        }
                    });
                }

                allTab.addEventListener('click', function() {
                    setActiveTab(this);
                    filterTable();
                });

                createdTab.addEventListener('click', function() {
                    setActiveTab(this);
                    filterTable('created');
                });

                deletedTab.addEventListener('click', function() {
                    setActiveTab(this);
                    filterTable('deleted');
                });

                activeTab.addEventListener('click', function() {
                    setActiveTab(this);
                    filterTable(null, 'active');
                });
            });
        </script>
    @endpush

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .container,
            .container * {
                visibility: visible;
            }

            .container {
                position: absolute;
                left: 0;
                top: 0;
            }

            .shadow {
                box-shadow: none !important;
            }

            button,
            select,
            a[href],
            form {
                display: none !important;
            }
        }

        /* Flowbite-inspired chart styles */
        canvas {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</x-distributor-layout>
