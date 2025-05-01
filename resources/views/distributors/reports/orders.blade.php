<x-distributor-layout>
    <div class="container px-4 mx-auto">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Order Reports</h1>
        
        <!-- Filter and Export Controls -->
        <div class="flex flex-wrap items-center justify-between p-4 mb-6 bg-white rounded-lg shadow">
            <div class="mb-3 sm:mb-0">
                <form action="{{ route('distributors.reports.orders') }}" method="GET" class="flex items-center space-x-2">
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
                <a href="{{ route('distributors.reports.orders', ['period' => $period, 'export' => 'pdf']) }}" 
                   class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700">
                    <i class="mr-1 bi bi-file-pdf"></i> Export PDF
                </a>
                <button onclick="window.print()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                    <i class="mr-1 bi bi-printer"></i> Print
                </button>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-4">
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Orders</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">{{ $orders->count() }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
            
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Completed Orders</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-green-600">{{ $orders->where('status', 'completed')->count() }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
            
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Cancelled Orders</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-red-600">{{ $orders->where('status', 'cancelled')->count() }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
            
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Returned/Refunded</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-indigo-600">{{ $orders->filter(function($order) {
                        return $order->returnRequests && $order->returnRequests->where('status', 'approved')->count() > 0 || 
                               ($order->payment && $order->payment->payment_status === 'refunded');
                    })->count() }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
            <!-- Pie Chart Section -->
            <div class="p-4 bg-white rounded-lg shadow">
                <h2 class="mb-4 text-xl font-medium text-gray-700">Order Status Distribution</h2>
                <div class="relative h-80">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
            
            <!-- Trends Chart Section -->
            <div class="p-4 bg-white rounded-lg shadow">
                <h2 class="mb-4 text-xl font-medium text-gray-700">Order Trends</h2>
                <div class="relative h-80">
                    <canvas id="ordersTrendChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Orders Table -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Order Details</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Order ID</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Items</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Payment</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $order->formatted_order_id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $order->user->first_name ?? 'Unknown' }} {{ $order->user->last_name ?? '' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $order->orderDetails->count() }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">₱{{ number_format($order->orderDetails->sum('subtotal'), 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($order->status === 'completed')
                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Completed</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Cancelled</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">{{ ucfirst($order->status) }}</span>
                                    @endif

                                    @if($order->returnRequests && $order->returnRequests->where('status', 'approved')->count() > 0)
                                        <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Returned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($order->payment && $order->payment->payment_status === 'refunded')
                                        <span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-100 rounded-full">Refunded</span>
                                    @elseif($order->payment && $order->payment->payment_status === 'paid')
                                        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Paid</span>
                                    @else
                                        {{ $order->payment ? ucfirst($order->payment->payment_status) : 'N/A' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-sm text-center text-gray-500">No orders found</td>
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
            // Pie Chart - Order Status Distribution
            const pieCtx = document.getElementById('ordersChart').getContext('2d');
            
            // Enhanced colors with better transparency and matching the application theme
            const pieColors = {
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',    // Completed - Green
                    'rgba(245, 158, 11, 0.8)',    // Returned - Yellow
                    'rgba(139, 92, 246, 0.8)',    // Refunded - Purple
                    'rgba(239, 68, 68, 0.8)',     // Cancelled - Red
                ],
                borderColor: [
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)', 
                    'rgb(139, 92, 246)',
                    'rgb(239, 68, 68)',
                ],
                hoverBackgroundColor: [
                    'rgba(16, 185, 129, 0.9)',
                    'rgba(245, 158, 11, 0.9)',
                    'rgba(139, 92, 246, 0.9)',
                    'rgba(239, 68, 68, 0.9)',
                ]
            };
            
            // Pie Chart Configuration
            const ordersChart = new Chart(pieCtx, {
                type: 'doughnut', // Changed to doughnut for a more modern look
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        data: @json($chartData['values']),
                        backgroundColor: pieColors.backgroundColor,
                        borderColor: pieColors.borderColor,
                        borderWidth: 1,
                        hoverBackgroundColor: pieColors.hoverBackgroundColor,
                        hoverBorderWidth: 2,
                        borderRadius: 4, // Rounded segments
                        spacing: 2     // Small spacing between segments for better visual
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15,
                                font: {
                                    family: "'Figtree', 'Segoe UI', 'Arial'",
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
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
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Line Chart - Order Trends
            const lineCtx = document.getElementById('ordersTrendChart').getContext('2d');
            
            // Create gradients for better visual appearance
            const completedGradient = lineCtx.createLinearGradient(0, 0, 0, 400);
            completedGradient.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
            completedGradient.addColorStop(1, 'rgba(16, 185, 129, 0.1)');
            
            const cancelledGradient = lineCtx.createLinearGradient(0, 0, 0, 400);
            cancelledGradient.addColorStop(0, 'rgba(239, 68, 68, 0.8)');
            cancelledGradient.addColorStop(1, 'rgba(239, 68, 68, 0.1)');
            
            // Line Chart Configuration
            const ordersTrendChart = new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: @json($trendData['dates'] ?? []),
                    datasets: [
                        {
                            label: 'Completed Orders',
                            data: @json($trendData['completed'] ?? []),
                            backgroundColor: completedGradient,
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgb(16, 185, 129)',
                            pointBorderColor: '#fff',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.3 // Smooth curve
                        },
                        {
                            label: 'Cancelled Orders',
                            data: @json($trendData['cancelled'] ?? []),
                            backgroundColor: cancelledGradient,
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgb(239, 68, 68)',
                            pointBorderColor: '#fff',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.3
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
                            usePointStyle: true
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