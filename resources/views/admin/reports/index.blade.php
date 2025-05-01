<x-app-layout>
    <div class="flex">
        {{-- Include the admin sidebar --}}
        @include('components.admin-sidebar')

        {{-- Main content area --}}
        <div class="flex-1 ml-64 p-4">
            @if (session('error'))
                <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-semibold mb-4">System Reports</h1>

                    <!-- Time Range Filters -->
                    <div class="flex mb-4 border-b">
                        <a href="{{ route('admin.reports.index', ['time_range' => '24_hours', 'view' => request('view', 'distributors')]) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $timeRange === '24_hours' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Last 24 Hours
                        </a>
                        <a href="{{ route('admin.reports.index', ['time_range' => '1_week', 'view' => request('view', 'distributors')]) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $timeRange === '1_week' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Last Week
                        </a>
                        <a href="{{ route('admin.reports.index', ['time_range' => '1_month', 'view' => request('view', 'distributors')]) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $timeRange === '1_month' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Last Month
                        </a>
                        <a href="{{ route('admin.reports.index', ['time_range' => '1_year', 'view' => request('view', 'distributors')]) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $timeRange === '1_year' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Last Year
                        </a>
                    </div>

                    <!-- Dropdown Filter -->
                    <div class="flex items-center justify-between mb-4">
                        <select id="reportFilter" class="px-4 py-2 border rounded" onchange="changeView(this.value)">
                            <option value="distributors" {{ request('view', 'distributors') === 'distributors' ? 'selected' : '' }}>Distributors</option>
                            <option value="subscriptions" {{ request('view', 'distributors') === 'subscriptions' ? 'selected' : '' }}>Subscriptions</option>
                        </select>

                        <!-- Dynamic Download Button -->
                        <div class="flex justify-end mt-4 space-x-4">
                            @if ($view === 'distributors')
                                <!-- Download Table PDF Button -->
                                <a href="{{ route('admin.reports.downloadPdf', ['time_range' => $timeRange]) }}"
                                   class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                                    Download Table PDF
                                </a>
                            @elseif ($view === 'subscriptions')
                                <!-- Download Graph PDF Button -->
                                <button id="downloadGraphButton" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                                    Download Graph PDF
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Distributors Table -->
                    <div id="distributorsTable" class="{{ $view === 'distributors' ? '' : 'hidden' }} overflow-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Distributor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products Sold</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($reports as $report)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $report->distributor->company_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $report->total_orders }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $report->total_products_sold }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${{ number_format($report->total_revenue, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Subscriptions Graph -->
                    <div id="subscriptionsGraph" class="{{ $view === 'subscriptions' ? '' : 'hidden' }}">
                        <div class="overflow-hidden rounded-lg shadow bg-white p-4">
                            <canvas id="subscriptionsChart"></canvas>
                        </div>
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <!-- Inline CSS -->
    <style>
        /* Ensure the graph container does not overflow */
        #subscriptionsGraph {
            overflow-x: hidden; /* Prevent horizontal overflow */
            max-width: 100%; /* Ensure the graph fits within the container */
        }

        /* Ensure the canvas scales properly */
        #subscriptionsChart {
            display: block;
            max-width: 100%; /* Ensure the chart does not exceed the container width */
            height: auto; /* Maintain aspect ratio */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reportFilter = document.getElementById('reportFilter');
            const distributorsTable = document.getElementById('distributorsTable');
            const subscriptionsGraph = document.getElementById('subscriptionsGraph');
            const downloadGraphButton = document.getElementById('downloadGraphButton');

            // Toggle between table and graph
            reportFilter.addEventListener('change', function () {
                const url = new URL(window.location.href);
                url.searchParams.set('view', this.value);
                window.location.href = url.toString();
            });

            // Render the subscriptions graph
            const ctx = document.getElementById('subscriptionsChart').getContext('2d');
            const subscriptionsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total Subscribers', 'Revenue'],
                    datasets: [{
                        label: 'Subscriptions Data',
                        data: [{{ $stats['totalSubscribers'] }}, {{ $stats['revenue'] }}],
                        backgroundColor: ['#4CAF50', '#2196F3'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Download the graph as a PDF
            if (downloadGraphButton) {
            downloadGraphButton.addEventListener('click', function () {
                const canvas = document.getElementById('subscriptionsChart');
                const imageData = canvas.toDataURL('image/png');


                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF();

                pdf.setFontSize(16);
                pdf.text('System Reports', 10, 20);

                pdf.setFontSize(12);
                const timeRange = document.querySelector('.text-blue-600').textContent; 
                pdf.text(`Time Range: ${timeRange}`, 10, 28); 

                pdf.addImage(imageData, 'PNG', 15, 40, 180, 100); 


                pdf.save('subscriptions_graph.pdf');
            });
            }
        });
    </script>
            <!-- Include Admin Dashboard Scripts -->
            @vite(['resources/js/admin_dash.js'])
</x-app-layout>