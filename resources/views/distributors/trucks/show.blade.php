
<x-distributor-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Truck Info Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold">Truck Details</h2>
                            <p class="mt-1 text-gray-600">Plate Number: {{ $truck->plate_number }}</p>
                            <p class="mt-1 text-gray-600">Location: {{ $truck->delivery_location ?? 'Not Assigned' }}</p>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $truck->status === 'available' ? 'bg-green-100 text-green-800' : 
                                   ($truck->status === 'on_delivery' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $truck->status)) }}
                            </span>
                        </div>
                        <a href="{{ route('distributors.trucks.index') }}" 
                           class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700">
                            Back to Trucks
                        </a>
                    </div>

                    <!-- Deliveries Table -->
                    <div class="mt-8">
                        <h3 class="mb-4 text-xl font-semibold">Delivery History</h3>
                        @if($deliveries->isEmpty())
                            <div class="p-4 text-center bg-gray-50">
                                <p class="text-gray-600">No deliveries found for this truck.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tracking Number</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Customer</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Delivery Address</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Started At</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($deliveries as $delivery)
                                            <tr>
                                                <td class="px-6 py-4">{{ $delivery->tracking_number }}</td>
                                                <td class="px-6 py-4">
                                                    {{ $delivery->order->user->first_name }} 
                                                    {{ $delivery->order->user->last_name }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    @if($delivery->order->orderDetails->isNotEmpty())
                                                        {{ $delivery->order->orderDetails->first()->delivery_address }}
                                                    @else
                                                        <span class="text-gray-400">No address provided</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                        {{ $delivery->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                           ($delivery->status === 'in_transit' ? 'bg-blue-100 text-blue-800' :
                                                           ($delivery->status === 'delivered' ? 'bg-green-100 text-green-800' :
                                                           'bg-red-100 text-red-800')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    @if($delivery->pivot && $delivery->pivot->started_at)
                                                        {{ \Carbon\Carbon::parse($delivery->pivot->started_at)->format('M d, Y H:i') }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-distributor-layout>