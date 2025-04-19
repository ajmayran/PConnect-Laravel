<x-app-layout>
    <x-dashboard-nav />

    <div class="py-2 sm:py-8 bg-gray-50">
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
                                <p class="p-2 mb-4 text-sm text-gray-700 rounded-md bg-green-50 sm:text-base">
                                    {{ $product->description }}</p>
                            </div>

                            <!-- Stock Information -->
                            <div class="mb-4 sm:mb-6">
                                <p class="text-sm text-gray-600 sm:text-base">
                                    Stock Available: <span class="font-semibold">{{ $product->stock_quantity }}</span>
                                </p>
                                <p class="text-sm text-gray-600 sm:text-base">
                                    Minimum Purchase Quantity: <span
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
                                @elseif ($product->stock_quantity <= 0)
                                    <div class="w-full p-4 text-center bg-red-100 border border-red-200 rounded-lg">
                                        <p class="flex items-center justify-center text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Out of Stock
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

    <!-- Distributor Info Section -->
    <div class="pb-4 bg-gray-50">
        <div class="container px-4 mx-auto">
            <div class="mx-auto max-w-7xl">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <div class="flex flex-col md:flex-row">
                        <!-- Left Side - Profile Image -->
                        <div class="flex items-center justify-center p-4 md:w-1/6 md:p-6">
                            <img src="{{ $product->distributor->company_profile_image ? asset('storage/' . $product->distributor->company_profile_image) : asset('img/default-distributor.jpg') }}"
                                alt="{{ $product->distributor->company_name }}"
                                class="object-cover w-24 h-24 border-2 border-green-500 rounded-full">
                        </div>

                        <!-- Right Side - Split into two columns on larger screens -->
                        <div class="flex flex-col p-4 md:w-5/6 md:p-6">
                            <div class="flex flex-col gap-4 md:flex-row md:justify-between">
                                <!-- Left Column: Company Info & Action Buttons -->
                                <div class="flex flex-col md:w-1/2">
                                    <!-- Company Name -->
                                    <div class="mb-3">
                                        <h3 class="text-xl font-bold text-gray-800">
                                            {{ $product->distributor->company_name }}</h3>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-col mt-2 space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
                                        <a href="{{ route('retailers.distributor-page', $product->distributor->id) }}"
                                            class="flex items-center justify-center px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                            View Shop
                                        </a>

                                        <!-- Add Follow Button -->
                                        <button id="followDistributorBtn"
                                            data-distributor-id="{{ $product->distributor->id }}"
                                            class="flex items-center justify-center px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                @if ($isFollowing ?? false)
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                @endif
                                            </svg>
                                            <span>{{ $isFollowing ?? false ? 'Unfollow' : 'Follow' }}</span>
                                        </button>

                                        <!-- Contact Button -->
                                        <a href="{{ route('retailers.messages.show', $product->distributor->user_id) }}"
                                            class="flex items-center justify-center px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            Contact
                                        </a>
                                    </div>
                                </div>

                                <!-- Right Column: Stats -->
                                <div class="flex justify-end gap-4 mt-4 md:mt-0 md:gap-6">
                                    <div class="px-3 py-2 text-center rounded-lg ">
                                        <p class="text-sm font-medium text-gray-600">Ratings</p>
                                        <p class="text-lg font-bold text-gray-800">
                                            {{ number_format($rating ?? 0, 1) }}</p>
                                    </div>
                                    <div class="px-3 py-2 text-center rounded-lg ">
                                        <p class="text-sm font-medium text-gray-600">Products</p>
                                        <p class="text-lg font-bold text-gray-800">{{ $productsCount ?? 0 }}</p>
                                    </div>

                                    <div class="px-3 py-2 text-center rounded-lg ">
                                        <p class="text-sm font-medium text-gray-600">Cut-off Time</p>
                                        <p class="text-lg font-bold text-gray-800">
                                            {{ $product->distributor->cut_off_time ? $product->distributor->formatted_cut_off_time : 'No limit' }}
                                        </p>
                                    </div>

                                    <div class="px-3 py-2 text-center rounded-lg ">
                                        <p class="text-sm font-medium text-gray-600">Joined</p>
                                        <p class="text-lg font-bold text-gray-800">
                                            {{ \Carbon\Carbon::parse($product->distributor->created_at)->diffForHumans(null, true) }}
                                            ago</p>
                                    </div>
                                    <div class="px-3 py-2 text-center rounded-lg ">
                                        <p class="text-sm font-medium text-gray-600">Followers</p>
                                        <p class="text-lg font-bold text-gray-800">
                                            {{ number_format($product->distributor->followers_count ?? 0) }}</p>
                                    </div>
                                </div>
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

            if (addToCartBtn && maxStock > 0) {
                addToCartBtn.addEventListener('click', addToCart);
            }

            if (buyNowBtn && maxStock > 0) {
                buyNowBtn.addEventListener('click', buyNow);
            }

            // Assigning quantity increase/decrease to respective buttons
            if (maxStock > 0) {
                document.querySelectorAll('[onclick="increaseQuantity()"]').forEach(btn => btn.addEventListener(
                    'click',
                    increaseQuantity));
                document.querySelectorAll('[onclick="decreaseQuantity()"]').forEach(btn => btn.addEventListener(
                    'click',
                    decreaseQuantity));
            }
        });


        // Follow/Unfollow Distributor
        const followBtn = document.getElementById('followDistributorBtn');
        if (followBtn) {
            followBtn.addEventListener('click', function() {
                const distributorId = this.dataset.distributorId;

                fetch('{{ route('retailers.distributors.follow') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            distributor_id: distributorId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button text
                            const buttonText = followBtn.querySelector('span');
                            buttonText.textContent = data.is_following ? 'Unfollow' : 'Follow';

                            // Update icon (optional)
                            const iconElement = followBtn.querySelector('svg');
                            if (iconElement) {
                                if (data.is_following) {
                                    // Change to "check" or "minus" icon for following
                                    iconElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" 
                            stroke-width="2" d="M5 13l4 4L19 7" />`;
                                } else {
                                    // Change back to "plus" icon for not following
                                    iconElement.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" 
                            stroke-width="2" d="M12 4v16m8-8H4" />`;
                                }
                            }

                            // Update follower count
                            const followerCountElement = document.querySelector(
                                '.text-lg.font-bold:last-of-type');
                            if (followerCountElement) {
                                followerCountElement.textContent = new Intl.NumberFormat().format(data
                                    .follower_count);
                            }

                            // Show a notification
                            showSuccess('Success', data.message);
                        } else {
                            showError('Error', 'Unable to update follow status');
                        }
                    })
                    .catch(error => {
                        console.error('Follow error:', error);
                        showError('Error', 'An error occurred while processing your request');
                    });
            });
        }
    </script>

    <x-footer />
</x-app-layout>
