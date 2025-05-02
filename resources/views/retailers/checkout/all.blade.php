<x-app-layout>
    <x-dashboard-nav />
    <div class="container py-8 mx-auto">
        @if ($checkoutProducts->count())
            <form id="orderForm" action="{{ route('retailers.checkout.placeOrderAll') }}" method="POST">
                @csrf

                @foreach ($carts as $cart)
                    <input type="hidden" name="all_carts[]" value="{{ $cart->id }}">
                @endforeach

                <div class="flex flex-col gap-8 md:flex-row">
                    <!-- Left Column: Checkout Products -->
                    <div class="md:w-2/3">
                        @foreach ($checkoutProducts->groupBy('product.distributor_id') as $distributorId => $products)
                            <div class="p-6 mb-6 bg-white rounded-lg shadow-md cart-group"
                                data-distributor-id="{{ $distributorId }}">
                                <h2 class="mb-4 text-2xl font-semibold">
                                    Checkout Products for
                                    <span>{{ $products->first()->product->distributor->company_name ?? $distributorId }}</span>
                                </h2>
                                @if ($products->isEmpty())
                                    <p>No products added for this distributor.</p>
                                @else
                                    <div class="space-y-4">

                                        <div class="flex px-4 py-2 font-bold border-b border-gray-200">
                                            <span class="flex-1">Product</span>
                                            <span class="w-24 text-right">Price</span>
                                            <span class="w-24 text-right">Subtotal</span>
                                        </div>
                                        @foreach ($products as $product)
                                            <div class="flex items-center px-4 py-4 border-b border-gray-100">
                                                <div class="flex items-center flex-1">
                                                    <img class="object-cover w-16 h-16 mr-4 rounded"
                                                        src="{{ $product['product']->image ? asset('storage/products/' . basename($product['product']->image)) : asset('img/default-product.jpg') }}"
                                                        onerror="this.src='{{ asset('img/default-product.jpg') }}'"
                                                        alt="{{ $product['product']->product_name }}">
                                                    <div>
                                                        <p class="font-semibold">
                                                            {{ $product['product']->product_name }}</p>
                                                        <p class="text-sm text-gray-600">Qty: {{ $product['quantity'] }}
                                                        </p>

                                                        <!-- Display discount information -->
                                                        @if ($product['applied_discount'])
                                                            <p class="text-sm text-green-600">
                                                                Discount: {{ $product['applied_discount'] }}
                                                            </p>
                                                            @if ($product['discount_amount'] > 0)
                                                                <p class="text-sm text-green-600">
                                                                    -₱{{ number_format($product['discount_amount'], 2) }}
                                                                </p>
                                                            @endif
                                                            @if ($product['free_items'] > 0)
                                                                <p class="text-sm text-green-600">
                                                                    +{{ $product['free_items'] }} free item(s)
                                                                </p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="w-24 text-right">
                                                    @if ($product['discount_amount'] > 0)
                                                        <p class="text-sm text-gray-500 line-through">
                                                            ₱{{ number_format($product['product']->price * $product['quantity'], 2) }}
                                                        </p>
                                                    @endif
                                                    <p>₱{{ number_format($product['subtotal'], 2) }}</p>
                                                </div>
                                            </div>
                                            <input type="hidden" name="cart_details[{{ $distributorId }}][]"
                                                value="{{ $product['id'] }}">
                                        @endforeach
                                        <div class="flex px-4 py-4 font-semibold border-t border-gray-200">
                                            <span class="flex-1 text-right">Total Amount:</span>
                                            <span
                                                class="w-24 text-right">₱{{ number_format($distributorTotals[$distributorId] ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="carts[]" value="{{ $products->first()['cart_id'] }}">
                                @endif
                            </div>
                        @endforeach

                        <!-- Pagination links -->
                        <div class="flex justify-end mt-6">
                            {{ $checkoutProducts->links() }}
                        </div>
                    </div>

                    <!-- Right Column: Retailer Profile & Delivery Address -->
                    <div class="space-y-8 md:w-1/3">
                        <!-- Retailer Profile Card -->
                        <div class="p-6 bg-white rounded-lg shadow">
                            <h2 class="mb-4 text-2xl font-bold">Retailer Profile</h2>
                            <div class="flex items-center space-x-4">
                                <img class="object-cover w-16 h-16 rounded-full"
                                    src="{{ Auth::user()->retailerProfile && Auth::user()->retailerProfile->profile_picture ? asset('storage/' . Auth::user()->retailerProfile->profile_picture) : asset('img/default-profile.png') }}"
                                    alt="Profile">
                                <div>
                                    <p class="font-bold text-gray-800">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </p>
                                    <p class="text-gray-600">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p><strong>Business Name:</strong> {{ $user->retailerProfile->business_name ?? 'N/A' }}
                                </p>
                                <p><strong>Phone:</strong> {{ $user->retailerProfile->phone ?? 'N/A' }}</p>
                                <p><strong>Address:</strong>
                                    {{ $user->retailerProfile->barangay_name }},{{ $user->retailerProfile->street ?? '' }}
                                </p>
                            </div>

                            <div class="pt-4 mt-6 border-t">
                                <p class="text-xl font-bold">
                                    Grand Total: ₱ {{ number_format($grandTotal, 2) }}
                                </p>
                            </div>
                        </div>

                        <!-- Delivery Address Card -->
                        <div class="p-6 bg-white rounded-lg shadow">
                            <h2 class="mb-4 text-xl font-semibold">Delivery Address</h2>
                            <div class="flex flex-col space-y-4">
                                <div class="flex items-center">
                                    <input type="radio" id="default_address" name="delivery_option" value="default"
                                        checked class="form-radio">
                                    <label for="default_address" class="ml-2">
                                        Use my default address:
                                        <span
                                            class="font-medium">{{ $user->retailerProfile->barangay_name }},{{ $user->retailerProfile->street ?? '' }}</span>
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="new_address" name="delivery_option" value="other"
                                        class="form-radio">
                                    <label for="new_address" class="ml-2">Deliver to a different address</label>
                                </div>

                                <!-- New address input, hidden by default -->
                                <div id="newAddressInput" class="hidden space-y-3">
                                    <div>
                                        <label for="new_barangay"
                                            class="block text-sm font-medium text-gray-700">Barangay</label>
                                        <select id="new_barangay" name="new_barangay"
                                            class="block w-full px-3 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                            <option value="">Select Barangay</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="new_street" class="block text-sm font-medium text-gray-700">Street
                                            Address</label>
                                        <textarea id="new_street" name="new_street" rows="2"
                                            class="block w-full px-3 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                            placeholder="Enter street address"></textarea>
                                    </div>

                                    <input type="hidden" id="new_delivery_address" name="new_delivery_address"
                                        value="">
                                </div>

                                <!-- Multi-Address Option -->
                                <div class="pt-4 mt-6 border-t border-gray-200">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="enable_multi_address" name="is_multi_address"
                                            value="1" class="w-5 h-5 text-green-600 form-checkbox"
                                            {{ $grandTotal < 5000 ? 'disabled' : '' }}>
                                        <label for="enable_multi_address" class="ml-2 font-medium">
                                            Split Order for Multiple Addresses
                                        </label>
                                    </div>

                                    @if ($grandTotal < 5000)
                                        <p class="mt-1 text-sm text-orange-600 ml-7">
                                            Your order must be at least ₱5,000 to qualify for multiple address delivery.
                                        </p>
                                    @else
                                        <p class="mt-1 text-sm text-gray-600 ml-7">
                                            You can split this order and deliver to multiple addresses.
                                        </p>
                                    @endif
                                </div>

                                <!-- Multi-Address Selection (initially hidden) -->
                                <div id="multiAddressSection"
                                    class="hidden pt-4 mt-4 space-y-6 border-t border-gray-200">
                                    <h3 class="text-lg font-medium">Split Order Items by Delivery Location</h3>
                                    <p class="text-sm text-gray-600">Create delivery locations and assign products to
                                        each location.</p>

                                    <div id="locationContainer" class="space-y-8">
                                        <!-- First location (automatically added) -->
                                        <div id="location-1" class="p-5 border border-gray-200 rounded-lg">
                                            <h4 class="mb-3 font-medium">Delivery Location 1</h4>

                                            <div class="mb-3">
                                                <label class="block text-sm font-medium text-gray-700">Address</label>
                                                <select name="locations[1][address_id]"
                                                    class="block w-full px-3 py-2 mt-1 border rounded-lg location-address focus:outline-none focus:ring-2 focus:ring-green-500">
                                                    <option value="default">Default Address</option>
                                                    @if ($user->retailerProfile && $user->retailerProfile->addresses && $user->retailerProfile->addresses->count() > 0)
                                                        @foreach ($user->retailerProfile->addresses as $address)
                                                            <option value="{{ $address->id }}">
                                                                {{ $address->label }}: {{ $address->barangay_name }},
                                                                {{ $address->street }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="hidden mt-1 text-xs text-red-600 address-error-1">
                                                    This address is already used in another location
                                                </div>
                                            </div>

                                            <div>
                                                <h5 class="mb-2 text-sm font-medium">Products for this location:</h5>
                                                <div id="location-1-products"
                                                    class="p-3 space-y-2 border border-gray-100 rounded-lg">
                                                    <p class="text-sm text-center text-gray-500">No products assigned.
                                                        Use the "Add Product" buttons below.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add more locations button -->
                                    <div class="text-center">
                                        <button type="button" id="addLocationBtn"
                                            class="px-4 py-2 text-sm text-green-600 border border-green-600 rounded-lg hover:bg-green-50">
                                            + Add Another Delivery Location
                                        </button>
                                    </div>

                                    <!-- Product assignment section -->
                                    <div class="mt-4 space-y-4">
                                        <h3 class="text-lg font-medium">Available Products</h3>
                                        <p class="text-sm text-gray-600">Click "Add Product" to assign products to each
                                            delivery location.</p>

                                        @foreach ($checkoutProducts->groupBy('product.distributor_id') as $distributorId => $products)
                                            <div class="p-4 border border-gray-200 rounded-lg">
                                                <h4 class="mb-2 font-medium">
                                                    {{ $products->first()->product->distributor->company_name ?? 'Distributor' }}
                                                </h4>

                                                <div class="space-y-3">
                                                    @foreach ($products as $product)
                                                        <div
                                                            class="flex items-center justify-between p-2 border-b border-gray-100">
                                                            <div class="flex items-center space-x-3">
                                                                <img class="object-cover w-12 h-12 rounded"
                                                                    src="{{ $product['product']->image ? asset('storage/products/' . basename($product['product']->image)) : asset('img/default-product.jpg') }}"
                                                                    alt="{{ $product['product']->product_name }}">
                                                                <div>
                                                                    <p class="font-medium">
                                                                        {{ $product['product']->product_name }}</p>
                                                                    <p class="text-sm text-gray-600">Qty:
                                                                        {{ $product['quantity'] }}</p>
                                                                    <!-- Pass product data to be used by JavaScript -->
                                                                    <input type="hidden" class="product-data"
                                                                        data-id="{{ $product['id'] }}"
                                                                        data-product-id="{{ $product['product']->id }}"
                                                                        data-name="{{ $product['product']->product_name }}"
                                                                        data-quantity="{{ $product['quantity'] }}"
                                                                        data-price="{{ $product['product']->price }}"
                                                                        data-subtotal="{{ $product['subtotal'] }}"
                                                                        data-image="{{ $product['product']->image ? asset('storage/products/' . basename($product['product']->image)) : asset('img/default-product.jpg') }}">
                                                                </div>
                                                            </div>

                                                            <button type="button"
                                                                class="px-3 py-1 text-sm text-white bg-blue-600 rounded add-product-btn hover:bg-blue-700"
                                                                data-product-id="{{ $product['product']->id }}"
                                                                data-id="{{ $product['id'] }}">
                                                                Add Product
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full px-4 py-3 mt-6 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700">
                                Place Order
                            </button>
                        </div>
                    </div>
            </form>
        @else
            <p>No distributor carts available.</p>
        @endif
    </div>
    @include('components.footer')
    <script>
        const newAddressRadio = document.getElementById("new_address");
        const defaultAddressRadio = document.getElementById("default_address");
        const newAddressInput = document.getElementById("newAddressInput");
        const enableMultiAddressCheckbox = document.getElementById("enable_multi_address");
        const multiAddressSection = document.getElementById("multiAddressSection");
        const addLocationBtn = document.getElementById("addLocationBtn");
        const products = [];
        document.querySelectorAll('.product-data').forEach(el => {
            products.push({
                id: el.dataset.id,
                productId: el.dataset.productId,
                name: el.dataset.name,
                quantity: parseInt(el.dataset.quantity),
                price: parseFloat(el.dataset.price),
                subtotal: parseFloat(el.dataset.subtotal),
                available: parseInt(el.dataset.quantity), // Initialize available with full quantity
                image: el.dataset.image
            });
        });

        // Track location count and assigned products
        let locationCount = 1;
        const assignedProducts = {}; // { productId: { locationId: quantity, ... }, ... }

        // Initialize product assignments tracking
        products.forEach(product => {
            assignedProducts[product.productId] = {};
        });

        // Initialize UI event listeners
        function initializeLocations() {
            // Add product button listeners
            document.querySelectorAll('.add-product-btn').forEach(btn => {
                if (!btn.hasAssignListener) {
                    btn.addEventListener('click', function() {
                        const productId = this.dataset.productId;
                        const cartDetailId = this.dataset.id;
                        showProductAssignModal(productId, cartDetailId);
                    });
                    btn.hasAssignListener = true;
                }
            });
        }

        // Show product assignment modal
        function showProductAssignModal(productId, cartDetailId) {
            const product = products.find(p => p.productId === productId);
            if (!product) return;

            // Calculate remaining available quantity
            const assignedTotal = Object.values(assignedProducts[productId]).reduce((sum, val) => sum + val, 0);
            const remainingQuantity = product.quantity - assignedTotal;

            if (remainingQuantity <= 0) {
                Swal.fire({
                    title: 'No Quantity Available',
                    text: 'All units of this product have been assigned to locations',
                    icon: 'warning'
                });
                return;
            }

            // Create the location options
            let locationOptions = '';
            for (let i = 1; i <= locationCount; i++) {
                locationOptions += `<option value="${i}">Location ${i}</option>`;
            }

            Swal.fire({
                title: 'Assign Product to Location',
                html: `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <img src="${product.image}" class="object-cover w-16 h-16 rounded">
                            <div class="text-left">
                                <h3 class="font-medium">${product.name}</h3>
                                <p class="text-sm text-gray-600">Available: ${remainingQuantity} of ${product.quantity}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block mb-1 text-sm font-medium text-left">Select Location:</label>
                            <select id="assign-location" class="w-full px-3 py-2 border rounded">
                                ${locationOptions}
                            </select>
                        </div>
                        <div class="mt-2">
                            <label class="block mb-1 text-sm font-medium text-left">Quantity:</label>
                            <input type="number" id="assign-quantity" class="w-full px-3 py-2 border rounded" 
                                min="1" max="${remainingQuantity}" value="1">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Assign',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                preConfirm: () => {
                    const locationId = document.getElementById('assign-location').value;
                    const quantity = parseInt(document.getElementById('assign-quantity').value);

                    // Validate quantity
                    if (isNaN(quantity) || quantity < 1 || quantity > remainingQuantity) {
                        Swal.showValidationMessage(`Please enter a valid quantity (1-${remainingQuantity})`);
                        return false;
                    }

                    return {
                        locationId,
                        quantity
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        locationId,
                        quantity
                    } = result.value;
                    assignProductToLocation(product, locationId, quantity, cartDetailId);
                }
            });
        }

        // Assign product to a location
        function assignProductToLocation(product, locationId, quantity, cartDetailId) {
            // Update the tracking object
            if (!assignedProducts[product.productId][locationId]) {
                assignedProducts[product.productId][locationId] = 0;
            }
            assignedProducts[product.productId][locationId] += quantity;

            // Update the product's available quantity
            product.available -= quantity;

            // Update UI
            const locationProductsContainer = document.getElementById(`location-${locationId}-products`);

            // Clear the "no products" message if this is the first product
            if (locationProductsContainer.querySelector('p.text-gray-500')) {
                locationProductsContainer.innerHTML = '';
            }

            // Check if this product is already in this location
            const existingRow = locationProductsContainer.querySelector(
                `.product-row[data-product-id="${product.productId}"]`);

            if (existingRow) {
                // Update existing row
                const currentQty = parseInt(existingRow.dataset.quantity);
                const newQty = currentQty + quantity;
                existingRow.dataset.quantity = newQty;

                // Update displayed quantity
                existingRow.querySelector('.product-quantity').textContent = newQty;

                // Update hidden input
                const hiddenInput = existingRow.querySelector(
                    `input[name="locations[${locationId}][products][${product.productId}][quantity]"]`);
                hiddenInput.value = newQty;
            } else {
                // Create new row
                const productRow = document.createElement('div');
                productRow.className = 'product-row flex items-center justify-between py-2 border-b border-gray-100';
                productRow.dataset.productId = product.productId;
                productRow.dataset.quantity = quantity;

                productRow.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <img src="${product.image}" class="object-cover w-8 h-8 rounded">
                        <div>
                            <p class="text-sm font-medium">${product.name}</p>
                            <p class="text-xs text-gray-600">Qty: <span class="product-quantity">${quantity}</span></p>
                        </div>
                    </div>
                    <input type="hidden" name="locations[${locationId}][products][${product.productId}][quantity]" value="${quantity}">
                    <input type="hidden" name="locations[${locationId}][products][${product.productId}][cart_detail_id]" value="${cartDetailId}">
                    <button type="button" class="text-red-600 remove-product-btn hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                `;

                locationProductsContainer.appendChild(productRow);

                // Add remove product event listener
                productRow.querySelector('.remove-product-btn').addEventListener('click', function() {
                    removeProductFromLocation(product.productId, locationId, quantity);
                    productRow.remove();

                    // If no products left, show the "no products" message
                    if (locationProductsContainer.querySelectorAll('.product-row').length === 0) {
                        locationProductsContainer.innerHTML =
                            '<p class="text-sm text-center text-gray-500">No products assigned. Use the "Add Product" buttons below.</p>';
                    }
                });
            }
        }

        // Remove product from location tracking
        function removeProductFromLocation(productId, locationId, quantity) {
            // Update tracking
            assignedProducts[productId][locationId] -= quantity;
            if (assignedProducts[productId][locationId] <= 0) {
                delete assignedProducts[productId][locationId];
            }

            // Update product available quantity
            const product = products.find(p => p.productId === productId);
            if (product) {
                product.available += quantity;
            }
        }

        // Add new location
        addLocationBtn.addEventListener('click', function() {
            if (locationCount >= 3) {
                Swal.fire({
                    title: 'Maximum Locations',
                    text: 'You can only have up to 3 delivery locations',
                    icon: 'warning'
                });
                return;
            }

            locationCount++;

            const locationDiv = document.createElement('div');
            locationDiv.id = `location-${locationCount}`;
            locationDiv.className = 'p-5 border border-gray-200 rounded-lg';

            locationDiv.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium">Delivery Location ${locationCount}</h4>
                    <button type="button" class="text-red-600 hover:text-red-800 remove-location-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <select name="locations[${locationCount}][address_id]" 
                        class="block w-full px-3 py-2 mt-1 border rounded-lg location-address focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="default">Default Address</option>
                        @if ($user->retailerProfile && $user->retailerProfile->addresses && $user->retailerProfile->addresses->count() > 0)
                            @foreach ($user->retailerProfile->addresses as $address)
                                <option value="{{ $address->id }}">
                                    {{ $address->label }}: {{ $address->barangay_name }}, {{ $address->street }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <div class="address-error-${locationCount} hidden mt-1 text-xs text-red-600">
                        This address is already used in another location
                    </div>
                </div>
                
                <div>
                    <h5 class="mb-2 text-sm font-medium">Products for this location:</h5>
                    <div id="location-${locationCount}-products" class="p-3 space-y-2 border border-gray-100 rounded-lg">
                        <p class="text-sm text-center text-gray-500">No products assigned. Use the "Add Product" buttons below.</p>
                    </div>
                </div>
            `;

            document.getElementById('locationContainer').appendChild(locationDiv);

            // Add remove location event listener
            locationDiv.querySelector('.remove-location-btn').addEventListener('click', function() {
                removeLocation(locationCount);
            });

            // Add address change listener
            const addressSelect = locationDiv.querySelector('.location-address');
            addressSelect.addEventListener('change', function() {
                validateAddressSelections();
            });
        });

        // Remove location function
        function removeLocation(locationId) {
            // Get all products assigned to this location
            const productsToRemove = [];

            Object.entries(assignedProducts).forEach(([productId, locations]) => {
                if (locations[locationId]) {
                    productsToRemove.push({
                        productId,
                        quantity: locations[locationId]
                    });
                }
            });

            // Update product availability
            productsToRemove.forEach(item => {
                removeProductFromLocation(item.productId, locationId, item.quantity);
            });

            // Remove the location div
            const locationDiv = document.getElementById(`location-${locationId}`);
            if (locationDiv) {
                locationDiv.remove();
            }

            // Don't decrement locationCount since we want to keep the IDs unique
            // We'll just reuse the IDs when adding new locations

            // Re-validate addresses
            validateAddressSelections();
        }

        // Validate address selections to prevent duplicates
        function validateAddressSelections() {
            const addressSelects = document.querySelectorAll('.location-address');
            const usedAddresses = {};
            let isValid = true;

            // Reset previous errors
            document.querySelectorAll('[class^="address-error-"]').forEach(el => {
                el.classList.add('hidden');
            });

            // Check for duplicates
            addressSelects.forEach(select => {
                const addressId = select.value;
                const locationId = select.name.match(/\[(\d+)\]/)[1];

                if (usedAddresses[addressId]) {
                    // Show error
                    const errorDiv = document.querySelector(`.address-error-${locationId}`);
                    if (errorDiv) {
                        errorDiv.classList.remove('hidden');
                    }
                    isValid = false;
                } else {
                    usedAddresses[addressId] = true;
                }
            });

            return isValid;
        }

        // Validate total product quantities
        function validateTotalProductQuantities() {
            let isValid = true;

            products.forEach(product => {
                // Calculate total assigned quantity
                let totalAssigned = 0;
                Object.values(assignedProducts[product.productId]).forEach(qty => {
                    totalAssigned += qty;
                });

                // Check if total matches the original quantity
                if (totalAssigned !== product.quantity) {
                    isValid = false;
                    console.error(`Product ${product.name}: Assigned ${totalAssigned} of ${product.quantity}`);
                }
            });

            return isValid;
        }

        // Handle basic address form events
        newAddressRadio.addEventListener("change", function() {
            if (this.checked) {
                newAddressInput.classList.remove("hidden");
                loadBarangays();
            }
        });

        defaultAddressRadio.addEventListener("change", function() {
            if (this.checked) {
                newAddressInput.classList.add("hidden");
            }
        });

        // Toggle multi-address section
        enableMultiAddressCheckbox.addEventListener("change", function() {
            if (this.checked) {
                multiAddressSection.classList.remove("hidden");
                // Disable other delivery options
                defaultAddressRadio.disabled = true;
                newAddressRadio.disabled = true;

                // Initialize UI if not already done
                initializeLocations();
            } else {
                multiAddressSection.classList.add("hidden");
                // Enable other delivery options
                defaultAddressRadio.disabled = false;
                newAddressRadio.disabled = false;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize product buttons and locations
            if (products.length > 0) {
                initializeLocations();
            }
        });

        // Form submission handler with validation
        const orderForm = document.getElementById("orderForm");
        orderForm.addEventListener("submit", function(e) {
            e.preventDefault();

            // Form validation for all modes
            if (newAddressRadio.checked) {
                const newBarangaySelect = document.getElementById("new_barangay");
                if (newBarangaySelect.value === "") {
                    Swal.fire({
                        title: 'Missing Information',
                        text: 'Please select a barangay for your delivery address',
                        icon: 'warning',
                    });
                    return;
                }
                updateNewDeliveryAddress();
            }

            // Multi-address validation
            if (enableMultiAddressCheckbox.checked) {
                // Check that there's at least one product assigned to each location
                let hasEmptyLocation = false;
                for (let i = 1; i <= locationCount; i++) {
                    const productContainer = document.querySelector(`#location-${i}-products`);
                    if (productContainer && productContainer.querySelectorAll('.product-row').length === 0) {
                        hasEmptyLocation = true;
                        break;
                    }
                }

                if (hasEmptyLocation) {
                    Swal.fire({
                        title: 'Empty Location',
                        text: 'Each delivery location must have at least one product assigned',
                        icon: 'error'
                    });
                    return;
                }

                // Validate address selections
                if (!validateAddressSelections()) {
                    Swal.fire({
                        title: 'Duplicate Addresses',
                        text: 'Each delivery location must have a unique address',
                        icon: 'error'
                    });
                    return;
                }

                // Validate product quantities
                if (!validateTotalProductQuantities()) {
                    Swal.fire({
                        title: 'Invalid Product Quantities',
                        text: 'The total quantity for each product must match the original order quantity',
                        icon: 'error'
                    });
                    return;
                }
            }

            // Show confirmation dialog
            Swal.fire({
                title: 'Confirm Order?',
                text: 'Do you want to place this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, place order',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    orderForm.submit();
                }
            });
        });

        // Existing barangay loading code
        function loadBarangays() {
            fetch('/barangays/093170')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Clear previous options
                    newBarangaySelect.innerHTML = '<option value="">Select Barangay</option>';

                    // Add options
                    data.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay.code;
                        option.textContent = barangay.name;
                        option.setAttribute('data-name', barangay.name);
                        newBarangaySelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading barangays:', error);
                });
        }

        // Update delivery address when barangay or street changes
        function updateNewDeliveryAddress() {
            const newBarangaySelect = document.getElementById("new_barangay");
            const newStreetInput = document.getElementById("new_street");
            const newDeliveryAddressInput = document.getElementById("new_delivery_address");

            const selectedOption = newBarangaySelect.options[newBarangaySelect.selectedIndex];
            const barangayName = selectedOption.textContent;
            const street = newStreetInput.value;

            if (barangayName && barangayName !== 'Select Barangay') {
                newDeliveryAddressInput.value = street ? `${barangayName}, ${street}` : barangayName;
            } else {
                newDeliveryAddressInput.value = street;
            }
        }
    </script>

</x-app-layout>
