<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold">Trucks Management</h2>
                        <a href="{{ route('distributors.trucks.create') }}" 
                           class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                            Add New Truck
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2">Plate Number</th>
                                    <th class="px-4 py-2">Location</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Active Deliveries</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trucks as $truck)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $truck->plate_number }}</td>
                                    <td class="px-4 py-2 border">{{ $truck->delivery_location ?? 'Not Yet Assigned' }}</td>
                                    <td class="px-4 py-2 border">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $truck->status === 'available' ? 'bg-green-100 text-green-800' : 
                                               ($truck->status === 'on_delivery' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $truck->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-center border">{{ $truck->deliveries_count }}</td>
                                    <td class="px-4 py-2 border">
                                        <div class="flex justify-around">
                                            <a href="{{ route('distributors.trucks.show', $truck) }}" 
                                               class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('distributors.trucks.edit', $truck) }}" 
                                               class="text-green-600 hover:text-green-900">Edit</a>
                                            <form action="{{ route('distributors.trucks.destroy', $truck) }}" 
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>