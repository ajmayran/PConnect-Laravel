<a href="{{ route('retailers.products.show', $product->id) }}" class="flex flex-col h-full">
    <div class="relative w-full h-48 sm:h-40">
        <img src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
            alt="{{ $product->product_name }}"
            onerror="this.src='{{ asset('img/default-product.jpg') }}'"
            class="absolute inset-0 object-contain w-full h-full p-3">
    </div>
    <div class="flex flex-col flex-grow p-4">
        <h3 class="mb-2 text-sm font-bold text-gray-900 line-clamp-2">{{ $product->product_name }}
        </h3>
        <p class="text-xs text-gray-500">{{ $product->distributor->company_name }}</p>
        <div class="flex-grow"></div>
        <div class="mt-4">
            <p class="text-lg font-bold text-green-600">₱{{ number_format($product->price, 2) }}
            </p>
            <p class="text-xs text-gray-500">Stock: {{ $product->stock_quantity }}</p>
        </div>
    </div>
</a>