<x-app-layout>
    <div class="container px-4 py-8 mx-auto">
        <div class="max-w-3xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Edit Product</h1>
                <a href="{{ route('distributors.products.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-600 transition duration-200 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Back to Products
                </a>
            </div>

            <div class="p-6 bg-white rounded-lg shadow">
                <form action="{{ route('distributors.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label for="product_name" class="block mb-2 text-sm font-medium text-gray-700">Product Name</label>
                        <input type="text" name="product_name" id="product_name" 
                               value="{{ old('product_name', $product->product_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('product_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="price" class="block mb-2 text-sm font-medium text-gray-700">Price</label>
                            <input type="number" name="price" id="price" step="0.01"
                                   value="{{ old('price', $product->price) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('price')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="stock_quantity" class="block mb-2 text-sm font-medium text-gray-700">Stock Quantity</label>
                            <input type="number" name="stock_quantity" id="stock_quantity"
                                   value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('stock_quantity')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="minimum_purchase_qty" class="block mb-2 text-sm font-medium text-gray-700">Minimum Purchase Quantity</label>
                            <input type="number" name="minimum_purchase_qty" id="minimum_purchase_qty"
                                   value="{{ old('minimum_purchase_qty', $product->minimum_purchase_qty) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('minimum_purchase_qty')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_id" class="block mb-2 text-sm font-medium text-gray-700">Category</label>
                            <select name="category_id" id="category_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $category->id == $product->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="image" class="block mb-2 text-sm font-medium text-gray-700">Product Image</label>
                        @if($product->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="Current product image"
                                     class="object-cover w-32 h-32 rounded-lg">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('image')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="submit" 
                                class="px-6 py-2 text-sm font-medium text-white transition duration-200 bg-blue-500 rounded-lg hover:bg-blue-600">
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>