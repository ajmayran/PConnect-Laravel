<x-distributor-layout>
    <div class="container px-4 mx-auto">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Delivery Reports</h1>
        
        <!-- Filter and Export Controls -->
        <div class="flex flex-wrap items-center justify-between p-4 mb-6 bg-white rounded-lg shadow">
            <div class="mb-3 sm:mb-0">
                <form action="{{ route('distributors.reports.delivery') }}" method="GET" class="flex items-center space-x-2">
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
                <a href="{{ route('distributors.reports.delivery', ['period' => $period, 'export' => 'pdf']) }}" 
                   class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md shadow-sm hover:bg-red-700">
                    <i class="mr-1 bi bi-file-pdf"></i> Export PDF
                </a>
                <button onclick="window.print()" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700">
                    <i class="mr-1 bi bi-printer"></i> Print
                </button>
            </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Trucks</h3>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $trucks->count() }}</div>
            </div>
            
            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Total Deliveries</h3>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $trucks->sum(function($truck) { return $truck->deliveries->count(); }) }}</div>
            </div>
            
            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Out for Delivery</h3>
                <div class="mt-2 text-3xl font-bold text-yellow-600">{{ $trucks->sum(function($truck) { return $truck->deliveries->where('status', 'out_for_delivery')->count(); }) }}</div>
            </div>
            
            <div class="p-4 bg-white rounded-lg shadow">
                <h3 class="text-sm font-medium text-gray-500">Completed Deliveries</h3>
                <div class="mt-2 text-3xl font-bold text-green-600">{{ $trucks->sum(function($truck) { return $truck->deliveries->where('status', 'delivered')->count(); }) }}</div>
            </div>
        </div>
        
        <!-- Chart Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">Delivery Status by Truck</h2>
            <div class="relative h-80">
                <canvas id="deliveryChart"></canvas>
            </div>
        </div>
        
        <!-- Out for Delivery Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Out for Delivery
                </span>
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Truck</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Order ID</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Est. Delivery</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $outForDeliveryCount = 0;
                        @endphp
                        
                        @foreach($trucks as $truck)
                            @php 
                                $outForDeliveries = $truck->deliveries->where('status', 'out_for_delivery');
                                $outForDeliveryCount += $outForDeliveries->count();
                            @endphp
                            
                            @foreach($outForDeliveries as $delivery)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $truck->plate_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $delivery->order->formatted_order_id ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $delivery->order->user->first_name ?? '' }} {{ $delivery->order->user->last_name ?? '' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($delivery->created_at)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $delivery->estimated_delivery ? \Carbon\Carbon::parse($delivery->estimated_delivery)->format('M d, Y') : 'Not specified' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        
                        @if($outForDeliveryCount == 0)
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-sm text-center text-gray-500">No deliveries currently out for delivery</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Delivered Section -->
        <div class="p-4 mb-6 bg-white rounded-lg shadow">
            <h2 class="mb-4 text-xl font-medium text-gray-700">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Delivered
                </span>
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Truck</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Order ID</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Delivery Date</th>
                            <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Payment Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $deliveredCount = 0;
                        @endphp
                        
                        @foreach($trucks as $truck)
                            @php 
                                $deliveredItems = $truck->deliveries->where('status', 'delivered'); 
                                $deliveredCount += $deliveredItems->count();
                            @endphp
                            
                            @foreach($deliveredItems as $delivery)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $truck->plate_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $delivery->order->formatted_order_id ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $delivery->order->user->first_name ?? '' }} {{ $delivery->order->user->last_name ?? '' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($delivery->updated_at)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($delivery->order && $delivery->order->payment)
                                            @if($delivery->order->payment->payment_status === 'paid')
                                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Paid</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Unpaid</span>
                                            @endif
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">No Data</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        
                        @if($deliveredCount == 0)
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-sm text-center text-gray-500">No completed deliveries in this period</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Truck Details Section (Collapsed) -->
        <div class="mb-6">
            <div class="p-4 mb-2 bg-white rounded-lg shadow collapse-header" onclick="toggleSection('truckDetails')">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-medium text-gray-700">Truck Details</h2>
                    <svg id="truckDetailsIcon" class="w-5 h-5 text-gray-500 transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
            
            <div id="truckDetails" class="hidden">
                @foreach($trucks as $truck)
                    <div class="p-4 mb-2 bg-white rounded-lg shadow">
                        <h3 class="mb-3 text-lg font-medium text-gray-700">{{ $truck->plate_number }}</h3>
                        
                        <div class="flex flex-wrap items-center gap-4 mb-4">
                            <div class="px-3 py-2 bg-blue-100 rounded-lg">
                                <p class="text-sm text-gray-600">Total Deliveries</p>
                                <p class="text-xl font-bold text-blue-700">{{ $truck->deliveries->count() }}</p>
                            </div>
                            <div class="px-3 py-2 bg-yellow-100 rounded-lg">
                                <p class="text-sm text-gray-600">Out for Delivery</p>
                                <p class="text-xl font-bold text-yellow-700">{{ $truck->deliveries->where('status', 'out_for_delivery')->count() }}</p>
                            </div>
                            <div class="px-3 py-2 bg-green-100 rounded-lg">
                                <p class="text-sm text-gray-600">Delivered</p>
                                <p class="text-xl font-bold text-green-700">{{ $truck->deliveries->where('status', 'delivered')->count() }}</p>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Order ID</th>
                                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Customer</th>
                                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($truck->deliveries as $delivery)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $delivery->order->formatted_order_id ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                {{ $delivery->order->user->first_name ?? '' }} {{ $delivery->order->user->last_name ?? '' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($delivery->status === 'delivered')
                                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Delivered</span>
                                                @elseif($delivery->status === 'out_for_delivery')
                                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Out for Delivery</span>
                                                @elseif($delivery->status === 'failed')
                                                    <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Failed</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ \Carbon\Carbon::parse($delivery->created_at)->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500">No deliveries found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Include Flowbite and Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Chart with Flowbite styling
            const ctx = document.getElementById('deliveryChart').getContext('2d');
            
            // Create gradient for better visual appearance
            const outForDeliveryGradient = ctx.createLinearGradient(0, 0, 0, 400);
            outForDeliveryGradient.addColorStop(0, 'rgba(245, 158, 11, 0.8)');
            outForDeliveryGradient.addColorStop(1, 'rgba(245, 158, 11, 0.2)');
            
            const deliveredGradient = ctx.createLinearGradient(0, 0, 0, 400);
            deliveredGradient.addColorStop(0, 'rgba(16, 185, 129, 0.8)');
            deliveredGradient.addColorStop(1, 'rgba(16, 185, 129, 0.2)');
            
            // Chart configuration
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [
                        {
                            label: 'Out for Delivery',
                            data: @json($chartData['outForDelivery']),
                            backgroundColor: outForDeliveryGradient,
                            borderColor: 'rgb(245, 158, 11)',
                            borderWidth: 1,
                            borderRadius: 6,
                            barThickness: 16,
                            maxBarThickness: 20
                        },
                        {
                            label: 'Delivered',
                            data: @json($chartData['delivered']),
                            backgroundColor: deliveredGradient,
                            borderColor: 'rgb(16, 185, 129)',
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
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        x: {
                            stacked: true,
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
                            stacked: true,
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
            
            // Add collapsible section functionality
            window.toggleSection = function(sectionId) {
                const section = document.getElementById(sectionId);
                const icon = document.getElementById(sectionId + 'Icon');
                
                if (section.classList.contains('hidden')) {
                    section.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                } else {
                    section.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            }
        });
    </script>
    @endpush

    <style>
        .collapse-header {
            cursor: pointer;
        }
        
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
            #truckDetails {
                display: block !important;
                visibility: visible;
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