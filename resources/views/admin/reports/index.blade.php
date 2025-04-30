<!-- filepath: c:\Users\EMMAN\Documents\PConnect-Laravel\resources\views\admin\reports\index.blade.php -->
<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="mb-6 text-2xl font-semibold">System Reports</h1>

                    <!-- Filter tabs -->
                    <div class="flex mb-4 border-b">
                        <a href="{{ route('admin.reports.index', ['time_range' => '24_hours']) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $timeRange === '24_hours' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Last 24 Hours
                        </a>
                        <a href="{{ route('admin.reports.index', ['time_range' => '1_week']) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $timeRange === '1_week' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Last Week
                        </a>
                        <a href="{{ route('admin.reports.index', ['time_range' => '1_month']) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $timeRange === '1_month' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Last Month
                        </a>
                        <a href="{{ route('admin.reports.index', ['time_range' => '1_year']) }}"
                            class="px-4 py-2 -mb-px font-semibold {{ $timeRange === '1_year' ? 'text-blue-600 border-blue-600 border-b-2' : 'text-gray-600 border-transparent' }}">
                            Last Year
                        </a>
                    </div>

                    <!-- Reports Table -->
                    <div class="overflow-auto bg-white rounded-lg shadow">
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

                    <!-- Pagination Links -->
                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>

                    <!-- No reports message -->
                    @if ($reports->isEmpty())
                        <div class="mt-4 text-center">
                            <p class="text-gray-500">No reports available for the selected time range.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>