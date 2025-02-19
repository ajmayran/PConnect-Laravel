<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <h1 class="mb-6 text-2xl font-bold">Business Insights</h1>

        <!-- Earnings Summary Card -->
        <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
            <h2 class="mb-4 text-xl font-semibold">Earnings Overview</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="p-4 text-center rounded-lg bg-gray-50">
                    <p class="text-gray-600">Total Earnings</p>
                    <p class="text-2xl font-bold text-green-600">₱{{ number_format($totalEarnings, 2) }}</p>
                </div>
                <div class="p-4 text-center rounded-lg bg-gray-50">
                    <p class="text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-green-600">
                        ₱{{ number_format($monthlyEarnings->where('month', date('n'))->first()?->total ?? 0, 2) }}
                    </p>
                </div>
                <div class="p-4 text-center rounded-lg bg-gray-50">
                    <p class="text-gray-600">Average Monthly</p>
                    <p class="text-2xl font-bold text-green-600">
                        ₱{{ number_format($monthlyEarnings->avg('total') ?? 0, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Monthly Earnings Chart -->
        <div class="p-6 mb-6 bg-white rounded-lg shadow-sm">
            <h2 class="mb-4 text-xl font-semibold">Monthly Earnings ({{ date('Y') }})</h2>
            <canvas id="earningsChart" height="100"></canvas>
        </div>

        <!-- Recent Earnings Table -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold">Recent Earnings</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left">Order ID</th>
                            <th class="px-6 py-3 text-left">Retailer</th>
                            <th class="px-6 py-3 text-left">Amount</th>
                            <th class="px-6 py-3 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentEarnings as $earning)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $earning->payment->order->formatted_order_id }}</td>
                                <td class="px-6 py-4">
                                    {{ $earning->payment->order->user->first_name }}
                                    {{ $earning->payment->order->user->last_name }}
                                </td>
                                <td class="px-6 py-4 text-green-600">₱{{ number_format($earning->amount, 2) }}</td>
                                <td class="px-6 py-4">{{ $earning->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No earnings recorded yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const monthlyData = @json($monthlyEarnings);
        
        const ctx = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Monthly Earnings (₱)',
                    data: monthNames.map((_, index) => {
                        const monthData = monthlyData.find(d => d.month === index + 1);
                        return monthData ? monthData.total : 0;
                    }),
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₱' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-distributor-layout>