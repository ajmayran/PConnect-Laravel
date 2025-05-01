<x-app-layout>
    <x-dashboard-nav />
    <div class="container py-8 mx-auto">
        <div class="flex flex-col gap-8 md:flex-row">
            <!-- Left Column: Checkout Products Card -->
            <div class="md:w-2/3">
                <div class="p-6 bg-white rounded-lg shadow min-h-[500px]">
                    <h2 class="mb-4 text-2xl font-bold">Checkout Products</h2>
                    @if ($checkoutProducts->isEmpty())
                        <p>No products added to checkout.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($checkoutProducts as $product)
                                <div class="flex items-center justify-between p-4 border rounded">
                                    <div class="flex items-center space-x-4">
                                        <img class="object-cover w-16 h-16"
                                            src="{{ asset('storage/' . $product['product']->image) }}"
                                            alt="{{ $product['product']->product_name }}">
                                        <div>
                                            <p class="font-semibold">{{ $product['product']->product_name }}</p>
                                            <p class="text-sm text-gray-600">Qty: {{ $product['quantity'] }}</p>

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
                                    <div class="text-right">
                                        @if ($product['discount_amount'] > 0)
                                            <p class="text-sm text-gray-500 line-through">
                                                ₱{{ number_format($product['original_subtotal'], 2) }}</p>
                                        @endif
                                        <p class="font-bold">₱{{ number_format($product['final_subtotal'], 2) }}</p>
                                        <!-- Add product ID as data attribute for use in JavaScript -->
                                        <input type="hidden" class="product-data" data-id="{{ $product['id'] }}"
                                            data-product-id="{{ $product['product']->id }}"
                                            data-name="{{ $product['product']->product_name }}"
                                            data-quantity="{{ $product['quantity'] }}"
                                            data-price="{{ $product['product']->price }}"
                                            data-subtotal="{{ $product['final_subtotal'] }}"
                                            data-image="{{ $product['product']->image }}">>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Pagination links -->
                            <div class="flex justify-end mt-6">
                                {{ $checkoutProducts->links() }}
                            </div>
                        </div>
                        <div class="mt-6 text-right">
                            <p class="text-2xl font-bold">
                                Total: ₱ {{ number_format($grandTotal, 2) }}
                            </p>
                        </div>
                    @endif
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
                        <p><strong>Business Name:</strong> {{ $user->retailerProfile->business_name ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $user->retailerProfile->phone ?? 'N/A' }}</p>
                        <p><strong>Default Address:</strong>
                            @if ($user->retailerProfile && $user->retailerProfile->defaultAddress)
                                {{ $user->retailerProfile->defaultAddress->barangay_name }},
                                {{ $user->retailerProfile->defaultAddress->street ?? '' }}
                            @elseif ($user->retailerProfile && $user->retailerProfile->barangay_name)
                                {{ $user->retailerProfile->barangay_name }},
                                {{ $user->retailerProfile->street ?? '' }}
                            @else
                                N/A
                            @endif
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
                    @if (isset($cart) && $cart)
                        <form id="orderForm"
                            action="{{ route('retailers.checkout.placeOrder', ['distributorId' => $cart->distributor_id]) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="cart_id" value="{{ $cart->id }}">
                            <input type="hidden" name="total_amount" value="{{ $grandTotal }}">

                            <div class="flex flex-col space-y-4">
                                <!-- Standard delivery options -->
                                <div class="flex items-center">
                                    <input type="radio" id="default_address" name="delivery_option" value="default"
                                        checked class="form-radio">
                                    <label for="default_address" class="ml-2">
                                        Use my default address:
                                        <span class="font-medium">
                                            @if ($user->retailerProfile && $user->retailerProfile->defaultAddress)
                                                {{ $user->retailerProfile->defaultAddress->barangay_name }},
                                                {{ $user->retailerProfile->defaultAddress->street ?? '' }}
                                            @elseif ($user->retailerProfile && $user->retailerProfile->barangay_name)
                                                {{ $user->retailerProfile->barangay_name }},
                                                {{ $user->retailerProfile->street ?? '' }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="saved_address" name="delivery_option" value="saved"
                                        class="form-radio">
                                    <label for="saved_address" class="ml-2">Use a saved address</label>
                                </div>
                                <!-- Saved addresses selector (hidden by default) -->
                                <div id="savedAddressesContainer" class="hidden mt-2 ml-6">
                                    @php
                                        $nonDefaultAddresses =
                                            $user->retailerProfile && $user->retailerProfile->addresses
                                                ? $user->retailerProfile->addresses->filter(function ($address) {
                                                    return !$address->is_default;
                                                })
                                                : collect();
                                    @endphp

                                    @if ($nonDefaultAddresses->count() > 0)
                                        @foreach ($nonDefaultAddresses as $address)
                                            <div class="flex items-center mb-2">
                                                <input type="radio" id="address_{{ $address->id }}"
                                                    name="selected_address_id" value="{{ $address->id }}"
                                                    class="form-radio">
                                                <label for="address_{{ $address->id }}" class="ml-2">
                                                    {{ $address->label }}: {{ $address->barangay_name }},
                                                    {{ $address->street }}
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-sm text-gray-500">No additional saved addresses found. Please add
                                            an address in your profile.</p>
                                    @endif
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
                                            You can split this order and deliver products to multiple addresses.
                                        </p>
                                    @endif
                                </div>

                                <!-- Multi-Address Selection (initially hidden) -->
                                <div id="multiAddressSection"
                                    class="hidden pt-4 mt-4 space-y-4 border-t border-gray-200">
                                    <h3 class="text-lg font-medium">Split Order Items</h3>
                                    <p class="text-sm text-gray-600">Create delivery locations and assign products to
                                        each location.</p>

                                    <div id="locationContainer" class="space-y-8">
                                        <!-- First location (automatically added) -->
                                        <div id="location-1" class="p-5 border border-gray-200 rounded-lg">
                                            <div class="flex items-center justify-between mb-4">
                                                <h4 class="text-lg font-medium text-gray-900">Delivery Location 1</h4>
                                            </div>

                                            <!-- Address selection -->
                                            <div class="mb-4">
                                                <label class="block mb-1 text-sm font-medium text-gray-700">Delivery
                                                    Address</label>
                                                <select name="locations[0][address_id]"
                                                    class="block w-full px-3 py-2 text-gray-900 border-gray-300 rounded-md address-select focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                                    data-location="1">
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
                                                <div class="hidden mt-1 text-sm text-red-500 address-error-1">
                                                    This address is already used in another location
                                                </div>
                                            </div>

                                            <!-- Product assignment table -->
                                            <div class="mt-4">
                                                <div class="flex items-center justify-between mb-3">
                                                    <h5 class="font-medium text-gray-700">Assign Products</h5>
                                                    <button type="button"
                                                        class="px-2 py-1 text-xs font-medium text-green-600 bg-green-100 rounded hover:bg-green-200 add-all-products"
                                                        data-location="1">
                                                        Add All Products
                                                    </button>
                                                </div>

                                                <div class="overflow-x-auto">
                                                    <table class="w-full text-left border-collapse product-table">
                                                        <thead>
                                                            <tr class="text-sm font-medium text-gray-700 border-b">
                                                                <th class="px-4 py-2">Product</th>
                                                                <th class="px-4 py-2">Unit Price</th>
                                                                <th class="px-4 py-2">Quantity</th>
                                                                <th class="px-4 py-2">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="location-1-products" class="products-container">
                                                            <!-- Products will be added dynamically -->
                                                            <tr class="text-sm text-gray-600 empty-row">
                                                                <td colspan="4" class="px-4 py-3 text-center">
                                                                    No products assigned. Click "Add Product" to assign
                                                                    products to this location.
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="flex justify-center mt-3">
                                                    <button type="button"
                                                        class="flex items-center px-3 py-1 text-sm text-green-600 border border-green-600 rounded-md hover:bg-green-50 add-product-btn"
                                                        data-location="1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Add Product
                                                    </button>
                                                </div>

                                                <!-- Products available for selection (initially hidden) -->
                                                <div id="product-selector-1"
                                                    class="hidden p-3 mt-3 border rounded-md product-selector">
                                                    <div class="mb-2 text-sm font-medium text-gray-700">Select a
                                                        product to add:</div>
                                                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                                                        @foreach ($checkoutProducts as $product)
                                                            <div class="flex items-center p-2 border rounded-md hover:bg-gray-50 product-option"
                                                                data-id="{{ $product['id'] }}"
                                                                data-product-id="{{ $product['product']->id }}"
                                                                data-name="{{ $product['product']->product_name }}"
                                                                data-price="{{ $product['product']->price }}"
                                                                data-quantity="{{ $product['quantity'] }}"
                                                                data-available="{{ $product['quantity'] }}"
                                                                data-location="1">
                                                                <img class="object-cover w-10 h-10 mr-2"
                                                                    src="{{ asset('storage/' . $product['product']->image) }}"
                                                                    alt="{{ $product['product']->product_name }}">
                                                                <div>
                                                                    <div class="font-medium">
                                                                        {{ $product['product']->product_name }}</div>
                                                                    <div class="text-xs text-gray-500">Available:
                                                                        {{ $product['quantity'] }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
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
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full px-4 py-3 mt-6 text-white transition-colors duration-200 bg-green-600 rounded-xl hover:bg-green-700">
                                Place Order
                            </button>
                        </form>
                    @else
                        <div class="p-4 mb-4 rounded bg-red-50">
                            <p class="text-red-600">Unable to process checkout due to missing cart or distributor
                                information.</p>
                            <p class="mt-2">
                                <a href="{{ route('retailers.cart.index') }}" class="text-blue-600 underline">Return
                                    to
                                    your cart</a> and try again or contact support if the problem persists.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('components.footer')

    <script>
        // Initialize product data with available quantities
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure the radio buttons have event listeners
            if (defaultAddressRadio) {
                defaultAddressRadio.addEventListener("change", function() {
                    if (this.checked) {
                        savedAddressesContainer.classList.add("hidden");
                    }
                });
            }

            if (savedAddressRadio) {
                savedAddressRadio.addEventListener("change", function() {
                    if (this.checked) {
                        savedAddressesContainer.classList.remove("hidden");
                    }
                });
            }

            // Initialize product data if available
            if (products.length > 0) {
                initializeLocations();
            }
        });
        const defaultAddressRadio = document.getElementById("default_address");
        const savedAddressRadio = document.getElementById("saved_address");
        const savedAddressesContainer = document.getElementById("savedAddressesContainer");
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
                available: parseInt(el.dataset.quantity),
                image: el.dataset.image  // Initialize available with full quantity
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
                btn.addEventListener('click', function() {
                    const locationId = this.dataset.location;
                    const selector = document.getElementById(`product-selector-${locationId}`);
                    selector.classList.toggle('hidden');
                });
            });

            // Add "Add All Products" button listeners
            document.querySelectorAll('.add-all-products').forEach(btn => {
                btn.addEventListener('click', function() {
                    const locationId = this.dataset.location;

                    // Add each available product that hasn't been assigned yet
                    products.forEach(product => {
                        if (product.available > 0) {
                            addProductToLocation(product, locationId, product.available);
                        }
                    });
                });
            });

            // Add product selection listeners
            document.querySelectorAll('.product-option').forEach(option => {
                option.addEventListener('click', function() {
                    const locationId = this.dataset.location;
                    const productId = this.dataset.productId;
                    const product = products.find(p => p.productId == productId);

                    // Add product with maximum available quantity
                    if (product && product.available > 0) {
                        addProductToLocation(product, locationId, product.available);

                        // Hide the selector after selection
                        document.getElementById(`product-selector-${locationId}`).classList.add('hidden');
                    }
                });
            });
        }

        // Add Location button functionality
        addLocationBtn.addEventListener("click", function() {
            if (locationCount >= 3) {
                Swal.fire({
                    title: 'Maximum Locations Reached',
                    text: 'You can only deliver to a maximum of 3 locations.',
                    icon: 'warning'
                });
                return;
            }

            locationCount++;
            createNewLocation(locationCount);
        });

        // Create a new location section
        function createNewLocation(locationId) {
            const locationContainer = document.getElementById('locationContainer');

            // Create location HTML
            const newLocation = document.createElement('div');
            newLocation.id = `location-${locationId}`;
            newLocation.className = 'p-5 border border-gray-200 rounded-lg';

            newLocation.innerHTML = `
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-medium text-gray-900">Delivery Location ${locationId}</h4>
            <button type="button" onclick="removeLocation(${locationId})" 
                class="text-red-600 hover:text-red-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        
        <!-- Address selection -->
        <div class="mb-4">
            <label class="block mb-1 text-sm font-medium text-gray-700">Delivery Address</label>
            <select name="locations[${locationId-1}][address_id]" 
                class="block w-full px-3 py-2 text-gray-900 border-gray-300 rounded-md address-select focus:border-green-500 focus:ring-green-500 sm:text-sm"
                data-location="${locationId}">
                <option value="default">Default Address</option>
                @if ($user->retailerProfile && $user->retailerProfile->addresses && $user->retailerProfile->addresses->count() > 0)
                    @foreach ($user->retailerProfile->addresses as $address)
                        <option value="{{ $address->id }}">
                            {{ $address->label }}: {{ $address->barangay_name }}, {{ $address->street }}
                        </option>
                    @endforeach
                @endif
            </select>
            <div class="address-error-${locationId} hidden mt-1 text-sm text-red-500">
                This address is already used in another location
            </div>
        </div>

        <!-- Product assignment table -->
        <div class="mt-4">
            <div class="flex items-center justify-between mb-3">
                <h5 class="font-medium text-gray-700">Assign Products</h5>
                <button type="button" class="px-2 py-1 text-xs font-medium text-green-600 bg-green-100 rounded hover:bg-green-200 add-all-products" data-location="${locationId}">
                    Add All Products
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse product-table">
                    <thead>
                        <tr class="text-sm font-medium text-gray-700 border-b">
                            <th class="px-4 py-2">Product</th>
                            <th class="px-4 py-2">Unit Price</th>
                            <th class="px-4 py-2">Quantity</th>
                            <th class="px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody id="location-${locationId}-products" class="products-container">
                        <!-- Products will be added dynamically -->
                        <tr class="text-sm text-gray-600 empty-row">
                            <td colspan="4" class="px-4 py-3 text-center">
                                No products assigned. Click "Add Product" to assign products to this location.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center mt-3">
                <button type="button" class="flex items-center px-3 py-1 text-sm text-green-600 border border-green-600 rounded-md hover:bg-green-50 add-product-btn" data-location="${locationId}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Product
                </button>
            </div>

            <!-- Products available for selection (initially hidden) -->
            <div id="product-selector-${locationId}" class="hidden p-3 mt-3 border rounded-md product-selector">
                <div class="mb-2 text-sm font-medium text-gray-700">Select a product to add:</div>
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2 product-options-container-${locationId}">
                    <!-- Product options will be added dynamically -->
                </div>
            </div>
        </div>
    `;

            locationContainer.appendChild(newLocation);

            // Create product options for this location
            const optionsContainer = document.querySelector(`.product-options-container-${locationId}`);

            products.forEach(product => {
                if (product.available > 0) {
                    const option = document.createElement('div');
                    option.className = 'flex items-center p-2 border rounded-md hover:bg-gray-50 product-option';
                    option.dataset.id = product.id;
                    option.dataset.productId = product.productId;
                    option.dataset.name = product.name;
                    option.dataset.price = product.price;
                    option.dataset.quantity = product.quantity;
                    option.dataset.available = product.available;
                    option.dataset.location = locationId;

                    option.innerHTML = `
                <img class="object-cover w-10 h-10 mr-2" src="{{ asset('storage/') }}/${product.image || 'products/default.png'}" alt="${product.name}">
                <div>
                    <div class="font-medium">${product.name}</div>
                    <div class="text-xs text-gray-500">Available: ${product.available}</div>
                </div>
            `;
                
                    optionsContainer.appendChild(option);
                }
            });

            // Add event listeners for the new location
            const addressSelect = newLocation.querySelector('.address-select');
            addressSelect.addEventListener('change', validateAddressSelections);

            const addProductBtn = newLocation.querySelector('.add-product-btn');
            addProductBtn.addEventListener('click', function() {
                const selector = document.getElementById(`product-selector-${locationId}`);
                selector.classList.toggle('hidden');
            });

            const addAllProductsBtn = newLocation.querySelector('.add-all-products');
            addAllProductsBtn.addEventListener('click', function() {
                // Add each available product
                products.forEach(product => {
                    if (product.available > 0) {
                        addProductToLocation(product, locationId, product.available);
                    }
                });
            });

            // Add listeners to product options
            newLocation.querySelectorAll('.product-option').forEach(option => {
                option.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const product = products.find(p => p.productId == productId);

                    if (product && product.available > 0) {
                        addProductToLocation(product, locationId, product.available);
                        document.getElementById(`product-selector-${locationId}`).classList.add('hidden');
                    }
                });
            });
        }

        // Remove location function
        window.removeLocation = function(locationId) {
            const locationDiv = document.getElementById(`location-${locationId}`);
            if (locationDiv) {
                // Release all products assigned to this location
                products.forEach(product => {
                    if (assignedProducts[product.productId][locationId]) {
                        const quantity = assignedProducts[product.productId][locationId];
                        product.available += quantity;
                        delete assignedProducts[product.productId][locationId];
                    }
                });

                // Remove the location DOM element
                locationDiv.remove();

                // Update product options for all locations
                updateAllProductOptions();

                // Revalidate address selections
                validateAddressSelections();
            }
        };

        // Add a product to a location
        function addProductToLocation(product, locationId, quantity) {
            const containerSelector = `#location-${locationId}-products`;
            const container = document.querySelector(containerSelector);

            // Remove the empty row if it exists
            const emptyRow = container.querySelector('.empty-row');
            if (emptyRow) {
                emptyRow.remove();
            }

            // Check if product is already assigned to this location
            const existingRow = container.querySelector(`[data-product-id="${product.productId}"]`);
            if (existingRow) {
                // Update quantity if already assigned
                const quantityInput = existingRow.querySelector('input[type="number"]');
                const currentQty = parseInt(quantityInput.value);
                quantityInput.value = currentQty + quantity;

                // Update tracking
                assignedProducts[product.productId][locationId] = currentQty + quantity;
            } else {
                // Create new row for product
                const row = document.createElement('tr');
                row.className = 'text-sm border-b product-row';
                row.dataset.productId = product.productId;

                row.innerHTML = `
            <td class="px-4 py-3">
                <div class="flex items-center">
                    <span class="font-medium">${product.name}</span>
                    <input type="hidden" name="locations[${locationId-1}][products][${product.productId}][product_id]" value="${product.productId}">
                    <input type="hidden" name="locations[${locationId-1}][products][${product.productId}][cart_detail_id]" value="${product.id}">
                </div>
            </td>
            <td class="px-4 py-3">₱${product.price.toFixed(2)}</td>
            <td class="px-4 py-3">
                <input type="number" 
                    name="locations[${locationId-1}][products][${product.productId}][quantity]" 
                    min="1" 
                    max="${quantity}" 
                    value="${quantity}" 
                    class="w-20 px-2 py-1 text-gray-900 border-gray-300 rounded-md product-quantity focus:border-green-500 focus:ring-green-500"
                    data-product-id="${product.productId}" 
                    data-location="${locationId}" 
                    data-original-qty="${quantity}"
                    onchange="updateProductQuantity(${product.productId}, ${locationId}, this.value)">
            </td>
            <td class="px-4 py-3">
                <button type="button" onclick="removeProduct(${product.productId}, ${locationId})" class="text-red-600 hover:text-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </td>
        `;

                container.appendChild(row);

                // Track this assignment
                assignedProducts[product.productId][locationId] = quantity;
            }

            // Update available quantity
            product.available -= quantity;

            // Update product options across all locations
            updateAllProductOptions();
        }

        // Update product quantity when user changes the input
        window.updateProductQuantity = function(productId, locationId, newQuantity) {
            newQuantity = parseInt(newQuantity);
            const product = products.find(p => p.productId == productId);

            if (!product) return;

            // Get current assigned quantity
            const currentQuantity = assignedProducts[productId][locationId] || 0;

            // Calculate difference
            const difference = newQuantity - currentQuantity;

            // Check if the change is valid
            if (difference > 0 && difference > product.available) {
                // Not enough available quantity
                Swal.fire({
                    title: 'Insufficient Quantity',
                    text: `Only ${product.available} more units available for this product.`,
                    icon: 'error'
                });

                // Reset to previous value
                document.querySelector(
                        `#location-${locationId}-products [data-product-id="${productId}"] input[type="number"]`)
                    .value = currentQuantity;
                return;
            }

            // Update tracking
            assignedProducts[productId][locationId] = newQuantity;

            // Update available quantity
            product.available -= difference;

            // Update product options
            updateAllProductOptions();

            // Validate total quantities
            validateTotalProductQuantities();
        };

        // Remove a product from a location
        window.removeProduct = function(productId, locationId) {
            const row = document.querySelector(`#location-${locationId}-products [data-product-id="${productId}"]`);
            if (!row) return;

            // Get quantity to release
            const quantity = assignedProducts[productId][locationId] || 0;

            // Release quantity back to available
            const product = products.find(p => p.productId == productId);
            if (product) {
                product.available += quantity;
            }

            // Remove tracking
            delete assignedProducts[productId][locationId];

            // Remove row
            row.remove();

            // Add empty row if no products left
            const container = document.querySelector(`#location-${locationId}-products`);
            if (container.querySelectorAll('.product-row').length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.className = 'text-sm text-gray-600 empty-row';
                emptyRow.innerHTML = `
            <td colspan="4" class="px-4 py-3 text-center">
                No products assigned. Click "Add Product" to assign products to this location.
            </td>
        `;
                container.appendChild(emptyRow);
            }

            // Update product options
            updateAllProductOptions();

            // Validate total quantities
            validateTotalProductQuantities();
        };

        // Update all product options based on available quantities
        function updateAllProductOptions() {
            for (let i = 1; i <= locationCount; i++) {
                const optionsContainer = document.querySelector(`.product-options-container-${i}`);
                if (!optionsContainer) continue;

                // Clear existing options
                optionsContainer.innerHTML = '';

                // Add options for products with available quantity
                products.forEach(product => {
                    if (product.available > 0) {
                        const option = document.createElement('div');
                        option.className =
                            'flex items-center p-2 border rounded-md hover:bg-gray-50 product-option';
                        option.dataset.id = product.id;
                        option.dataset.productId = product.productId;
                        option.dataset.name = product.name;
                        option.dataset.price = product.price;
                        option.dataset.quantity = product.quantity;
                        option.dataset.available = product.available;
                        option.dataset.location = i;

                        option.innerHTML = `
                    <img class="object-cover w-10 h-10 mr-2" src="{{ asset('storage/') }}/${product.image || 'products/default.png'}" alt="${product.name}">
                    <div>
                        <div class="font-medium">${product.name}</div>
                        <div class="text-xs text-gray-500">Available: ${product.available}</div>
                    </div>
                `;

                        optionsContainer.appendChild(option);

                        // Add click event
                        option.addEventListener('click', function() {
                            const locationId = this.dataset.location;
                            const productId = this.dataset.productId;
                            const product = products.find(p => p.productId == productId);

                            if (product && product.available > 0) {
                                addProductToLocation(product, locationId, product.available);
                                document.getElementById(`product-selector-${locationId}`).classList.add(
                                    'hidden');
                            }
                        });
                    }
                });

                // Show "no products" message if none available
                if (optionsContainer.children.length === 0) {
                    const noProducts = document.createElement('div');
                    noProducts.className = 'p-3 text-sm text-gray-500 text-center';
                    noProducts.textContent = 'No products available to add.';
                    optionsContainer.appendChild(noProducts);
                }
            }
        }

        // Validate address selections
        function validateAddressSelections() {
            const addressSelects = document.querySelectorAll('.address-select');
            const usedAddresses = {};
            let isValid = true;

            // Reset all errors
            document.querySelectorAll('[class^="address-error-"]').forEach(el => {
                el.classList.add('hidden');
            });

            // Check for duplicates
            addressSelects.forEach(select => {
                const addressId = select.value;
                const locationId = select.dataset.location;

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
                }
            });

            return isValid;
        }

        // Form submission handling
        const orderForm = document.getElementById("orderForm");
        orderForm.addEventListener("submit", function(e) {
            e.preventDefault();

            // If multi-address enabled, validate
            if (enableMultiAddressCheckbox.checked) {
                // Check that there's at least one product assigned to each location
                let hasEmptyLocation = false;
                for (let i = 1; i <= locationCount; i++) {
                    const productContainer = document.querySelector(`#location-${i}-products`);
                    if (productContainer.querySelectorAll('.product-row').length === 0) {
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

        // Toggle multi-address section
        enableMultiAddressCheckbox.addEventListener("change", function() {
            if (this.checked) {
                multiAddressSection.classList.remove("hidden");
                // Disable other delivery options
                defaultAddressRadio.disabled = true;
                savedAddressRadio.disabled = true;

                // Initialize UI if not already done
                initializeLocations();
            } else {
                multiAddressSection.classList.add("hidden");
                // Enable other delivery options
                defaultAddressRadio.disabled = false;
                savedAddressRadio.disabled = false;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize product buttons and locations
            if (products.length > 0) {
                initializeLocations();
            }
        });
    </script>
</x-app-layout>
