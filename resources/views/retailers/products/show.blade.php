<x-app-layout>
    <x-dashboard-nav />

    <div class="py-8 bg-gray-50">
        <div class="container px-4 mx-auto">
            <div class="mx-auto max-w-7xl">
                <!-- Back Button -->
                <a href="{{ url()->previous() }}" class="inline-flex items-center mb-6 text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>

                <!-- Product Details -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="md:flex">
                        <!-- Product Image -->
                        <div class="md:w-1/2">
                            <div class="aspect-w-4 aspect-h-3">
                                <img src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                    alt="{{ $product->product_name }}" class="object-cover w-full h-full"
                                    onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-8 md:w-1/2">
                            <div class="mb-4">
                                <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ $product->product_name }}</h1>
                                <p class="text-lg text-gray-600">By {{ $product->distributor->company_name }}</p>
                            </div>

                            <div class="mb-6">
                                <h2 class="mb-2 text-5xl font-bold text-green-600">
                                    ₱{{ number_format($product->price, 2) }}</h2>
                                <p class="mb-4 text-gray-700">{{ $product->description }}</p>
                            </div>

                            <!-- Product Categories -->
                            <div class="mb-6">
                                <h3 class="mb-2 text-sm font-medium text-gray-900">Category</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 text-sm text-gray-800 bg-gray-100 rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Add to Cart Form -->
                            <form id="addToCartForm" class="mt-6">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="flex justify-between mb-4">
                                    <label for="quantity" class="block mb-2 text-sm font-medium text-gray-700">
                                        Stock Quantity: <span
                                            class="text-3xl font-semibold text-green-600">{{ $product->stock_quantity }}</span>
                                    </label>
                                    <label for="quantity" class="block mb-2 text-sm font-medium text-gray-700">
                                        Minimum Purchase Quantity: <span
                                            class="text-lg font-semibold text-green-600">{{ $product->minimum_purchase_qty }}</span>
                                    </label>
                                </div>

                                <div class="flex items-center justify-center">
                                    <button type="button" onclick="decreaseQuantity()"
                                        class="px-3 py-1 m-2 bg-gray-300 rounded-md hover:bg-green-400">-</button>
                                    <input type="number" name="quantity" id="quantity"
                                        value="{{ $product->minimum_purchase_qty }}"
                                        min="{{ $product->minimum_purchase_qty }}"
                                        class="w-16 border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <button type="button" onclick="increaseQuantity()"
                                        class="px-3 py-1 m-2 bg-gray-300 rounded-md hover:bg-green-400">+</button>
                                </div>
                                <button type="submit"
                                    class="w-full px-6 py-3 mt-5 text-white transition-colors duration-200 bg-green-600 rounded-md hover:bg-green-700">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-12 bg-white">
        <div class="container px-4 mx-auto">
            <div class="mx-auto max-w-7xl">
                <h2 class="mb-8 text-2xl font-bold text-gray-900">Related Products</h2>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div
                            class="overflow-hidden transition-shadow duration-300 bg-white rounded shadow-lg hover:shadow-xl">
                            <a href="{{ route('retailers.products.show', $relatedProduct) }}">
                                <div class="relative h-48">
                                    <img src="{{ $relatedProduct->image ? Storage::url($relatedProduct->image) : asset('img/default-product.jpg') }}"
                                        alt="{{ $relatedProduct->product_name }}" class="object-cover w-full h-full"
                                        onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                                </div>
                                <div class="p-4">
                                    <h3 class="mb-2 text-lg font-semibold text-gray-900 truncate">
                                        {{ $relatedProduct->product_name }}
                                    </h3>
                                    <p class="mb-2 text-sm text-gray-600 truncate">
                                        {{ $relatedProduct->description }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xl font-bold text-green-600">
                                            ₱{{ number_format($relatedProduct->price, 2) }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            Stock: {{ $relatedProduct->stock_quantity }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('addToCartForm').addEventListener('submit', function(event) {
            event.preventDefault();

            if (!validateQuantity()) {
                return false;
            }

            const formData = {
                product_id: {{ $product->id }},
                quantity: parseInt(document.getElementById('quantity').value)
            };

            fetch('{{ route('retailers.cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                            timer: 2000
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Failed to add product to cart',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                });
        });

        function increaseQuantity() {
            var quantityInput = document.getElementById('quantity');
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }

        function decreaseQuantity() {
            var quantityInput = document.getElementById('quantity');
            if (quantityInput.value > {{ $product->minimum_purchase_qty }}) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        }

        function validateQuantity() {
            var quantityInput = document.getElementById('quantity');
            if (parseInt(quantityInput.value) < {{ $product->minimum_purchase_qty }}) {
                alert(
                    'Quantity cannot be less than the minimum purchase quantity of {{ $product->minimum_purchase_qty }}.'
                );
                return false;
            }
            return true;
        }

        function handleFormSubmit(event) {
            event.preventDefault();

            if (!validateQuantity()) {
                return false;
            }

            const form = event.target;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Item added to cart successfully',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                        timer: 2000
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong while adding to cart',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                });

            return false;
        }
    </script>

    <x-footer />
</x-app-layout>
