<x-distributor-layout>
    <div class="container p-4 mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Create New Discount</h1>
        </div>

        <div class="p-6 bg-white rounded-lg shadow-md">
            <form action="{{ route('distributors.discounts.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Discount Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Discount Code (Optional)</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Discount Type</label>
                        <select name="type" id="type" required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage Off</option>
                            <option value="freebie" {{ old('type') == 'freebie' ? 'selected' : '' }}>Buy X Get Y Free</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="percentage-fields {{ old('type') == 'freebie' ? 'hidden' : '' }}">
                        <label for="percentage" class="block text-sm font-medium text-gray-700">Percentage Off (%)</label>
                        <input type="number" name="percentage" id="percentage" value="{{ old('percentage') }}" min="1" max="100"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @error('percentage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="freebie-fields {{ old('type') == 'freebie' ? '' : 'hidden' }}">
                        <label for="buy_quantity" class="block text-sm font-medium text-gray-700">Buy Quantity</label>
                        <input type="number" name="buy_quantity" id="buy_quantity" value="{{ old('buy_quantity') }}" min="1"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @error('buy_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="freebie-fields {{ old('type') == 'freebie' ? '' : 'hidden' }}">
                        <label for="free_quantity" class="block text-sm font-medium text-gray-700">Free Quantity</label>
                        <input type="number" name="free_quantity" id="free_quantity" value="{{ old('free_quantity') }}" min="1"
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @error('free_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                               class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500" {{ old('is_active') ? 'checked' : '' }}>
                            <label for="is_active" class="block ml-2 text-sm text-gray-700">Active Discount</label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-700">Apply Discount to Products</h3>
                    <p class="text-sm text-gray-500">Select products that this discount will apply to.</p>
                    
                    <div class="p-4 mt-4 space-y-4 overflow-y-auto border border-gray-200 rounded-md max-h-96">
                        @foreach ($products as $product)
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="product_ids[]" id="product_{{ $product->id }}" value="{{ $product->id }}" 
                                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                           {{ is_array(old('product_ids')) && in_array($product->id, old('product_ids')) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="product_{{ $product->id }}" class="font-medium text-gray-700">{{ $product->product_name }}</label>
                                    <p class="text-gray-500">Price: ₱{{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('product_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end mt-8 space-x-3">
                    <a href="{{ route('distributors.discounts.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Create Discount
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const percentageFields = document.querySelectorAll('.percentage-fields');
            const freebieFields = document.querySelectorAll('.freebie-fields');
            
            typeSelect.addEventListener('change', function() {
                if (this.value === 'percentage') {
                    percentageFields.forEach(field => field.classList.remove('hidden'));
                    freebieFields.forEach(field => field.classList.add('hidden'));
                } else {
                    percentageFields.forEach(field => field.classList.add('hidden'));
                    freebieFields.forEach(field => field.classList.remove('hidden'));
                }
            });
        });
    </script>
</x-distributor-layout>