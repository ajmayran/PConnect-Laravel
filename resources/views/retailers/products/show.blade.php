<x-app-layout>
    <x-dashboard-nav />

    <div class="py-4 sm:py-8 bg-gray-50">
        <div class="container px-4 mx-auto">
            <div class="mx-auto max-w-7xl">
                <!-- Back Button -->
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center mb-4 text-gray-600 sm:mb-6 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-1 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>

                <!-- Product Details -->
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="flex flex-col md:flex-row">
                        <!-- Product Image -->
                        <div class="w-full md:w-1/2">
                            <div class="relative pt-[75%] md:pt-0 md:h-full">
                                <img src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                    alt="{{ $product->product_name }}"
                                    class="absolute inset-0 object-contain w-full h-full p-4 md:object-cover md:p-0"
                                    onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="w-full p-4 sm:p-6 md:p-8 md:w-1/2">
                            <div class="mb-3 sm:mb-4">
                                <h1 class="mb-1 text-xl font-bold text-gray-900 sm:text-2xl md:text-3xl sm:mb-2">
                                    {{ $product->product_name }}</h1>
                                <p class="text-base text-gray-600 sm:text-lg">By
                                    {{ $product->distributor->company_name }}</p>
                            </div>

                            <div class="mb-4 sm:mb-6">
                                <h2 class="mb-2 text-3xl font-bold text-green-600 sm:text-4xl md:text-5xl">
                                    ₱{{ number_format($product->price, 2) }}</h2>
                                <p class="mb-4 text-sm text-gray-700 sm:text-base">{{ $product->description }}</p>
                            </div>

                            <!-- Product Categories -->
                            <div class="mb-4 sm:mb-6">
                                <h3 class="mb-2 text-sm font-medium text-gray-900">Category</h3>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 text-xs text-gray-800 bg-gray-100 rounded-full sm:text-sm">
                                        {{ $product->category->name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Add to Cart Form -->
                            <form id="productActionForm" class="mt-4 sm:mt-6">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="price" value="{{ $product->price }}">
                                <div class="flex flex-col justify-between gap-2 mb-4 sm:flex-row">
                                    <div class="text-sm font-medium text-gray-700">
                                        Stock Quantity: <span
                                            class="text-xl font-semibold text-green-600 sm:text-2xl md:text-3xl">{{ $product->stock_quantity }}</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-700">
                                        Min. Purchase: <span
                                            class="text-base font-semibold text-green-600 sm:text-lg">{{ $product->minimum_purchase_qty }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-center mb-4">
                                    <button type="button" onclick="decreaseQuantity()"
                                        class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded-full hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <input type="number" name="quantity" id="quantity"
                                        value="{{ $product->minimum_purchase_qty }}"
                                        min="{{ $product->minimum_purchase_qty }}"
                                        class="w-16 mx-3 text-center border-gray-300 rounded-md shadow-sm sm:w-20 focus:border-green-500 focus:ring-green-500">
                                    <button type="button" onclick="increaseQuantity()"
                                        class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded-full hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v12M6 12h12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                                    <button type="submit" id="addToCartBtn"
                                        class="w-full px-4 py-2 text-green-600 transition-colors duration-200 bg-green-200 border border-green-600 rounded-md sm:px-6 sm:py-3 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-100 focus:ring-offset-2">
                                        Add to Cart
                                    </button>
                                    <button type="button" id="buyNowBtn"
                                        class="w-full px-4 py-2 text-white transition-colors duration-200 bg-green-600 rounded-md sm:px-6 sm:py-3 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                        Buy Now
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Product -->
    <div class="py-8 bg-white sm:py-12">
        <div class="container px-4 mx-auto">
            <div class="mx-auto max-w-7xl">
                <h2 class="mb-4 text-xl font-bold text-gray-900 sm:mb-8 sm:text-2xl">Related Products</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 sm:gap-6">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div
                            class="overflow-hidden transition-shadow duration-300 bg-white rounded shadow hover:shadow-xl">
                            <a href="{{ route('retailers.products.show', $relatedProduct) }}" class="block h-full">
                                <div class="relative pt-[75%]">
                                    <img src="{{ $relatedProduct->image ? Storage::url($relatedProduct->image) : asset('img/default-product.jpg') }}"
                                        alt="{{ $relatedProduct->product_name }}"
                                        class="absolute inset-0 object-cover w-full h-full"
                                        onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                                </div>
                                <div class="p-3 sm:p-4">
                                    <h3 class="mb-1 text-base font-semibold text-gray-900 truncate sm:text-lg sm:mb-2">
                                        {{ $relatedProduct->product_name }}
                                    </h3>
                                    <p class="mb-2 text-xs text-gray-600 truncate sm:text-sm">
                                        {{ $relatedProduct->description }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold text-green-600 sm:text-xl">
                                            ₱{{ number_format($relatedProduct->price, 2) }}
                                        </span>
                                        <span class="text-xs text-gray-500 sm:text-sm">
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
        // Add to Cart button handler - unchanged
        document.getElementById('addToCartBtn').addEventListener('click', function(event) {
            event.preventDefault();

            if (!validateQuantity()) {
                return false;
            }

            // Get form data
            const formData = {
                product_id: {{ $product->id }},
                price: {{ $product->price }},
                quantity: parseInt(document.getElementById('quantity').value)
            };

            // Send the add to cart request
            fetch('{{ route('retailers.cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
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
                        throw new Error(data.message || 'Something went wrong');
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

        // Buy Now button handler 
        document.getElementById('buyNowBtn').addEventListener('click', function(event) {
            event.preventDefault();

            if (!validateQuantity()) {
                return false;
            }

            // Show confirmation dialog
            Swal.fire({
                title: 'Proceed to Checkout?',
                text: "You'll be redirected to complete your purchase.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Preparing your checkout. Please wait.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        timer: 4000, 
                        timerProgressBar: true,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Get form data
                    const formData = {
                        product_id: {{ $product->id }},
                        quantity: parseInt(document.getElementById('quantity').value)
                    };

                    // Direct checkout flow
                    fetch('{{ route('retailers.direct-purchase.buy-now') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Close loading animation and redirect
                                Swal.close();
                                // Redirect to direct checkout page
                                window.location.href = data.redirect_url;
                            } else {
                                throw new Error(data.message || 'Something went wrong');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: error.message || 'Failed to process request',
                                icon: 'error',
                                confirmButtonColor: '#EF4444'
                            });
                        });
                }
            });
        });
        // Existing quantity functions - unchanged
        function increaseQuantity() {
            var quantityInput = document.getElementById('quantity');
            var currentVal = parseInt(quantityInput.value);
            var maxStock = {{ $product->stock_quantity }};

            if (currentVal < maxStock) {
                quantityInput.value = currentVal + 1;
            } else {
                Swal.fire({
                    title: 'Maximum Stock Reached',
                    text: 'You cannot add more than the available stock quantity',
                    icon: 'warning',
                    confirmButtonColor: '#10B981',
                });
            }
        }

        function decreaseQuantity() {
            var quantityInput = document.getElementById('quantity');
            var minQty = {{ $product->minimum_purchase_qty }};

            if (parseInt(quantityInput.value) > minQty) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        }

        function validateQuantity() {
            var quantityInput = document.getElementById('quantity');
            var currentQty = parseInt(quantityInput.value);
            var minQty = {{ $product->minimum_purchase_qty }};
            var maxStock = {{ $product->stock_quantity }};

            if (currentQty < minQty) {
                Swal.fire({
                    title: 'Invalid Quantity',
                    text: 'Quantity cannot be less than the minimum purchase quantity of ' + minQty + '.',
                    icon: 'warning',
                    confirmButtonColor: '#10B981'
                });
                return false;
            }

            if (currentQty > maxStock) {
                Swal.fire({
                    title: 'Invalid Quantity',
                    text: 'Quantity cannot exceed the available stock of ' + maxStock + '.',
                    icon: 'warning',
                    confirmButtonColor: '#10B981'
                });
                return false;
            }

            if (isNaN(currentQty) || currentQty <= 0) {
                Swal.fire({
                    title: 'Invalid Quantity',
                    text: 'Please enter a valid quantity.',
                    icon: 'warning',
                    confirmButtonColor: '#10B981'
                });
                return false;
            }

            return true;
        }
    </script>

    <x-footer />
</x-app-layout>
