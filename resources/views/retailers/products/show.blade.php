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
                            <div class="relative pt-[100%] md:pt-0 md:h-full">
                                <img src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                    alt="{{ $product->product_name }}"
                                    class="absolute inset-0 object-contain w-full h-full p-4 transition-transform duration-300 hover:scale-105 touch-manipulation"
                                    onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="w-full p-4 sm:p-6 md:p-8 md:w-1/2">
                            <!-- Hidden Inputs for JavaScript -->
                            <input type="hidden" id="product-id" value="{{ $product->id }}">
                            <input type="hidden" id="product-price" value="{{ $product->price }}">
                            <input type="hidden" id="min-purchase-qty" value="{{ $product->minimum_purchase_qty }}">
                            <input type="hidden" id="max-stock" value="{{ $product->stock_quantity }}">

                            <div class="mb-3 sm:mb-4">
                                <h1 class="mb-1 text-xl font-bold text-gray-900 sm:text-2xl md:text-3xl sm:mb-2">
                                    {{ $product->product_name }}</h1>
                                <p class="text-sm text-gray-600 sm:text-base md:text-lg">
                                    By {{ $product->distributor->company_name }}</p>
                            </div>

                            <!-- Price and Description -->
                            <div class="mb-4 sm:mb-6">
                                <h2 class="mb-2 text-2xl font-bold text-green-600 sm:text-3xl md:text-4xl">
                                    ₱{{ number_format($product->price, 2) }}</h2>
                                <p class="mb-4 text-sm text-gray-700 sm:text-base">{{ $product->description }}</p>
                            </div>

                            <!-- Stock Information -->
                            <div class="mb-4 sm:mb-6">
                                <p class="text-sm text-gray-600 sm:text-base">
                                    Stock Available: <span class="font-semibold">{{ $product->stock_quantity }}</span>
                                </p>
                                <p class="text-sm text-gray-600 sm:text-base">
                                    Min. Purchase: <span
                                        class="font-semibold">{{ $product->minimum_purchase_qty }}</span>
                                </p>
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center mb-4 space-x-2 sm:mb-6">
                                <button onclick="decreaseQuantity()"
                                    class="px-3 py-1 text-lg font-bold text-green-600 border-2 border-green-600 rounded-lg hover:bg-green-600 hover:text-white active:bg-green-700 touch-manipulation">
                                    -
                                </button>
                                <input type="number" id="quantity" value="{{ $product->minimum_purchase_qty }}"
                                    min="{{ $product->minimum_purchase_qty }}"
                                    data-min-qty="{{ $product->minimum_purchase_qty }}"
                                    class="w-20 px-3 py-1 text-center border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    oninput="validateQuantity()">
                                <button onclick="increaseQuantity()"
                                    class="px-3 py-1 text-lg font-bold text-green-600 border-2 border-green-600 rounded-lg hover:bg-green-600 hover:text-white active:bg-green-700 touch-manipulation">
                                    +
                                </button>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
                                @if (!$product->distributor->accepting_orders)
                                    <div
                                        class="w-full p-4 text-center bg-yellow-100 border border-yellow-200 rounded-lg">
                                        <p class="flex items-center justify-center text-yellow-700">
                                            <i class="mr-2 bi bi-exclamation-triangle"></i>
                                            This distributor is currently not accepting new orders. Please check back
                                            later.
                                        </p>
                                    </div>
                                @else
                                    <button id="addToCartBtn" type="submit"
                                        class="px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600">
                                        Add to Cart
                                    </button>
                                    <button id="buyNowBtn"
                                        class="w-full px-4 py-2 text-sm font-medium text-green-600 bg-white border-2 border-green-600 rounded-lg sm:text-base hover:bg-green-600 hover:text-white active:bg-green-700 touch-manipulation">
                                        Buy Now
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    <div class="py-8 bg-white sm:py-12">
        <div class="container px-4 mx-auto">
            <div class="mx-auto max-w-7xl">
                <h2 class="mb-6 text-xl font-bold text-gray-800 sm:text-2xl">Recommended Products</h2>

                <div class="grid grid-cols-2 gap-3 sm:gap-6 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach ($relatedProducts as $product)
                        <a href="{{ route('retailers.products.show', $product->id) }}"
                            class="relative flex flex-col overflow-hidden transition-all duration-300 bg-white rounded-lg shadow-md group hover:shadow-xl">
                            <div class="relative w-full pt-[100%] overflow-hidden bg-gray-100">
                                <img class="absolute inset-0 object-contain w-full h-full p-2 transition-transform duration-300 group-hover:scale-110"
                                    src="{{ $product->image ? asset('storage/products/' . basename($product->image)) : asset('img/default-product.jpg') }}"
                                    alt="{{ $product->product_name }}"
                                    onerror="this.src='{{ asset('img/default-product.jpg') }}'">
                            </div>
                            <div class="flex flex-col flex-grow p-2 sm:p-4">
                                <h3 class="mb-1 text-sm font-bold text-gray-800 sm:mb-2 sm:text-lg line-clamp-2">
                                    {{ $product->product_name }}
                                </h3>
                                <p class="mt-1 text-xs text-gray-500 sm:text-sm line-clamp-1">
                                    {{ $product->distributor->company_name }}
                                </p>
                                <div class="flex items-center justify-between pt-2 mt-auto sm:pt-4">
                                    <span
                                        class="text-sm font-bold text-green-600 sm:text-lg">₱{{ number_format($product->price, 2) }}</span>
                                    <span class="text-xs text-gray-500 sm:text-sm">View Details →</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cache DOM elements
            const quantityInput = document.getElementById('quantity');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const buyNowBtn = document.getElementById('buyNowBtn');

            // Get values from hidden inputs
            const productId = parseInt(document.getElementById('product-id').value);
            const productPrice = parseFloat(document.getElementById('product-price').value);
            const minPurchaseQty = parseInt(document.getElementById('min-purchase-qty').value) || 1;
            const maxStock = parseInt(document.getElementById('max-stock').value);

            console.log("Initial Values: ", {
                minPurchaseQty,
                maxStock,
                productId,
                productPrice
            });

            // Ensure input has correct min value
            quantityInput.min = minPurchaseQty;
            quantityInput.value = minPurchaseQty;

            // ---------------------- QUANTITY CONTROL FUNCTIONS ----------------------

            function increaseQuantity() {
                let currentVal = parseInt(quantityInput.value);
                if (currentVal < maxStock) {
                    quantityInput.value = currentVal + 1;
                } else {
                    showWarning("Maximum Stock Reached", `You cannot add more than ${maxStock} items.`);
                }
            }

            function decreaseQuantity() {
                let currentVal = parseInt(quantityInput.value);
                if (currentVal > minPurchaseQty) {
                    quantityInput.value = currentVal - 1;
                } else {
                    showWarning("Minimum Purchase Limit", `You cannot have less than ${minPurchaseQty} items.`);
                }
            }

            function validateQuantity() {
                let currentQty = parseInt(quantityInput.value);

                if (isNaN(currentQty) || currentQty < minPurchaseQty) {
                    showWarning("Invalid Quantity", `Quantity cannot be less than ${minPurchaseQty}.`);
                    quantityInput.value = minPurchaseQty;
                    return false;
                }

                if (currentQty > maxStock) {
                    showWarning("Invalid Quantity", `Quantity cannot exceed ${maxStock}.`);
                    quantityInput.value = maxStock;
                    return false;
                }

                return true;
            }

            // ---------------------- CART ACTIONS ----------------------

            function addToCart(event) {
                event.preventDefault();
                if (!validateQuantity()) return;

                const quantity = parseInt(quantityInput.value);

                // Log the request data
                console.log("🛒 Sending Add to Cart Request:", {
                    productId,
                    productPrice,
                    quantity,
                    minPurchaseQty
                });

                const formData = {
                    product_id: productId,
                    price: productPrice,
                    quantity: quantity,
                    minimum_purchase_qty: minPurchaseQty
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
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        data
                    })))
                    .then(({
                        status,
                        data
                    }) => {
                        console.log("Server Response:", data);

                        if (status === 422 || !data.success) {
                            throw new Error(data.message || "Failed to add product to cart.");
                        }

                        showSuccess("Success!", data.message);
                    })
                    .catch(error => showError("Error!", error.message));
            }

            function buyNow(event) {
                event.preventDefault();
                if (!validateQuantity()) return;

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
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Preparing your checkout. Please wait.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            willOpen: () => Swal.showLoading()
                        });

                        const formData = {
                            product_id: productId,
                            quantity: parseInt(quantityInput.value)
                        };

                        fetch('{{ route('retailers.direct-purchase.buy-now') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(formData)
                            })
                            .then(response => response.json().then(data => ({
                                status: response.status,
                                data
                            })))
                            .then(({
                                status,
                                data
                            }) => {
                                if (status !== 200 || !data.success) {
                                    throw new Error(data.message || "Failed to process request.");
                                }

                                Swal.close();
                                window.location.href = data.redirect_url;
                            })
                            .catch(error => showError("Error!", error.message));
                    }
                });
            }

            // ---------------------- UTILITY FUNCTIONS ----------------------

            function showSuccess(title, message) {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'success',
                    confirmButtonColor: '#10B981',
                    timer: 2000
                });
            }

            function showError(title, message) {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'error',
                    confirmButtonColor: '#EF4444'
                });
            }

            function showWarning(title, message) {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    confirmButtonColor: '#10B981'
                });
            }

            // ---------------------- EVENT LISTENERS ----------------------

            addToCartBtn.addEventListener('click', addToCart);
            buyNowBtn.addEventListener('click', buyNow);

            // Assigning quantity increase/decrease to respective buttons
            document.querySelectorAll('[onclick="increaseQuantity()"]').forEach(btn => btn.addEventListener('click',
                increaseQuantity));
            document.querySelectorAll('[onclick="decreaseQuantity()"]').forEach(btn => btn.addEventListener('click',
                decreaseQuantity));
        });
    </script>

    <x-footer />
</x-app-layout>
