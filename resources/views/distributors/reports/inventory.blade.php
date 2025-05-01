<x-distributor-layout>
    <div class="container px-4 mx-auto">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Inventory Reports</h1>
        
        <!-- Filter and Export Controls -->
        <div class="flex flex-wrap items-center justify-between p-4 mb-6 bg-white rounded-lg shadow">
            <div class="mb-3 sm:mb-0">
                <form action="{{ route('distributors.reports.inventory') }}" method="GET" class="flex items-center space-x-2">
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
                <a href="{{ route('distributors.reports.inventory', ['period' => $period, 'export' => 'pdf']) }}" 
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
                <h3 class="text-sm font-medium text-gray-500">Total Stock In</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">{{ $inventory->where('type', 'in')->sum('quantity') }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
            
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Stock Out</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold text-gray-900">{{ $inventory->where('type', 'out')->sum('quantity') }}</div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
            
            <div class="p-6 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Net Inventory Change</h3>
                <div class="flex items-center mt-2">
                    <div class="text-2xl font-bold {{ $inventory->where('type', 'in')->sum('quantity') - $inventory->where('type', 'out')->sum('quantity') >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $inventory->where('type', 'in')->sum('quantity') - $inventory->where('type', 'out')->sum('quantity') }}
                    </div>
                </div>
                <div class="text-xs text-gray-500">{{ ucfirst($period) }} period</div>
            </div>
        </div>
        
        <!-- Chart Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Inventory Movement Trend</h2>
            <div class="relative h-80">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
        
        <!-- Inventory Details Table -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Inventory Movement Details</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Batch</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Quantity</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Supplier</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($inventory as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $movement->product->product_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $movement->batch ? $movement->batch->batch_number : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $movement->type == 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($movement->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $movement->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $movement->batch ? $movement->batch->supplier : 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $movement->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-sm text-center text-gray-500">No inventory movement records found</td>
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
            const ctx = document.getElementById('inventoryChart').getContext('2d');
            
            // Create gradients for better visual appearance
            const stockInGradient = ctx.createLinearGradient(0, 0, 0, 400);
            stockInGradient.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
            stockInGradient.addColorStop(1, 'rgba(16, 185, 129, 0.2)');
            
            const stockOutGradient = ctx.createLinearGradient(0, 0, 0, 400);
            stockOutGradient.addColorStop(0, 'rgba(239, 68, 68, 0.8)');
            stockOutGradient.addColorStop(1, 'rgba(239, 68, 68, 0.2)');
            
            // Chart configuration
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Stock In',
                            data: @json($chartData['stockIn']),
                            backgroundColor: stockInGradient,
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1,
                            borderRadius: 6,
                            barThickness: 16,
                            maxBarThickness: 20
                        },
                        {
                            label: 'Stock Out',
                            data: @json($chartData['stockOut']),
                            backgroundColor: stockOutGradient,
                            borderColor: 'rgb(239, 68, 68)',
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