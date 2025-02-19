<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold">Edit Truck</h2>
                    </div>

                    <form action="{{ route('distributors.trucks.update', $truck) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="plate_number" class="block mb-2 text-sm font-bold text-gray-700">
                                Plate Number
                            </label>
                            <input type="text" name="plate_number" id="plate_number" 
                                   class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                   required value="{{ old('plate_number', $truck->plate_number) }}">
                            @error('plate_number')
                                <p class="mt-1 text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="delivery_location" class="block mb-2 text-sm font-bold text-gray-700">
                                Delivery Location
                            </label>
                            <input type="text" name="delivery_location" id="delivery_location" 
                                   class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                   value="{{ old('delivery_location', $truck->delivery_location) }}">
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block mb-2 text-sm font-bold text-gray-700">
                                Status
                            </label>
                            <select name="status" id="status" 
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                                <option value="available" {{ $truck->status === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="on_delivery" {{ $truck->status === 'on_delivery' ? 'selected' : '' }}>On Delivery</option>
                                <option value="maintenance" {{ $truck->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" 
                                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
                                Update Truck
                            </button>
                            <a href="{{ route('distributors.trucks.index') }}" 
                               class="text-gray-600 hover:text-gray-800">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>