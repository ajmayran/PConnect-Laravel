<x-distributor-layout>
    <div class="container px-4 mx-auto">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Revenue Reports</h1>
        
        <!-- Filter and Export Controls -->
        <div class="flex flex-wrap items-center justify-between p-4 mb-6 bg-white rounded-lg shadow">
            <div class="mb-3 sm:mb-0">
                <form action="{{ route('distributors.reports.revenue') }}" method="GET" class="flex items-center space-x-2">
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
                <a href="{{ route('distributors.reports.revenue', ['period' => $period, 'export' => 'pdf']) }}" 
                   class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700">
                    <i class="mr-1 bi bi-file-pdf"></i> Export PDF
                </a>
                <button onclick="window.print()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                    <i class="mr-1 bi bi-printer"></i> Print
                </button>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-3">
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">₱{{ number_format($totalRevenue, 2) }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
            
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Orders</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">{{ $revenueData->sum('orders_count') }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
            
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Avg. Revenue per Order</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">
                        @if($revenueData->sum('orders_count') > 0)
                            ₱{{ number_format($totalRevenue / $revenueData->sum('orders_count'), 2) }}
                        @else
                            ₱0.00
                        @endif
                    </div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
        </div>
        
        <!-- Chart Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Revenue Trend</h2>
            <div class="relative h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        
        <!-- Top Products Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Top Selling Products</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Units Sold</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topProducts as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $product->product_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">₱{{ number_format($product->price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $product->total_sold }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">₱{{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500">No products sold</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Daily Revenue Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Daily Revenue Breakdown</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Orders</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($revenueData as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $data->orders_count }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">₱{{ number_format($data->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm text-center text-gray-500">No revenue data available</td>
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
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            // Create gradient for better visual appearance
            const revenueGradient = ctx.createLinearGradient(0, 0, 0, 400);
            revenueGradient.addColorStop(0, 'rgba(16, 185, 129, 0.7)');
            revenueGradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'Revenue',
                        data: @json($chartData['values']),
                        backgroundColor: revenueGradient,
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: 'rgb(16, 185, 129)',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#ffffff',
                        pointHoverBorderColor: 'rgb(16, 185, 129)',
                        pointHoverBorderWidth: 3
                    }]
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
                                font: {
                                    family: "'Figtree', 'Segoe UI', 'Arial'",
                                    size: 12
                                },
                                color: '#6B7280',
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
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
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ₱${value.toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })}`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .container, .container * {
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
            button, select, a[href], form {
                display: none !important;
            }
        }
        
        /* Flowbite-inspired chart styles */
        canvas {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</x-distributor-layout>