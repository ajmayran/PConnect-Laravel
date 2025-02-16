<x-distributor-layout>
    <div class="container p-4 mx-auto" style="height: 100vh;">
        <span class="absolute text-3xl text-white cursor-pointer top-5 left-4 lg:hidden" onclick="toggleSidebar()">
            <i class="px-2 bg-gray-900 rounded-md bi bi-filter-left"></i>
        </span>
        <div class="container p-4 mx-auto">
            <div class="flex flex-wrap justify-between">
                <h1 class="mb-6 text-2xl font-bold text-center text-gray-800 sm:text-3xl">Delivery Management</h1>
                <a href="{{ route('distributors.trucks.index') }}"
                    class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                    <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                    </svg>
                    Trucks
                </a>
            </div>

            @if ($deliveries->isEmpty())
                <div class="p-8 text-center bg-white rounded-lg shadow-sm">
                    <p class="text-gray-600 sm:text-lg">No deliveries found.</p>
                </div>
            @else
                <div class="overflow-x-auto bg-white rounded-lg shadow-sm">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Tracking Number</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Retailer</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Delivery Address</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Status</th>
                                <th class="px-4 py-3 font-medium text-left text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($deliveries as $delivery)
                                <tr>
                                    <td class="px-4 py-3">{{ $delivery->tracking_number }}</td>
                                    <td class="px-4 py-3">
                                        {{ $delivery->order->user->first_name }} {{ $delivery->order->user->last_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($delivery->order->orderDetails->isNotEmpty())
                                            {{ $delivery->order->orderDetails->first()->delivery_address }}
                                        @else
                                            <span class="text-gray-400">No address provided</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 text-sm rounded-full 
                                            @if ($delivery->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($delivery->status === 'in_transit') bg-blue-100 text-blue-800
                                            @elseif($delivery->status === 'out_for_delivery') bg-purple-100 text-purple-800
                                            @elseif($delivery->status === 'delivered') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border">
                                        @if ($delivery->status === 'pending')
                                            <button onclick="openAssignTruckModal({{ $delivery->id }})"
                                                class="px-3 py-1 text-sm font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                                Assign Truck
                                            </button>
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
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
    <div id="assignTruckModal" class="fixed inset-0 hidden w-full h-full overflow-y-auto bg-gray-600 bg-opacity-50">
        <div class="relative p-5 mx-auto bg-white border rounded-md shadow-lg top-20 w-96">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Assign Truck to Delivery</h3>
                <form id="assignTruckForm" method="POST" class="mt-2">
                    @csrf
                    <select name="truck_id" class="block w-full mt-2 border-gray-300 rounded-md shadow-sm">
                        @foreach ($availableTrucks as $truck)
                            <option value="{{ $truck->id }}">{{ $truck->plate_number }}</option>
                        @endforeach
                    </select>
                    <div class="items-center px-4 py-3">
                        <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md">Assign</button>
                        <button type="button" onclick="closeAssignTruckModal()"
                            class="px-4 py-2 text-white bg-gray-500 rounded-md">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAssignTruckModal(deliveryId) {
            const modal = document.getElementById('assignTruckModal');
            const form = document.getElementById('assignTruckForm');
            form.action = `/delivery/${deliveryId}/assign-truck`;
            modal.classList.remove('hidden');
        }

        function closeAssignTruckModal() {
            document.getElementById('assignTruckModal').classList.add('hidden');
        }
    </script>
</x-distributor-layout>
